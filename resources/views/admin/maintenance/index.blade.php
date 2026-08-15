<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">System Maintenance</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Database --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-800">🗄️ Database</h3>
                        <p class="text-xs text-gray-400 mt-1">
                            Defragments and reclaims unused space left behind by deletes/updates. Rebuilds tables in place — no rows are touched, nothing is lost. Safe to run anytime.
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-gray-800">{{ $dbTotalMb }} MB</p>
                        <p class="text-xs text-gray-400">{{ $tables->count() }} tables</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.maintenance.optimize-database') }}"
                    onsubmit="return confirm('Optimize all {{ $tables->count() }} database tables now?')" class="mb-4">
                    @csrf
                    <button class="bg-teal-600 text-white text-sm px-4 py-2 rounded hover:bg-teal-700">
                        ⚡ Optimize Database
                    </button>
                </form>

                <details>
                    <summary class="text-xs text-gray-500 cursor-pointer">Show table breakdown</summary>
                    <div class="mt-3 overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead class="text-gray-400 uppercase">
                                <tr>
                                    <th class="text-left py-1 pr-4">Table</th>
                                    <th class="text-right py-1 pr-4">Rows</th>
                                    <th class="text-right py-1">Size</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($tables as $table)
                                <tr>
                                    <td class="py-1 pr-4 text-gray-700">{{ $table->table_name }}</td>
                                    <td class="py-1 pr-4 text-right text-gray-500">{{ number_format($table->row_count) }}</td>
                                    <td class="py-1 text-right text-gray-500">{{ $table->size_mb }} MB</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            </div>

            {{-- Images & Assets --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-800">🖼️ Images & Assets</h3>
                        <p class="text-xs text-gray-400 mt-1">
                            Re-runs every already-uploaded image through the same resize/compress step new uploads go through automatically. Only ever replaces a file if the result is smaller — nothing is deleted or degraded further than it already is.
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-gray-800">{{ $imagesTotalMb }} MB</p>
                        <p class="text-xs text-gray-400">{{ $imageStats->sum('fileCount') }} files</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.maintenance.optimize-images') }}"
                    onsubmit="return confirm('Re-optimize all existing images now? This may take a moment for a large library.')" class="mb-4">
                    @csrf
                    <button class="bg-teal-600 text-white text-sm px-4 py-2 rounded hover:bg-teal-700">
                        ⚡ Optimize Existing Images
                    </button>
                </form>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($imageStats as $stat)
                    <div class="border border-gray-100 rounded-lg p-3 text-sm flex justify-between items-center">
                        <div>
                            <p class="text-gray-700">{{ $stat['label'] }}</p>
                            <p class="text-xs text-gray-400">{{ $stat['fileCount'] }} files · max {{ $stat['maxDimension'] }}px</p>
                        </div>
                        <p class="font-semibold text-gray-800">{{ $stat['sizeMb'] }} MB</p>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
