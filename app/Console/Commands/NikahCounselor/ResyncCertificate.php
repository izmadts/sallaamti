<?php

namespace App\Console\Commands\NikahCounselor;

use App\Models\Certificate;
use App\Models\MatchmakerApplication;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// One-off repair for the duplicate-account bug Admin\MatchmakerApplication
// Controller::certify() used to have: an applicant with no linked account
// at certification time always got a brand-new User created from just
// their name/phone (no email match), so anyone who already had a real
// Sallaamti account under their real email ended up with the 'matchmaker'
// role and Certificate attached to a phantom duplicate account instead —
// their real account (the one they actually log into the NC app with)
// shows no certificate. certify() itself is already fixed to match by
// email before creating a new account; this is only for applications
// certified BEFORE that fix, where the wrong account is already baked in.
//
// Deliberately takes BOTH the application id and the correct email as
// explicit arguments, rather than guessing from the data, so this can
// never silently merge the wrong two accounts.
class ResyncCertificate extends Command
{
    protected $signature = 'nikah-counselor:resync-certificate {application_id} {correct_email}';

    protected $description = 'Re-point a certified Nikah Counselor\'s application/certificate/role from a phantom duplicate account to their real one, by email.';

    public function handle(): int
    {
        $application = MatchmakerApplication::find($this->argument('application_id'));

        if (!$application) {
            $this->error('No MatchmakerApplication found with id ' . $this->argument('application_id'));

            return self::FAILURE;
        }

        if (!$application->isCertified()) {
            $this->error('Application #' . $application->id . ' is not certified (status: ' . $application->status . ') — nothing to resync.');

            return self::FAILURE;
        }

        $correctUser = User::where('email', $this->argument('correct_email'))->first();

        if (!$correctUser) {
            $this->error('No user found with email ' . $this->argument('correct_email'));

            return self::FAILURE;
        }

        $oldUser = $application->user;

        if ($oldUser && $oldUser->id === $correctUser->id) {
            $this->info('Application #' . $application->id . ' is already linked to ' . $correctUser->email . ' — nothing to do.');

            return self::SUCCESS;
        }

        $this->line('Application #' . $application->id . ' (' . $application->full_name . ', counselor_code ' . ($application->counselor_code ?? '—') . ')');
        $this->line('  currently linked to: ' . ($oldUser ? "user #{$oldUser->id} ({$oldUser->email})" : 'no account'));
        $this->line('  will be re-linked to: user #' . $correctUser->id . ' (' . $correctUser->email . ')');

        if (!$this->confirm('Proceed?')) {
            $this->warn('Cancelled.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($application, $oldUser, $correctUser) {
            $application->update(['user_id' => $correctUser->id, 'email' => $correctUser->email]);

            if (!$correctUser->hasRole('matchmaker')) {
                $correctUser->assignRole('matchmaker');
            }

            // Move the existing Certificate row if one exists under the old
            // account; otherwise create it fresh — covers both "the
            // phantom account got a certificate" and "certify() somehow
            // never created one at all" (certificate_number issue is
            // backfilled from counselor_code either way).
            $certificate = $oldUser
                ? Certificate::where('user_id', $oldUser->id)->where('type', 'nikah_counselor_id')->first()
                : null;

            if ($certificate) {
                $certificate->update(['user_id' => $correctUser->id, 'certificate_number' => $certificate->certificate_number ?? $application->counselor_code]);
            } else {
                Certificate::firstOrCreate(
                    ['user_id' => $correctUser->id, 'type' => 'nikah_counselor_id'],
                    [
                        'course_id' => null,
                        'title' => 'Sallaamti Certified Nikah Counselor',
                        'certificate_number' => $application->counselor_code,
                        'issued_by' => auth()->id(),
                        'issued_at' => now(),
                    ]
                );
            }
        });

        $this->info('Done. Application #' . $application->id . ' and its certificate now point to ' . $correctUser->email . '.');

        if ($oldUser) {
            $this->warn('The old phantom account (user #' . $oldUser->id . ', ' . ($oldUser->email ?? 'no email') . ') was left untouched — review and delete it manually once you\'ve confirmed this fix.');
        }

        return self::SUCCESS;
    }
}
