<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route as RouteFacade;

// A Postman-lite baked into /admin so the mobile app's API can be verified
// without an external tool. Route-locked to 'admin.only' (not the general
// 'admin' gate) because firing a test call means impersonating any chosen
// user via a real access token, the same privilege-escalation category as
// Certificates/Courses/Subscribers/Localization (see the security audit
// note on those route groups).
class ApiConsoleController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.api-console.index', [
            'routes' => $this->apiRoutes(),
            'users' => User::orderBy('name')->limit(200)->get(['id', 'name', 'email']),
            'result' => null,
            'lastRequest' => null,
        ]);
    }

    public function test(Request $request)
    {
        $validated = $request->validate([
            'method' => ['required', 'in:GET,POST,PUT,PATCH,DELETE'],
            'uri' => ['required', 'string'],
            'test_as_user_id' => ['nullable', 'exists:users,id'],
            'body' => ['nullable', 'string'],
        ]);

        $body = [];
        if (filled($validated['body'] ?? null)) {
            $decoded = json_decode($validated['body'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['body' => 'Request body must be valid JSON.'])->withInput();
            }

            $body = $decoded ?? [];
        }

        $testUser = null;
        $tokenId = null;
        $result = null;

        try {
            $http = Http::acceptJson()->timeout(15);

            if ($validated['test_as_user_id'] ?? null) {
                $testUser = User::find($validated['test_as_user_id']);
                $issued = $testUser->createToken('admin-api-console-test');
                $tokenId = $issued->accessToken->id;
                $http = $http->withToken($issued->plainTextToken);
            }

            $url = rtrim(config('app.url'), '/') . '/' . ltrim($validated['uri'], '/');

            $response = match ($validated['method']) {
                'GET' => $http->get($url, $body),
                'POST' => $http->post($url, $body),
                'PUT' => $http->put($url, $body),
                'PATCH' => $http->patch($url, $body),
                'DELETE' => $http->delete($url, $body),
            };

            $result = [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ];
        } finally {
            // Never leave a test token behind, whether the call succeeded,
            // failed, or threw.
            if ($tokenId) {
                $testUser->tokens()->where('id', $tokenId)->delete();
            }
        }

        Log::info('Admin API console test fired', [
            'admin_id' => Auth::id(),
            'test_as_user_id' => $testUser?->id,
            'method' => $validated['method'],
            'uri' => $validated['uri'],
            'status' => $result['status'],
        ]);

        return view('admin.api-console.index', [
            'routes' => $this->apiRoutes(),
            'users' => User::orderBy('name')->limit(200)->get(['id', 'name', 'email']),
            'result' => $result,
            'lastRequest' => $validated,
        ]);
    }

    // Reads the live route table rather than a hand-maintained list, so
    // this can never go stale as new api/v1 endpoints are added in later
    // phases.
    private function apiRoutes()
    {
        return collect(RouteFacade::getRoutes())
            ->filter(fn ($route) => str_starts_with($route->uri(), 'api/v1'))
            ->map(function ($route) {
                $methods = array_values(array_diff($route->methods(), ['HEAD']));

                return [
                    'method' => $methods[0] ?? null,
                    'uri' => $route->uri(),
                    'name' => $route->getName(),
                    'requires_auth' => in_array('auth:sanctum', $route->gatherMiddleware(), true),
                ];
            })
            ->filter(fn ($r) => $r['method'])
            ->sortBy('uri')
            ->values();
    }
}
