<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TestMailSetup extends Command
{
    protected $signature = 'mail:test {email? : Address to send a real test email to}';

    protected $description = 'Show live mail/queue configuration and optionally send a real test email';

    public function handle(): int
    {
        $this->info('--- Mail config ---');
        $this->line('MAIL_MAILER: ' . config('mail.default'));
        $this->line('MAIL_HOST: ' . config('mail.mailers.smtp.host'));
        $this->line('MAIL_PORT: ' . (string) config('mail.mailers.smtp.port'));
        $this->line('MAIL_USERNAME set: ' . (config('mail.mailers.smtp.username') ? 'yes' : 'no'));
        $this->line('MAIL_FROM: ' . config('mail.from.address') . ' <' . config('mail.from.name') . '>');
        $this->line('APP_URL: ' . config('app.url'));

        if (config('mail.default') === 'log') {
            $this->warn('MAIL_MAILER is "log" — emails are being written to storage/logs/laravel.log instead of actually being sent. Set MAIL_MAILER=smtp (or your real driver) in .env.');
        }

        $this->info('--- Queue config ---');
        $queueConnection = config('queue.default');
        $this->line('QUEUE_CONNECTION: ' . $queueConnection);

        if ($queueConnection === 'database') {
            $pending = DB::table('jobs')->count();
            $failed = DB::table('failed_jobs')->count();
            $this->line("Pending jobs waiting on a worker: {$pending}");
            $this->line("Failed jobs: {$failed}");
            if ($pending > 0) {
                $this->warn('Jobs are piling up — every notification in this app is queued, so nothing will actually send unless a queue worker is running (a cron entry running "php artisan queue:work --stop-when-empty" every minute, or a persistent worker). Set QUEUE_CONNECTION=sync instead if you don\'t have a worker running, so notifications send immediately.');
            }
            if ($failed > 0) {
                $this->warn("There are {$failed} failed job(s) — run \"php artisan queue:failed\" to see why they didn't send.");
            }
        } elseif ($queueConnection === 'sync') {
            $this->line('sync means notifications send immediately inline, no worker needed — good for shared hosting without a persistent process.');
        }

        $email = $this->argument('email');
        if ($email) {
            $this->info("--- Sending a real test email to {$email} ---");
            try {
                Mail::raw('This is a test email from Sallaamti confirming mail delivery works on this server.', function ($message) use ($email) {
                    $message->to($email)->subject('Sallaamti mail test — ' . now()->toDateTimeString());
                });
                $this->info('Sent without a transport error. Check the inbox (and spam folder) to confirm it actually arrived.');
            } catch (\Throwable $e) {
                $this->error('Failed to send: ' . $e->getMessage());
                return self::FAILURE;
            }
        } else {
            $this->comment('Pass an email address to also send a real test message, e.g.: php artisan mail:test you@example.com');
        }

        return self::SUCCESS;
    }
}
