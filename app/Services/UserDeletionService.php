<?php

namespace App\Services;

use App\Exceptions\UserDeletionBlockedException;
use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\DuaRequest;
use App\Models\Lead;
use App\Models\QuranClassGroup;
use App\Models\QuranLiveCourse;
use App\Models\QuranSubscription;
use App\Models\Reaction;
use App\Models\SavedPost;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// The single place both the admin "Delete User" action and the scheduled
// account-purge job go through. Most related tables already cascade at the
// DB level (nikah_profiles, enrollments, certificates, posts, comments,
// reactions, admissions, etc. — see the 2026_09_02_130000 migration for the
// staff/audit-trail columns that were fixed to nullOnDelete instead), but
// three things a plain `$user->delete()` never handles on its own:
// 1. Active operational roles (teacher, hired counselor) must block
//    deletion rather than silently orphan the students/clients depending
//    on them.
// 2. Polymorphic comment/reaction/saved-post rows aren't real foreign
//    keys, so InnoDB's cascade never reaches them.
// 3. Files on disk never clean up on their own.
class UserDeletionService
{
    public function delete(User $user): void
    {
        $this->assertDeletable($user);

        DB::transaction(function () use ($user) {
            $this->deleteOrphanedPolymorphicRows($user);
            $this->deleteFiles($user);

            DB::table('sessions')->where('user_id', $user->id)->delete();

            $user->syncRoles([]);
            $user->syncPermissions([]);
            $user->delete();
        });
    }

    private function assertDeletable(User $user): void
    {
        $hasActiveTeachingRole = QuranClassGroup::where('teacher_id', $user->id)->where('is_active', true)->exists()
            || QuranLiveCourse::where('teacher_id', $user->id)->where('is_published', true)->exists();

        if ($hasActiveTeachingRole) {
            throw new UserDeletionBlockedException(
                "{$user->name} is the teacher on an active Quran Live course or class group — reassign it to another teacher before deleting this account."
            );
        }

        $hasActiveClients = Lead::where('assigned_to', $user->id)
            ->whereNotIn('status', ['not_interested', 'closed'])
            ->exists();

        if ($hasActiveClients) {
            throw new UserDeletionBlockedException(
                "{$user->name} has active Nikah Counselor clients assigned to them — reassign those leads to another counselor before deleting this account."
            );
        }
    }

    // Comments/reactions/saved-posts point at their parent via
    // (*_type, *_id) pairs rather than a real foreign key, so InnoDB's
    // cascade from dua_requests (a real FK, cascadeOnDelete) never reaches
    // rows that merely reference the dua_request's id/type by string —
    // clean those up explicitly before the parent disappears, or they
    // become permanent, silent orphans pointing at nothing.
    private function deleteOrphanedPolymorphicRows(User $user): void
    {
        $duaRequestIds = DuaRequest::where('user_id', $user->id)->pluck('id');

        if ($duaRequestIds->isEmpty()) {
            return;
        }

        Comment::where('commentable_type', DuaRequest::class)->whereIn('commentable_id', $duaRequestIds)->delete();
        Reaction::where('reactable_type', DuaRequest::class)->whereIn('reactable_id', $duaRequestIds)->delete();
        SavedPost::where('saveable_type', DuaRequest::class)->whereIn('saveable_id', $duaRequestIds)->delete();
    }

    // Only for rows that are actually about to cascade-delete. Rows that
    // survive by design — donations, matchmaker applications, testimonials,
    // leads (all nullOnDelete, kept as anonymized/historical records) —
    // keep their files too, since the row referencing them still exists.
    private function deleteFiles(User $user): void
    {
        if ($user->avatar) {
            Storage::disk('private')->delete($user->avatar);
        }

        if ($profile = $user->nikahProfile) {
            $profile->loadMissing('photos');

            foreach (['photo', 'cnic_front_image', 'cnic_back_image', 'payment_screenshot'] as $field) {
                if ($profile->$field) {
                    Storage::disk('private')->delete($profile->$field);
                }
            }

            foreach ($profile->photos as $galleryPhoto) {
                Storage::disk('private')->delete($galleryPhoto->path);
            }
        }

        foreach (QuranSubscription::where('user_id', $user->id)->get() as $subscription) {
            if ($subscription->payment_screenshot) {
                Storage::disk('private')->delete($subscription->payment_screenshot);
            }
        }

        foreach ($user->posts as $post) {
            if ($post->cover_image) {
                Storage::disk('public')->delete($post->cover_image);
            }
        }

        foreach (BlogPost::where('user_id', $user->id)->get() as $blogPost) {
            if ($blogPost->cover_image) {
                Storage::disk('public')->delete($blogPost->cover_image);
            }
        }
    }
}
