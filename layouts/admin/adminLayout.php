<?php

namespace layouts\admin;

/**
 *
 * @author Gustav
 */
class adminLayout extends \DGZ_library\DGZ_Layout {

	
	public function display() { ?>

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

		<div style="margin-top: 8%;"></div>
		<!--THIS CODE IS PART OF THE 'page-plugin' WHICH WILL DISPLAY A LIKE BOX SO VISITORS CAN SEE THE NUMBER OF LIKES U HAVE, CAN LIKE/SHARE WITHOUT LEAVING YOUR SITE
		NOTE: ADJUSTING THE HEIGHT WILL AUTOMATICALLY MAKE IT A FB NEWS FEED WIDGET-->
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.9";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>



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
				  <?php include('admin_header.inc.php'); ?>
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




				  <?php echo $this->content;  ?>




				<section>
					<div class="well">

						<!-- ==========================
							  FOOTER - START
						  =========================== -->
						<?php include('admin_footer.inc.php'); ?>
						<!-- ==========================
							FOOTER - END
						=========================== -->

					</div><!--END OF THE WELL DIV INSIDE THE first_footer SECTION-->
				</section><!--End of first footer section-->


				</div> <!-- PAGE - END -->


			<?php include('html_dependencies_bottom.inc.php');  ?>


			<!-- Include scripts required by Bootstrap -->
			<?= $this->getJavascriptHtml() ?>

		</body>
		</html>


		<?php

	}

	
}
