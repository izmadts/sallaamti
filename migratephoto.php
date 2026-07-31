<?php
use App\Models\NikahPhoto;
use Illuminate\Support\Facades\Storage;

NikahPhoto::all()->each(function ($photo) {
    if (\Illuminate\Support\Str::startsWith($photo->path, 'public/'))
     {
        $oldPath = $photo->path;
        $newPath = 'nikah/photos/' . basename($oldPath);

    if (Storage::disk('public')->exists(str_replace('public/', '', $oldPath))) {
        $file = Storage::disk('public')->get(str_replace('public/', '', $oldPath));
        Storage::disk('private')->put($newPath, $file);
        Storage::disk('public')->delete(str_replace('public/', '', $oldPath));
        $photo->update(['path' => $newPath]);
        echo "Moved: {$oldPath} → {$newPath}\n";
        }
    }
});