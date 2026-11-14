<?php

use App\Models\LibraryItem;
use Database\Seeders\SettingsSeeder;

beforeEach(function () {
    $this->seed(SettingsSeeder::class);
});

function makeLibraryItem(array $overrides = []): LibraryItem
{
    return LibraryItem::create(array_merge([
        'title' => 'Test Booklet',
        'category' => 'family',
        'language' => 'ur',
        'slug' => 'test-booklet-' . uniqid(),
        'file_path' => 'library/fake.pdf',
        'file_size' => 12345,
        'is_published' => true,
    ], $overrides))->forceFill(['published_at' => now()])->tap(fn ($item) => $item->save());
}

test('library index only returns published items', function () {
    makeLibraryItem(['title' => 'Published One']);
    makeLibraryItem(['title' => 'Draft One', 'is_published' => false]);

    $response = $this->getJson(route('api.v1.library.index'));

    $response->assertOk();
    $titles = collect($response->json('items'))->pluck('title');
    expect($titles)->toContain('Published One');
    expect($titles)->not->toContain('Draft One');
});

test('library index filters by category', function () {
    makeLibraryItem(['title' => 'Family Item', 'category' => 'family']);
    makeLibraryItem(['title' => 'Nikah Item', 'category' => 'nikah']);

    $response = $this->getJson(route('api.v1.library.index', ['category' => 'nikah']));

    $response->assertOk();
    $titles = collect($response->json('items'))->pluck('title');
    expect($titles)->toContain('Nikah Item');
    expect($titles)->not->toContain('Family Item');
});

test('library show returns a published item with pdf_url and share_url', function () {
    $item = makeLibraryItem(['title' => 'Show Me']);

    $response = $this->getJson(route('api.v1.library.show', $item));

    $response->assertOk();
    $response->assertJsonPath('item.title', 'Show Me');
    $response->assertJsonStructure(['item' => ['pdf_url', 'share_url']]);
});

test('library show 404s for a draft item', function () {
    $item = makeLibraryItem(['title' => 'Still a draft', 'is_published' => false]);

    $this->getJson(route('api.v1.library.show', $item))->assertNotFound();
});

test('library show 404s for an unknown slug', function () {
    $this->getJson('/api/v1/library/does-not-exist')->assertNotFound();
});

test('library event increments the matching counter and 404s for a draft', function () {
    $item = makeLibraryItem(['title' => 'Countable']);

    $this->postJson(route('api.v1.library.event', $item), ['type' => 'view'])->assertOk();
    $this->postJson(route('api.v1.library.event', $item), ['type' => 'download'])->assertOk();
    $this->postJson(route('api.v1.library.event', $item), ['type' => 'share'])->assertOk();

    $item->refresh();
    expect($item->view_count)->toBe(1);
    expect($item->download_count)->toBe(1);
    expect($item->share_count)->toBe(1);

    $draft = makeLibraryItem(['title' => 'Draft Countable', 'is_published' => false]);
    $this->postJson(route('api.v1.library.event', $draft), ['type' => 'view'])->assertNotFound();
});
