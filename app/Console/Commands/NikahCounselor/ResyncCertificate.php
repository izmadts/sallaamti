<?php

namespace App\Console\Commands\NikahCounselor;

use App\Models\Certificate;
use App\Models\MatchmakerApplication;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// One-off repair for two certify()-time failure modes that both land the
// same way: a certified application whose counselor can't download their
// certificate.
//
// 1. The duplicate-account bug: an applicant with no linked account at
//    certification time always got a brand-new User created from just
//    their name/phone (no email match), so anyone who already had a real
//    Sallaamti account under their real email ended up with the
//    'matchmaker' role and Certificate attached to a phantom duplicate
//    account instead — their real account shows no certificate. certify()
//    itself is already fixed to match by email before creating a new
//    account; this only re-points applications certified BEFORE that fix.
//
// 2. A certify() run that updated status to 'certified' but never reached
//    the counselor_code/Certificate::firstOrCreate() step below it (no DB
//    transaction wraps the two statements in updateStatus(), so a
//    mid-certify() failure can leave status=certified with counselor_code
//    still null and no Certificate row at all) — same visible symptom
//    (download fails, "no certificate" in the DB) even when user_id was
//    already correct the whole time.
//
// Deliberately takes BOTH the application id and the correct email as
// explicit arguments, rather than guessing from the data, so this can
// never silently merge the wrong two accounts.
class ResyncCertificate extends Command
{
    protected $signature = 'nikah-counselor:resync-certificate {application_id} {correct_email}';

    protected $description = 'Re-point a certified Nikah Counselor\'s application/certificate/role to their real account, and backfill a missing counselor_code/Certificate — by email.';

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
        $alreadyLinked = $oldUser && $oldUser->id === $correctUser->id;

        $existingCertificate = Certificate::where('user_id', $correctUser->id)->where('type', 'nikah_counselor_id')->first();
        $oldAccountCertificate = (!$alreadyLinked && $oldUser)
            ? Certificate::where('user_id', $oldUser->id)->where('type', 'nikah_counselor_id')->first()
            : null;

        $needsCodeOrCertificate = !$application->counselor_code || (!$existingCertificate && !$oldAccountCertificate);

        if ($alreadyLinked && !$needsCodeOrCertificate) {
            $this->info('Application #' . $application->id . ' is already correctly linked to ' . $correctUser->email . ', with a counselor_code and certificate in place — nothing to do.');

            return self::SUCCESS;
        }

        $this->line('Application #' . $application->id . ' (' . $application->full_name . ')');
        $this->line('  counselor_code: ' . ($application->counselor_code ?? 'MISSING — will generate one'));
        $this->line('  currently linked to: ' . ($oldUser ? "user #{$oldUser->id} ({$oldUser->email})" : 'no account'));
        if (!$alreadyLinked) {
            $this->line('  will be re-linked to: user #' . $correctUser->id . ' (' . $correctUser->email . ')');
        }
        $this->line('  certificate: ' . (($existingCertificate ?? $oldAccountCertificate) ? 'exists' : 'MISSING — will create one'));

        if (!$this->confirm('Proceed?')) {
            $this->warn('Cancelled.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($application, $oldUser, $correctUser, $existingCertificate, $oldAccountCertificate) {
            $application->update(['user_id' => $correctUser->id, 'email' => $correctUser->email]);

            if (!$correctUser->hasRole('matchmaker')) {
                $correctUser->assignRole('matchmaker');
            }

            // Same generation logic as Admin\MatchmakerApplicationController
            // ::generateCounselorCode() — kept in sync manually since that
            // one is private and this command needs the identical format/
            // uniqueness guarantee.
            if (!$application->counselor_code) {
                $lastNumber = MatchmakerApplication::whereNotNull('counselor_code')
                    ->get()
                    ->map(fn ($a) => (int) substr($a->counselor_code, -6))
                    ->max() ?? 0;

                $application->update([
                    'counselor_code' => 'MM-PK-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT),
                    'certified_at' => $application->certified_at ?? now(),
                ]);
            }

            $certificate = $existingCertificate ?? $oldAccountCertificate;

            if ($certificate) {
                $certificate->update(['user_id' => $correctUser->id, 'certificate_number' => $certificate->certificate_number ?? $application->counselor_code]);
            } else {
                Certificate::create([
                    'user_id' => $correctUser->id,
                    'type' => 'nikah_counselor_id',
                    'course_id' => null,
                    'title' => 'Sallaamti Certified Nikah Counselor',
                    'certificate_number' => $application->counselor_code,
                    'issued_by' => auth()->id(),
                    'issued_at' => now(),
                ]);
            }
        });

        $application->refresh();
        $this->info('Done. Application #' . $application->id . ' (counselor_code ' . $application->counselor_code . ') and its certificate now point to ' . $correctUser->email . '.');

        if ($oldUser && $oldUser->id !== $correctUser->id) {
            $this->warn('The old account (user #' . $oldUser->id . ', ' . ($oldUser->email ?? 'no email') . ') was left untouched — review and delete it manually once you\'ve confirmed this fix.');
        }

        return self::SUCCESS;
    }
}
