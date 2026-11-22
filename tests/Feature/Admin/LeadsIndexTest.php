<?php

namespace Tests\Feature\Admin;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

// Regression test for the 21 Nov 2026 /admin/leads 500 (filteredLeadsQuery()
// refactor in 8f815c4 moved $statuses/$sort out of index()'s own scope,
// which the view still read — php -l and a Blade cache rebuild both missed
// this since it only breaks at actual runtime, not compile time; a fast
// hotfix branch caught and fixed it before this test existed). Covers the
// index page (with filters, since that's exactly where the bug was) and the
// show page's Next/Previous nav added the same day.
class LeadsIndexTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->assignRole('admin');

        return $user;
    }

    public function test_leads_index_loads_with_no_filters(): void
    {
        $admin = $this->makeAdmin();
        Lead::create(['name' => 'A', 'status' => 'new', 'source' => 'manual']);
        Lead::create(['name' => 'B', 'status' => 'account_registered', 'source' => 'manual']);
        Lead::create(['name' => 'C', 'status' => 'pending_payment', 'source' => 'manual']);
        Lead::create(['name' => 'D', 'status' => 'verified', 'source' => 'manual']);
        Lead::create(['name' => 'E', 'status' => 'paid', 'source' => 'manual']);

        $this->actingAs($admin)->get('/admin/leads')->assertOk();
    }

    public function test_leads_index_loads_with_status_filter_and_each_sort(): void
    {
        $admin = $this->makeAdmin();
        Lead::create(['name' => 'A', 'status' => 'hot', 'source' => 'manual']);
        Lead::create(['name' => 'B', 'status' => 'new', 'source' => 'manual']);

        $this->actingAs($admin)->get('/admin/leads?status[]=hot')->assertOk();
        $this->actingAs($admin)->get('/admin/leads?sort=newest')->assertOk();
        $this->actingAs($admin)->get('/admin/leads?sort=follow_up')->assertOk();
        $this->actingAs($admin)->get('/admin/leads?sort=priority')->assertOk();
        $this->actingAs($admin)->get('/admin/leads?follow_ups_due=1')->assertOk();
    }

    public function test_lead_show_page_loads_with_next_previous_nav(): void
    {
        $admin = $this->makeAdmin();
        $first = Lead::create(['name' => 'A', 'status' => 'new', 'source' => 'manual']);
        Lead::create(['name' => 'B', 'status' => 'new', 'source' => 'manual']);

        $this->actingAs($admin)->get("/admin/leads/{$first->id}")->assertOk();
    }
}
