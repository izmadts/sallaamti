 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="utf-8">
     <title>Sallaamti Importance of Quran Eudction and Empowring Humanity</title>
     <meta content="width=device-width, initial-scale=1.0" name="viewport">
     <meta content="" name="keywords">
     <meta content="" name="description">

     <!-- Google Web Fonts -->
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&family=Pacifico&display=swap" rel="stylesheet">

     <!-- Icon Font Stylesheet -->
     <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

     <!-- Libraries Stylesheet -->
     <link href="{{ asset('lib/animate/animate.min.css') }}" rel="stylesheet">
     <link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css')}}" rel="stylesheet">

     <!-- Customized Bootstrap Stylesheet -->
     <link href="{{ asset('css/bootstrap.min.css')}}" rel="stylesheet">

     <!-- Template Stylesheet -->
     <link href="{{ asset('css/style.css')}}" rel="stylesheet">



 </head>

 <body>

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
                             <a href="{{ route('login') }}" class="btn btn-outline-warning px-4"><i class="fa fa-lock text-dark me-1">‌</i> Log in</a>
                             @if (Route::has('register'))
                             <a href="{{ route('register') }}" class="btn btn-outline-warning px-4"><i class="fa fa-user-plus text-dark me-1">‌</i>Register
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
                     <a href="" class="btn btn-primary py-2 px-4 d-none d-xl-inline-block">Donate</a>
                 </div>
             </nav>
         </div>
     </div>
     <!-- Topbar End -->


     <!-- Hero Start -->
     <div class="container-fluid hero-header">
         <div class="container">
             <div class="row">
                 <div class="col-lg-7">
                     <div class="hero-header-inner animated zoomIn">
                         <h1 class="display-1 text-dark">Testimonial</h1>
                         <ol class="breadcrumb mb-0">
                             <li class="breadcrumb-item"><a href="#">Home</a></li>
                             <li class="breadcrumb-item"><a href="#">Pages</a></li>
                             <li class="breadcrumb-item text-dark" aria-current="page">Testimonial</li>
                         </ol>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <!-- Hero End -->


     <!-- Testiminial Start -->
     <div class="container-fluid testimonial py-5">
         <div class="container py-5">
             <div class="text-center mx-auto mb-5 wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
                 <p class="fs-5 text-uppercase text-primary">Testimonial</p>
                 <h1 class="display-3">What People Say About Islam</h1>
             </div>
             <div class="owl-carousel testimonial-carousel wow fadeIn" data-wow-delay="0.1s">
                 <div class="testimonial-item">
                     <div class="d-flex mb-3">
                         <div class="position-relative">
                             <img src=" {{ asset('img/testimonial-1.jpg') }}" class="img-fluid" alt="">
                             <div class="btn-md-square bg-primary rounded-circle position-absolute" style="top: 25px; left: -25px;">
                                 <i class="fa fa-quote-left text-dark"></i>
                             </div>
                         </div>
                         <div class="ps-3 my-auto ">
                             <h5 class="mb-0">Full Name</h5>
                             <p class="m-0">Profession</p>
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
                         <p class="fs-5 m-0 pt-3">Lorem ipsum dolor sit amet elit, sed do tempor ut labore et dolore magna aliqua. Ut enim ad minim quis.</p>
                     </div>
                 </div>
                 <div class="testimonial-item">
                     <div class="d-flex mb-3">
                         <div class="position-relative">
                             <img src=" {{ asset('img/testimonial-2.jpg') }}" class="img-fluid" alt="">
                             <div class="btn-md-square bg-primary rounded-circle position-absolute" style="top: 25px; left: -25px;">
                                 <i class="fa fa-quote-left text-dark"></i>
                             </div>
                         </div>
                         <div class="ps-3 my-auto ">
                             <h5 class="mb-0">Full Name</h5>
                             <p class="m-0">Profession</p>
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
                         <p class="fs-5 m-0 pt-3">Lorem ipsum dolor sit amet elit, sed do tempor ut labore et dolore magna aliqua. Ut enim ad minim quis.</p>
                     </div>
                 </div>
                 <div class="testimonial-item">
                     <div class="d-flex mb-3">
                         <div class="position-relative">
                             <img src=" {{ asset('img/testimonial-3.jpg') }}" class="img-fluid" alt="">
                             <div class="btn-md-square bg-primary rounded-circle position-absolute" style="top: 25px; left: -25px;">
                                 <i class="fa fa-quote-left text-dark"></i>
                             </div>
                         </div>
                         <div class="ps-3 my-auto ">
                             <h5 class="mb-0">Full Name</h5>
                             <p class="m-0">Profession</p>
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
                         <p class="fs-5 m-0 pt-3">Lorem ipsum dolor sit amet elit, sed do tempor ut labore et dolore magna aliqua. Ut enim ad minim quis.</p>
                     </div>
                 </div>
                 <div class="testimonial-item">
                     <div class="d-flex mb-3">
                         <div class="position-relative">
                             <img src=" {{ asset('img/testimonial-4.jpg') }}" class="img-fluid" alt="">
                             <div class="btn-md-square bg-primary rounded-circle position-absolute" style="top: 25px; left: -25px;">
                                 <i class="fa fa-quote-left text-dark"></i>
                             </div>
                         </div>
                         <div class="ps-3 my-auto ">
                             <h5 class="mb-0">Full Name</h5>
                             <p class="m-0">Profession</p>
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
                         <p class="fs-5 m-0 pt-3">Lorem ipsum dolor sit amet elit, sed do tempor ut labore et dolore magna aliqua. Ut enim ad minim quis.</p>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <!-- Testiminial End -->


     <!-- Footer Start -->
     <div class="container-fluid footer pt-5 wow fadeIn" data-wow-delay="0.1s">
         <div class="container py-5">
             <div class="row py-5">
                 <div class="col-lg-7">
                     <h1 class="text-light mb-0">Subscribe our newsletter</h1>
                     <p class="text-secondary">Get the latest news and other tips</p>
                 </div>
                 <div class="col-lg-5">
                     <div class="position-relative mx-auto">
                         <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
                         <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">Subcribe</button>
                     </div>
                 </div>
                 <div class="col-12">
                     <div class="border-top border-secondary"></div>
                 </div>
             </div>
             <div class="row g-4 footer-inner">
                 <div class="col-md-6 col-lg-6 col-xl-3">
                     <div class="footer-item mt-5">
                         <img src=" {{ asset('{{ asset('img/logo-w.png')}}" class="img-fluid">
                         <p class="mb-4 text-secondary">Sallaamti (سلامتی) is an organization dedicated to spreading peace, knowledge, and compassion through the teachings of the Quran and Hadith. </p>
                         <a href="" class="btn btn-primary py-2 px-4">Donate Now</a>
                     </div>
                 </div>
                 <div class="col-md-6 col-lg-6 col-xl-3">
                     <div class="footer-item mt-5">
                         <h4 class="text-light mb-4">Contact</h4>
                         <div class="d-flex flex-column">
                             <h6 class="text-secondary mb-0">Our Address</h6>
                             <div class="d-flex align-items-center border-bottom py-4">
                                 <span class="flex-shrink-0 btn-square bg-primary me-3 p-4"><i class="fa fa-map-marker-alt text-dark"></i></span>
                                 <a href="" class="text-body">Gulshan Faiz Colony Multan</a>
                             </div>
                             <h6 class="text-secondary mt-4 mb-0">Our Mobile</h6>
                             <div class="d-flex align-items-center py-4">
                                 <span class="flex-shrink-0 btn-square bg-primary me-3 p-4"><i class="fa fa-phone-alt text-dark"></i></span>
                                 <a href="" class="text-body">+92 334 6145566</a>
                             </div>
                         </div>
                     </div>
                 </div>
                 <div class="col-md-6 col-lg-6 col-xl-3">
                     <div class="footer-item mt-5">
                         <h4 class="text-light mb-4">Explore Link</h4>
                         <div class="d-flex flex-column align-items-start">
                             <a class="text-body mb-2" href=""><i class="fa fa-check text-primary me-2"></i>Home</a>
                             <a class="text-body mb-2" href=""><i class="fa fa-check text-primary me-2"></i>About Us</a>
                             <a class="text-body mb-2" href=""><i class="fa fa-check text-primary me-2"></i>Our Features</a>
                             <a class="text-body mb-2" href=""><i class="fa fa-check text-primary me-2"></i>Contact us</a>
                             <a class="text-body mb-2" href=""><i class="fa fa-check text-primary me-2"></i>Our Blog</a>
                             <a class="text-body mb-2" href=""><i class="fa fa-check text-primary me-2"></i>Our Events</a>
                             <a class="text-body mb-2" href=""><i class="fa fa-check text-primary me-2"></i>Donations</a>
                             <a class="text-body mb-2" href=""><i class="fa fa-check text-primary me-2"></i>Sermons</a>
                         </div>
                     </div>
                 </div>
                 <div class="col-md-6 col-lg-6 col-xl-3">
                     <div class="footer-item mt-5">
                         <h4 class="text-light mb-4">Latest Post</h4>
                         <div class="d-flex border-bottom border-secondary py-4">
                             <img src=" {{ asset('{{ asset('img/blog-mini-1.jpg')}}" class="img-fluid flex-shrink-0" alt="">
                             <div class="ps-3">
                                 <p class="mb-0 text-muted">01 Jan 2045</p>
                                 <a href="" class="text-body">Lorem ipsum dolor sit amet elit eros vel</a>
                             </div>
                         </div>
                         <div class="d-flex py-4">
                             <img src=" {{ asset('{{ asset('img/blog-mini-2.jpg')}}" class="img-fluid flex-shrink-0" alt="">
                             <div class="ps-3">
                                 <p class="mb-0 text-muted">01 Jan 2045</p>
                                 <a href="" class="text-body">Lorem ipsum dolor sit amet elit eros vel</a>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
         <div class="container py-4">
             <div class="border-top border-secondary pb-4"></div>
             <div class="row">
                 <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                     &copy; <a class="border-bottom" href="#">www.sallaamti.com</a>, All Right Reserved.
                 </div>
                 <div class="col-md-6 text-center text-md-end">
                     Designed & Developed By <a class="border-bottom" href="https://izmadts.com">IZMAdts</a>
                 </div>
             </div>
         </div>
     </div>
     <!-- Footer End -->


     <!-- Back to Top -->
     <a href="#" class="btn btn-primary border-3 border-light back-to-top"><i class="fa fa-arrow-up"></i></a>


     <!-- JavaScript Libraries -->
     <script src=" {{ asset('https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
     <script src=" {{ asset('https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
     <script src=" {{ asset('{{ asset('lib/wow/wow.min.js') }}"></script>
     <script src=" {{ asset('{{ asset('lib/easing/easing.min.js') }}"></script>
     <script src=" {{ asset('{{ asset('lib/waypoints/waypoints.min.js') }}"></script>
     <script src=" {{ asset('{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>

     <!-- Template Javascript -->
     <script src=" {{ asset('{{ asset('js/main.js')}}"></script>
 </body>

 </html>