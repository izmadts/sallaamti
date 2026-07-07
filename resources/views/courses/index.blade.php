<x-guest-layout>
    <x-slot name="header">
        <h2 class="h4 fw-bold text-dark mb-0">
            Quran & Islamic Learning
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7xl mx-auto px-4">

            {{-- Categories --}}
            <div class="mb-4 flex flex-wrap gap-2">

                <a href="{{ route('courses.index') }}"
                    class="btn-base {{ !request('category') ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill btn-sm">
                    All
                </a>

                @foreach ($categories as $cat)

                <a href="{{ route('courses.index', ['category' => $cat]) }}"
                    class="btn-base {{ request('category') === $cat ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill btn-sm">

                    {{ $cat }}

                </a>

                @endforeach

            </div>

            {{-- Courses --}}
            <div class="row g-4">

                @forelse($courses as $course)

                <div class="grid md:grid-cols-6 gap-6">

                    <a href="{{ route('courses.show', $course) }}"
                        class="text-decoration-none text-dark">

                        <div class="card h-100 shadow-sm border-0">

                            @if($course->thumbnail)

                            <img
                                src="{{ Storage::url($course->thumbnail) }}"
                                class="card-img-top"
                                style="height:220px;object-fit:cover;"
                                alt="{{ $course->title }}">

                            @else

                            <div
                                class="d-flex align-items-center justify-content-center bg-light"
                                style="height:220px;font-size:70px;">

                                📖

                            </div>

                            @endif

                            <div class="card-body">

                                <small class="text-uppercase text-muted">

                                    {{ $course->category }}

                                </small>

                                <h5 class="card-title mt-2">

                                    {{ $course->title }}

                                </h5>

                                <p class="card-text text-muted">

                                    {{ $course->lessons_count }} Lessons

                                </p>

                            </div>

                        </div>

                    </a>

                </div>

                @empty

                <div class="col-12">

                    <div class="alert alert-info">

                        No courses available yet.

                    </div>

                </div>

                @endforelse

            </div>

            {{-- Pagination --}}
            <div class="mt-4 d-flex justify-content-center">

                {{ $courses->links() }}

            </div>

        </div>
    </div>
</x-guest-layout>