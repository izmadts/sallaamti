<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">📘 {{ __('db.User Guide') }}</h2>
    </x-slot>

    @php
        $tabs = collect([
            ['key' => 'member', 'label' => '👤 Member', 'show' => true],
            ['key' => 'matchmaker', 'label' => '💍 Matchmaker', 'show' => $showMatchmaker],
            ['key' => 'teacher', 'label' => '🎓 Teacher', 'show' => $showTeacher],
            ['key' => 'counselor', 'label' => '🤝 Counselor', 'show' => $showCounselor],
            ['key' => 'content', 'label' => '📝 Content Manager', 'show' => $showContentManager],
            ['key' => 'admin', 'label' => '🛠️ Admin', 'show' => $showAdmin],
        ])->filter(fn ($t) => $t['show'])->values();
    @endphp

    <div class="py-8" x-data="{ tab: '{{ $tabs->first()['key'] }}', lang: 'en' }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <p class="text-sm text-gray-500">
                    {{ __('db.Everything you need to know about using Sallaamti — tabs below only show for the roles your account actually holds.') }}
                </p>
                <div class="flex gap-1 bg-gray-100 rounded-lg p-1 flex-shrink-0">
                    <button type="button" @click="lang = 'en'" :class="lang === 'en' ? 'bg-white shadow-sm text-teal-700' : 'text-gray-500'" class="text-xs font-semibold px-3 py-1.5 rounded-md transition">English</button>
                    <button type="button" @click="lang = 'ur'" :class="lang === 'ur' ? 'bg-white shadow-sm text-teal-700' : 'text-gray-500'" class="text-xs font-semibold px-3 py-1.5 rounded-md transition">اردو</button>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 pb-3">
                @foreach ($tabs as $t)
                <button type="button" @click="tab = '{{ $t['key'] }}'"
                    :class="tab === '{{ $t['key'] }}' ? 'bg-teal-700 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                    class="text-sm font-medium px-4 py-2 rounded-lg transition">
                    {{ $t['label'] }}
                </button>
                @endforeach
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8">
                <div x-show="tab === 'member'" x-cloak>
                    @include('guide.partials.member')
                </div>

                @if ($showMatchmaker)
                <div x-show="tab === 'matchmaker'" x-cloak>
                    @include('guide.partials.matchmaker')
                </div>
                @endif

                @if ($showTeacher)
                <div x-show="tab === 'teacher'" x-cloak>
                    @include('guide.partials.teacher')
                </div>
                @endif

                @if ($showCounselor)
                <div x-show="tab === 'counselor'" x-cloak>
                    @include('guide.partials.counselor')
                </div>
                @endif

                @if ($showContentManager)
                <div x-show="tab === 'content'" x-cloak>
                    @include('guide.partials.content-manager')
                </div>
                @endif

                @if ($showAdmin)
                <div x-show="tab === 'admin'" x-cloak>
                    @include('guide.partials.admin')
                </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .prose h3 { color: #0d6b6b; margin-top: 1.75em; font-size: 1.05rem; font-weight: 700; }
        .prose h3:first-child { margin-top: 0; }
        .prose h4 { color: #374151; margin-top: 1.25em; margin-bottom: 0.4em; font-weight: 600; }
        .prose p, .prose li { color: #4b5563; line-height: 1.7; }
        .prose ul { margin-top: 0.5em; }
        .prose-ur { font-family: 'Noto Nastaliq Urdu', 'Jameel Noori Nastaleeq', 'Segoe UI', Tahoma, sans-serif; line-height: 2.4; font-size: 1.05rem; }
        .prose-ur h3, .prose-ur h4 { line-height: 2; }
    </style>
</x-app-layout>
