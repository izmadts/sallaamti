<?php

namespace App\Console\Commands;

use App\Models\NikahProfile;
use Illuminate\Console\Command;

class NikahProfileStats extends Command
{
    protected $signature = 'nikah:profile-stats';

    protected $description = 'Break down Nikah profiles by the exact conditions Check Profiles filters on, to explain why the browse count looks low';

    public function handle(): int
    {
        $total = NikahProfile::count();
        $this->info("Total Nikah profiles: {$total}");

        $this->line('');
        $this->info('--- By gender (via the linked user account) ---');
        NikahProfile::join('users', 'users.id', '=', 'nikah_profiles.user_id')
            ->selectRaw('users.gender, count(*) as c')
            ->groupBy('users.gender')
            ->get()
            ->each(fn($row) => $this->line(($row->gender ?? 'NULL') . ': ' . $row->c));

        $this->line('');
        $this->info('--- By verification_status ---');
        NikahProfile::selectRaw('verification_status, count(*) as c')->groupBy('verification_status')->get()
            ->each(fn($row) => $this->line($row->verification_status . ': ' . $row->c));

        $this->line('');
        $this->info('--- By visibility ---');
        NikahProfile::selectRaw('visibility, count(*) as c')->groupBy('visibility')->get()
            ->each(fn($row) => $this->line($row->visibility . ': ' . $row->c));

        $this->line('');
        $this->info('--- is_active / suspended ---');
        $this->line('is_active = true: ' . NikahProfile::where('is_active', true)->count());
        $this->line('is_active = false: ' . NikahProfile::where('is_active', false)->count());
        $this->line('suspended (suspended_at not null): ' . NikahProfile::whereNotNull('suspended_at')->count());

        $this->line('');
        $this->info('--- Fully browsable right now (is_active + not suspended + public), by gender ---');
        NikahProfile::join('users', 'users.id', '=', 'nikah_profiles.user_id')
            ->where('nikah_profiles.is_active', true)
            ->whereNull('nikah_profiles.suspended_at')
            ->where('nikah_profiles.visibility', 'public')
            ->selectRaw('users.gender, count(*) as c')
            ->groupBy('users.gender')
            ->get()
            ->each(fn($row) => $this->line(($row->gender ?? 'NULL') . ': ' . $row->c));

        $this->line('');
        $this->comment('A male viewer only ever sees the "female" row above (and vice versa) — that\'s the actual number Check Profiles can show them, regardless of verification status.');

        return self::SUCCESS;
    }
}
