<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NikahProfileView extends Model
{
    protected $fillable = ['viewer_profile_id', 'viewed_profile_id', 'view_count', 'last_viewed_at'];

    protected function casts(): array
    {
        return ['last_viewed_at' => 'datetime'];
    }

    public function viewer()
    {
        return $this->belongsTo(NikahProfile::class, 'viewer_profile_id');
    }

    public function viewed()
    {
        return $this->belongsTo(NikahProfile::class, 'viewed_profile_id');
    }

    // Called from NikahBrowseController::show() — one row per viewer/viewed
    // pair, incremented in place on a repeat view rather than growing
    // unbounded, same reasoning as the one-row-per-pair saved/block tables.
    public static function record(int $viewerProfileId, int $viewedProfileId): void
    {
        if ($viewerProfileId === $viewedProfileId) {
            return;
        }

        $row = static::firstOrNew(['viewer_profile_id' => $viewerProfileId, 'viewed_profile_id' => $viewedProfileId]);
        $row->view_count = ($row->view_count ?? 0) + 1;
        $row->last_viewed_at = now();
        $row->save();
    }
}
