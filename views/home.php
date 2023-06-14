<?php

namespace views;





class home extends \DGZ_library\DGZ_HtmlView
{ 
     


     function show()
     {
         $this->addMetadata(
             [
                 '<meta name="description" content="Welcome to DorguzApp (replace this with your application name).">',
                 '<meta name="keywords" content="DorguzApp (replace this with your application name) contact form. Separate multiple keywords by commas">',
                 '<meta name="author" content="Your name here">'
             ]);
         /* ?>
         <!-- ==========================
    	BREADCRUMB - START
		=========================== -->
         <section class="breadcrumb-wrapper" xmlns="http://www.w3.org/1999/html">
             <div class="container">
                 <div class="row">
                     <div class="col-xs-6">
                     </div>
                     <div class="col-xs-6">
                         <ol class="breadcrumb">
                             <li><a></a></li>
                         </ol>
                     </div>
                 </div>
             </div>
         </section>
         <!-- ==========================
             BREADCRUMB - END
         =========================== -->

         <!-- ==========================
             MAIN PAGE - START
         =========================== -->
         <section class="content news">
             <div class="container">
                 <h2 class="hidden">Home</h2>
                 <div class="row">

                     <div class="viewText">
                         <h2>Welcome to the Dorguzen Framework</h2>

                         <p>
                             Alright we know you have heard about many PHP development frameworks out there, so the pressure is on. What do we tell you about Dorguzen to
                             make it stand out?
                             Well...there are so many points we can make but it is really true what they say; that the proof is in the pudding. As opposed to other
                             frameworks which make you spend a lot of time trying to decipher the code and get your head around all the abstractions, the Dorguzen framework
                             strives to <span class="text-primary">cut to the chase</span> and give you what you need to get up and running with your development.
                             It is a lightweight but at the same time a very powerful PHP development framework that comes packed full with tools to get you going.
                             </p>

                             <p>Its key hallmarks are:</p>
                            <ul>
                                 <li>Very simple (as a matter of fact, over-simplified routing</li>
                                 <li>Simple HTTP API system-there are only a handful or character sets you can pass in the browser URL to make your web pages come alive</li>
                                 <li>Simple <span class="text-primary">Object Relational Mapping (ORM)</span> system built into its models</li>
                                 <li>A model-database chaining system based on the rule of 'convention over configuration' which if followed will give you a great head start</li>
                                <li>Dorguzen aims to strip out the over-complicated learning curves involved with modern frameworks, and hand the reigns of control to you
                                    the engineer/developer as quickly as possible. You will be pleased to find how easy it is to get under the hood and tweak absolutely
                                    every part of the code base - and you are actually encouraged to do so, in order to learn about what it does. This hands-on type of
                                    approach is known to be the best way to gain mastery in any craft. You will be surprised at what you would be able to build in no time.
                                </li>
                                <li>
                                     Helper classes for just about anything you are likely to run into when developing like <span class="text-primary">file uploading and resizing </span>,
                                     easy <span class="text-primary">PDF creation </span>, bootstrap and font awesome libraries pulled in to help you create sleek user interfaces
                                     that are fully responsive, and much much more...
                                 </li>
                            </ul>
                             <p>In short, the Dorguzen framework is, <span class="text-primary">Your Rapid Web Development Toolkit</span>
                         </p>


                     </div>
                 </div><!--END OF ROW DIV, THE FIRST ELEMENT INSIDE THE CONTAINER DIV - WH WRAPS AROUND, OR IS FOLLOWED (INSIDE OF IT) BY THE COL-SM-9 DIV THAT HOLDS THE MAIN BODY OF THE PAGE-->
             </div><!--END OF CONTAINER DIV-->
         </section>
         <!-- ==========================
             MAIN PAGE - END
         =========================== -->
         <section>

        </section>
          <?php */ ?>

         <!------------------------------------------------------
            NAV WAS HERE BUT IS NOW IN HEADER.INC.PHP
         <!------------------------------------------------------>

                 <div class="container-xxl py-5 bg-primary hero-header mb-5">
                     <div class="container my-5 py-5 px-lg-5">
                         <div class="row g-5 py-5">
                             <div class="col-lg-6 text-center text-lg-start">
                                 <h1 class="text-white mb-4 animated zoomIn">Welcome to the Dorguzen Framework</h1>
                                 <p class="text-white pb-3 animated zoomIn">Alright we know you have heard about many PHP development frameworks out there, so the pressure is on. What do we tell you about Dorguzen to
                                     make it stand out?
                                     Well...there are so many points we can make but it is really true what they say; that the proof is in the pudding. As opposed to other
                                     frameworks which make you spend a lot of time trying to decipher the code and get your head around all the abstractions, the Dorguzen framework
                                     strives to <span class="text-primary">cut to the chase</span> and give you what you need to get up and running with your development.
                                     It is a lightweight but at the same time a very powerful PHP development framework that comes packed full with tools to get you going.</p>

                                 <h2 class="text-white pb-3 animated zoomIn">Its key hallmarks are:</h2>

                                 <p class="text-white pb-3 animated zoomIn">
                                     <ul class="text-white pb-3 animated zoomIn">
                                         <li>Very simple (as a matter of fact, over-simplified routing</li>
                                         <li>Simple HTTP API system-there are only a handful or character sets you can pass in the browser URL to make your web pages come alive</li>
                                         <li>Simple Object Relational Mapping (ORM) system built into its models</li>
                                         <li>A model-database chaining system based on the rule of 'convention over configuration' which if followed will give you a great head start</li>
                                         <li>Dorguzen aims to strip out the over-complicated learning curves involved with modern frameworks, and hand the reigns of control to you
                                             the engineer/developer as quickly as possible. You will be pleased to find how easy it is to get under the hood and tweak absolutely
                                             every part of the code base - and you are actually encouraged to do so, in order to learn about what it does. This hands-on type of
                                             approach is known to be the best way to gain mastery in any craft. You will be surprised at what you would be able to build in no time.
                                         </li>
                                         <li>
                                             Helper classes for just about anything you are likely to run into when developing like file uploading and resizing,
                                             easy PDF creation, bootstrap and font awesome libraries pulled in to help you create sleek user interfaces
                                             that are fully responsive, and much much more...
                                         </li>
                                     </ul>
                                    <p class="text-white pb-3 animated zoomIn">In short, the Dorguzen framework is, Your Rapid Web Development Toolkit</p>
                                 </p>

                                 <a href="" class="btn btn-light py-sm-3 px-sm-5 rounded-pill me-3 animated slideInLeft">Free Quote</a>
                                 <a href="" class="btn btn-outline-light py-sm-3 px-sm-5 rounded-pill animated slideInRight">Contact Us</a>
                             </div>
                             <div class="col-lg-6 text-center text-lg-start">
                                 <img class="img-fluid" src="<?=$this->controller->config->getFileRootPath()?>assets/images/hero.png" alt="">
                             </div>
                         </div>
                     </div>
                 </div>
             <!--</div>-->
             <!-- Navbar & Hero End -->


             <!------------------------------
                SEARCH MODAL FORM WAS HERE
             <!------------------------------


             <!-- About Start -->
             <div class="container-xxl bg-white py-5">
                 <div class="container px-lg-5">
                     <div class="row g-5">
                         <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                             <div class="section-title position-relative mb-4 pb-2">
                                 <h6 class="position-relative text-primary ps-4">About Us</h6>
                                 <h2 class="mt-2">The best SEO solution with 10 years of experience</h2>
                             </div>
                             <p class="mb-4">Tempor erat elitr rebum at clita. Diam dolor diam ipsum et tempor sit. Aliqu diam amet diam et eos labore. Clita erat ipsum et lorem et sit, sed stet no labore lorem sit. Sanctus clita duo justo et tempor eirmod magna dolore erat amet</p>
                             <div class="row g-3">
                                 <div class="col-sm-6">
                                     <h6 class="mb-3"><i class="fa fa-check text-primary me-2"></i>Award Winning</h6>
                                     <h6 class="mb-0"><i class="fa fa-check text-primary me-2"></i>Professional Staff</h6>
                                 </div>
                                 <div class="col-sm-6">
                                     <h6 class="mb-3"><i class="fa fa-check text-primary me-2"></i>24/7 Support</h6>
                                     <h6 class="mb-0"><i class="fa fa-check text-primary me-2"></i>Fair Prices</h6>
                                 </div>
                             </div>
                             <div class="d-flex align-items-center mt-4">
                                 <a class="btn btn-primary rounded-pill px-4 me-3" href="">Read More</a>
                                 <a class="btn btn-outline-primary btn-square me-3" href=""><i class="fab fa-facebook-f"></i></a>
                                 <a class="btn btn-outline-primary btn-square me-3" href=""><i class="fab fa-twitter"></i></a>
                                 <a class="btn btn-outline-primary btn-square me-3" href=""><i class="fab fa-instagram"></i></a>
                                 <a class="btn btn-outline-primary btn-square" href=""><i class="fab fa-linkedin-in"></i></a>
                             </div>
                         </div>
                         <div class="col-lg-6">
                             <img class="img-fluid wow zoomIn" data-wow-delay="0.5s" src="<?=$this->controller->config->getFileRootPath()?>assets/images/about.jpg">
                         </div>
                     </div>
                 </div>
             </div>
             <!-- About End -->


             <!-- Newsletter Start -->
             <div class="container-xxl bg-primary newsletter my-5 wow fadeInUp" data-wow-delay="0.1s">
                 <div class="container px-lg-5">
                     <div class="row align-items-center" style="height: 250px;">
                         <div class="col-12 col-md-6">
                             <h3 class="text-white">Ready to get started</h3>
                             <small class="text-white">Diam elitr est dolore at sanctus nonumy.</small>
                             <div class="position-relative w-100 mt-3">
                                 <input class="form-control border-0 rounded-pill w-100 ps-4 pe-5" type="text" placeholder="Enter Your Email" style="height: 48px;">
                                 <button type="button" class="btn shadow-none position-absolute top-0 end-0 mt-1 me-2"><i class="fa fa-paper-plane text-primary fs-4"></i></button>
                             </div>
                         </div>
                         <div class="col-md-6 text-center mb-n5 d-none d-md-block">
                             <img class="img-fluid mt-5" style="height: 250px;" src="<?=$this->controller->config->getFileRootPath()?>assets/images/newsletter.png">
                         </div>
                     </div>
                 </div>
             </div>
             <!-- Newsletter End -->


             <!-- Service Start -->
             <div class="container-xxl py-5">
                 <div class="container px-lg-5">
                     <div class="section-title position-relative text-center mb-5 pb-2 wow fadeInUp" data-wow-delay="0.1s">
                         <h6 class="position-relative d-inline text-primary ps-4">Our Services</h6>
                         <h2 class="mt-2">What Solutions We Provide</h2>
                     </div>
                     <div class="row g-4">
                         <div class="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.1s">
                             <div class="service-item d-flex flex-column justify-content-center text-center rounded">
                                 <div class="service-icon flex-shrink-0">
                                     <i class="fa fa-home fa-2x"></i>
                                 </div>
                                 <h5 class="mb-3">SEO Optimization</h5>
                                 <p>Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.</p>
                                 <a class="btn px-3 mt-auto mx-auto" href="">Read More</a>
                             </div>
                         </div>
                         <div class="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.3s">
                             <div class="service-item d-flex flex-column justify-content-center text-center rounded">
                                 <div class="service-icon flex-shrink-0">
                                     <i class="fa fa-home fa-2x"></i>
                                 </div>
                                 <h5 class="mb-3">Web Design</h5>
                                 <p>Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.</p>
                                 <a class="btn px-3 mt-auto mx-auto" href="">Read More</a>
                             </div>
                         </div>
                         <div class="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.6s">
                             <div class="service-item d-flex flex-column justify-content-center text-center rounded">
                                 <div class="service-icon flex-shrink-0">
                                     <i class="fa fa-home fa-2x"></i>
                                 </div>
                                 <h5 class="mb-3">Social Media Marketing</h5>
                                 <p>Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.</p>
                                 <a class="btn px-3 mt-auto mx-auto" href="">Read More</a>
                             </div>
                         </div>
                         <div class="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.1s">
                             <div class="service-item d-flex flex-column justify-content-center text-center rounded">
                                 <div class="service-icon flex-shrink-0">
                                     <i class="fa fa-home fa-2x"></i>
                                 </div>
                                 <h5 class="mb-3">Email Marketing</h5>
                                 <p>Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.</p>
                                 <a class="btn px-3 mt-auto mx-auto" href="">Read More</a>
                             </div>
                         </div>
                         <div class="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.3s">
                             <div class="service-item d-flex flex-column justify-content-center text-center rounded">
                                 <div class="service-icon flex-shrink-0">
                                     <i class="fa fa-home fa-2x"></i>
                                 </div>
                                 <h5 class="mb-3">PPC Advertising</h5>
                                 <p>Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.</p>
                                 <a class="btn px-3 mt-auto mx-auto" href="">Read More</a>
                             </div>
                         </div>
                         <div class="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.6s">
                             <div class="service-item d-flex flex-column justify-content-center text-center rounded">
                                 <div class="service-icon flex-shrink-0">
                                     <i class="fa fa-home fa-2x"></i>
                                 </div>
                                 <h5 class="mb-3">App Development</h5>
                                 <p>Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.</p>
                                 <a class="btn px-3 mt-auto mx-auto" href="">Read More</a>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
             <!-- Service End -->


             <!-- Portfolio Start -->
             <div class="container-xxl py-5">
                 <div class="container px-lg-5">
                     <div class="section-title position-relative text-center mb-5 pb-2 wow fadeInUp" data-wow-delay="0.1s">
                         <h6 class="position-relative d-inline text-primary ps-4">Our Projects</h6>
                         <h2 class="mt-2">Recently Launched Projects</h2>
                     </div>
                     <div class="row mt-n2 wow fadeInUp" data-wow-delay="0.1s">
                         <div class="col-12 text-center">
                             <ul class="list-inline mb-5" id="portfolio-flters">
                                 <li class="btn px-3 pe-4 active" data-filter="*">All</li>
                                 <li class="btn px-3 pe-4" data-filter=".first">Design</li>
                                 <li class="btn px-3 pe-4" data-filter=".second">Development</li>
                                 <li class="btn px-3 pe-4" data-filter=".products">Products</li>
                             </ul>
                         </div>
                     </div>
                     <div class="row g-4 portfolio-container">
                         <div class="col-lg-4 col-md-6 portfolio-item first wow zoomIn" data-wow-delay="0.1s">
                             <div class="position-relative rounded overflow-hidden">
                                 <img class="img-fluid w-100" src="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-1.jpg" alt="">
                                 <div class="portfolio-overlay">
                                     <a class="btn btn-light" href="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-1.jpg" data-lightbox="portfolio"><i class="fa fa-plus fa-2x text-primary"></i></a>
                                     <div class="mt-auto">
                                         <small class="text-white"><i class="fa fa-folder me-2"></i>Web Design</small>
                                         <a class="h5 d-block text-white mt-1 mb-0" href="">Project Name</a>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="col-lg-4 col-md-6 portfolio-item second wow zoomIn" data-wow-delay="0.3s">
                             <div class="position-relative rounded overflow-hidden">
                                 <img class="img-fluid w-100" src="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-2.jpg" alt="">
                                 <div class="portfolio-overlay">
                                     <a class="btn btn-light" href="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-2.jpg" data-lightbox="portfolio"><i class="fa fa-plus fa-2x text-primary"></i></a>
                                     <div class="mt-auto">
                                         <small class="text-white"><i class="fa fa-folder me-2"></i>Web Design</small>
                                         <a class="h5 d-block text-white mt-1 mb-0" href="">Project Name</a>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="col-lg-4 col-md-6 portfolio-item first wow zoomIn" data-wow-delay="0.6s">
                             <div class="position-relative rounded overflow-hidden">
                                 <img class="img-fluid w-100" src="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-3.jpg" alt="">
                                 <div class="portfolio-overlay">
                                     <a class="btn btn-light" href="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-3.jpg" data-lightbox="portfolio"><i class="fa fa-plus fa-2x text-primary"></i></a>
                                     <div class="mt-auto">
                                         <small class="text-white"><i class="fa fa-folder me-2"></i>Web Design</small>
                                         <a class="h5 d-block text-white mt-1 mb-0" href="">Project Name</a>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="col-lg-4 col-md-6 portfolio-item second wow zoomIn" data-wow-delay="0.1s">
                             <div class="position-relative rounded overflow-hidden">
                                 <img class="img-fluid w-100" src="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-4.jpg" alt="">
                                 <div class="portfolio-overlay">
                                     <a class="btn btn-light" href="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-4.jpg" data-lightbox="portfolio"><i class="fa fa-plus fa-2x text-primary"></i></a>
                                     <div class="mt-auto">
                                         <small class="text-white"><i class="fa fa-folder me-2"></i>Web Design</small>
                                         <a class="h5 d-block text-white mt-1 mb-0" href="">Project Name</a>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="col-lg-4 col-md-6 portfolio-item first wow zoomIn" data-wow-delay="0.3s">
                             <div class="position-relative rounded overflow-hidden">
                                 <img class="img-fluid w-100" src="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-5.jpg" alt="">
                                 <div class="portfolio-overlay">
                                     <a class="btn btn-light" href="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-5.jpg" data-lightbox="portfolio"><i class="fa fa-plus fa-2x text-primary"></i></a>
                                     <div class="mt-auto">
                                         <small class="text-white"><i class="fa fa-folder me-2"></i>Web Design</small>
                                         <a class="h5 d-block text-white mt-1 mb-0" href="">Project Name</a>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="col-lg-4 col-md-6 portfolio-item second wow zoomIn" data-wow-delay="0.6s">
                             <div class="position-relative rounded overflow-hidden">
                                 <img class="img-fluid w-100" src="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-6.jpg" alt="">
                                 <div class="portfolio-overlay">
                                     <a class="btn btn-light" href="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-6.jpg" data-lightbox="portfolio"><i class="fa fa-plus fa-2x text-primary"></i></a>
                                     <div class="mt-auto">
                                         <small class="text-white"><i class="fa fa-folder me-2"></i>Web Design</small>
                                         <a class="h5 d-block text-white mt-1 mb-0" href="">Project Name</a>
                                     </div>
                                 </div>
                             </div>
                         </div>

                         <div class="col-lg-4 col-md-6 portfolio-item products wow zoomIn" data-wow-delay="0.1s">
                             <div class="position-relative rounded overflow-hidden">
                                 <img class="img-fluid w-100" src="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-4.jpg" alt="">
                                 <div class="portfolio-overlay">
                                     <a class="btn btn-light" href="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-4.jpg" data-lightbox="portfolio"><i class="fa fa-plus fa-2x text-primary"></i></a>
                                     <div class="mt-auto">
                                         <small class="text-white"><i class="fa fa-folder me-2"></i>DGZ-Shop</small>
                                         <a class="h5 d-block text-white mt-1 mb-0" href="">Cool web store</a>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="col-lg-4 col-md-6 portfolio-item products wow zoomIn" data-wow-delay="0.3s">
                             <div class="position-relative rounded overflow-hidden">
                                 <img class="img-fluid w-100" src="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-5.jpg" alt="">
                                 <div class="portfolio-overlay">
                                     <a class="btn btn-light" href="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-5.jpg" data-lightbox="portfolio"><i class="fa fa-plus fa-2x text-primary"></i></a>
                                     <div class="mt-auto">
                                         <small class="text-white"><i class="fa fa-folder me-2"></i>DGZ-Classified Ads</small>
                                         <a class="h5 d-block text-white mt-1 mb-0" href="">Basic</a>
                                     </div>
                                 </div>
                             </div>
                         </div>

                         <div class="col-lg-4 col-md-6 portfolio-item products wow zoomIn" data-wow-delay="0.6s">
                             <div class="position-relative rounded overflow-hidden">
                                 <img class="img-fluid w-100" src="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-6.jpg" alt="">
                                 <div class="portfolio-overlay">
                                     <a class="btn btn-light" href="<?=$this->controller->config->getFileRootPath()?>assets/images/portfolio-6.jpg" data-lightbox="portfolio"><i class="fa fa-plus fa-2x text-primary"></i></a>
                                     <div class="mt-auto">
                                         <small class="text-white"><i class="fa fa-folder me-2"></i>DGZ-Classified Ads</small>
                                         <a class="h5 d-block text-white mt-1 mb-0" href="">Multi-vendor</a>
                                     </div>
                                 </div>
                             </div>
                         </div>

                     </div>
                 </div>
             </div>
             <!-- Portfolio End -->


             <!-- Testimonial Start -->
             <div class="container-xxl bg-primary testimonial py-5 my-5 wow fadeInUp" data-wow-delay="0.1s">
                 <div class="container py-5 px-lg-5">

                     <div class="section-title-testimonials position-relative text-center mb-5 pb-2 wow fadeInUp" data-wow-delay="0.1s">
                         <!--<h6 class="position-relative d-inline text-primary ps-4">Testimonials</h6>-->
                         <h6 class="text-white mb-4 animated zoomIn position-relative d-inline text-primary ps-4">Testimonials</h6>
                         <h2 class="mt-2 text-white">What Our Clients Say</h2>
                     </div>

                     <div class="owl-carousel testimonial-carousel">
                         <div class="testimonial-item bg-transparent border rounded text-white p-4">
                             <i class="fa fa-quote-left fa-2x mb-3"></i>
                             <p>Dolor et eos labore, stet justo sed est sed. Diam sed sed dolor stet amet eirmod eos labore diam</p>
                             <div class="d-flex align-items-center">
                                 <img class="img-fluid flex-shrink-0 rounded-circle" src="<?=$this->controller->config->getFileRootPath()?>assets/images/testimonial-1.jpg" style="width: 50px; height: 50px;">
                                 <div class="ps-3">
                                     <h6 class="text-white mb-1">Client Name</h6>
                                     <small>Profession</small>
                                 </div>
                             </div>
                         </div>
                         <div class="testimonial-item bg-transparent border rounded text-white p-4">
                             <i class="fa fa-quote-left fa-2x mb-3"></i>
                             <p>Dolor et eos labore, stet justo sed est sed. Diam sed sed dolor stet amet eirmod eos labore diam</p>
                             <div class="d-flex align-items-center">
                                 <img class="img-fluid flex-shrink-0 rounded-circle" src="<?=$this->controller->config->getFileRootPath()?>assets/images/testimonial-2.jpg" style="width: 50px; height: 50px;">
                                 <div class="ps-3">
                                     <h6 class="text-white mb-1">Client Name</h6>
                                     <small>Profession</small>
                                 </div>
                             </div>
                         </div>
                         <div class="testimonial-item bg-transparent border rounded text-white p-4">
                             <i class="fa fa-quote-left fa-2x mb-3"></i>
                             <p>Dolor et eos labore, stet justo sed est sed. Diam sed sed dolor stet amet eirmod eos labore diam</p>
                             <div class="d-flex align-items-center">
                                 <img class="img-fluid flex-shrink-0 rounded-circle" src="<?=$this->controller->config->getFileRootPath()?>assets/images/testimonial-3.jpg" style="width: 50px; height: 50px;">
                                 <div class="ps-3">
                                     <h6 class="text-white mb-1">Client Name</h6>
                                     <small>Profession</small>
                                 </div>
                             </div>
                         </div>
                         <div class="testimonial-item bg-transparent border rounded text-white p-4">
                             <i class="fa fa-quote-left fa-2x mb-3"></i>
                             <p>Dolor et eos labore, stet justo sed est sed. Diam sed sed dolor stet amet eirmod eos labore diam</p>
                             <div class="d-flex align-items-center">
                                 <img class="img-fluid flex-shrink-0 rounded-circle" src="<?=$this->controller->config->getFileRootPath()?>assets/images/testimonial-4.jpg" style="width: 50px; height: 50px;">
                                 <div class="ps-3">
                                     <h6 class="text-white mb-1">Client Name</h6>
                                     <small>Profession</small>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
                 <a href="https://htmlcodex.com/startup-company-website-template" class="btn btn-secondary text-light rounded-pill py-2 px-4 ms-3">View all Testimonials</a>
             </div>
             <!-- Testimonial End -->


             <!-- Team Start -->
             <div class="container-xxl py-5">
                 <div class="container px-lg-5">
                     <div class="section-title position-relative text-center mb-5 pb-2 wow fadeInUp" data-wow-delay="0.1s">
                         <h6 class="position-relative d-inline text-primary ps-4">Our Team</h6>
                         <h2 class="mt-2">Meet Our Team Members</h2>
                     </div>
                     <div class="row g-4">
                         <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                             <div class="team-item">
                                 <div class="d-flex">
                                     <div class="flex-shrink-0 d-flex flex-column align-items-center mt-4 pt-5" style="width: 75px;">
                                         <a class="btn btn-square text-primary bg-white my-1" href=""><i class="fab fa-facebook-f"></i></a>
                                         <a class="btn btn-square text-primary bg-white my-1" href=""><i class="fab fa-twitter"></i></a>
                                         <a class="btn btn-square text-primary bg-white my-1" href=""><i class="fab fa-instagram"></i></a>
                                         <a class="btn btn-square text-primary bg-white my-1" href=""><i class="fab fa-linkedin-in"></i></a>
                                     </div>
                                     <img class="img-fluid rounded w-100" src="<?=$this->controller->config->getFileRootPath()?>assets/images/team-1.jpg" alt="">
                                 </div>
                                 <div class="px-4 py-3">
                                     <h5 class="fw-bold m-0">Jhon Doe</h5>
                                     <small>CEO</small>
                                 </div>
                             </div>
                         </div>
                         <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                             <div class="team-item">
                                 <div class="d-flex">
                                     <div class="flex-shrink-0 d-flex flex-column align-items-center mt-4 pt-5" style="width: 75px;">
                                         <a class="btn btn-square text-primary bg-white my-1" href=""><i class="fab fa-facebook-f"></i></a>
                                         <a class="btn btn-square text-primary bg-white my-1" href=""><i class="fab fa-twitter"></i></a>
                                         <a class="btn btn-square text-primary bg-white my-1" href=""><i class="fab fa-instagram"></i></a>
                                         <a class="btn btn-square text-primary bg-white my-1" href=""><i class="fab fa-linkedin-in"></i></a>
                                     </div>
                                     <img class="img-fluid rounded w-100" src="<?=$this->controller->config->getFileRootPath()?>assets/images/team-2.jpg" alt="">
                                 </div>
                                 <div class="px-4 py-3">
                                     <h5 class="fw-bold m-0">Emma William</h5>
                                     <small>Manager</small>
                                 </div>
                             </div>
                         </div>
                         <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.6s">
                             <div class="team-item">
                                 <div class="d-flex">
                                     <div class="flex-shrink-0 d-flex flex-column align-items-center mt-4 pt-5" style="width: 75px;">
                                         <a class="btn btn-square text-primary bg-white my-1" href=""><i class="fab fa-facebook-f"></i></a>
                                         <a class="btn btn-square text-primary bg-white my-1" href=""><i class="fab fa-twitter"></i></a>
                                         <a class="btn btn-square text-primary bg-white my-1" href=""><i class="fab fa-instagram"></i></a>
                                         <a class="btn btn-square text-primary bg-white my-1" href=""><i class="fab fa-linkedin-in"></i></a>
                                     </div>
                                     <img class="img-fluid rounded w-100" src="<?=$this->controller->config->getFileRootPath()?>assets/images/team-3.jpg" alt="">
                                 </div>
                                 <div class="px-4 py-3">
                                     <h5 class="fw-bold m-0">Noah Michael</h5>
                                     <small>Designer</small>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
             <!-- Team End -->



         <!--</div>-->


         <?php ?>
     <?php

     }

}

?>