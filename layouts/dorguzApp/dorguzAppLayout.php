<?php

namespace layouts\dorguzApp;


class dorguzAppLayout extends \DGZ_library\DGZ_Layout {
	

	
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
			<?=$this->getMetadata()?>
			<title><?php echo self::$appName."-".$this->pageTitle; ?></title>

			<!-- Include custom styles meant for individual views. These can override those set above -->
			 <?= $this->getCssHtml()  ?>



			<!-- ==========================
                - START
            =========================== -->
			<?php include('html_dependencies_top.inc.php'); ?>
			<!-- ==========================
                 - END
            =========================== -->

		</head>
		<body>

		<div style="margin-top: 8%;"></div>


		<!-- ==========================
            SCROLL TOP - START
        =========================== -->
		<div id="scrolltop" class="hidden-xs"><i class="fa fa-angle-up"></i></div>
		<!-- ==========================
            SCROLL TOP - END
        =========================== -->



			  <div id="page-wrapper"> <!-- PAGE - START -->
				  

				  <!-- ==========================
                      HEADER - START
                  =========================== -->
				  <?php include('header.inc.php'); ?>
				  <!-- ==========================
                      HEADER - END
                  =========================== -->

				  <?php ##################### END OF HEADER ##################################### ?>


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



				  <?php if ($this->showImageSlider)
				  {
					  if ($this->settings->getSettings()['sliderType'] == 'slider')
					  { ?>
						  	<!--
               This jumbotron holds the site's carousel slider. We currently have 5 divs having the classes 'slide-1', 'slide-2' 'slide-3' etc but u can add
               more/remove the divs for as many slides as u want). Note that you can get images to display in the browser as a background to the slides by using CSS 
               ('background-image' property). A stylesheet is already prepared for you for this purpose, and it is called 'slider_carousel.css' located at
               assets/css/slider_carousel.css. To activate it, just do two things;
               	i) go into the carousel_slider.css file and replace all instances of the text 'YOURIMAGE' with the images you want displayed as background for each slide.
               	ii) Make sure you create the directory 'assets/images/carousel_slider_images' (or go into it) and put the images you referred to in slider_carousel.css
               		in there.
               	Now you will see your images appearing nicely as backgrounds to the slides. For best results, make sure your image sizes match those specified in the stylesheet.
              				-->
							<!-- ==========================
							SLIDER JUMBOTRON - START
							=========================== -->
						  <section class="content jumbotron jumbotron-full-height" id="slider-jumbo">
							  <div id="homepage-2-carousel" class="nav-inside">

								  <div class="item slide-1">
									  <div class="slide-mask"></div>
									  <div class="slide-body">
										  <div class="container">
											  <h1>Welcome to <span class="color">your Dorguzen Framework App</span></h1>
											  <h2>Your Rapid Web Development Toolkit</h2>
											  <a href="<?=$this->settings->getFileRootPath()?>feedback/contact" class="btn btn-default btn-lg">Contact Us</a>
											  <a data-toggle="modal" class="btn btn-inverse btn-lg" data-target="#testimonialModal">Modal popup link here</a>
										  </div>
									  </div>
								  </div>


								  <div class="item slide-2">
									  <div class="slide-mask"></div>
									  <div class="slide-body">
										  <div class="container">
											  <h1 class="grey-background">Fold your sleeves</h1>
											  <ul class="list-unstyled">
                                                  <li><i class="fa fa-check"></i>Performance driven</li>
                                                  <li><i class="fa fa-check"></i>Control</li>
                                                  <li><i class="fa fa-check"></i>Ownership</li>
                                              </ul>
										  </div>
									  </div>
								  </div>

								  <div class="item slide-3">
									  <div class="slide-mask"></div>
									  <div class="slide-body">
										  <div class="container">
											  <h1 class="grey-background">...And Build Something</h1>
											  <ul class="list-unstyled">
                                                  <li><i class="fa fa-check"></i>Professional</li>
                                              </ul>
										  </div>
									  </div>
								  </div>

								  <div class="item slide-4">
									  <div class="slide-mask"></div>
									  <div class="slide-body">
										  <div class="container">
											  <h1 class="grey-background">We created something awesome</h1>
											  <ul class="list-unstyled">
											  </ul>
										  </div>
									  </div>
								  </div>

								  <div class="item slide-5">
									  <div class="slide-mask"></div>
									  <div class="slide-body">
										  <div class="container">
											  <h1 class="grey-background">The Power is in your hands</h1>
											  <ul class="list-unstyled">
												  <li>SOME LIST ITEM(S) HERE</li>
                                              </ul>
										  </div>
									  </div>
								  </div>


							  </div>
						  </section>
						  <!-- ==========================
                              SLIDER JUMBOTRON - END
                          =========================== -->

					  <?php
					  }
			          else if ($this->settings->getSettings()['sliderType'] == 'sliderEngine')
					  { ?>
						  <!-- ==========================
                              V8SLIDERENGINE JUMBOTRON - START
                          =========================== -->
						  <?php
						  ############ YOUR PAGE SLIDER GOES HERE - WE PUT IT IN A CONDITIONAL SO U DECIDE WH PAGE U WANNA SHOW A SLIDER ON ##########
						  ?>
						  <div class="jumbotron" style="margin:70px auto;max-width:800px;" class="col-xs-12">
							  <?php
							  //pass ypur images to the DGZ_SliderEngine like this
							  $imageClass = new \DGZ_library\DGZ_SliderEngine([['name' => 'slide1.jpg', 'alt' => 'The perfect job'], ['name' => 'wegotyourback.jpg', 'alt' => 'Professionalism'], ['name' => 'slide3.jpg', 'alt' => 'Reliability'], ['name' => 'slide4.jpg', 'alt' => 'Call us'], ['name' => 'slide5.JPG', 'alt' => 'ISO Certified']]);
							  $imageClass->showSlider();
							  ?>
						  </div>

						  <?php ?>
						  <!-- ==========================
                              V8SLIDERENGINE JUMBOTRON - END
                          =========================== -->
						  <?php
					  }
				  } ?>
				  <!-------------------------------END OF SITE IMAGE SLIDER-------------------------------------------->





				  <?php echo $this->content;  ?>







				  <!-- ==========================
                      BRAND SLIDER - START
                  =========================== -->
				  <section class="content brands pattern border-top border-bottom">
					  <div class="container">
						  <div id="brands-carousel">
							  <!--<div class="item"><a href="#"><img src="assets/images/clients/1.png" class="img-responsive" alt=""></a></div>
							  <div class="item"><a href="#"><img src="assets/images/clients/2.png" class="img-responsive" alt=""></a></div>
							  <div class="item"><a href="#"><img src="assets/images/clients/3.png" class="img-responsive" alt=""></a></div>
							  <div class="item"><a href="#"><img src="assets/images/clients/4.png" class="img-responsive" alt=""></a></div>
							  <div class="item"><a href="#"><img src="assets/images/clients/5.png" class="img-responsive" alt=""></a></div>
							  <div class="item"><a href="#"><img src="assets/images/clients/6.png" class="img-responsive" alt=""></a></div>
							  <div class="item"><a href="#"><img src="assets/images/clients/7.png" class="img-responsive" alt=""></a></div>-->
						  </div>
					  </div>
				  </section>
				  <!-- ==========================
                      BRAND SLIDER - END
                  =========================== -->

				  <?php /*  ############################## END OF YOUR SITE MAIN CONTENT ########################################*/ ?>



		<section>
			<div class="well">
				<?php ################### START OF INCLUDED PART OF FIRST FOOTER ######################?>

				<!-- ==========================
                      FOOTER - START
                  =========================== -->
				<?php include('footer.inc.php');  //include the 1st footer here ################### END OF INCLUDED PART OF FIRST FOOTER ###################### ?>
				<!-- ==========================
                    FOOTER - END
                =========================== -->

			</div><!--END OF THE WELL DIV INSIDE THE first_footer SECTION-->
		</section><!--End of first footer section-->




		</div> <!-- PAGE - END -->

			<?php include('html_dependencies_bottom.inc.php');  //include the 2nd footer here ?>


		<!-- Include custom scripts meant for individual views -->
		<?= $this->getJavascriptHtml() ?>

		</body>
		</html>
		<?php
	}
	
}
