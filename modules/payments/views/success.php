<?php

namespace modules\payments\views;

class success extends \DGZ_library\DGZ_HtmlView
{

	function show($globalDataSet = [], $seoData = [])
	{ 
		$this->addStyle('payments.css'); ?>

		<!-- ==========================
			BREADCRUMB - START
			=========================== -->
			<div class="container-xxl py-5 bg-primary hero-header mb-5">
					<div class="container my-5 py-5 px-lg-5">
						<div class="row g-5 py-5">
							<div class="col-12 text-center">
								<h1 class="text-white animated zoomIn">Thank you!</h1>
								<hr class="bg-white mx-auto mt-0" style="width: 90px;">
								<nav aria-label="breadcrumb">
									<ol class="breadcrumb justify-content-center">
										<li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>">Home</a></li>
										<li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath()?>admin/dashboard">Dashboard</a></li>
										<li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath()?>payments">Payments</a></li>
										<li class="breadcrumb-item text-white active" aria-current="page">Success</li>
									</ol>
								</nav>
							</div>
						</div>
					</div>
				</div>
				</div>
			<!-- ==========================
				BREADCRUMB - END
			=========================== -->
        <?php
		if ((isset($_SESSION['authenticated'])) && ($_SESSION['authenticated'] == 'Let Go-'.$this->controller->config->getConfig()['appName'])) 
         { ?>

			<!-- ==========================
				PAGE CONTENT - START
			=========================== -->
			<section class="content news">
				<div class="container">

					<!-- START SIDE SLIDE-IN MENU -->
					<?php
					$slideInMenu = \DGZ_library\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
					$slideInMenu->show();
					?>
					<!-- END OF SIDE SLIDE-IN MENU --> 

					<div class="row">
						<!------------------------------------
						MAIN PAGE SECTION START
						-------------------------------------->
						<div class="col-sm-12 col-lg-12">
							<h1 style="text-align:center;color:green;">Success!</h1>
							<h4 class="text-center"><b>Thank you</b></h4>
							<p>We appreciate your business and will be in touch with you shortly</p>
						</div>
						<!------------------------------------
						MAIN PAGE END
						-------------------------------------->
					</div>
				</div>
			</section>
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
									  <h3 style="color:red;">Sorry! You have no access to this page 
										<a href="<?=$this->controller->config->getFileRootPath()?>auth" class="btn btn-info">Login</a>
										<a href="<?=$this->controller->config->getFileRootPath()?>" class="btn btn-info">Home</a></h3>
								 </div>
							</div>
					   </div>
				  </section>
			 </div>
		<?php
		} ?>

		<?php
		$paymentsJs = \DGZ_library\DGZ_View::getModuleInsideView('payments', 'jsPaymentsPartial', $this->controller);
		$paymentsJs->show();
	}
}

