<?php

namespace Dorguzen\layouts\seoMaster;

class seoMasterLayout extends \Dorguzen\Core\DGZ_Layout {

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
			<?php
			// Always emit a <title>: when a view sets metadata but omits a <title>,
			// still render the fallback title so the page is never titleless.
			$dgzMeta = $this->getMetadata();
			if ($dgzMeta === null || $dgzMeta === '') {
				echo "<title>" . self::$appName . "-" . $this->pageTitle . "</title>";
			} else {
				if (stripos($dgzMeta, '<title') === false) {
					echo "<title>" . self::$appName . "-" . $this->pageTitle . "</title>" . PHP_EOL;
				}
				echo $dgzMeta;
			}
			?>

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

			<?php
			// Inject DB-driven theme colour. Falls back silently if the setting is missing.
			$_dgzTheme = $this->config->getAppColorTheme();
			if (!empty($_dgzTheme)) { ?>
			<style>:root { --site-theme: <?= htmlspecialchars($_dgzTheme, ENT_QUOTES) ?>; }</style>
			<?php } ?>
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


				<!-- ==========================
					BRAND SLIDER - START
				=========================== -->
				<?php
				if ($this->showImageSlider) {
					$baseSettings = $this->config->getBaseSettings();
					$srcDir      = $baseSettings['brand_slider_source'] ?? 'assets/images/gallery';
					$fullDirPath = rtrim(DGZ_BASE_PATH, '/') . '/' . ltrim($srcDir, '/');
					$brandImages = glob($fullDirPath . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE) ?: [];
					?>
					<section class="content brands pattern border-top border-bottom">
						<div class="container">
							<div id="brands-carousel" class="owl-carousel">
								<?php
								foreach ($brandImages as $imgPath) { ?>
									<div class="item">
										<a href="#">
											<img src="<?=$this->config->getFileRootPath()?><?=$srcDir?>/<?=basename($imgPath)?>"
												 class="img-responsive" alt="">
										</a>
									</div>
								<?php
								} ?>
							</div>
						</div>
					</section>
				<?php
				} ?>
				<!-- ==========================
					BRAND SLIDER - END
				=========================== -->


			<section>
				<div class="well">
					<div id="consent-popup" class="consent-hidden">
						<p>Be aware that we use cookies to improve your experience, and nothing more. See our <a href="<?=$this->config->getFileRootPath()?>privacy">Privacy &amp; Cookie Policy</a>.
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

		</div> <!-- Page wrapper div - END -->

		<?php include('html_dependencies_bottom.inc.php'); ?>

		<!-- Include custom scripts meant for individual views -->
		<?= $this->getJavascriptHtml() ?>

		</body>
		</html>
		<?php
	}
	
}
