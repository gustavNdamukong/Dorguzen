<?php

namespace layouts\dorguzApp;

/**
 * Layout suitable for non-HTML display, where you don't want anything
 * wrapping around the content.
 *
 * @author Gustav
 */
class BlankLayout extends \DGZ_library\DGZ_Layout {
	
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


			<?php /* <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.8.16.custom.css"/>

			<link href='http://fonts.googleapis.com/css?family=Lobster|Terminal+Dosis' rel='stylesheet' type='text/css'> */ ?>

			<!--<link rel="icon" href="../../favicon.ico">--><!--COME BACK AND PUT BACK THIS FAVICON IN TO SET UP FAVICON, CHECK YOUR HTML NOTES ON THIS-->
			<!--<link rel="stylesheet" type="text/css" href="css/bootstrap.css">-->

			<!--<link rel="stylesheet" type="text/css" href="css/normalize.min.css">
			<link rel="stylesheet" href="css/normalize.min.css">
			<link href="css/superfish.css" rel="stylesheet" type="text/css" />-->

			<!-- Custom styles as defined by the application. These can override those set above -->
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

			<header class="navbar navbar-transparent navbar-fixed-top">
				<div class="container">
					<div class="navbar-header">
						<!--<a href="<?=$this->settings->getFileRootPath()?>home/home" class="navbar-brand"><span>Nolimit</span>Media</a>-->
						<a href="<?=$this->settings->getHomePage()?>home/home" class="navbar-brand"><img width="150" height="50" src="<?=$this->settings->getFileRootPath()?>assets/images/logos/final_p3.svg" class="img-responsive center-block" alt="Nolimit Media Logo" /></a>
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-bars"></i></button>
					</div>
					<div class="navbar-collapse collapse">
						<p class="navbar-text hidden-xs hidden-sm">RELIABLE SOFTWARE DEVELOPMENT</p>
					</div>
				</div>
			</header>
			<!-- ==========================
                HEADER - END
            =========================== -->

			<?php ##################### END OF HEADER ##################################### ?>

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






		<?= $this->content ?>









		<section>
			<div class="well">
				<?php ################### START OF INCLUDED PART OF FIRST FOOTER ######################?>

				<!-- ==========================
					  FOOTER - START
				  =========================== -->
				<?php //////include('footer.inc.php');  //include the 1st footer here ################### END OF INCLUDED PART OF FIRST FOOTER ###################### ?>
				<!-- ==========================
					FOOTER - END
				=========================== -->

				<?php /*<div class="clearer" id="firstfooterdivclear"></div> */ ?><!--NOTE THAT THIS IS THE LAST DIV (THING) INSIDE well of THE first_footer-->
			</div><!--END OF THE WELL DIV INSIDE THE first_footer SECTION-->
		</section><!--End of first footer section-->




		</div> <!-- PAGE - END -->



		<?php /*  <article><!--HERE'S THE START OF THE SECOND FOOTER; IT'S ENCLOSED IN THIS PAIR OF <article></article> tags ided
						'footer'-->*/ ?>
		<?php include('html_dependencies_bottom.inc.php');  //include the 2nd footer here ?>
		<?php /* 		<div class="clearer"></div><!--to make this empty div work n keep everything before it above it; that's why it's the last
							thing (div) inside the second-footer part of the website (enclosed in a pair of <article tags>. You also need
							to make sure the footer element has no height rule-->
		</article>


		</section><!--END OF THE MAINWRAPPER N CONTAINER SECTION THAT CONTAINS EVERYTHING--> */ ?>


		<?php /*
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>

		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

		<!--SCRIPTS FOR THE SLIDER-->

		<script src="js/sliderengine/jquery.js"></script>

		<script src="js/sliderengine/amazingslider.js"></script>

		<script src="js/sliderengine/initslider-1.js"></script>
 		*/ ?>

		<!-- Include scripts required by Bootstrap -->
		<?= $this->getJavascriptHtml() ?>

		</body>
		</html>







		<?php
	
	}
	
}
