<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LibraryItem extends Model
{
    protected $fillable = ['code', 'title', 'slug', 'category', 'description', 'file_path', 'cover_path', 'pages', 'language', 'is_published', 'sort_order'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'pages' => 'integer', 'sort_order' => 'integer', 'download_count' => 'integer'];
    }

    // Sections of /library, in display order (Urdu label, English label, icon).
    public const CATEGORIES = [
        'about'  => ['ur' => 'سلامتی کیا ہے؟', 'en' => 'About Sallaamti', 'icon' => '🌿'],
        'manhaj' => ['ur' => 'منہجِ سلامتی', 'en' => 'Our Manhaj & Charter', 'icon' => '📜'],
        'family' => ['ur' => 'والدین اور خاندان کی تعلیم', 'en' => 'Parental & Family Education', 'icon' => '👨‍👩‍👧'],
        'nikah'  => ['ur' => 'نکاح کی رہنمائی', 'en' => 'Nikah Guidance', 'icon' => '💍'],
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
}
