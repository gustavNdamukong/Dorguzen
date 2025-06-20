<?php

namespace layouts\halpes;

class halpesLayout extends \DGZ_library\DGZ_Layout {

	public function display() {

	?>

		<!DOCTYPE HTML>
		<html class="no-js" lang="en-gb" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
		<head>
			<!-- ==========================
                    Meta Tags
                =========================== -->
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<?=$this->getGlobalSeoData() ?? ''?>
			<?=($this->getMetadata() != null) ? $this->getMetadata() : "<title>".self::$appName."-".$this->pageTitle."</title>" ?>

			<!-- favicons Icons -->
			<link rel="apple-touch-icon" sizes="180x180" href="assets/halpes/images/favicons/apple-touch-icon.png" />
			<link rel="icon" type="image/png" sizes="32x32" href="assets/halpes/images/favicons/favicon-32x32.png" />
			<link rel="icon" type="image/png" sizes="16x16" href="assets/halpes/images/favicons/favicon-16x16.png" />
			<link rel="manifest" href="assets/halpes/images/favicons/site.webmanifest" />


			<!-- Google Web Fonts -->
			<!-- fonts -->
			<link rel="preconnect" href="https://fonts.gstatic.com">
			<link
					href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap"
					rel="stylesheet">


			<!--<link rel="preconnect" href="https://fonts.googleapis.com">
			<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
			<link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">-->

			<!-- Icon Font Stylesheet -->
			<!--<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
			<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">-->

			<!-- Include custom styles meant for individual views. These can override those set above -->
			 <?= $this->getCssHtml()  ?>


			<?php include('html_dependencies_top.inc.php'); ?>
		</head>
		<body>

		<!-- ==========================
            SCROLL TOP - START
        =========================== -->
		<!--<div id="scrolltop" class="hidden-xs"><i class="fa fa-angle-up"></i></div>///// REMOVED THIS /////-->
		<!-- ==========================
            SCROLL TOP - END
        =========================== -->
		<div class="preloader">
        	<img class="preloader__image" width="60" src="assets/halpes/images/loader.png" alt="" />
    	</div>
		<!-- /.preloader -->
		<div class="page-wrapper">

		<!--<div id="page-wrapper" class="bg-white">///// REMOVED THIS /////--> <!-- PAGE - START -->

		<!-- ==========================
			HEADER - START
		=========================== -->
		<?php include('header.inc.php'); ?>
		<!-- ==========================
			HEADER - END
		=========================== -->
		

			  <!-- ==================================
			  			SLIDER
			  =================================== -->
			<div class="stricky-header stricked-menu main-menu">
            	<div class="sticky-header__content"></div><!-- /.sticky-header__content -->
        	</div><!-- /.stricky-header -->
			<section class="main-slider">
				<div class="swiper-container thm-swiper__slider" data-swiper-options='{"slidesPerView": 1, "loop": true,
					"effect": "fade",
					"pagination": {
						"el": "#main-slider-pagination",
						"type": "bullets",
						"clickable": true
					},
					"navigation": {
						"nextEl": "#main-slider__swiper-button-next",
						"prevEl": "#main-slider__swiper-button-prev"
					},
					"autoplay": {
						"delay": 5000
					}}'>
					<div class="swiper-wrapper">
						<div class="swiper-slide">
							<div class="image-layer"
								style="background-image: url(assets/halpes/images/backgrounds/main-slider-1-1.jpg);">
							</div>
							<div class="image-layer-overlay"></div>
							<!-- /.image-layer -->
							<div class="container">
								<div class="row">
									<div class="col-lg-8">
										<div class="main-slider__content">
											<p>Helping Them Today</p>
											<h2>Help the Poor <br> in Need</h2>
											<a href="#" class="thm-btn"><i class="fas fa-arrow-circle-right"></i>Learn
												More</a>
											<div class="main-slider__shape-1 zoom-fade">
												<img src="assets/halpes/images/shapes/main-slider-1-shape-1.png" alt="">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="swiper-slide">
							<div class="image-layer"
								style="background-image: url(assets/halpes/images/backgrounds/main-slider-2-1.jpg);">
							</div>
							<div class="image-layer-overlay"></div>
							<!-- /.image-layer -->
							<div class="container">
								<div class="row">
									<div class="col-lg-8">
										<div class="main-slider__content">
											<p>Helping Them Today</p>
											<h2>Help the Poor <br> in Need</h2>
											<a href="#" class="thm-btn"><i class="fas fa-arrow-circle-right"></i>Learn
												More</a>
											<div class="main-slider__shape-1 zoom-fade">
												<img src="assets/halpes/images/shapes/main-slider-1-shape-1.png" alt="">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="swiper-slide">
							<div class="image-layer"
								style="background-image: url(assets/halpes/images/backgrounds/main-slider-3-1.jpg);">
							</div>
							<div class="image-layer-overlay"></div>
							<!-- /.image-layer -->
							<div class="container">
								<div class="row">
									<div class="col-lg-8">
										<div class="main-slider__content">
											<p>Helping Them Today</p>
											<h2>Help the Poor <br> in Need</h2>
											<a href="#" class="thm-btn"><i class="fas fa-arrow-circle-right"></i>Learn
												More</a>
											<div class="main-slider__shape-1 zoom-fade">
												<img src="assets/halpes/images/shapes/main-slider-1-shape-1.png" alt="">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="main-slider__counter">
						<ul class="main-slider__counter-box list-unstyled">
							<li>
								<h3 class="main-sldier__counter-digit">4390</h3>
								<span class="main-slider__counter-text">Volunteers are Available</span>
							</li>
							<li>
								<h3 class="main-sldier__counter-digit">8900</h3>
								<span class="main-slider__counter-text">Funds Raised by People</span>
							</li>
						</ul>
					</div>
					<!-- If we need navigation buttons -->
					<div class="swiper-pagination" id="main-slider-pagination"></div>
					<div class="main-slider__nav">
						<div class="swiper-button-prev" id="main-slider__swiper-button-next"><i
								class="icon-right-arrow icon-left-arrow"></i>
						</div>
						<div class="swiper-button-next" id="main-slider__swiper-button-prev"><i
								class="icon-right-arrow"></i>
						</div>
					</div>
				</div>
			</section>
			  <!-- ==================================
			  			END SLIDER
			  =================================== -->


			  

