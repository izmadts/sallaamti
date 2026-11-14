<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LibraryItem extends Model
{
    protected $fillable = [
        'code', 'title', 'slug', 'category', 'description', 'review_note', 'file_path', 'cover_path',
        'pages', 'language', 'is_published', 'sort_order', 'version', 'file_size',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'pages' => 'integer',
            'sort_order' => 'integer',
            'download_count' => 'integer',
            'view_count' => 'integer',
            'share_count' => 'integer',
            'version' => 'integer',
            'file_size' => 'integer',
            'published_at' => 'datetime',
            'notified_at' => 'datetime',
        ];
    }

    // Sections of /library, in display order (Urdu label, English label, icon).
    public const CATEGORIES = [
        'about'    => ['ur' => 'سلامتی کیا ہے؟', 'en' => 'About Sallaamti', 'icon' => '🌿'],
        'manhaj'   => ['ur' => 'منہجِ سلامتی', 'en' => 'Our Manhaj & Charter', 'icon' => '📜'],
        'family'   => ['ur' => 'والدین اور خاندان کی تعلیم', 'en' => 'Parental & Family Education', 'icon' => '👨‍👩‍👧'],
        'nikah'    => ['ur' => 'نکاح کی رہنمائی', 'en' => 'Nikah Guidance', 'icon' => '💍'],
        'learning' => ['ur' => 'ڈیجیٹل مہارت اور قرآن رہنمائی', 'en' => 'Digital Skills & Quran Guides', 'icon' => '💻'],
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished($q)
    {
        return $q->where('is_published', true);
    }

    public function fileUrl(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function coverUrl(): ?string
    {
        return $this->cover_path ? Storage::disk('public')->url($this->cover_path) : null;
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category]['ur'] ?? $this->category;
    }

    // "New" badge window (app + website) — 7 days from first publish.
    public function isNew(): bool
    {
        return $this->published_at && $this->published_at->gt(now()->subDays(7));
    }

    public function fileSizeHuman(): ?string
    {
        if (!$this->file_size) {
            return null;
        }
        $mb = $this->file_size / 1048576;

        return $mb >= 0.1 ? number_format($mb, 1) . ' MB' : number_format($this->file_size / 1024, 0) . ' KB';
    }

    // "10 صفحات · PDF · 1.2 MB · مفت" — built here, not with @if/@php in the
    // view: a @php block right here tripped Livewire's Blade-compiler
    // extension (which wraps every @if/@endif for wire:key tracking) into
    // compiling @php without its matching close, on the public card/show
    // views specifically (an isolated partial compiled fine; only inside
    // the <x-guest-layout> wrapped page did it break) — caught by actually
    // rendering the view, not just linting it.
    public function metaLine(): string
    {
        return implode(' · ', array_filter([
            $this->pages ? $this->pages . ' صفحات' : null,
            'PDF',
            $this->fileSizeHuman(),
            'مفت',
        ]));
    }
}
