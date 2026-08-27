<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">✏️ {{ __('db.Edit Post') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">

                @if ($post->status === 'published' && !\App\Models\Post::isAutoPublishedFor(Auth::user()))
                <p class="text-sm text-amber-600 bg-amber-50 border border-amber-200 rounded-lg p-3 mb-6">
                    ⚠️ {{ __('db.This post is already live. Saving changes will send it back for admin review before the edits go public.') }}
                </p>
                @endif

                @if ($post->status === 'rejected' && $post->rejection_reason)
                <p class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg p-3 mb-6">
                    {{ __('db.This post wasn\'t approved: :reason. Update it below and resubmit.', ['reason' => $post->rejection_reason]) }}
                </p>
                @endif

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 text-red-700 rounded text-sm">
                    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form method="POST" action="{{ route('posts.update', $post) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="title" :value="__('db.Title')" />
                        <x-text-input id="title" name="title" class="w-full mt-1" :value="old('title', $post->title)" required maxlength="150" />
                    </div>
                    <div>
                        <x-input-label for="excerpt" :value="__('db.Short Summary (optional)')" />
                        <x-text-input id="excerpt" name="excerpt" class="w-full mt-1" :value="old('excerpt', $post->excerpt)" maxlength="300" />
                    </div>
                    <div>
                        <x-input-label for="body" :value="__('db.Your Post')" />
                        <input id="trix-body" type="hidden" name="body" value="{{ old('body', $post->body) }}">
                        <trix-editor input="trix-body"></trix-editor>
                    </div>
                    <div>
                        <x-input-label for="cover_image" :value="__('db.Cover Image (optional)')" />
                        @if ($post->cover_image)
                        <img src="{{ Storage::url($post->cover_image) }}" class="w-32 h-20 object-cover rounded mt-1 mb-2">
                        @endif
                        <input id="cover_image" name="cover_image" type="file" accept="image/*" class="w-full mt-1">
                    </div>

                    <div class="flex justify-between pt-2">
                        <a href="{{ route('posts.mine') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">{{ __('db.← Back') }}</a>
                        <x-primary-button>{{ __('db.Save Changes') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
