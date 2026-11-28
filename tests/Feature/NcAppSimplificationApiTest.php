<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\NikahProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

// The simplified Nikah Counselor app (owner decisions, 28 Nov 2026):
// verification state per client, today's tasks + customer-service number on
// the dashboard, automatic consent when a batch is started, and the
// package-only Quality Score.
class NcAppSimplificationApiTest extends TestCase
{
    use RefreshDatabase;

    private static int $seq = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    private function makeUser(): User
    {
        self::$seq++;
        $user = User::create([
            'name' => 'NC Test ' . self::$seq,
            'email' => 'nc-test-' . self::$seq . '@example.test',
            'password' => Hash::make('password'),
        ]);
        $user->forceFill(['is_active' => true])->save();

        return $user;
    }

    private function makeCounselor(): User
    {
        $user = $this->makeUser();
        $user->assignRole('matchmaker');

        return $user;
    }

    private function makeProfile(string $verification = 'pending', string $payment = 'unpaid'): NikahProfile
    {
        $profile = NikahProfile::create([
            'user_id' => $this->makeUser()->id,
            'age' => 28,
            'city' => 'Lahore',
            'guardian_name' => 'Guardian',
            'guardian_contact' => '03001234567',
        ]);
        $profile->forceFill(['verification_status' => $verification, 'payment_status' => $payment])->save();

        return $profile;
    }

    private function makeLead(User $counselor, ?NikahProfile $profile = null, string $status = 'contacted'): Lead
    {
        $lead = Lead::create([
            'name' => 'Client ' . (++self::$seq),
            'assigned_to' => $counselor->id,
            'created_by' => $counselor->id,
            'nikah_profile_id' => $profile?->id,
        ]);
        $lead->forceFill(['status' => $status])->saveQuietly();

        return $lead;
    }

    public function test_clients_report_their_verification_state(): void
    {
        $nc = $this->makeCounselor();
        $notRegistered = $this->makeLead($nc);
        $pending = $this->makeLead($nc, $this->makeProfile());
        $rejected = $this->makeLead($nc, $this->makeProfile('rejected'));
        $verified = $this->makeLead($nc, $this->makeProfile('verified', 'confirmed'));

        $states = collect($this->actingAs($nc)->getJson('/api/v1/matchmaker/clients')->assertOk()->json('clients'))
            ->pluck('verification_state', 'id');

        $this->assertSame('not_registered', $states[$notRegistered->id]);
        $this->assertSame('pending', $states[$pending->id]);
        $this->assertSame('rejected', $states[$rejected->id]);
        $this->assertSame('verified', $states[$verified->id]);

        $this->actingAs($nc)->getJson("/api/v1/matchmaker/clients/{$pending->id}")
            ->assertOk()
            ->assertJsonPath('client.verification_state', 'pending');
    }

    public function test_dashboard_lists_todays_tasks_and_the_customer_service_number(): void
    {
        $nc = $this->makeCounselor();
        $newLead = $this->makeLead($nc, null, 'new');
        $unregistered = $this->makeLead($nc);
        $pending = $this->makeLead($nc, $this->makeProfile());
        $verified = $this->makeLead($nc, $this->makeProfile('verified', 'confirmed'));

        $response = $this->actingAs($nc)->getJson('/api/v1/matchmaker/dashboard')->assertOk();
        $tasks = collect($response->json('tasks'))->pluck('task', 'id');

        $this->assertSame('call', $tasks[$newLead->id]);
        $this->assertSame('register', $tasks[$unregistered->id]);
        $this->assertSame('verify', $tasks[$pending->id]);
        $this->assertSame('requirements', $tasks[$verified->id]);
        // Ordered by the app's flow: call before register before verify.
        $this->assertSame([$newLead->id, $unregistered->id, $pending->id, $verified->id], collect($response->json('tasks'))->pluck('id')->all());
        $response->assertJsonStructure(['support' => ['whatsapp_number']]);
    }

    public function test_starting_a_batch_records_consent_automatically(): void
    {
        $nc = $this->makeCounselor();
        $lead = $this->makeLead($nc, $this->makeProfile('verified', 'confirmed'));
        $this->assertFalse($lead->hasActiveConsent('matchmaking_participation'));

        $this->actingAs($nc)->postJson("/api/v1/matchmaker/clients/{$lead->id}/proposal-batches")->assertCreated();

        $this->assertTrue($lead->fresh()->hasActiveConsent('matchmaking_participation'));
    }

    public function test_quality_score_is_package_conversion_only(): void
    {
        $nc = $this->makeCounselor();
        $withPackage = $this->makeLead($nc, $this->makeProfile('verified', 'confirmed'));
        $this->makeLead($nc, $this->makeProfile('verified', 'confirmed'));
        $this->makeLead($nc, $this->makeProfile()); // pending: not eligible yet
        $withPackage->forceFill(['nikah_package_id' => $this->packageId()])->saveQuietly();

        $this->actingAs($nc)->getJson('/api/v1/matchmaker/performance')
            ->assertOk()
            ->assertJsonPath('stats.packages_sold', 1)
            ->assertJsonPath('stats.eligible_clients', 2)
            ->assertJsonPath('score.package_conversion_rate', 50)
            ->assertJsonPath('score.overall', 50);
    }

    private function packageId(): int
    {
        return \App\Models\NikahPackage::create([
            'name' => 'Test Package',
            'slug' => 'test-package-' . (++self::$seq),
            'price' => 1000,
        ])->id;
    }
}
