<?php

namespace layouts\admin;

/**
 *
 * @author Gustav Ndamukong
 */
class adminLayout extends \DGZ_library\DGZ_Layout {

	
	public function display()
	{ ?>

		<!DOCTYPE HTML>
		<html class="no-js" lang="en-gb">
		<head>
			<!-- ==========================
                    Meta Tags
                =========================== -->
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<meta name="description" content="">
			<meta name="author" content="">

			<title><?php echo self::$appName . "-" . $this->pageTitle; ?></title>


			<?= $this->getCssHtml() ?>


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
			<?php
			if ((isset($_SESSION['authenticated'])) && ($_SESSION['authenticated'] == 'Let Go-' . $this->settings->getSettings()['appName'])
				/////&& ($this->viewName != 'login')) {
			) {
				?>

				<div id="page-wrapper" class="bg-white"> <!-- PAGE - START -->

					<!-- ==========================
						HEADER - START
					=========================== -->
					<?php include('admin_header.inc.php'); ?>
					<!-- ==========================
						HEADER - END
					=========================== -->


					<?php

					if (!empty($this->exceptions)):
						?>
						<div class="alert exceptions text-center" role="alert" style="margin-top: 10%;">
							<?= $this->exceptions ?>
						</div>
						<?php
					endif;
					if (!empty($this->warnings)):
						?>
						<div class="alert warnings text-center" role="alert" style="margin-top: 10%;">
							<?= $this->warnings ?>
						</div>
						<?php
					endif;
					if (!empty($this->errors)):
						?>
						<div class="alert danger text-center" role="alert" style="margin-top: 10%;">
							<?= $this->errors ?>
						</div>
						<?php
					endif;
					if (!empty($this->notices)):
						?>
						<div class="alert notices text-center" role="alert" style="margin-top: 10%;">
							<?= $this->notices ?>
						</div>
						<?php
					endif;
					if (!empty($this->successes)):
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
					<?php echo $this->content; ?>
					<!-- ==========================
						  PAGE CONTENT - END
					=========================== -->


					<section>
						<div class="well">

							<!-- ==========================
								  FOOTER - START
							  =========================== -->
							<?php include('admin_footer.inc.php'); ?>
							<!-- ==========================
								FOOTER - END
							=========================== -->

						</div>
					</section>

				</div>
				<?php
				/*}
				elseif ($this->viewName == 'login') { ?>

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
							</div>
						</section>

					</div> <!-- PAGE - END -->

					<?php
				}
				else
				{ ?>
					<div class="main">
						<section class="content account">
							<div class="container">
								<div class="row">
									<div class="col-sm-3">
									</div>
									<div class="col-sm-9">
										<h3 style="color:red;">Sorry! You have no access to this page <a href="<?=$this->settings->getFileRootPath()?>admin" class="btn btn-default">Login</a><a href="<?=$this->settings->getFileRootPath()?>home" class="btn btn-default">Home</a></h3>
									</div>
								</div>
							</div>
						</section>
					</div>
					<?php
				} */ ?>
				<?php include('html_dependencies_bottom.inc.php'); ?>

				<!-- Include scripts required -->
				<?= $this->getJavascriptHtml() ?>

			</body>
			</html>
			<?php
		}
	}
}
