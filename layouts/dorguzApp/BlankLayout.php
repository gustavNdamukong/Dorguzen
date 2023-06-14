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
			<?=$this->getMetadata()?>
			<title><?php echo self::$appName."-".$this->pageTitle; ?></title>

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
						<a href="<?=$this->config->getFileRootPath()?>home" class="navbar-brand"><span>Dor</span>Guzen</a>
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-bars"></i></button>
					</div>
					<div class="navbar-collapse collapse">
						<p class="navbar-text hidden-xs hidden-sm">Your Rapid Web Development Toolkit</p>
					</div>
				</div>
			</header>
			<!-- ==========================
                HEADER - END
            =========================== -->

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


			</div><!--END OF THE WELL DIV INSIDE THE first_footer SECTION-->
		</section><!--End of first footer section-->




		</div> <!-- PAGE - END -->

		<?php include('html_dependencies_bottom.inc.php'); ?>




		<!-- Include scripts required by Bootstrap -->
		<?= $this->getJavascriptHtml() ?>

		</body>
		</html>







		<?php
	
	}
	
}
