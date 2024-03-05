<?php

namespace modules\payments\views;


class index extends \DGZ_library\DGZ_HtmlView
{

	function show($globalDataSet = [], $seoData = [])
	{ 
		$this->addStyle('payments.css'); ?>

		<script src="https://js.stripe.com/v3/"></script>

		<!-- ==========================
			BREADCRUMB - START
			=========================== -->
			<div class="container-xxl py-5 bg-primary hero-header mb-5">
					<div class="container my-5 py-5 px-lg-5">
						<div class="row g-5 py-5">
							<div class="col-12 text-center">
								<h1 class="text-white animated zoomIn">PAYMENTS</h1>
								<hr class="bg-white mx-auto mt-0" style="width: 90px;">
								<nav aria-label="breadcrumb">
									<ol class="breadcrumb justify-content-center">
										<li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>">Home</a></li>
										<li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath()?>admin/dashboard">Dashboard</a></li>
										<li class="breadcrumb-item text-white active" aria-current="page">Payments</li>
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
							<h1 style="text-align:center;">Welcome to the Payments module</h1>
							<h4 class="text-center"><b>Receive payments in your web application</b></h4>

							<form action="<?=$this->controller->config->getFileRootPath()?>payments/payTEST2" method="post" id="payment-form">
								<div class="form-row">
									<input type="text" name="first_name" class="form-control mb-3 StripeElement StripeElement--empty" placeholder="First Name">
									<input type="text" name="last_name" class="form-control mb-3 StripeElement StripeElement--empty" placeholder="Last Name">
									<input type="email" name="email" class="form-control mb-3 StripeElement StripeElement--empty" placeholder="Email Address">
									
									<!--UNCOMMENT THE BELOW LINE IF YOUR SITE HAS A VALID SSL CERTIFICATE (eg WHEN IN PRODUCTION) 
									TO CREATE THE STRIPE CARD FIELD THAT WILL VALIDATE & TOKENISE THE CARD FIELD FOR SECURE HANDLING
									ON THE BACKEND ONCE SUBMITTED-->
									<!--<div id="card-element" class="form-control mb-3 StripeElement StripeElement--empty">-->
									<!-- a Stripe Element will be inserted here. -->
									</div>

									<!-- Used to display form errors -->
									<div id="card-errors" role="alert"></div>
								</div>

								<button class="btn btn-primary btn-sm">Submit Payment</button>
		 					</form>
								
						   <br />
						   <hr />
							
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