			  <?php
			  if(!empty($this->exceptions)):
				  ?>
				  <div class="alert exceptions text-center" role="alert" style="margin-top: 10%;">
					  <?= $this->exceptions ?>
				  </div>
				  <?php
			  endif;
			  if(!empty($this->warnings)):
				  ?>
				  <div class="alert warnings text-center" role="alert" style="margin-top: 10%;">
					  <?= $this->warnings ?>
				  </div>
				  <?php
			  endif;
			  if(!empty($this->errors)):
				  ?>
				  <div class="alert danger text-center" role="alert" style="margin-top: 10%;">
					  <?= $this->errors ?>
				  </div>
				  <?php
			  endif;
			  if(!empty($this->notices)):
				  ?>
				  <div class="alert notices text-center" role="alert" style="margin-top: 10%;">
					  <?= $this->notices ?>
				  </div>
				  <?php
			  endif;
			  if(!empty($this->successes)):
				  ?>
				  <div class="alert success text-center" role="alert" style="margin-top: 10%;">
					  <?= $this->successes ?>
				  </div>
				  <?php
			  endif;
			  ?>


			<!-- ==========================
			  PAGE CONTENT - START
		    =========================== -->
			<?php echo $this->content;  ?>
			<!-- ==========================
			  PAGE CONTENT - END
			=========================== -->


