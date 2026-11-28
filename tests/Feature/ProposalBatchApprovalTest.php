<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\MatchmakerApplication;
use App\Models\MatchProposal;
use App\Models\NikahProfile;
use App\Models\ProposalBatch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

// Verifies the 15/16 Nov 2026 trainee-access addendum's Probation
// proposal-batch approval workflow against SQLite (the local MySQL dev DB
// has an unrelated, pre-existing orphaned proposal_batches tablespace that
// blocks live manual testing of this one table — see session notes).
class ProposalBatchApprovalTest extends TestCase
{
    use RefreshDatabase;

    private static int $seq = 0;

    // Roles come from RoleSeeder, which RefreshDatabase doesn't run — without
    // it every assignRole() here threw RoleDoesNotExist.
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    private function makeUser(): User
    {
        self::$seq++;

        $user = User::create([
            'name' => 'Test User ' . self::$seq,
            'email' => "test-user-" . self::$seq . "@example.test",
            'password' => Hash::make('password'),
        ]);
        // is_active isn't mass-assignable on User, so create() silently
        // dropped it and EnsureUserIsActiveApi rejected every API call.
        $user->forceFill(['is_active' => true])->save();

        return $user;
    }

    private function makeProfile(User $owner): NikahProfile
    {
        return NikahProfile::create([
            'user_id' => $owner->id,
            'age' => 28,
            'city' => 'Lahore',
            'guardian_name' => 'Test Guardian',
            'guardian_contact' => '03001234567',
        ]);
    }

    private function makeTrainee(string $stage): array
    {
        $user = $this->makeUser();
        $user->assignRole('matchmaker_trainee');

        self::$seq++;
        $application = MatchmakerApplication::create([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'guardian_name' => 'Test Guardian',
            'mobile_number' => '0300' . str_pad((string) self::$seq, 7, '0', STR_PAD_LEFT),
            'gender' => 'female',
            'age' => 28,
            'marital_status' => 'never_married',
            'qualification' => 'bachelors',
            'selfie_photo' => 'test/selfie.jpg',
            'status' => $stage,
        ]);

        return [$user, $application];
    }

    public function test_probation_trainee_batch_waits_for_approval(): void
    {
        Notification::fake();

        [$user] = $this->makeTrainee('probation');
        $admin = $this->makeUser();
        $admin->assignRole('admin');

        $profileOwner = $this->makeUser();
        $profile = $this->makeProfile($profileOwner);
        $candidate = $this->makeProfile($this->makeUser());
        $lead = Lead::create(['name' => 'Client', 'assigned_to' => $user->id, 'created_by' => $user->id, 'nikah_profile_id' => $profile->id]);

        $batch = ProposalBatch::create([
            'lead_id' => $lead->id,
            'nikah_profile_id' => $profile->id,
            'matchmaker_id' => $user->id,
            'batch_number' => 1,
            'status' => 'ready',
        ]);
        MatchProposal::create(['proposal_batch_id' => $batch->id, 'candidate_profile_id' => $candidate->id]);

        $this->actingAs($user)
            ->postJson("/api/v1/matchmaker/clients/{$lead->id}/proposal-batches/{$batch->id}/send")
            ->assertOk();

        $this->assertSame('pending_approval', $batch->fresh()->status);
        Notification::assertSentTo($admin, \App\Notifications\ProposalBatchAwaitingApproval::class);
    }

    public function test_admin_approve_sends_it_on_to_the_client(): void
    {
        Notification::fake();

        [$user] = $this->makeTrainee('probation');
        $admin = $this->makeUser();
        $admin->assignRole('admin');

        $clientUser = $this->makeUser();
        $profile = $this->makeProfile($clientUser);
        $candidate = $this->makeProfile($this->makeUser());
        $lead = Lead::create(['name' => 'Client', 'assigned_to' => $user->id, 'created_by' => $user->id, 'nikah_profile_id' => $profile->id]);

        $batch = ProposalBatch::create([
            'lead_id' => $lead->id, 'nikah_profile_id' => $profile->id, 'matchmaker_id' => $user->id,
            'batch_number' => 1, 'status' => 'pending_approval',
        ]);
        MatchProposal::create(['proposal_batch_id' => $batch->id, 'candidate_profile_id' => $candidate->id]);

        $this->actingAs($admin)
            ->post("/admin/proposal-batches/{$batch->id}/approve")
            ->assertRedirect();

        $batch->refresh();
        $this->assertSame('sent', $batch->status);
        $this->assertSame('sent', $batch->proposals()->first()->status);
        Notification::assertSentTo($clientUser, \App\Notifications\NewProposalBatchReceived::class);
    }

    public function test_admin_reject_sends_it_back_to_the_counselor(): void
    {
        Notification::fake();

        [$user] = $this->makeTrainee('probation');
        $admin = $this->makeUser();
        $admin->assignRole('admin');

        $profile = $this->makeProfile($this->makeUser());
        $lead = Lead::create(['name' => 'Client', 'assigned_to' => $user->id, 'created_by' => $user->id, 'nikah_profile_id' => $profile->id]);

        $batch = ProposalBatch::create([
            'lead_id' => $lead->id, 'nikah_profile_id' => $profile->id, 'matchmaker_id' => $user->id,
            'batch_number' => 1, 'status' => 'pending_approval',
        ]);

        $this->actingAs($admin)
            ->post("/admin/proposal-batches/{$batch->id}/reject", ['reason' => 'Wrong candidate picked'])
            ->assertRedirect();

        $this->assertSame('ready', $batch->fresh()->status);
        Notification::assertSentTo($user, \App\Notifications\ProposalBatchApprovalRejected::class);
    }

    public function test_certified_matchmaker_still_sends_straight_through(): void
    {
        Notification::fake();

        $user = $this->makeUser();
        $user->assignRole('matchmaker');

        $clientUser = $this->makeUser();
        $profile = $this->makeProfile($clientUser);
        $candidate = $this->makeProfile($this->makeUser());
        $lead = Lead::create(['name' => 'Client', 'assigned_to' => $user->id, 'created_by' => $user->id, 'nikah_profile_id' => $profile->id]);

        $batch = ProposalBatch::create([
            'lead_id' => $lead->id, 'nikah_profile_id' => $profile->id, 'matchmaker_id' => $user->id,
            'batch_number' => 1, 'status' => 'ready',
        ]);
        MatchProposal::create(['proposal_batch_id' => $batch->id, 'candidate_profile_id' => $candidate->id]);

        $this->actingAs($user)
            ->postJson("/api/v1/matchmaker/clients/{$lead->id}/proposal-batches/{$batch->id}/send")
            ->assertOk();

        $this->assertSame('sent', $batch->fresh()->status);
        Notification::assertSentTo($clientUser, \App\Notifications\NewProposalBatchReceived::class);
    }
}
