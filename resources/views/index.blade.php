<x-guest-layout>

    <!-- Carousel Start -->
    <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('img/hero.jpg')}}" class="d-block w-100" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <p class="fs-1 text-dark">Quran Education</p>
                    <h1 class="display-1 mb-2 text-dark text-uppercase">MOST IMPORTANT FOR MANKIND</h1>
                    <p class="fs-2 text-dark">than any education in the world</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary py-3 px-5">Start Learning</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="{{ asset('img/hero1.jpg')}}" class="d-block w-100" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <p class="fs-1 text-dark">Avoid Zinnah</p>
                    <h1 class="display-1 mb-2 text-dark text-uppercase">Prefer Marriage Between Ummah</h1>
                    <p class="fs-2 text-dark">Big negligency in Muslims Society</p>
                    <a href="{{ route('nikah.create') }}" class="btn btn-primary py-3 px-5">Find Match</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="{{ asset('img/hero2.jpg')}}" class="d-block w-100" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <p class="fs-1 text-dark">Parental Couching</p>
                    <h1 class="display-1 mb-2 text-dark text-uppercase">SAVE YOUR FAMILY GET COUCHING</h1>
                    <p class="fs-2 text-dark">before any big trouble in life</p>
                    <a href="#" class="btn btn-primary py-3 px-5">Get Enrolled</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Carousel End -->

    <!-- About Satrt -->
    <div class="container-fluid about py-5">
        <div class="container py-5">
            <div class="row g-5 mb-5">
                <div class="col-xl-6">
                    <div class="row g-4">
                        <div class="col-6">
                            <img src="{{ asset('img/about-1.jpg')}}" class="img-fluid h-100 wow zoomIn" data-wow-delay="0.1s" alt="">
                        </div>
                        <div class="col-6">
                            <img src="{{ asset('img/about-2.jpg')}}" class="img-fluid pb-3 wow zoomIn" data-wow-delay="0.1s" alt="">
                            <img src="{{ asset('img/about-3.jpg')}}" class="img-fluid pt-3 wow zoomIn" data-wow-delay="0.1s" alt="">
                        </div>
                        <div class="col-6">
                            <img src="{{ asset('img/about-4.jpg')}}" class="img-fluid pb-3 wow zoomIn" data-wow-delay="0.1s" alt="">
                            <img src="{{ asset('img/about-5.jpg')}}" class="img-fluid pt-3 wow zoomIn" data-wow-delay="0.1s" alt="">
                        </div>
                        <div class="col-6">
                            <img src="{{ asset('img/about-6.jpg')}}" class="img-fluid h-100 wow zoomIn" data-wow-delay="0.1s" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 wow fadeIn" data-wow-delay="0.5s">
                    <p class="fs-5 text-uppercase text-primary">About Sallaamti</p>
                    <h1 class="display-5 pb-4 m-0">Allah Help Those Who Help Themselves</h1>
                    <p class="pb-4">Sallaamti (سلامتی): Empowering Through Knowledge and Compassion Sallaamti is dedicated to spreading the teachings of the Quran and Hadith, enlightening individuals of all ages with the wisdom and guidance found within Islamic scripture. Our mission is to foster harmony and understanding among humanity, promoting peace (Sallaamati) for all. Through educational programs, workshops, and seminars, we equip people with the spiritual insights and moral principles essential for personal growth and societal well-being. Additionally, our charity initiatives focus on empowering the less fortunate, providing educational support and assistance to help them build stronger, more stable lives. Furthermore, we actively support the institution of marriage and work to prevent social injustices by providing counseling and intervention services. At Sallaamti, we strive to create a world where knowledge, compassion, and righteousness prevail, enriching the lives of individuals and communities alike.</p>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="ps-3 d-flex align-items-center justify-content-start">
                                <span class="bg-primary btn-md-square rounded-circle mt-4 me-2"><i class="fa fa-eye text-dark fa-4x mb-5 pb-2"></i></span>
                                <div class="ms-4">
                                    <h5>Our Vision</h5>
                                    <p>Our vision at Sallaamti (سلامتی) is to create a world where the profound teachings of the Quran and Hadith inspire individuals to lead lives of peace, knowledge, and compassion. We envision empowered communities where education bridges gaps, charity uplifts the disadvantaged, and ethical values guide everyday actions. Through our dedication, we aspire to foster a global society rooted in harmony, justice, and mutual respect for all humanity.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="ps-3 d-flex align-items-center justify-content-start">
                                <span class="bg-primary btn-md-square rounded-circle mt-4 me-2"><i class="fa fa-flag text-dark fa-4x mb-5 pb-2"></i></span>
                                <div class="ms-4">
                                    <h5>Our Mission</h5>
                                    <p>At Sallaamti (سلامتی), our mission is to enlighten individuals and uplift communities through the teachings of the Quran and Hadith. We are dedicated to spreading peace, fostering knowledge, and promoting compassion for all. Through education, charitable initiatives, and support for ethical living, we strive to empower the less fortunate, strengthen family bonds, and create a harmonious, just society. Our commitment is to cultivate a world where spiritual insight, moral integrity, and human dignity are cherished and upheld.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-light p-3 mb-4">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-3">
                                <img src="{{ asset('img/about-child.jpg')}}" class="img-fluid rounded-circle" alt="">
                            </div>
                            <div class="col-6">
                                <p class="mb-0">To continue our vital work in educating the youth, supporting the less fortunate, and promoting ethical living, we need your generous support. Your donation will help us empower individuals, strengthen communities, and create a more harmonious and just society. Join us in making a meaningful difference—please donate today and help us spread Salaamati to all.</p>
                            </div>
                            <div class="col-3">
                                <h2 class="mb-0 text-primary text-center">$10,46</h2>
                                <h5 class="mb-0 text-center">Raised</h5>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <p class="mb-2"><i class="fa fa-check text-primary me-3"></i>Charity & Donation</p>
                            <p class="mb-0"><i class="fa fa-check text-primary me-3"></i>Parent Education</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><i class="fa fa-check text-primary me-3"></i>Hadith & Sunnah</p>
                            <p class="mb-0"><i class="fa fa-check text-primary me-3"></i>Empowering the Deserving</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container text-center bg-primary py-5 wow fadeIn" data-wow-delay="0.1s">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-2">
                        <i class="fa fa-mosque fa-5x text-white"></i>
                    </div>
                    <div class="col-lg-7 text-center text-lg-start">
                        <h1 class="mb-0">Every Muslim Needs To Realise The Importance Of The "Quranic" Education</h1>
                    </div>
                    <div class="col-lg-3">
                        <a href="{{ route('courses.index') }}" class="btn btn-light py-2 px-4">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->


    <!-- Activities Start -->
    <div class="container-fluid activities py-5">
        <div class="container py-5">
            <div class="mx-auto text-center mb-5 wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
                <p class="fs-5 text-uppercase text-primary">Activities</p>
                <h1 class="display-3">Here Are Our Activities</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-6 col-xl-4">
                    <div class="activities-item p-4 wow fadeIn" data-wow-delay="0.5s">
                        <i class="fa fa-book-open fa-4x text-dark"></i>
                        <div class="ms-4">
                            <h4>📖Quran Learning</h4>
                            <p class="mb-4">Learn the Quran with proper Tajweed, understanding, and authentic Islamic teachings for children and adults of all ages.</p>
                            <a href="{{ route('courses.index') }}" class="btn btn-primary px-3">Read More</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-4">
                    <div class="activities-item p-4 wow fadeIn" data-wow-delay="0.1s">
                        <i class="fa fa-laptop fa-4x text-dark"></i>
                        <div class="ms-4">
                            <h4>🎧Live Quran Classes</h4>
                            <p class="mb-4">Join our live Quran classes and enhance your understanding of the Holy Quran with expert guidance.</p>
                            <a href="{{ route('quran-live.index') }}" class="btn btn-primary px-3">Read More</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-4">
                    <div class="activities-item p-4 wow fadeIn" data-wow-delay="0.5s">
                        <i class="fa fa-heart fa-4x text-danger"></i>
                        <div class="ms-4">
                            <h4>💍Sallaamti Nikah</h4>
                            <p class="mb-4">Get married in a beautiful and meaningful way with our Islamic marriage services.</p>
                            <a href="{{ route('nikah.create') }}" class="btn btn-primary px-3">Read More</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-4">
                    <div class="activities-item p-4 wow fadeIn" data-wow-delay="0.1s">
                        <i class="fa fa-quran fa-4x text-dark"></i>
                        <div class="ms-4">
                            <h4>💑Couples Care</h4>
                            <p class="mb-4">Receive guidance and support for building a strong and healthy relationship with our couples counseling services.</p>
                            <a href="#" class="btn btn-primary px-3">Coming Soon</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-4">
                    <div class="activities-item p-4 wow fadeIn" data-wow-delay="0.3s">
                        <i class="fa fa-desktop fa-4x text-dark"></i>
                        <div class="ms-4">
                            <h4>💻Digital Skills Training</h4>
                            <p class="mb-4">Enhance your digital literacy and develop essential tech skills with our comprehensive training programs.</p>
                            <a href="#" class="btn btn-primary px-3">Coming Soon</a>
                        </div>
                    </div>
                </div>


                <div class="col-lg-6 col-xl-4">
                    <div class="activities-item p-4 wow fadeIn" data-wow-delay="0.3s">
                        <i class="fa fa-users fa-4x text-dark"></i>
                        <div class="ms-4">
                            <h4>👨‍👧‍👦Parental Counseling</h4>
                            <p class="mb-4">Receive professional guidance and support for effective parenting and family communication with our parental counseling services.</p>
                            <a href="#" class="btn btn-primary px-3">Coming Soon</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Activities Start -->


    <!-- Events Start -->
    <!-- <div class="container-fluid event py-5">
        <div class="container py-5">
            <h1 class="display-3 mb-5 wow fadeIn" data-wow-delay="0.1s">Upcoming <span class="text-primary">Events</span></h1>
            <div class="row g-4 event-item wow fadeIn" data-wow-delay="0.5s">
                <div class="col-3 col-lg-2 pe-0">
                    <div class="text-center border-bottom border-dark py-3 px-2">
                        <h6>17 Jun 2024</h6>
                        <p class="mb-0">Thu 11:30</p>
                    </div>
                </div>
                <div class="col-9 col-lg-6 border-start border-dark pb-5">
                    <div class="ms-3">
                        <h4 class="mb-3">Eid Ul Azha</h4>
                        <p class="mb-4">This Eid Ul Azha, Sallaamti (سلامتی) is dedicated to making a positive impact in our community. We will be organizing Qurbani meat distribution to support needy families, ensuring they have a joyous and blessed celebration. Additionally, we will host educational sessions on the significance of Eid Ul Azha, emphasizing the values of sacrifice, compassion, and community spirit. Join us in spreading joy and Salaamati by participating in our charity initiatives and helping those in need. Together, we can make this Eid truly special for everyone. </p>
                        <a href="#" class="btn btn-primary px-3">Join Now</a>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="overflow-hidden mb-5">
                        <img src="{{ asset('img/events-3.jpg')}}" class="img-fluid w-100" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <!-- Events End -->


    <!-- Sermon Start -->
    <!-- <div class="container-fluid sermon py-5">
             <div class="container py-5">
                 <div class="text-center mx-auto mb-5 wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
                     <p class="fs-5 text-uppercase text-primary">Sermons</p>
                     <h1 class="display-3">Join The Islamic Community</h1>
                 </div>
                 <div class="row g-4 justify-content-center">
                     <div class="col-lg-6 col-xl-4">
                         <div class="sermon-item wow fadeIn" data-wow-delay="0.1s">
                             <div class="overflow-hidden p-4 pb-0">
                                 <img src="img/sermon-1.jpg" class="img-fluid w-100" alt="">
                             </div>
                             <div class="p-4">
                                 <div class="sermon-meta d-flex justify-content-between pb-2">
                                     <div class="">
                                         <small><i class="fa fa-calendar me-2 text-muted"></i><a href="" class="text-muted me-2">13 Nov 2023</small></a>
                                         <small><i class="fas fa-user me-2 text-muted"></i><a href="" class="text-muted">Admin</small></a>
                                     </div>
                                     <div class="">
                                         <a href="" class="me-1"><i class="fas fa-video text-muted"></i></a>
                                         <a href="" class="me-1"><i class="fas fa-headphones text-muted"></i></a>
                                         <a href="" class="me-1"><i class="fas fa-file-alt text-muted"></i></a>
                                         <a href="" class=""><i class="fas fa-image text-muted"></i></a>
                                     </div>
                                 </div>
                                 <a href="" class="d-inline-block h4 lh-sm mb-3">How to get closer to Allah</a>
                                 <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, 
                                 aliquip ex ea commodo consequat.</p>
                             </div>
                         </div>
                     </div>
                    <div class="col-lg-6 col-xl-4">
                         <div class="sermon-item wow fadeIn" data-wow-delay="0.3s">
                             <div class="overflow-hidden p-4 pb-0">
                                 <img src="img/sermon-2.jpg" class="img-fluid w-100" alt="">
                             </div>
                             <div class="p-4">
                                 <div class="sermon-meta d-flex justify-content-between pb-2">
                                     <div class="">
                                         <small><i class="fa fa-calendar me-2 text-muted"></i><a href="" class="text-muted me-2">13 Nov 2023</small></a>
                                         <small><i class="fas fa-user me-2 text-muted"></i><a href="" class="text-muted">Admin</small></a>
                                     </div>
                                     <div class="">
                                         <a href="" class="me-1"><i class="fas fa-video text-muted"></i></a>
                                         <a href="" class="me-1"><i class="fas fa-headphones text-muted"></i></a>
                                         <a href="" class="me-1"><i class="fas fa-file-alt text-muted"></i></a>
                                         <a href="" class=""><i class="fas fa-image text-muted"></i></a>
                                     </div>
                                 </div>
                                 <a href="" class="d-inline-block h4 lh-sm mb-3">Importance of Hajj in Islam</a>
                                 <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, 
                                 aliquip ex ea commodo consequat.</p>
                             </div>
                         </div>
                     </div>
                    <div class="col-lg-6 col-xl-4">
                         <div class="sermon-item wow fadeIn" data-wow-delay="0.5s">
                             <div class="overflow-hidden p-4 pb-0">
                                 <img src="img/sermon-3.jpg" class="img-fluid w-100" alt="">
                             </div>
                             <div class="p-4">
                                 <div class="sermon-meta d-flex justify-content-between pb-2">
                                     <div class="">
                                         <small><i class="fa fa-calendar me-2 text-muted"></i><a href="" class="text-muted me-2">13 Nov 2023</small></a>
                                         <small><i class="fas fa-user me-2 text-muted"></i><a href="" class="text-muted">Admin</small></a>
                                     </div>
                                     <div class="">
                                         <a href="" class="me-1"><i class="fas fa-video text-muted"></i></a>
                                         <a href="" class="me-1"><i class="fas fa-headphones text-muted"></i></a>
                                         <a href="" class="me-1"><i class="fas fa-file-alt text-muted"></i></a>
                                         <a href="" class=""><i class="fas fa-image text-muted"></i></a>
                                     </div>
                                 </div>
                                 <a href="" class="d-inline-block h4 lh-sm mb-3">Importance of “Piller” of Islam</a>
                                 <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, 
                                 aliquip ex ea commodo consequat.</p>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div> -->
    <!-- Sermon End -->


    <!-- Blog Start -->
    <!-- <div class="container-fluid py-5">
             <div class="container py-5">
                 <h1 class="display-3 mb-5 wow fadeIn" data-wow-delay="0.1s">Latest From <span class="text-primary">Our Blog</span></h1>
                 <div class="row g-4 justify-content-center">
                     <div class="col-lg-6 col-xl-4">
                         <div class="blog-item wow fadeIn" data-wow-delay="0.1s">
                             <div class="blog-img position-relative overflow-hidden">
                                 <img src="img/blog-1.jpg" class="img-fluid w-100" alt="">
                                 <div class="bg-primary d-inline px-3 py-2 text-center text-white position-absolute top-0 end-0">01 Jan 2045</div>
                             </div>
                             <div class="p-4">
                                 <div class="blog-meta d-flex justify-content-between pb-2">
                                     <div class="">
                                         <small><i class="fas fa-user me-2 text-muted"></i><a href="" class="text-muted me-2">By Admin</small></a>
                                         <small><i class="fa fa-comment-alt me-2 text-muted"></i><a href="" class="text-muted me-2">12 Comments</small></a>
                                     </div>
                                     <div class="">
                                         <a href=""><i class="fas fa-bookmark text-muted"></i></a>
                                     </div>
                                 </div>
                                 <a href="" class="d-inline-block h4 lh-sm mb-3">Importance of “Piller” of Islam</a>
                                 <p class="mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, 
                                 aliquip ex ea commodo consequat.</p>
                                 <a href="#" class="btn btn-primary px-3">More Details</a>
                             </div>
                         </div>
                     </div>
                     <div class="col-lg-6 col-xl-4">
                         <div class="blog-item wow fadeIn" data-wow-delay="0.3s">
                             <div class="blog-img position-relative overflow-hidden">
                                 <img src="img/blog-2.jpg" class="img-fluid w-100" alt="">
                                 <div class="bg-primary d-inline px-3 py-2 text-center text-white position-absolute top-0 end-0">01 Jan 2045</div>
                             </div>
                             <div class="p-4">
                                 <div class="blog-meta d-flex justify-content-between pb-2">
                                     <div class="">
                                         <small><i class="fas fa-user me-2 text-muted"></i><a href="" class="text-muted me-2">By Admin</small></a>
                                         <small><i class="fa fa-comment-alt me-2 text-muted"></i><a href="" class="text-muted me-2">12 Comments</small></a>
                                     </div>
                                     <div class="">
                                         <a href=""><i class="fas fa-bookmark text-muted"></i></a>
                                     </div>
                                 </div>
                                 <a href="" class="d-inline-block h4 lh-sm mb-3">How to get closer to Allah</a>
                                 <p class="mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, 
                                 aliquip ex ea commodo consequat.</p>
                                 <a href="#" class="btn btn-primary px-3">More Details</a>
                             </div>
                         </div>
                     </div>
                    <div class="col-lg-6 col-xl-4">
                         <div class="blog-item wow fadeIn" data-wow-delay="0.5s">
                             <div class="blog-img position-relative overflow-hidden">
                                 <img src="img/blog-3.jpg" class="img-fluid w-100" alt="">
                                 <div class="bg-primary d-inline px-3 py-2 text-center text-white position-absolute top-0 end-0">01 Jan 2045</div>
                             </div>
                             <div class="p-4">
                                 <div class="blog-meta d-flex justify-content-between pb-2">
                                     <div class="">
                                         <small><i class="fas fa-user me-2 text-muted"></i><a href="" class="text-muted me-2">By Admin</small></a>
                                         <small><i class="fa fa-comment-alt me-2 text-muted"></i><a href="" class="text-muted me-2">12 Comments</small></a>
                                     </div>
                                     <div class="">
                                         <a href=""><i class="fas fa-bookmark text-muted"></i></a>
                                     </div>
                                 </div>
                                 <a href="" class="d-inline-block h4 lh-sm mb-3">Importance of Hajj in Islam</a>
                                 <p class="mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, 
                                 aliquip ex ea commodo consequat.</p>
                                 <a href="#" class="btn btn-primary px-3">More Details</a>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div> -->
    <!-- Blog End -->


    <!-- Team Start -->
    <!-- <div class="container-fluid team py-5">
             <div class="container py-5">
                 <div class="text-center mx-auto mb-5 wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
                     <p class="fs-5 text-uppercase text-primary">Our Team</p>
                     <h1 class="display-3">Meet Our Organizer</h1>
                 </div>
                 <div class="row g-5">
                     <div class="col-lg-4 col-xl-5">
                         <div class="team-img wow zoomIn" data-wow-delay="0.1s">
                             <img src="img/team-1.jpg" class="img-fluid" alt="">
                         </div>
                     </div>
                     <div class="col-lg-8 col-xl-7">
                         <div class="team-item wow fadeIn" data-wow-delay="0.1s">
                             <h1>Anamul Hasan</h1>
                             <h5 class="fw-normal fst-italic text-primary mb-4">President</h5>
                             <p class="mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, 
                             sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. aliquip ex ea commodo consequat.</p>
                             <div class="team-icon d-flex pb-4 mb-4 border-bottom border-primary">
                                 <a class="btn btn-primary btn-lg-square me-2" href=""><i class="fab fa-facebook-f"></i></a>
                                 <a class="btn btn-primary btn-lg-square me-2" href=""><i class="fab fa-twitter"></i></a>
                                 <a href="#" class="btn btn-primary btn-lg-square me-2"><i class="fab fa-instagram"></i></a>
                                 <a href="#" class="btn btn-primary btn-lg-square"><i class="fab fa-linkedin-in"></i></a>
                             </div>
                         </div>
                         <div class="row g-4">
                             <div class="col-md-4">
                                 <div class="team-item wow zoomIn" data-wow-delay="0.2s">
                                     <img src="img/team-2.jpg" class="img-fluid w-100" alt="">
                                     <div class="team-content text-dark text-center py-3">
                                         <div class="team-content-inner">
                                             <h5 class="mb-0">Mustafa Kamal</h5>
                                             <p class="text-dark">Imam</p>
                                             <div class="team-icon d-flex align-items-center justify-content-center">
                                                 <a class="btn btn-primary btn-sm-square me-2" href=""><i class="fab fa-facebook-f"></i></a>
                                                 <a class="btn btn-primary btn-sm-square me-2" href=""><i class="fab fa-twitter"></i></a>
                                                 <a href="#" class="btn btn-primary btn-sm-square me-2"><i class="fab fa-instagram"></i></a>
                                                 <a href="#" class="btn btn-primary btn-sm-square"><i class="fab fa-linkedin-in"></i></a>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <div class="col-md-4">
                                 <div class="team-item wow zoomIn" data-wow-delay="0.4s">
                                     <img src="img/team-3.jpg" class="img-fluid w-100" alt="">
                                     <div class="team-content text-dark text-center py-3">
                                         <div class="team-content-inner">
                                             <h5 class="mb-0">Nahiyan Momen</h5>
                                             <p class="text-dark">Teacher</p>
                                             <div class="team-icon d-flex align-items-center justify-content-center">
                                                 <a class="btn btn-primary btn-sm-square me-2" href=""><i class="fab fa-facebook-f"></i></a>
                                                 <a class="btn btn-primary btn-sm-square me-2" href=""><i class="fab fa-twitter"></i></a>
                                                 <a href="#" class="btn btn-primary btn-sm-square me-2"><i class="fab fa-instagram"></i></a>
                                                 <a href="#" class="btn btn-primary btn-sm-square"><i class="fab fa-linkedin-in"></i></a>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <div class="col-md-4">
                                 <div class="team-item wow zoomIn" data-wow-delay="0.6s">
                                     <img src="img/team-4.jpg" class="img-fluid w-100" alt="">
                                     <div class="team-content text-dark text-center py-3">
                                         <div class="team-content-inner">
                                             <h5 class="mb-0">Asfaque Ali</h5>
                                             <p class="text-dark">Volunteer</p>
                                             <div class="team-icon d-flex align-items-center justify-content-center">
                                                 <a class="btn btn-primary btn-sm-square me-2" href=""><i class="fab fa-facebook-f"></i></a>
                                                 <a class="btn btn-primary btn-sm-square me-2" href=""><i class="fab fa-twitter"></i></a>
                                                 <a href="#" class="btn btn-primary btn-sm-square me-2"><i class="fab fa-instagram"></i></a>
                                                 <a href="#" class="btn btn-primary btn-sm-square"><i class="fab fa-linkedin-in"></i></a>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div> -->
    <!-- Team End -->


    <!-- Testiminial Start -->
    <div class="container-fluid testimonial py-5">
        <div class="container py-5">
            <div class="text-center mx-auto mb-5 wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
                <p class="fs-5 text-uppercase text-primary">Testimonial</p>
                <h1 class="display-3">What People Say About Sallaamti</h1>
            </div>
            <div class="owl-carousel testimonial-carousel wow fadeIn" data-wow-delay="0.1s">
                <div class="testimonial-item">
                    <div class="d-flex mb-3">
                        <div class="position-relative">
                            <img src="{{ asset('img/testimonial-1.jpg')}}" class="img-fluid" alt="">
                            <div class="btn-md-square bg-primary rounded-circle position-absolute" style="top: 25px; left: -25px;">
                                <i class="fa fa-quote-left text-dark"></i>
                            </div>
                        </div>
                        <div class="ps-3 my-auto ">
                            <h5 class="mb-0">Muhammad Ahmed</h5>
                            <p class="m-0">Lahore</p>
                        </div>
                    </div>
                    <div class="testimonial-content">
                        <div class="d-flex">
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                        </div>
                        <p class="fs-5 m-0 pt-3">I had been able to read the Qur'an since childhood, but I never truly understood its message. Through Sallaamti's Quran Learning & Understanding Program, I learned Tajweed and the meanings of many verses. It has strengthened my connection with Allah and brought peace into my daily life. I highly recommend this program to every Muslim family.</p>
                    </div>
                </div>
                <div class="testimonial-item">
                    <div class="d-flex mb-3">
                        <div class="position-relative">
                            <img src="{{ asset('img/testimonial-1.jpg')}}" class="img-fluid" alt="">
                            <div class="btn-md-square bg-primary rounded-circle position-absolute" style="top: 25px; left: -25px;">
                                <i class="fa fa-quote-left text-dark"></i>
                            </div>
                        </div>
                        <div class="ps-3 my-auto ">
                            <h5 class="mb-0">Ayesha Javaid</h5>
                            <p class="m-0">UK</p>
                        </div>
                    </div>
                    <div class="testimonial-content">
                        <div class="d-flex">
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                        </div>
                        <p class="fs-5 m-0 pt-3">The Sallaamti Nikah team guided us with sincerity, professionalism, and Islamic values throughout the process. They took the time to understand our family's expectations and helped us find a compatible match. Alhamdulillah, we are happily married and grateful for their support.</p>
                    </div>
                </div>
                <div class="testimonial-item">
                    <div class="d-flex mb-3">
                        <div class="position-relative">
                            <img src="{{ asset('img/testimonial-3.jpg')}}" class="img-fluid" alt="">
                            <div class="btn-md-square bg-primary rounded-circle position-absolute" style="top: 25px; left: -25px;">
                                <i class="fa fa-quote-left text-dark"></i>
                            </div>
                        </div>
                        <div class="ps-3 my-auto ">
                            <h5 class="mb-0">Abdullah & Maryam</h5>
                            <p class="m-0">Rawalpindi</p>
                        </div>
                    </div>
                    <div class="testimonial-content">
                        <div class="d-flex">
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                        </div>
                        <p class="fs-5 m-0 pt-3">After marriage, we faced communication challenges that affected our relationship. The counselors at Sallaamti listened without judgment and provided practical guidance based on Islamic principles. Their support helped us understand each other better and strengthen our marriage. We are thankful for their compassionate assistance.</p>
                    </div>
                </div>
                <div class="testimonial-item">
                    <div class="d-flex mb-3">
                        <div class="position-relative">
                            <img src="{{ asset('img/testimonial-4.jpg')}}" class="img-fluid" alt="">
                            <div class="btn-md-square bg-primary rounded-circle position-absolute" style="top: 25px; left: -25px;">
                                <i class="fa fa-quote-left text-dark"></i>
                            </div>
                        </div>
                        <div class="ps-3 my-auto ">
                            <h5 class="mb-0">Ahmed Azeem</h5>
                            <p class="m-0">Canada</p>
                        </div>
                    </div>
                    <div class="testimonial-content">
                        <div class="d-flex">
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                        </div>
                        <p class="fs-5 m-0 pt-3">The teachers are knowledgeable, patient, and genuinely care about every student. The online classes are well organized, making it easy to learn even with a busy schedule. My children now enjoy reading the Qur'an and are developing a better understanding of Islamic values.</p>
                    </div>
                </div>
            </div>
            <p class="text-muted text-center"><strong>Privacy Notice:</strong> Some names and identifying details have been changed to protect the privacy of the individuals while preserving the authenticity of their experiences.</p>
        </div>
    </div>
    <!-- Testiminial End -->



</x-guest-layout>