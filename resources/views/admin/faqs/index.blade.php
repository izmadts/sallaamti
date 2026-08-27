<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">❓ FAQ Management</h2>
            <a href="{{ route('admin.faqs.create') }}"
                class="bg-teal-600 hover:bg-teal-700 hover:-translate-y-0.5 transition text-white text-sm font-semibold px-4 py-2 rounded-lg shadow-sm">
                + Add FAQ
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-3 flex flex-wrap gap-2">
                <a href="{{ route('admin.faqs.index') }}"
                    class="px-3 py-1.5 rounded-full text-sm font-medium transition {{ $activeModule === '' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    All modules
                </a>
                @foreach ($modules as $key => $label)
                <a href="{{ route('admin.faqs.index', ['module' => $key]) }}"
                    class="px-3 py-1.5 rounded-full text-sm font-medium transition {{ $activeModule === $key ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($faqs as $faq)
                <div class="p-5 flex justify-between items-start flex-wrap gap-3">
                    <div class="max-w-2xl">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs px-2 py-0.5 rounded-full bg-teal-50 text-teal-700 font-medium">{{ $modules[$faq->module] ?? $faq->module }}</span>
                            @if (!$faq->is_active)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Hidden</span>
                            @endif
                            @if (blank($faq->question_ur))
                            <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-50 text-yellow-700">No Urdu yet</span>
                            @endif
                        </div>
                        <p class="font-medium text-gray-800 mt-2">{{ $faq->question_en }}</p>
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ Str::limit(strip_tags($faq->answer_en), 140) }}</p>
                        @if ($faq->question_ur)
                        <p class="font-medium text-gray-700 mt-2" dir="rtl">{{ $faq->question_ur }}</p>
                        @endif
                    </div>

                    <div class="flex flex-col items-end gap-2">
                        <span class="text-xs text-gray-400">Order: {{ $faq->sort_order }}</span>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.faqs.edit', $faq) }}" class="text-sm text-teal-700 hover:underline font-medium">Edit</a>
                            <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" onsubmit="return confirm('Delete this FAQ?')">
                                @csrf @method('DELETE')
                                <button class="text-sm text-red-400 hover:text-red-600 hover:underline">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <p class="p-5 text-gray-500">No FAQs {{ $activeModule ? 'for this module' : '' }} yet — click "+ Add FAQ" to create the first one.</p>
                @endforelse
            </div>

            {{ $faqs->links() }}
        </div>
    </div>
</x-admin-layout>
