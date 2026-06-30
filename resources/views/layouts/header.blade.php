

     <!-- Spinner Start -->
     <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
         <div class="spinner-grow text-primary" role="status"></div>
     </div>
     <!-- Spinner End -->


     <!-- Topbar start -->
     <div class="container-fluid fixed-top" id="header">
         <div class="container topbar">
             <div class="topbar-inner ">
                 <div class="row gx-0">
                     <div class="col-lg-7 text-start d-lg-block d-none">
                         <div class="h-100 d-inline-flex align-items-center me-4">
                             <span class="fa fa-phone-alt me-2 text-dark"></span>
                             <a href="#" class="text-secondary"><span>+92 334 6145566</span></a>
                         </div>
                         <div class="h-100 d-inline-flex align-items-center">
                             <span class="far fa-envelope me-2 text-dark"></span>
                             <a href="#" class="text-secondary"><span>info@sallaamti.com</span></a>
                         </div>
                     </div>
                     <div class="col-lg-5 text-end">
                         <div class="h-100 d-inline-flex align-items-center">
                             <span class="text-body">Follow Us:</span>
                             <a class="text-dark px-2" href="https://facebook.com/sallaamti"><i class="fab fa-facebook-f"></i></a>
                             <a class="text-dark px-2" href="https://tiktok.com/@sallaamti"><i class="fab fa-tiktok"></i></a>
                             <a class="text-dark px-2" href="https://youtube.com/@sallaamti"><i class="fab fa-youtube"></i></a>
                             <a class="text-dark px-2 me-2" href="https://instagram.com/sallaamti"><i class="fab fa-instagram"></i></a>
                             <a href="{{ route('login') }}" class="btn btn-outline-warning"><i class="fa fa-lock text-dark me-1">‌</i> Log in</a>
                             @if (Route::has('register'))
                             <a href="{{ route('register') }}" class="btn btn-outline-warning"><i class="fa fa-user-plus text-dark me-1">‌</i>Register
                             </a>
                             @endif
                         </div>
                     </div>
                 </div>
             </div>
         </div>
         <div class="container">
             <nav class="navbar navbar-light navbar-expand-lg py-3">
                 <a href="index.html" class="navbar-brand">
                     <x-application-logo />
                 </a>
                 <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                     <span class="fa fa-bars text-primary"></span>
                 </button>
                 <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                     <div class="navbar-nav ms-lg-auto mx-xl-auto">
                         <a href="{{ url('/') }}" class="nav-item nav-link active">Home</a>
                         <a href="{{ url('/about') }}" class="nav-item nav-link">About</a>
                         <a href="{{ url('/activities') }}" class="nav-item nav-link">Activities</a>
                         <a href="{{ url('/events') }}" class="nav-item nav-link">Events</a>
                         <a href="{{ url('/sermons') }}" class="nav-item nav-link">Sermons</a>
                         <div class="nav-item dropdown">
                             <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">More</a>
                             <div class="dropdown-menu m-0 rounded-0">
                                 <a href="{{ url('/blog') }}" class="dropdown-item">Latest Blog</a>
                                 <a href="{{ url('/team') }}" class="dropdown-item">Our Team</a>
                                 <a href="{{ url('/testimonial') }}" class="dropdown-item">Testimonial</a>
                             </div>
                         </div>
                         <a href="{{ url('/contact') }}" class="nav-item nav-link">Contact</a>
                     </div>
                     <a href="{{ url('/donate') }}" class="btn btn-primary py-2 px-4 d-none d-xl-inline-block me-2">Donate</a>
                     <a href="{{ url('/volunteer') }}" class="btn btn-primary py-2 px-4 d-none d-xl-inline-block">Volunteer</a>
                 </div>
             </nav>
         </div>
     </div>
     <!-- Topbar End -->


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