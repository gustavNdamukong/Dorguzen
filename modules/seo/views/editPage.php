<?php

namespace modules\seo\views;



use modules\seo\Seo;

class editPage extends \DGZ_library\DGZ_HtmlView
{


	function show($pageData = [])
	{ 
		echo '<pre>';
        die(print_r($pageData)); /////////////
		$globalData = [];?>
		<!-- ==========================
			BREADCRUMB - START
			=========================== -->
			<div class="container-xxl py-5 bg-primary hero-header mb-5">
					<div class="container my-5 py-5 px-lg-5">
						<div class="row g-5 py-5">
							<div class="col-12 text-center">
								<h1 class="text-white animated zoomIn">SEO</h1>
								<hr class="bg-white mx-auto mt-0" style="width: 90px;">
								<nav aria-label="breadcrumb">
									<ol class="breadcrumb justify-content-center">
										<li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>">Home</a></li>
										<li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath()?>auth/login">Login</a></li>
										<li class="breadcrumb-item text-white active" aria-current="page">Seo</li>
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
					//Pull in the PHP file that has the JS code that handles all the JS to do with placing an ad
					$slideInMenu = \DGZ_library\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
					$slideInMenu->show();
					?>
					<!-- END OF SIDE SLIDE-IN MENU --> 

					<div class="row">
						<div class="col-sm-12 col-lg-12">
							<h2 style="text-align:center;">Update Page SEO data</h2>
							<?php
							if ($pageData)
							{ ?>

								<div class="col-md-12 well">
									<div class="card mt-5">
										<div class="card-header bg-white">
											<h3>
												<i class="fa fa-bullhorn section-title-icon"></i>&nbsp;<span
													class="col-form-label"><b>Global settings</b></span>
											</h3>
											<p class="card-text"><b>Geo Country </b><?= $globalData['seo_global_geo_country'] ?? '---' ?></p>
											<p class="card-text"><b>Geo City </b><?= $globalData['seo_global_geo_city'] ?? '---' ?></p>
											<p class="card-text"><b>Geo Region </b><?= $globalData['seo_global_geo_region'] ?? '---' ?></p>
											<p class="card-text"><b>Geo Position </b><?= $globalData['seo_global_geo_position'] ?? '---' ?></p>
										</div>

										<div class="card-body">
											<div class="container bg-light p-2">
												<h3>Open Graph Settings <small>(Facebook:og tags)</small></h3>

												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title">FB Id</span>
													</div>

													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $globalData['seo_global_fb_id'] ?? '---' ?>
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title">Og:site</span>
													</div>

													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $globalData['seo_global_og_site'] ?? '---' ?>
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title">Og:locale</span>
													</div>

													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $globalData['seo_global_og_locale'] ?? '---' ?>
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title">Og:author</span>
													</div>

													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $globalData['seo_global_og_author'] ?? '---' ?>
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title">Og:publisher</span>
													</div>

													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $globalData['seo_global_og_article_publisher'] ?? '---' ?>
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-sm-12 col-md-2">
														<span class="font-weight-bold section-title">Og:video</span>
													</div>

													<div class="col-md-10 col-sm-12">
														<div id="policy-number"
															class="bg-white rounded-top p1-2">
															<?= $globalData['seo_global_og_video'] ?? '---' ?>
														</div>
													</div>
												</div>
												
												<h3>Twitter Card global settings <small>(Twitter tags)</small></h3>
												<div class="row">
													<div class="col-md-6 col-sm-12">
														<div class="row">
															<div class="col-sm-12 col-md-4 col-lg-4">
																<span class="font-weight-bold section-title">Twitter Card</span>
															</div>

															<div class="col-sm-12 col-md-8 col-lg-8">
																<div class="bg-white rounded-top p1-2">
																	<?= $globalData['seo_global_twitter_card'] ?? '---' ?>
																</div>
															</div>
														</div>

														<div class="row">
															<div class="col-sm-12 col-md-4 col-lg-4">
																<span class="font-weight-bold section-title">Twitter Site</span>
															</div>

															<div class="col-sm-12 col-md-8 col-lg-8 ">
																<div class="bg-white rounded-top p1-2">
																	<?= $globalData['seo_global_twitter_site'] ?? '---' ?>
																</div>
															</div>
														</div>
													</div>
												</div>

												<h3>Miscellaneos global SEO settings</h3>
												<div class="row">
													<div class="col-md-6 col-sm-12">
														<div class="row">
															<div class="col-sm-12 col-md-4 col-lg-4">
																<span class="font-weight-bold section-title">Reflang Alternate1</span>
															</div>

															<div class="col-sm-12 col-md-8 col-lg-8">
																<div class="bg-white rounded-top p1-2">
																	<?= $globalData['seo_global_reflang_alternate1'] ?? '---' ?>
																</div>
															</div>
														</div>

														<div class="row">
															<div class="col-md-4 col-sm-12">
																<span class="font-weight-bold section-title">Reflang Alternate2</span>
															</div>

															<div class="col-md-8 col-sm-12">
																<div class="bg-white rounded-top p1-2">
																	<?= $globalData['seo_global_reflang_alternate2'] ?? '---' ?>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card-footer">
											<div class="row">
												<div class="col-md-6 col-sm-12">
													<div class="row">
														<div class="col-sm-9 col-md-9 col-lg-9">
															<b>Actions</b>
														</div>

														<div class="col-sm-3 col-md-3 col-lg-3">
															<button href="<?=$this->controller->config->getFileRootPath()?>" class="btn btn-warning"><i class="fa fa-pen"></i></button>&nbsp;&nbsp;
															<button href="<?=$this->controller->config->getFileRootPath()?>" class="btn btn-danger" type="submit"><i class="fa fa-trash"></i></button>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
                            <?php
							}
							else 
							{ ?>
								<p style="color:red;text-align: center;">
									 <b>There SEO data for this page yet!</b>
								</p>
								<h3 style="font-weight:bold;color:#3d78d8;">
									 <a href="<?=$this->controller->config->getFileRootPath()?>shop/manage-shop?userId=<?=$_SESSION['custo_id']?>" class="btn btn-primary btn-sm">Create Global data</a>
								</h3>
								<?php
						   } ?>


						</div>
						<!------------------------------------
						MAIN PAGE END
						-------------------------------------->
					</div><!--END OF ROW DIV, THE FIRST ELEMENT INSIDE THE CONTAINER DIV - WH WRAPS AROUND, OR IS FOLLOWED (INSIDE OF IT) BY THE COL-SM-9 DIV THAT HOLDS THE MAIN BODY OF THE PAGE-->
				</div><!--END OF CONTAINER DIV-->
			</section>
			<!-- ==========================
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
		}
	}
}

