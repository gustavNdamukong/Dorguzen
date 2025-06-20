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
             ]); ?>

<!--<div class="preloader">
        <img class="preloader__image" width="60" src="assets/halpes/images/loader.png" alt="" />
    </div>-->

    <!-- /.preloader -->
    <!--<div class="page-wrapper">-->
        <!--REMOVED HEADER CODE FROM HERE-->

        

        <!--Welcome One Start-->
        <section class="welcome-one">
            <div class="container">
                <div class="row">
                    <div class="col-xl-6 col-lg-6">
                        <div class="welcome-one__left">
                            <div class="welcome-one__img-box">
                                <img src="assets/halpes/images/resources/welcome-one-img-1.jpg" alt="">
                                <div class="welcome-one__img-box-2">
                                    <img src="assets/halpes/images/resources/welcome-one-img-2.jpg" alt="">
                                </div>
                                <h2 class="welcome-one__bottom-text">Helping Today</h2>
                                <div class="welcome-one__heart-icon">
                                    <img src="assets/halpes/images/resources/welcome-one-heart-icon.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <div class="welcome-one__right">
                            <div class="section-title text-left">
                                <span class="section-title__tagline">Welcome to Charity</span>
                                <h2 class="section-title__title">Helping each other can make world better</h2>
                            </div>
                            <p class="welcome-one__right-text">Lorem ipsum dolor sit amet, consectetur notted
                                adipisicing elit sed do eiusmod tempor incididunt ut labore et simply free text dolore
                                magna aliqua lonm andhn.</p>
                            <div class="welcome-one__our-mission-and-story">
                                <div class="welcome-one__mission-and-story-single">
                                    <h3><i class="fas fa-arrow-circle-right"></i>Our Mission</h3>
                                    <p class="welcome-one__our-mission-and-story-text">Lorem ipsum dolor sit amet not is
                                        consectetur notted.</p>
                                </div>
                                <div class="welcome-one__mission-and-story-single">
                                    <h3><i class="fas fa-arrow-circle-right"></i>Our Story</h3>
                                    <p class="welcome-one__our-mission-and-story-text">Lorem ipsum dolor sit amet not is
                                        consectetur notted.</p>
                                </div>
                            </div>
                            <div class="welcome-one__progress">
                                <div class="welcome-one__progress-single">
                                    <h4 class="welcome-one__progress-title">Charity</h4>
                                    <div class="bar">
                                        <div class="bar-inner count-bar" data-percent="83%">
                                            <div class="count-text">83%</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="welcome-one__progress-single">
                                    <h4 class="welcome-one__progress-title">Donations</h4>
                                    <div class="bar">
                                        <div class="bar-inner count-bar" data-percent="38%">
                                            <div class="count-text">38%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="#" class="welcome-one__btn thm-btn"><i class="fas fa-arrow-circle-right"></i>Learn
                                More</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Welcome One End-->

        <!--Causes One Start-->
        <section class="causes-one">
            <div class="container">
                <h1>WELCOME TO THE NEW LAYOUT (HALPES)</h1>
                <div class="section-title text-center">
                    <span class="section-title__tagline">Latest Causes</span>
                    <h2 class="section-title__title">Find the popular cause <br> and donate them</h2>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="causes-one__carousel owl-theme owl-carousel">
                            <div class="causes-one__single wow fadeInLeft" data-wow-duration="1500ms">
                                <div class="causes-one__img">
                                    <div class="causes-one__img-box">
                                        <img src="assets/halpes/images/resources/causes-one-img-1.jpg" alt="">
                                        <a href="#">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                    <div class="causes-one__category">
                                        <span>Medical</span>
                                    </div>
                                </div>
                                <div class="causes-one__content">
                                    <h3 class="causes-one__title">
                                        <a href="causes-details.html">Raise Fund for Clean & Healthy Water</a>
                                    </h3>
                                    <p class="causes-one__text">There are not many of passages of lorem ipsum avail isn
                                        alteration donationa in form simply free.</p>
                                </div>
                                <div class="causes-one__progress">
                                    <div class="bar">
                                        <div class="bar-inner count-bar" data-percent="36%">
                                            <div class="count-text">36%</div>
                                        </div>
                                    </div>
                                    <div class="causes-one__goals">
                                        <p><span>$25,270</span> Raised</p>
                                        <p><span>$30,000</span> Goal</p>
                                    </div>
                                </div>
                            </div>
                            <div class="causes-one__single wow fadeInUp" data-wow-duration="1500ms"
                                data-wow-delay="100ms">
                                <div class="causes-one__img">
                                    <div class="causes-one__img-box">
                                        <img src="assets/halpes/images/resources/causes-one-img-2.jpg" alt="">
                                        <a href="#">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                    <div class="causes-one__category">
                                        <span>Medical</span>
                                    </div>
                                </div>
                                <div class="causes-one__content">
                                    <h3 class="causes-one__title">
                                        <a href="causes-details.html">School Counseling for Children</a>
                                    </h3>
                                    <p class="causes-one__text">There are not many of passages of lorem ipsum avail isn
                                        alteration donationa in form simply free.</p>
                                </div>
                                <div class="causes-one__progress">
                                    <div class="bar">
                                        <div class="bar-inner count-bar" data-percent="36%">
                                            <div class="count-text">36%</div>
                                        </div>
                                    </div>
                                    <div class="causes-one__goals">
                                        <p><span>$25,270</span> Raised</p>
                                        <p><span>$30,000</span> Goal</p>
                                    </div>
                                </div>
                            </div>
                            <div class="causes-one__single wow fadeInRight" data-wow-duration="1500ms"
                                data-wow-delay="200ms">
                                <div class="causes-one__img">
                                    <div class="causes-one__img-box">
                                        <img src="assets/halpes/images/resources/causes-one-img-3.jpg" alt="">
                                        <a href="#">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                    <div class="causes-one__category">
                                        <span>Medical</span>
                                    </div>
                                </div>
                                <div class="causes-one__content">
                                    <h3 class="causes-one__title">
                                        <a href="causes-details.html">Fundrising for Early Childhood Rise</a>
                                    </h3>
                                    <p class="causes-one__text">There are not many of passages of lorem ipsum avail isn
                                        alteration donationa in form simply free.</p>
                                </div>
                                <div class="causes-one__progress">
                                    <div class="bar">
                                        <div class="bar-inner count-bar" data-percent="36%">
                                            <div class="count-text">36%</div>
                                        </div>
                                    </div>
                                    <div class="causes-one__goals">
                                        <p><span>$25,270</span> Raised</p>
                                        <p><span>$30,000</span> Goal</p>
                                    </div>
                                </div>
                            </div>
                            <div class="causes-one__single wow fadeInLeft" data-wow-duration="1500ms">
                                <div class="causes-one__img">
                                    <div class="causes-one__img-box">
                                        <img src="assets/halpes/images/resources/causes-one-img-1.jpg" alt="">
                                        <a href="#">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                    <div class="causes-one__category">
                                        <span>Medical</span>
                                    </div>
                                </div>
                                <div class="causes-one__content">
                                    <h3 class="causes-one__title">
                                        <a href="causes-details.html">Promoting The Rights of Children</a>
                                    </h3>
                                    <p class="causes-one__text">There are not many of passages of lorem ipsum avail isn
                                        alteration donationa in form simply free.</p>
                                </div>
                                <div class="causes-one__progress">
                                    <div class="bar">
                                        <div class="bar-inner count-bar" data-percent="36%">
                                            <div class="count-text">36%</div>
                                        </div>
                                    </div>
                                    <div class="causes-one__goals">
                                        <p><span>$25,270</span> Raised</p>
                                        <p><span>$30,000</span> Goal</p>
                                    </div>
                                </div>
                            </div>
                            <div class="causes-one__single wow fadeInUp" data-wow-duration="1500ms"
                                data-wow-delay="100ms">
                                <div class="causes-one__img">
                                    <div class="causes-one__img-box">
                                        <img src="assets/halpes/images/resources/causes-one-img-2.jpg" alt="">
                                        <a href="#">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                    <div class="causes-one__category">
                                        <span>Medical</span>
                                    </div>
                                </div>
                                <div class="causes-one__content">
                                    <h3 class="causes-one__title">
                                        <a href="causes-details.html">Education for Poor Children</a>
                                    </h3>
                                    <p class="causes-one__text">There are not many of passages of lorem ipsum avail isn
                                        alteration donationa in form simply free.</p>
                                </div>
                                <div class="causes-one__progress">
                                    <div class="bar">
                                        <div class="bar-inner count-bar" data-percent="36%">
                                            <div class="count-text">36%</div>
                                        </div>
                                    </div>
                                    <div class="causes-one__goals">
                                        <p><span>$25,270</span> Raised</p>
                                        <p><span>$30,000</span> Goal</p>
                                    </div>
                                </div>
                            </div>
                            <div class="causes-one__single wow fadeInRight" data-wow-duration="1500ms"
                                data-wow-delay="200ms">
                                <div class="causes-one__img">
                                    <div class="causes-one__img-box">
                                        <img src="assets/halpes/images/resources/causes-one-img-3.jpg" alt="">
                                        <a href="#">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                    <div class="causes-one__category">
                                        <span>Medical</span>
                                    </div>
                                </div>
                                <div class="causes-one__content">
                                    <h3 class="causes-one__title">
                                        <a href="causes-details.html">Raise Fund for Clean & Healthy Water</a>
                                    </h3>
                                    <p class="causes-one__text">There are not many of passages of lorem ipsum avail isn
                                        alteration donationa in form simply free.</p>
                                </div>
                                <div class="causes-one__progress">
                                    <div class="bar">
                                        <div class="bar-inner count-bar" data-percent="36%">
                                            <div class="count-text">36%</div>
                                        </div>
                                    </div>
                                    <div class="causes-one__goals">
                                        <p><span>$25,270</span> Raised</p>
                                        <p><span>$30,000</span> Goal</p>
                                    </div>
                                </div>
                            </div>
                            <div class="causes-one__single wow fadeInLeft" data-wow-duration="1500ms">
                                <div class="causes-one__img">
                                    <div class="causes-one__img-box">
                                        <img src="assets/halpes/images/resources/causes-one-img-1.jpg" alt="">
                                        <a href="#">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                    <div class="causes-one__category">
                                        <span>Medical</span>
                                    </div>
                                </div>
                                <div class="causes-one__content">
                                    <h3 class="causes-one__title">
                                        <a href="causes-details.html">Raise Fund for Clean & Healthy Water</a>
                                    </h3>
                                    <p class="causes-one__text">There are not many of passages of lorem ipsum avail isn
                                        alteration donationa in form simply free.</p>
                                </div>
                                <div class="causes-one__progress">
                                    <div class="bar">
                                        <div class="bar-inner count-bar" data-percent="36%">
                                            <div class="count-text">36%</div>
                                        </div>
                                    </div>
                                    <div class="causes-one__goals">
                                        <p><span>$25,270</span> Raised</p>
                                        <p><span>$30,000</span> Goal</p>
                                    </div>
                                </div>
                            </div>
                            <div class="causes-one__single wow fadeInUp" data-wow-duration="1500ms"
                                data-wow-delay="100ms">
                                <div class="causes-one__img">
                                    <div class="causes-one__img-box">
                                        <img src="assets/halpes/images/resources/causes-one-img-2.jpg" alt="">
                                        <a href="#">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                    <div class="causes-one__category">
                                        <span>Medical</span>
                                    </div>
                                </div>
                                <div class="causes-one__content">
                                    <h3 class="causes-one__title">
                                        <a href="causes-details.html">Raise Fund for Clean & Healthy Water</a>
                                    </h3>
                                    <p class="causes-one__text">There are not many of passages of lorem ipsum avail isn
                                        alteration donationa in form simply free.</p>
                                </div>
                                <div class="causes-one__progress">
                                    <div class="bar">
                                        <div class="bar-inner count-bar" data-percent="36%">
                                            <div class="count-text">36%</div>
                                        </div>
                                    </div>
                                    <div class="causes-one__goals">
                                        <p><span>$25,270</span> Raised</p>
                                        <p><span>$30,000</span> Goal</p>
                                    </div>
                                </div>
                            </div>
                            <div class="causes-one__single fadeInRight" data-wow-duration="1500ms"
                                data-wow-delay="200ms">
                                <div class="causes-one__img">
                                    <div class="causes-one__img-box">
                                        <img src="assets/halpes/images/resources/causes-one-img-3.jpg" alt="">
                                        <a href="#">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                    <div class="causes-one__category">
                                        <span>Medical</span>
                                    </div>
                                </div>
                                <div class="causes-one__content">
                                    <h3 class="causes-one__title">
                                        <a href="causes-details.html">Raise Fund for Clean & Healthy Water</a>
                                    </h3>
                                    <p class="causes-one__text">There are not many of passages of lorem ipsum avail isn
                                        alteration donationa in form simply free.</p>
                                </div>
                                <div class="causes-one__progress">
                                    <div class="bar">
                                        <div class="bar-inner count-bar" data-percent="36%">
                                            <div class="count-text">36%</div>
                                        </div>
                                    </div>
                                    <div class="causes-one__goals">
                                        <p><span>$25,270</span> Raised</p>
                                        <p><span>$30,000</span> Goal</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Causes One End-->

        <!--Join One Start-->
        <section class="join-one">
            <div class="join-one-bg jarallax" data-jarallax data-speed="0.2" data-imgPosition="50% 0%"
                style="background-image: url(assets/halpes/images/backgrounds/join-one-bg.jpg)"></div>
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="join-one__inner">
                            <h2 class="join-one__title">Join the community to give <br> education for children</h2>
                            <a href="#" class="join-one__btn thm-btn"><i class="fas fa-arrow-circle-right"></i>Learn
                                More</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Join One End-->

        <!--Three Boxes Start-->
        <section class="three-boxes">
            <div class="container-box">
                <div class="row">
                    <div class="col-xl-4">
                        <!--Three Boxes Single-->
                        <div class="three-boxes__single">
                            <div class="three-boxes__single-bg"
                                style="background-image: url(assets/halpes/images/resources/three-boxes-img-1.jpg)"></div>
                            <div class="three-boxes__content">
                                <div class="three-boxes__icon">
                                    <span class="icon-fast-food"></span>
                                </div>
                                <div class="three-boxes__text-box">
                                    <h2>Healthy Food</h2>
                                    <p class="three-boxes__text">Lorem ipsum is simply free text available amet,
                                        consectetuer adipiscing elit. There are not many passages of ipsum.</p>
                                    <a href="#" class="three-boxes__btn"><i class="fa fa-heart"></i>Donate </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <!--Three Boxes Single-->
                        <div class="three-boxes__single three-boxes__single-item-two">
                            <div class="three-boxes__single-bg"
                                style="background-image: url(assets/halpes/images/resources/three-boxes-img-1.jpg)"></div>
                            <div class="three-boxes__content">
                                <div class="three-boxes__icon">
                                    <span class="icon-water"></span>
                                </div>
                                <div class="three-boxes__text-box">
                                    <h2>Clean Water</h2>
                                    <p class="three-boxes__text">Lorem ipsum is simply free text available amet,
                                        consectetuer adipiscing elit. There are not many passages of ipsum.</p>
                                    <a href="#" class="three-boxes__btn"><i class="fa fa-heart"></i>Donate </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <!--Three Boxes Single-->
                        <div class="three-boxes__single three-boxes__single-item-three">
                            <div class="three-boxes__single-bg"
                                style="background-image: url(assets/halpes/images/resources/three-boxes-img-1.jpg)"></div>
                            <div class="three-boxes__content">
                                <div class="three-boxes__icon">
                                    <span class="icon-health-check"></span>
                                </div>
                                <div class="three-boxes__text-box">
                                    <h2>Medical Treatment</h2>
                                    <p class="three-boxes__text">Lorem ipsum is simply free text available amet,
                                        consectetuer adipiscing elit. There are not many passages of ipsum.</p>
                                    <a href="#" class="three-boxes__btn"><i class="fa fa-heart"></i>Donate </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Three Boxes End-->

        <!--Helping One Start-->
        <?php /*
        <section class="helping-one">
            <div class="container">
                <div class="section-title text-center">
                    <span class="section-title__tagline">Latest Causes</span>
                    <h2 class="section-title__title">Find the popular cause <br> and donate them</h2>
                </div>
                <div class="row">
                    <div class="col-xl-6 col-lg-6">
                        <div class="helping-one__left">
                            <h3 class="helping-one__title">We’re Helping Today. Helping Tommorow</h3>
                            <p class="helping-one__text">There are many variations of passages of available but the
                                majority have suffered alteration in some form, by injected humou or randomised words
                                even slightly believable. All the Lorem Ipsum generators on the Internet tend.</p>
                            <ul class="helping-one__left-list list-unstyled">
                                <li>
                                    <div class="helping-one__left-icon">
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </div>
                                    <div class="helping-one__left-text">
                                        <p>Making this the first true generator on the Internet</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="helping-one__left-icon">
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </div>
                                    <div class="helping-one__left-text">
                                        <p>Lorem Ipsum is not simply random text</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="helping-one__left-icon">
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </div>
                                    <div class="helping-one__left-text">
                                        <p>If you are going to use a passage</p>
                                    </div>
                                </li>
                            </ul>
                            <div class="helping-one__left-img">
                                <img src="assets/halpes/images/resources/helping-one-left-img.jpg" alt="">
                                <div class="helping-one__left-icon-box">
                                    <span class="icon-heart"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <div class="helping-one__right">
                            <form action="" class="helping-one__right-form">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="helping-one__right-input-box">
                                            <input type="text" name="amount" placeholder="Enter Donation Amount">
                                            <div class="helping-one__right-dolar-icon">
                                                <span>$</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" name="name" placeholder="Your Name">
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="email" name="email" placeholder="Email Address">
                                    </div>
                                    <div class="col-lg-12">
                                        <input type="text" name="phone" placeholder="Phone Number">
                                    </div>
                                    <div class="col-lg-12">
                                        <textarea name="message" placeholder="Write Message"></textarea>
                                    </div>
                                    <div class="col-lg-12">
                                        <button type="submit" class="thm-btn helping-one__right-btn"><i
                                                class="fas fa-arrow-circle-right"></i>Continue Now</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>*/ ?>
        <!--Helping One End-->

        <!--Gallery One Start-->
        <section class="gallery-one">
            <div class="gallery-one__container-box clearfix">
                <div class="gallery-one__carousel owl-theme owl-carousel">
                    <!--Gallery One Single-->
                    <div class="gallery-one__single">
                        <div class="gallery-one__img-box">
                            <img src="assets/halpes/images/gallery/gallery-1-1.jpg" alt="">
                            <div class="gallery-one__hover-content-box">
                                <h2>Child Education</h2>
                                <p>Charity</p>
                            </div>
                        </div>
                    </div>
                    <!--Gallery One Single-->
                    <div class="gallery-one__single">
                        <div class="gallery-one__img-box">
                            <img src="assets/halpes/images/gallery/gallery-1-2.jpg" alt="">
                            <div class="gallery-one__hover-content-box">
                                <h2>Child Education</h2>
                                <p>Charity</p>
                            </div>
                        </div>
                    </div>
                    <!--Gallery One Single-->
                    <div class="gallery-one__single">
                        <div class="gallery-one__img-box">
                            <img src="assets/halpes/images/gallery/gallery-1-3.jpg" alt="">
                            <div class="gallery-one__hover-content-box">
                                <h2>Child Education</h2>
                                <p>Charity</p>
                            </div>
                        </div>
                    </div>
                    <!--Gallery One Single-->
                    <div class="gallery-one__single">
                        <div class="gallery-one__img-box">
                            <img src="assets/halpes/images/gallery/gallery-1-4.jpg" alt="">
                            <div class="gallery-one__hover-content-box">
                                <h2>Child Education</h2>
                                <p>Charity</p>
                            </div>
                        </div>
                    </div>
                    <!--Gallery One Single-->
                    <div class="gallery-one__single">
                        <div class="gallery-one__img-box">
                            <img src="assets/halpes/images/gallery/gallery-1-5.jpg" alt="">
                            <div class="gallery-one__hover-content-box">
                                <h2>Child Education</h2>
                                <p>Charity</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Gallery One End-->

        <!--Testimonial One Start-->
        <section class="testimonial-one">
            <div class="testimonial-one-bg"
                style="background-image: url(assets/halpes/images/backgrounds/testimonial-1-bg.jpg)"></div>
            <div class="container">
                <div class="row">
                    <div class="col-xl-4">
                        <div class="testimonial-one__left">
                            <div class="section-title text-left">
                                <span class="section-title__tagline">Our Testimonials</span>
                                <h2 class="section-title__title">What they’re talking about charity</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="testimonial-one__right">
                            <div class="testimonial-one__carousel owl-theme owl-carousel">
                                <!--Testimonial One Single-->
                                <div class="testimonial-one__single">
                                    <p class="testimonial-one__text">Lorem ipsum is simply free text dolor sit amet,
                                        consectetur notted adipisicing elit sed do eiusmod tempor incididunt ut labore
                                        et dolore magna aliqua.</p>
                                    <div class="testimonial-one__client-info">
                                        <div class="testimonial-one__client-img">
                                            <img src="assets/halpes/images/testimonial/testimonial-1-img-1.png" alt="">
                                            <div class="testimonial-one__quote">

                                            </div>
                                        </div>
                                        <div class="testimonial-one__client-name">
                                            <h3>Kevin Martin</h3>
                                            <p>Volunteer</p>
                                        </div>
                                    </div>
                                </div>
                                <!--Testimonial One Single-->
                                <div class="testimonial-one__single">
                                    <p class="testimonial-one__text">Lorem ipsum is simply free text dolor sit amet,
                                        consectetur notted adipisicing elit sed do eiusmod tempor incididunt ut labore
                                        et dolore magna aliqua.</p>
                                    <div class="testimonial-one__client-info">
                                        <div class="testimonial-one__client-img">
                                            <img src="assets/halpes/images/testimonial/testimonial-1-img-2.png" alt="">
                                            <div class="testimonial-one__quote">

                                            </div>
                                        </div>
                                        <div class="testimonial-one__client-name">
                                            <h3>Jessica Brown</h3>
                                            <p>Volunteer</p>
                                        </div>
                                    </div>
                                </div>
                                <!--Testimonial One Single-->
                                <div class="testimonial-one__single">
                                    <p class="testimonial-one__text">Lorem ipsum is simply free text dolor sit amet,
                                        consectetur notted adipisicing elit sed do eiusmod tempor incididunt ut labore
                                        et dolore magna aliqua.</p>
                                    <div class="testimonial-one__client-info">
                                        <div class="testimonial-one__client-img">
                                            <img src="assets/halpes/images/testimonial/testimonial-1-img-1.png" alt="">
                                            <div class="testimonial-one__quote">

                                            </div>
                                        </div>
                                        <div class="testimonial-one__client-name">
                                            <h3>Jessica Brown</h3>
                                            <p>Volunteer</p>
                                        </div>
                                    </div>
                                </div>
                                <!--Testimonial One Single-->
                                <div class="testimonial-one__single">
                                    <p class="testimonial-one__text">Lorem ipsum is simply free text dolor sit amet,
                                        consectetur notted adipisicing elit sed do eiusmod tempor incididunt ut labore
                                        et dolore magna aliqua.</p>
                                    <div class="testimonial-one__client-info">
                                        <div class="testimonial-one__client-img">
                                            <img src="assets/halpes/images/testimonial/testimonial-1-img-2.png" alt="">
                                            <div class="testimonial-one__quote">

                                            </div>
                                        </div>
                                        <div class="testimonial-one__client-name">
                                            <h3>Kevin Martin</h3>
                                            <p>Volunteer</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Testimonial One End-->

        <!--Help Them Start-->
        <section class="help-them">
            <div class="help-them-bg jarallax" data-jarallax data-speed="0.2" data-imgPosition="50% 0%"
                style="background-image: url(assets/halpes/images/backgrounds/help-them-bg.jpg)"></div>
            <div class="container">
                <div class="help-them__top">
                    <div class="row">
                        <div class="col-xl-8 col-lg-8">
                            <div class="help-them__top-content">
                                <h2 class="help-them__top-content-title">Help them whenever they are in need</h2>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4">
                            <div class="help-them__top-video-box">
                                <a href="https://www.youtube.com/watch?v=i9E_Blai8vk"
                                    class="help-them__top-video-btn video-popup"><i class="fa fa-play"></i></a>
                                <p class="help-them__top-video-text">Watch the Video</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="help-them__bottom">
                    <div class="row">
                        <div class="col-xl-4 col-lg-4">
                            <!--Help Them Single-->
                            <div class="help-them__single">
                                <div class="help-them__icon">
                                    <span class="icon-charity"></span>
                                </div>
                                <div class="help-them__text">
                                    <h3>Become a Volunteer</h3>
                                    <p>There are many variations of but the majority have simply free text suffered.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4">
                            <!--Help Them Single-->
                            <div class="help-them__single">
                                <div class="help-them__icon">
                                    <span class="icon-generous"></span>
                                </div>
                                <div class="help-them__text">
                                    <h3>Quick Fundraising</h3>
                                    <p>There are many variations of but the majority have simply free text suffered.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4">
                            <!--Help Them Single-->
                            <div class="help-them__single">
                                <div class="help-them__icon">
                                    <span class="icon-fundraiser"></span>
                                </div>
                                <div class="help-them__text">
                                    <h3>Start Donating</h3>
                                    <p>There are many variations of but the majority have simply free text suffered.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Help Them End-->

        <section class="news-one">
            <div class="container">
                <div class="row">
                    <div class="col-xl-8 col-lg-8">
                        <div class="section-title text-left">
                            <span class="section-title__tagline">Get Daily Updates</span>
                            <h2 class="section-title__title">Latest news & articles directly <br> coming from the blog
                            </h2>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4">
                        <div class="news-one__button-box">
                            <a href="#" class="news-one__btn thm-btn"><i class="fas fa-arrow-circle-right"></i>View
                                More</a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6 col-lg-6">
                        <div class="news-one__left">
                            <div class="news-one__img">
                                <img src="assets/halpes/images/blog/news-one-img-1.jpg" alt="">
                                <a href="news-details.html">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </div>
                            <div class="news-one__bottom">
                                <ul class="list-unstyled news-one__meta">
                                    <li>20 Jan, 2021</li>
                                    <li><span>/</span></li>
                                    <li><a href="#">2 Comments</a></li>
                                </ul>
                                <h3 class="news-one__title">
                                    <a href="news-details.html">Donation is Hope for Poor Childrens in Africa</a>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <div class="news-one__right">
                            <div class="news-one__right-single">
                                <div class="news-one__right-img">
                                    <img src="assets/halpes/images/blog/news-one-right-img-1.jpg" alt="">
                                    <a href="news-details.html">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                                <div class="news-one__right-content">
                                    <ul class="list-unstyled news-one__right-meta">
                                        <li>20 Jan, 2021</li>
                                        <li><span>/</span></li>
                                        <li><a href="#">2 Comments</a></li>
                                    </ul>
                                    <h3 class="news-one__right-title">
                                        <a href="news-details.html">How Does Malnutrition Affect Children?</a>
                                    </h3>
                                </div>
                            </div>
                            <div class="news-one__right-single">
                                <div class="news-one__right-img">
                                    <img src="assets/halpes/images/blog/news-one-right-img-2.jpg" alt="">
                                    <a href="news-details.html">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                                <div class="news-one__right-content">
                                    <ul class="list-unstyled news-one__right-meta">
                                        <li>20 Jan, 2021</li>
                                        <li><span>/</span></li>
                                        <li><a href="#">2 Comments</a></li>
                                    </ul>
                                    <h3 class="news-one__right-title">
                                        <a href="news-details.html">Capitalize on low hanging fruit to identify</a>
                                    </h3>
                                </div>
                            </div>
                            <div class="news-one__right-single">
                                <div class="news-one__right-img">
                                    <img src="assets/halpes/images/blog/news-one-right-img-3.jpg" alt="">
                                    <a href="news-details.html">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                                <div class="news-one__right-content">
                                    <ul class="list-unstyled news-one__right-meta">
                                        <li>20 Jan, 2021</li>
                                        <li><span>/</span></li>
                                        <li><a href="#">2 Comments</a></li>
                                    </ul>
                                    <h3 class="news-one__right-title">
                                        <a href="news-details.html">Override the digital divide with additional</a>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--Brand One Start-->
        <section class="brand-one">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="brand-one__carousel owl-theme owl-carousel">
                            <!--Brand One Single-->
                            <div class="brand-one__single">
                                <div class="brand-one__img">
                                    <img src="assets/halpes/images/resources/brand-1-1.png" alt="">
                                </div>
                            </div>
                            <!--Brand One Single-->
                            <div class="brand-one__single">
                                <div class="brand-one__img">
                                    <img src="assets/halpes/images/resources/brand-1-2.png" alt="">
                                </div>
                            </div>
                            <!--Brand One Single-->
                            <div class="brand-one__single">
                                <div class="brand-one__img">
                                    <img src="assets/halpes/images/resources/brand-1-3.png" alt="">
                                </div>
                            </div>
                            <!--Brand One Single-->
                            <div class="brand-one__single">
                                <div class="brand-one__img">
                                    <img src="assets/halpes/images/resources/brand-1-4.png" alt="">
                                </div>
                            </div>
                            <!--Brand One Single-->
                            <div class="brand-one__single">
                                <div class="brand-one__img">
                                    <img src="assets/halpes/images/resources/brand-1-5.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Brand One End-->
     <?php
     }
}

?>