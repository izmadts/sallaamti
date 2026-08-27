<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">✍️ {{ __('db.Share Your Story') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">

                <p class="text-sm text-gray-500 mb-6">
                    {{ __('db.Write something worth sharing with the Sallaamti community — a story, a reflection, an announcement.') }}
                    @if (\App\Models\Post::isAutoPublishedFor(Auth::user()))
                    {{ __('db.It will be published immediately.') }}
                    @else
                    {{ __('db.It\'ll go live once our team reviews it, and you\'ll be notified either way.') }}
                    @endif
                </p>

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 text-red-700 rounded text-sm">
                    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="title" :value="__('db.Title')" />
                        <x-text-input id="title" name="title" class="w-full mt-1" :value="old('title')" required maxlength="150" />
                    </div>
                    <div>
                        <x-input-label for="excerpt" :value="__('db.Short Summary (optional)')" />
                        <x-text-input id="excerpt" name="excerpt" class="w-full mt-1" :value="old('excerpt')" maxlength="300"
                            placeholder="{{ __('db.Shown on the posts list and when shared — auto-generated from your post if left blank.') }}" />
                    </div>
                    <div>
                        <x-input-label for="body" :value="__('db.Your Post')" />
                        <input id="trix-body" type="hidden" name="body" value="{{ old('body') }}">
                        <trix-editor input="trix-body"></trix-editor>
                    </div>
                    <div>
                        <x-input-label for="cover_image" :value="__('db.Cover Image (optional)')" />
                        <input id="cover_image" name="cover_image" type="file" accept="image/*" class="w-full mt-1">
                    </div>

                    <div class="flex justify-end pt-2">
                        <x-primary-button>{{ \App\Models\Post::isAutoPublishedFor(Auth::user()) ? __('db.Publish Post') : __('db.Submit for Review') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
