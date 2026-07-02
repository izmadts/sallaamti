<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Edit Quran Live Course</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 bg-white rounded-lg shadow-sm p-6">
            <form method="POST" action="{{ route('admin.quran-live-courses.update', $course) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div><x-input-label value="Title" /><x-text-input name="title" class="w-full mt-1" required /></div>
                <div><x-input-label value="Description" /><textarea name="description" rows="3" class="border-gray-300 rounded-md w-full mt-1"></textarea></div>
                <div>
                    <x-input-label value="Assign Teacher" />
                    <select name="teacher_id" class="border-gray-300 rounded-md w-full mt-1">
                        <option value="">-- None yet --</option>
                        @foreach ($teachers as $t)
                        <option value="{{ $t->id }}" {{ $course->teacher_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Class Days" />
                    <div class="grid grid-cols-4 gap-2 mt-1 text-sm">
                        @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                        <label class="flex items-center gap-1"><input type="checkbox" name="class_days[]" value="{{ $day }}" {{ in_array($day, $course->class_days) ? 'checked' : '' }}> {{ $day }}</label>
                        @endforeach
                    </div>
                </div>
                <div><x-input-label value="Class Time" /><x-text-input name="class_time" class="w-full mt-1" placeholder="6:00 PM - 7:00 PM" /></div>
                <div><x-input-label value="Monthly Fee (Rs.)" /><x-text-input name="monthly_fee" type="number" step="0.01" class="w-full mt-1" required /></div>
                <div class="flex items-center gap-2"><input type="checkbox" name="is_published" value="1" id="is_published" {{ $course->is_published ? 'checked' : '' }}><label for="is_published" class="text-sm">Publish</label></div>
                <x-primary-button>Update Course</x-primary-button>
            </form>
        </div>
    </div>
</x-admin-layout>