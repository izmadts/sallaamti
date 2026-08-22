<?php

use App\Models\TeamMember;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * The About/Team pages used to render the founder block from a
     * hardcoded Blade partial (name, bio, and a static img/team-1.jpg
     * asset). Now that both pages render the founder from the
     * team_members table (is_founder = true) so an admin can manage it,
     * this seeds that same content as a real row — otherwise the founder
     * section would just be empty until someone manually re-typed it in
     * the admin UI.
     */
    public function up(): void
    {
        if (TeamMember::where('is_founder', true)->exists()) {
            return;
        }

        $photoPath = null;
        $sourcePhoto = public_path('img/team-1.jpg');
        if (file_exists($sourcePhoto)) {
            $photoPath = 'team-members/founder-mubashar-ahmed.jpg';
            Storage::disk('public')->put($photoPath, file_get_contents($sourcePhoto));
        }

        TeamMember::create([
            'name' => 'Mubashar Ahmed',
            'role' => 'Founder & Director',
            'bio' => '<p>With a deep passion for Islamic education and community service, Mubashar founded Sallaamti with the vision of creating a platform where every Muslim — regardless of age, location or background — can access quality Quranic education, find a halal spouse, and build a life aligned with the Quran and Sunnah.</p>'
                . '<p>"Our goal is not just to teach the Quran — it is to help Muslims live it. اقرأ، افهم، وطبّق — Read, Understand, and Implement."</p>',
            'photo' => $photoPath,
            'order' => 0,
            'is_active' => true,
            'is_founder' => true,
            'facebook_url' => setting('social_facebook') ?: null,
            'instagram_url' => setting('social_instagram') ?: null,
            'tiktok_url' => setting('social_tiktok') ?: null,
            'whatsapp_number' => setting('social_whatsapp') ?: null,
        ]);
    }

    public function down(): void
    {
        TeamMember::where('name', 'Mubashar Ahmed')->where('is_founder', true)->delete();
    }
};
