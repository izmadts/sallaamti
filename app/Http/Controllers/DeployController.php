<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeployController extends Controller
{
    public function handle(Request $request)
    {
        // Read secret from .env — different on each server
        $secret = env('DEPLOY_SECRET', '');

        if (empty($secret)) {
            return response('Deploy secret not configured', 500);
        }

        $payload = $request->getContent();
        $signature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($signature, $request->header('X-Hub-Signature-256') ?? '')) {
            return response('Invalid signature', 403);
        }

        $data = json_decode($payload, true);

        if (isset($data['ref']) && $data['ref'] === 'refs/heads/main') {
            // Read paths from .env — different on each server
            $scriptPath = env('DEPLOY_SCRIPT_PATH', '');
            $logPath    = env('DEPLOY_LOG_PATH', '');

            if (empty($scriptPath) || empty($logPath)) {
                return response('Deploy paths not configured', 500);
            }

            exec("/bin/bash $scriptPath >> $logPath 2>&1 &");

            return response('Deployed successfully', 200);
        }

        return response('Ignored - not main branch', 200);
    }
}
