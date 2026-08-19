<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Admission Form — {{ $course->title }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-quran-safety-card />
        <div class="bg-white rounded-lg shadow-sm p-6">
            @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 text-red-700 rounded text-sm">
                <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif
            <form method="POST" action="{{ route('quran-live.admission.store', $course) }}" class="space-y-4">
                @csrf
                <div><x-input-label value="Parent / Guardian Full Name" /><x-text-input name="guardian_name" class="w-full mt-1" required /></div>
                <div><x-input-label value="WhatsApp Number (with country code)" /><x-text-input name="whatsapp_number" class="w-full mt-1" placeholder="+92 3XX XXXXXXX" required /></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><x-input-label value="Country" /><x-text-input name="country" class="w-full mt-1" required /></div>
                    <div><x-input-label value="City / State" /><x-text-input name="city_state" class="w-full mt-1" /></div>
                </div>
                <div><x-input-label value="Student Full Name" /><x-text-input name="student_name" class="w-full mt-1" required /></div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Student Gender" />
                        <select name="student_gender" class="border-gray-300 rounded-md w-full mt-1" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div><x-input-label value="Age" /><x-text-input name="student_age" type="number" class="w-full mt-1" required /></div>
                </div>
                <div><x-input-label value="Current Education Grade" /><x-text-input name="education_grade" class="w-full mt-1" /></div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="learned_quran_before" value="1" id="learned_quran_before">
                    <label for="learned_quran_before" class="text-sm">Student has learned Quran before</label>
                </div>                
                <div>
                    <x-input-label value="Preferred Class Days" />
                    <div class="grid grid-cols-4 gap-2 mt-1 text-sm">
                        @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                        <label class="flex items-center gap-1"><input type="checkbox" name="preferred_days[]" value="{{ $day }}"> {{ $day }}</label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <x-input-label value="Preferred Time" />
                    <select name="preferred_time" class="border-gray-300 rounded-md w-full mt-1">
                        <option value="12:00 PM">12:00 PM - 1:00 PM ( UTC+05:00 )</option>
                        <option value="1:00 PM">1:00 PM - 2:00 PM ( UTC+05:00 )</option>
                        <option value="2:00 PM">2:00 PM - 3:00 PM ( UTC+05:00 )</option>
                        <option value="3:00 PM">3:00 PM - 4:00 PM ( UTC+05:00 )</option>
                        <option value="4:00 PM">4:00 PM - 5:00 PM ( UTC+05:00 )</option>
                        <option value="5:00 PM">5:00 PM - 6:00 PM ( UTC+05:00 )</option>
                        <option value="6:00 PM">6:00 PM - 7:00 PM ( UTC+05:00 )</option>
                        <option value="7:00 PM">7:00 PM - 8:00 PM ( UTC+05:00 )</option>
                        <option value="8:00 PM">8:00 PM - 9:00 PM ( UTC+05:00 )</option>
                        <option value="9:00 PM">9:00 PM - 10:00 PM ( UTC+05:00 )</option>
                        <option value="10:00 PM">10:00 PM - 11:00 PM ( UTC+05:00 )</option>
                        <option value="11:00 PM">11:00 PM - 12:00 AM ( UTC+05:00 )</option>
                        <option value="12:00 AM">12:00 AM - 1:00 AM ( UTC+05:00 )</option>

                    </select>

                </div>
                <div>
                    <x-input-label value="Your Timezone" />
                    <x-text-input name="timezone" class="w-full mt-1" placeholder="e.g. Asia/Karachi, GMT+5, EST" />
                </div>
                <div>
                    <x-input-label value="Teacher Preference" />
                    <select name="teacher_preference" class="border-gray-300 rounded-md w-full mt-1" required>
                        <option value="no_preference">No Preference</option>
                        <option value="male">Male Teacher</option>
                        <option value="female">Female Teacher</option>
                    </select>
                </div>
                <div>
                    <x-input-label value="Which Level are you applying for?" />
                    <select name="selected_level" class="border-gray-300 rounded-md w-full mt-1">
                        <option value="">-- Select Level --</option>
                        @foreach (\App\Models\QuranLiveCourse::where('is_published', true)->orderBy('level_number')->get() as $lvl)
                        <option value="{{ $lvl->title }}">{{ $lvl->level_number }} — {{ $lvl->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label value="Previous Level Completed (if any)" />
                    <x-text-input name="previous_level" class="w-full mt-1" placeholder="e.g. Level 1 Nazrah" />
                </div>

                <div>
                    <x-input-label value="Class Type Preference" />
                    <select name="class_type" class="border-gray-300 rounded-md w-full mt-1">
                        <option value="group">Group Class</option>
                        <option value="one_to_one">One-to-One (Private)</option>
                    </select>
                </div>

                <div><x-input-label value="Special Requirements / Comments" /><textarea name="comments" rows="3" class="border-gray-300 rounded-md w-full mt-1"></textarea></div>

                <div class="bg-gray-50 border rounded p-4 flex items-start gap-2">
                    <input type="checkbox" name="declaration_accepted" value="1" id="declaration_accepted" required class="mt-1">
                    <label for="declaration_accepted" class="text-sm text-gray-600">I, as parent/guardian, confirm the above information is accurate and I consent to my child's/my enrollment in this online Quran class with Sallaamti.</label>
                </div>

                <x-primary-button>Submit Admission</x-primary-button>
            </form>
        </div>
        </div>
    </div>
</x-app-layout>