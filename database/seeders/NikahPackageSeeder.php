<?php

namespace Database\Seeders;

use App\Models\NikahPackage;
use Illuminate\Database\Seeder;

// The initial three launch packages, plus a fourth (VIP) seeded inactive
// and hidden from the public page — ready for an admin to switch on later
// once there's a track record to justify the price, without needing a
// developer or a new migration. Every field here is editable afterward
// from admin > Nikah Packages; this only sets sensible starting values.
class NikahPackageSeeder extends Seeder
{
    public function run(): void
    {
        NikahPackage::updateOrCreate(['slug' => 'verified'], [
            'name' => 'Sallaamti Verified',
            'tagline' => 'Create and verify your own profile',
            'price' => 1000,
            'duration_days' => null,
            'proposal_limit' => null,
            'consultant_level' => 'Basic onboarding only',
            'description' => 'For people who want to use the platform themselves — registration, profile creation, verification, and full access to normal Sallaamti Nikah features. Consultant involvement is limited to basic onboarding, not personalized matchmaking.',
            'features' => [
                'Registration & profile creation',
                'Profile verification',
                'Verified badge',
                'Profile activation',
                'Full access to normal Sallaamti Nikah features',
                'Basic support',
            ],
            'color' => 'green',
            'icon' => '🟢',
            'sort_order' => 1,
            'is_active' => true,
            'show_on_public_page' => true,
        ]);

        NikahPackage::updateOrCreate(['slug' => 'assisted-matchmaking'], [
            'name' => 'Assisted Matchmaking',
            'tagline' => 'Guided matchmaking with a consultant\'s help',
            'price' => 5000,
            'duration_days' => 90,
            'proposal_limit' => 15,
            'consultant_level' => 'Assisted',
            'description' => 'Our main matchmaking package — a consultant works with you on your requirements and actively searches on your behalf. Up to 5 suitable proposals are reviewed and shared per month (up to 15 over 3 months), based on database availability and compatibility. This is a cap on proposals shared, not a guarantee of matches.',
            'features' => [
                'Everything in Sallaamti Verified',
                'Female/male consultant assistance as appropriate',
                'Requirement consultation',
                'Profile optimization',
                'Search according to your requirements',
                'Up to 15 suitable proposals reviewed & shortlisted over 3 months (subject to availability & compatibility)',
                'Follow-up on mutual interest',
                'WhatsApp support',
                'Monthly profile review',
                'Guidance regarding family involvement',
            ],
            'color' => 'amber',
            'icon' => '🟡',
            'sort_order' => 2,
            'is_active' => true,
            'show_on_public_page' => true,
        ]);

        NikahPackage::updateOrCreate(['slug' => 'premium-personal-matchmaking'], [
            'name' => 'Premium Personal Matchmaking',
            'tagline' => '"I don\'t have time — you manage everything."',
            'price' => 12000,
            'duration_days' => 90,
            'proposal_limit' => 30,
            'consultant_level' => 'Dedicated',
            'description' => 'A dedicated consultant manages your search end to end, from a detailed requirement interview to weekly progress updates. Up to 10 curated proposals are reviewed per month (up to 30 over 3 months), based on database availability and compatibility — a cap on proposals reviewed, not a guarantee of matches.',
            'features' => [
                'Everything in Assisted Matchmaking',
                'Dedicated personal consultant',
                'Detailed requirement interview',
                'Priority database search',
                'Profile optimization',
                'Up to 30 curated proposals reviewed over 3 months (subject to availability & compatibility)',
                'Compatibility review',
                'Priority follow-ups',
                'Family coordination assistance',
                'Scheduled consultation calls',
                'Weekly progress updates',
                'Priority support',
            ],
            'color' => 'blue',
            'icon' => '🔵',
            'sort_order' => 3,
            'is_active' => true,
            'show_on_public_page' => true,
        ]);

        // Inactive on purpose — enable + set show_on_public_page once there's
        // a database and success stories to justify the price (see the
        // package's own description).
        NikahPackage::updateOrCreate(['slug' => 'premium-vip'], [
            'name' => 'Sallaamti Premium / VIP',
            'tagline' => 'Dedicated senior consultant, fully bespoke search',
            'price' => 30000,
            'duration_days' => 90,
            'proposal_limit' => null,
            'consultant_level' => 'Dedicated senior consultant',
            'description' => 'A fully bespoke matchmaking service for highly specific requirements, overseas Pakistani searches, and confidential profiles — extensive manual research by a senior consultant rather than a fixed proposal count. Held back from public launch until the database and track record justify it.',
            'features' => [
                'Everything in Premium Personal Matchmaking',
                'Dedicated senior consultant',
                'Highly specific requirement handling',
                'Overseas Pakistani candidate search',
                'Confidential profile handling',
                'Family-to-family coordination',
                'Extensive manual research',
                'Priority access',
                'Personal consultation',
            ],
            'color' => 'red',
            'icon' => '🔴',
            'sort_order' => 4,
            'is_active' => false,
            'show_on_public_page' => false,
        ]);
    }
}
