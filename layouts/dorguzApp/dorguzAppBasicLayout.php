<?php

namespace layouts\dorguzApp;

/**
 * Description of BootstrapLayout
 *
 * @author Gustav
 */
class dorguzAppBasicLayout extends \DGZ_library\DGZ_Layout {
	

	
	public function display() {

	?>

		<!DOCTYPE HTML>
		<html class="no-js" lang="en-gb">
		<head>
			<!-- ==========================
                    Meta Tags
                =========================== -->
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<meta name="description" content="">
			<meta name="author" content="">
			<!--<meta name="viewport" content="width=device-width">-->
			<title><?php echo self::$appName."-".$this->pageTitle; ?></title>


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





		  <?php // THIS JUMBOTRON HOLDS THE SITE'S AWESOME SLIDER

		  if ($this->showImageSlider) { ?>

		  <!-- ==========================
			  JUMBOTRON - START
		  =========================== -->
		  <section class="content jumbotron jumbotron-full-height">
			  <div id="homepage-2-carousel" class="nav-inside">

				  <div class="item slide-1">
					  <div class="slide-mask"></div>
					  <div class="slide-body">
						  <div class="container">
							  <h1>Welcome to the <span class="color">Dorguzen Framework</span></h1>
							  <h2>Your Rapid Web Development Toolkit</h2>
						  </div>
					  </div>
				  </div>

				  <div class="item slide-2">
					  <div class="slide-mask"></div>
					  <div class="slide-body">
						  <div class="container">
							  <h1 class="grey-background">Fold your sleeves</h1>
							  <ul class="list-unstyled">
								  <li><i class="fa fa-check"></i>and Build something nice</li>
								  <li><i class="fa fa-check"></i>PHP</li>
								  <li><i class="fa fa-check"></i>MVC</li>
							  </ul>
						  </div>
					  </div>
				  </div>

			  </div>
		  </section>
		  <!-- ==========================
			  JUMBOTRON - END
		  =========================== -->
 				<?php } ?>

				  <?php

				  if(!empty($this->exceptions)):
					  ?>
					  <div class="exceptions">
						  <?= $this->exceptions ?>
					  </div>
					  <?php
				  endif;
				  if(!empty($this->warnings)):
					  ?>
					  <div class="warnings">
						  <?= $this->warnings ?>
					  </div>
					  <?php
				  endif;
				  if(!empty($this->errors)):
					  ?>
					  <div class="danger">
						  <?= $this->errors ?>
					  </div>
					  <?php
				  endif;
				  if(!empty($this->notices)):
					  ?>
					  <div class="notices">
						  <?= $this->notices ?>
					  </div>
					  <?php
				  endif;
				  if(!empty($this->successes)):
					  ?>
					  <div class="successes">
						  <?= $this->successes ?>
					  </div>
					  <?php
				  endif;
				  ?>





				  <?php ##################### START OF YOUR SITE MAIN CONTENT ##################################### ?>
				  <?php /*<h1>WELCOME TO THE DEFAULT ALL IN ONE THEME</h1> */?>
				  <?php echo $this->content;  ?>
				  <?php /*  ############################## END OF YOUR SITE MAIN CONTENT ######################################## */ ?>
			<!--</div><!--END OF THE WELL DIV INSIDE THE first_footer SECTION-->
		<!--</section><!--End of first footer section-->




		</div> <!-- PAGE - END -->


			<?php include('html_dependencies_bottom.inc.php'); ?>

		<?= $this->getJavascriptHtml() ?>

		</body>
		</html>

		<?php

	}

	
}
