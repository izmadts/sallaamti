<x-guest-layout>

    <div style="margin-top: 150px;">
        <!-- Contact Start -->
        <div class="container-fluid contact py-5">
            <div class="container py-5">
                <div class="text-center mx-auto mb-5 wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
                    <p class="fs-5 text-uppercase text-primary">Get In Touch</p>
                    <h1 class="display-3">Contact For Any Queries</h1>
                    <p class="mb-0">We would love to listen your feedback and quires, let's get in touch! </p>
                </div>
                <form action="{{ route('contact.store') }}" method="POST" class="bg-light rounded p-4">

                    <div class="row g-4 wow fadeIn" data-wow-delay="0.3s">
                        <div class="col-sm-6">
                            <input type="text" class="form-control bg-transparent p-3" placeholder="Your Name">
                        </div>
                        <div class="col-sm-6">
                            <input type="email" class="form-control bg-transparent p-3" placeholder="Your Email">
                        </div>
                        <div class="col-12">
                            <input type="text" class="form-control bg-transparent p-3" placeholder="Subject">
                        </div>
                        <div class="col-12">
                            <textarea class="w-100 form-control bg-transparent p-3" rows="6" cols="10" placeholder="Your Message"></textarea>
                        </div>
                        <div class="col-12 text-center">
                            <button  type="submit" class="btn btn-primary border-0 py-3 px-5">Send Message</button>
                        </div>
                    </div>
                </form>
                @if (session('contact_success'))
                <div class="alert alert-success">{{ session('contact_success') }}</div>
                @endif
            </div>
        </div>
        <!-- Contact Start -->
    </div>

</x-guest-layout>