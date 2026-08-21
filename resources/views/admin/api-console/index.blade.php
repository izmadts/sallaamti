<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">🧪 API Console</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <p class="text-sm text-gray-500">
                Every endpoint the Sallaamti mobile app uses, read live from the route table, plus a built-in tester
                to fire a real request against this server — optionally acting as a chosen member — without needing
                Postman or the app installed.
            </p>

            @if ($errors->any())
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- Endpoint docs --}}
                <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-4">
                    <h3 class="font-semibold text-gray-700 mb-3">Endpoints</h3>
                    <div class="divide-y max-h-[32rem] overflow-y-auto">
                        @foreach ($routes as $route)
                        <button type="button"
                            class="w-full text-left py-2.5 px-1 hover:bg-gray-50 rounded transition js-pick-route"
                            data-method="{{ $route['method'] }}" data-uri="/{{ $route['uri'] }}">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold px-1.5 py-0.5 rounded
                                    {{ match($route['method']) {
                                        'GET' => 'bg-teal-50 text-teal-700',
                                        'POST' => 'bg-green-50 text-green-700',
                                        'PUT', 'PATCH' => 'bg-yellow-50 text-yellow-700',
                                        'DELETE' => 'bg-red-50 text-red-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    } }}">{{ $route['method'] }}</span>
                                <span class="text-sm text-gray-700 font-mono">/{{ $route['uri'] }}</span>
                                @if ($route['requires_auth'])
                                <span class="text-xs text-gray-400" title="Requires a bearer token">🔒</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $route['name'] }}</p>
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Live tester --}}
                <div class="lg:col-span-3 bg-white rounded-lg shadow-sm p-4">
                    <h3 class="font-semibold text-gray-700 mb-3">Fire a test request</h3>

                    <form method="POST" action="{{ route('admin.api-console.test') }}" class="space-y-4">
                        @csrf

                        <div class="flex gap-3">
                            <div class="w-32">
                                <x-input-label value="Method" />
                                <select name="method" id="js-method" class="w-full mt-1 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500">
                                    <option value="GET" @selected(old('method', $lastRequest['method'] ?? 'GET') === 'GET')>GET</option>
                                    <option value="POST" @selected(old('method', $lastRequest['method'] ?? '') === 'POST')>POST</option>
                                    <option value="PUT" @selected(old('method', $lastRequest['method'] ?? '') === 'PUT')>PUT</option>
                                    <option value="PATCH" @selected(old('method', $lastRequest['method'] ?? '') === 'PATCH')>PATCH</option>
                                    <option value="DELETE" @selected(old('method', $lastRequest['method'] ?? '') === 'DELETE')>DELETE</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <x-input-label value="Path" />
                                <x-text-input id="js-uri" name="uri" class="w-full mt-1 font-mono text-sm" value="{{ old('uri', $lastRequest['uri'] ?? '/api/v1/faqs') }}" required />
                            </div>
                        </div>

                        <div>
                            <x-input-label value="Test as (optional — leave blank for a public/unauthenticated call)" />
                            <select name="test_as_user_id" class="w-full mt-1 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500">
                                <option value="">— No user (public request) —</option>
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected(old('test_as_user_id', $lastRequest['test_as_user_id'] ?? '') == $user->id)>{{ $user->name }} ({{ $user->email ?: 'no email' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label value="JSON body (optional)" />
                            <textarea name="body" rows="6" class="w-full mt-1 font-mono text-sm border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500" placeholder="{&#10;    &quot;key&quot;: &quot;value&quot;&#10;}">{{ old('body', $lastRequest['body'] ?? '') }}</textarea>
                        </div>

                        <x-primary-button>Send Request</x-primary-button>
                    </form>

                    @if ($result)
                    <div class="mt-6 border-t pt-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-sm font-semibold text-gray-600">Response</span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $result['status'] < 300 ? 'bg-green-100 text-green-800' : ($result['status'] < 500 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $result['status'] }}
                            </span>
                        </div>
                        <pre class="bg-gray-900 text-teal-300 text-xs rounded-lg p-4 overflow-x-auto max-h-96">{{ is_string($result['body']) ? $result['body'] : json_encode($result['body'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.js-pick-route').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('js-method').value = btn.dataset.method;
                document.getElementById('js-uri').value = btn.dataset.uri;
            });
        });
    </script>
</x-admin-layout>
