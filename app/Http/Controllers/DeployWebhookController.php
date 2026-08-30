<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

// Receives GitHub's push webhook and queues a deploy — it never runs git/composer
// itself. A web request on shared hosting can't be trusted to survive long enough
// to finish a pull + composer install (GitHub also only waits ~10s for a response
// before marking the delivery failed), so this just drops a flag file and returns
// immediately; a cPanel cron job (see docs/deployment.md) does the actual work.
class DeployWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $secret = config('services.deploy_webhook.secret');
        abort_unless($secret, 500, 'DEPLOY_WEBHOOK_SECRET is not set.');

        $payload = $request->getContent();
        $signature = $request->header('X-Hub-Signature-256', '');
        $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expected, $signature)) {
            Log::warning('Deploy webhook: signature mismatch, request rejected.');
            abort(403);
        }

        $data = json_decode($payload, true) ?? [];

        if (($data['ref'] ?? null) !== 'refs/heads/main') {
            return response('Ignored: not a push to main.', 200);
        }

        File::ensureDirectoryExists(storage_path('app'));
        File::put(storage_path('app/deploy.flag'), now()->toDateTimeString());

        Log::info('Deploy webhook: push to main received, deploy queued.');

        return response('Deploy queued.', 200);
    }
}
