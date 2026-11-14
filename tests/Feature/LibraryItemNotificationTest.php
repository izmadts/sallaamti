<?php

use App\Jobs\NotifyMembersOfNewLibraryItemJob;
use App\Models\LibraryItem;
use App\Models\User;
use Database\Seeders\SettingsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // admin-layout reads setting() on every admin page the redirect lands
    // on — without this the settings table is just empty on the fresh
    // RefreshDatabase SQLite DB, not missing, so this isn't optional.
    $this->seed(SettingsSeeder::class);
    Role::findOrCreate('admin');
    Role::findOrCreate('member');
});

test('publishing a library item with the notify checkbox queues the fan-out once', function () {
    Bus::fake();

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->post(route('admin.library.store'), [
        'title' => 'Test Booklet',
        'category' => 'family',
        'language' => 'ur',
        'file' => UploadedFile::fake()->create('booklet.pdf', 500, 'application/pdf'),
        'is_published' => '1',
        'notify' => '1',
    ]);

    $response->assertRedirect(route('admin.library.index'));

    $item = LibraryItem::firstWhere('title', 'Test Booklet');
    expect($item)->not->toBeNull();
    expect($item->is_published)->toBeTrue();
    expect($item->published_at)->not->toBeNull();
    expect($item->notified_at)->not->toBeNull();

    Bus::assertDispatchedTimes(NotifyMembersOfNewLibraryItemJob::class, 1);

    // Re-saving the same (already-notified) item, even with the checkbox
    // still on, must not queue a second fan-out.
    $this->actingAs($admin)->put(route('admin.library.update', $item), [
        'title' => 'Test Booklet (edited)',
        'category' => 'family',
        'language' => 'ur',
        'is_published' => '1',
        'notify' => '1',
    ]);

    Bus::assertDispatchedTimes(NotifyMembersOfNewLibraryItemJob::class, 1);
});

test('creating a library item without checking Published does not notify', function () {
    Bus::fake();

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->post(route('admin.library.store'), [
        'title' => 'Draft Booklet',
        'category' => 'family',
        'language' => 'ur',
        'file' => UploadedFile::fake()->create('booklet.pdf', 500, 'application/pdf'),
    ]);

    $item = LibraryItem::firstWhere('title', 'Draft Booklet');
    expect($item->is_published)->toBeFalse();
    expect($item->notified_at)->toBeNull();

    Bus::assertNotDispatched(NotifyMembersOfNewLibraryItemJob::class);
});
