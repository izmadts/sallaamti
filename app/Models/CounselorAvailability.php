<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CounselorAvailability extends Model
{
    protected $fillable = [
        'counselor_id',
        'day_of_week',
        'specific_date',
        'start_time',
        'end_time',
        'slot_duration_minutes',
        'is_active',
    ];

    protected $casts = [
        'specific_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    // Expands this counselor's weekly rule (or a one-off override for this
    // exact date, which takes precedence) into bookable start times, minus
    // slots that already have a requested/confirmed booking.
    public static function generateSlotsFor(int $counselorId, Carbon $date): array
    {
        $rules = static::where('counselor_id', $counselorId)
            ->where('is_active', true)
            ->where(function ($q) use ($date) {
                $q->where('specific_date', $date->toDateString())
                    ->orWhere(function ($q2) use ($date) {
                        $q2->whereNull('specific_date')->where('day_of_week', $date->dayOfWeek);
                    });
            })
            ->get();

        $override = $rules->firstWhere('specific_date', $date->toDateString());
        $applicable = $override ? collect([$override]) : $rules->whereNull('specific_date');

        $slots = collect();

        foreach ($applicable as $rule) {
            $cursor = Carbon::parse($date->toDateString() . ' ' . $rule->start_time);
            $end = Carbon::parse($date->toDateString() . ' ' . $rule->end_time);

            while ($cursor->copy()->addMinutes($rule->slot_duration_minutes)->lte($end)) {
                if (!$cursor->isPast()) {
                    $slots->push($cursor->copy());
                }
                $cursor->addMinutes($rule->slot_duration_minutes);
            }
        }

        $taken = CounselingBooking::where('counselor_id', $counselorId)
            ->whereIn('status', ['requested', 'confirmed'])
            ->whereDate('scheduled_at', $date->toDateString())
            ->get()
            ->map(fn ($booking) => Carbon::parse($booking->scheduled_at)->format('Y-m-d H:i'))
            ->all();

        return $slots
            ->reject(fn ($slot) => in_array($slot->format('Y-m-d H:i'), $taken))
            ->sort()
            ->values()
            ->all();
    }
}