			<section>
				<div class="well">
					<div id="consent-popup" class="consent-hidden">
						<p>Be aware that we use cookies to improve your experience, and nothing more <a href="#">Link to your Terms & Conditions or Data Policy here</a>.
							<a href="#" id="accept-cookie-use" class="btn btn-primary rounded-pill animated slidInRight">Okay</a>
						</p>
					</div>
					<!-- ==========================
						  FOOTER - START
					  =========================== -->
					<?php include('footer.inc.php'); ?>
					<!-- ==========================
						FOOTER - END
					=========================== -->
				</div>
			</section>

		</div> <!-- Page wrapper div - END --> <!-- /.page-wrapper -->



		<!-- =======================================
			MOBILE HAMBURGER (NAV) STUFF & SCROLLUP
		======================================== -->
		<div class="mobile-nav__wrapper">
			<div class="mobile-nav__overlay mobile-nav__toggler"></div>
			<!-- /.mobile-nav__overlay -->
			<div class="mobile-nav__content">
				<span class="mobile-nav__close mobile-nav__toggler"><i class="fa fa-times"></i></span>

				<div class="logo-box">
					<a href="index.html" aria-label="logo image"><img src="assets/halpes/images/resources/logo-1.png" width="155"
							alt="" /></a>
				</div>
				<!-- /.logo-box -->
				<div class="mobile-nav__container">
					<ul class="main-menu__list">
						<li class="dropdown current">
							<a href="#">
								Home
							</a>
							<!--<button aria-label="dropdown toggler">
								<i class="fa fa-angle-down"></i>
							</button>-->
						</li>
						<li class="dropdown">
							<a href="#">
								Pages
							</a>
						</li>
						<li class="dropdown">
							<a href="#">
								Donations
							</a>
						</li>
						<li class="dropdown">
							<a href="#">
								Events
							</a>
						</li>
						<li class="dropdown">
							<a href="#">
								News
							</a>
						</li>
						<li class="dropdown">
							<a href="#">
								Donations
							</a>
						</li>
					</ul>
				</div>
				<!-- /.mobile-nav__container -->

				<ul class="mobile-nav__contact list-unstyled">
					<li>
						<i class="fa fa-envelope"></i>
						<a href="mailto:needhelp@packageName__.com">needhelp@halpes.com</a>
					</li>
					<li>
						<i class="fa fa-phone-alt"></i>
						<a href="tel:666-888-0000">666 888 0000</a>
					</li>
				</ul><!-- /.mobile-nav__contact -->
				<div class="mobile-nav__top">
					<div class="mobile-nav__social">
						<a href="#" class="fab fa-twitter"></a>
						<a href="#" class="fab fa-facebook-square"></a>
						<a href="#" class="fab fa-pinterest-p"></a>
						<a href="#" class="fab fa-instagram"></a>
					</div><!-- /.mobile-nav__social -->
				</div><!-- /.mobile-nav__top -->



			</div>
			<!-- /.mobile-nav__content -->
		</div>
		<!-- /.mobile-nav__wrapper -->

		<div class="search-popup">
			<div class="search-popup__overlay search-toggler"></div>
			<!-- /.search-popup__overlay -->
			<div class="search-popup__content">
				<form action="#">
					<label for="search" class="sr-only">search here</label><!-- /.sr-only -->
					<input type="text" id="search" placeholder="Search Here..." />
					<button type="submit" aria-label="search submit" class="thm-btn">
						<i class="icon-magnifying-glass"></i>
					</button>
				</form>
			</div>
			<!-- /.search-popup__content -->
		</div>
		<!-- /.search-popup -->

		<a href="#" data-target="html" class="scroll-to-target scroll-to-top"><i class="fa fa-angle-up"></i></a>
		<!-- ======================================== 
			END MOBILE HAMBURGER (NAV) STUFF & SCROLLUP
		======================================== -->



		<?php include('html_dependencies_bottom.inc.php'); ?>

		<!-- Include custom scripts meant for individual views -->
		<?= $this->getJavascriptHtml() ?>

		</body>
		</html>
		<?php
	}
	
}
