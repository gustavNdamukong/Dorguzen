<?php

namespace layouts\seoMaster;

class seoMasterLayout extends \DGZ_library\DGZ_Layout {

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
			<?=($this->getMetadata() != null) ? $this->getMetadata() : "<title>".self::$appName."-".$this->pageTitle."</title>" ?>

			<!-- Google Web Fonts -->
			<link rel="preconnect" href="https://fonts.googleapis.com">
			<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
			<link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

			<!-- Icon Font Stylesheet -->
			<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
			<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

			<!-- Include custom styles meant for individual views. These can override those set above -->
			 <?= $this->getCssHtml()  ?>


			<?php include('html_dependencies_top.inc.php'); ?>
		</head>
		<body>

		<!-- ==========================
            SCROLL TOP - START
        =========================== -->
		<div id="scrolltop" class="hidden-xs"><i class="fa fa-angle-up"></i></div>
		<!-- ==========================
            SCROLL TOP - END
        =========================== -->

		<div id="page-wrapper" class="bg-white"> <!-- PAGE - START -->

			  <!-- ==========================
				  HEADER - START
			  =========================== -->
			  <?php include('header.inc.php'); ?>
			  <!-- ==========================
				  HEADER - END
			  =========================== -->

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
					<!-- ==========================
						  FOOTER - START
					  =========================== -->
					<?php include('footer.inc.php'); ?>
					<!-- ==========================
						FOOTER - END
					=========================== -->
				</div>
			</section>

		</div> <!-- Page wrapper div - END -->

		<?php include('html_dependencies_bottom.inc.php'); ?>

		<!-- Include custom scripts meant for individual views -->
		<?= $this->getJavascriptHtml() ?>

		</body>
		</html>
		<?php
	}
	
}
