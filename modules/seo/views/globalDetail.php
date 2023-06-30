<?php

namespace modules\seo\views;


use modules\seo\Seo;

class globalDetail extends \DGZ_library\DGZ_HtmlView
{

	function show($globalData = [])
	{ 
		$this->addStyle('seo.css');
		?>

		<script src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

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
										<li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath()?>admin/dashboard">Dashboard</a></li>
										<li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath()?>seo">SEO</a></li>
										<li class="breadcrumb-item text-white active" aria-current="page">Global Detail</li>
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
						<?php
						$jsValidation = \DGZ_library\DGZ_View::getModuleInsideView('seo', 'jsSeoValidationPartial', $this->controller);
						$jsValidation->show(); ?>
						<!------------------------------------
						MAIN PAGE SECTION START
						-------------------------------------->
						<div class="col-sm-12 col-lg-12">
						   <h2 class="text-center">Global SEO data record ID: <?=$globalData['seo_global_id']?></h2>
						   <div class="well">
						      <a href="<?=$this->controller->config->getFileRootPath()?>seo" class="btn btn-primary btn-sm">Back to all pages</a>
						   </div>
							<?php
							if ($globalData)
							{ ?>
								<div class="col-md-12 well">
									<div class="card mt-5 current-data-card">
										<div class="card-header bg-white">
											

											<p class="card-text"><span class="label">Geo Country/City </span><?= $globalData['seo_global_geo_placename'] ?? ' ' ?></p>
											<p class="card-text"><span class="label">Geo Region </span><?= $globalData['seo_global_geo_region'] ?? ' ' ?></p>
											<p class="card-text"><span class="label">Geo Position </span><?= $globalData['seo_global_geo_position'] ?? ' ' ?></p>
										</div>

										<div class="card-body">
											<div class="container bg-light p-2">
												<h4>Open Graph Settings <small>(Facebook:og tags)</small></h4>
												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title">FB Id</span>
													</div>

													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $globalData['seo_global_fb_id'] ?? ' ' ?>
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title">Og:site</span>
													</div>

													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $globalData['seo_global_og_site'] ?? ' ' ?>
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title">Og:locale</span>
													</div>

													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $globalData['seo_global_og_locale'] ?? ' ' ?>
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title">Og:author</span>
													</div>

													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $globalData['seo_global_og_author'] ?? ' ' ?>
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title">Og:publisher</span>
													</div>

													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $globalData['seo_global_og_article_publisher'] ?? ' ' ?>
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
															<?= $globalData['seo_global_og_video'] ?? ' ' ?>
														</div>
													</div>
												</div>
											
												<h4>Twitter Card global settings <small>(Twitter tags)</small></h4>
												<div class="row">
													<div class="col-md-6 col-sm-12">
														<div class="row">
															<div class="col-sm-12 col-md-4 col-lg-4">
																<span class="font-weight-bold section-title">Twitter Card</span>
															</div>

															<div class="col-sm-12 col-md-8 col-lg-8">
																<div class="bg-white rounded-top p1-2">
																	<?= $globalData['seo_global_twitter_card'] ?? ' ' ?>
																</div>
															</div>
														</div>

														<div class="row">
															<div class="col-sm-12 col-md-4 col-lg-4">
																<span class="font-weight-bold section-title">Twitter Site</span>
															</div>

															<div class="col-sm-12 col-md-8 col-lg-8 ">
																<div class="bg-white rounded-top p1-2">
																	<?= $globalData['seo_global_twitter_site'] ?? ' ' ?>
																</div>
															</div>
														</div>
													</div>
												</div>

												<h4>Miscellaneos global SEO settings</h4>
												<div class="row">
													<div class="col-md-6 col-sm-12">
														<div class="row">
															<div class="col-sm-12 col-md-4 col-lg-4">
																<span class="font-weight-bold section-title">Reflang Alternate1</span>
															</div>

															<div class="col-sm-12 col-md-8 col-lg-8">
																<div class="bg-white rounded-top p1-2">
																	<?= $globalData['seo_global_reflang_alternate1'] ?? ' ' ?>
																</div>
															</div>
														</div>

														<div class="row">
															<div class="col-md-4 col-sm-12">
																<span class="font-weight-bold section-title">Reflang Alternate2</span>
															</div>

															<div class="col-md-8 col-sm-12">
																<div class="bg-white rounded-top p1-2">
																	<?= $globalData['seo_global_reflang_alternate2'] ?? ' ' ?>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card-footer">
											<div class="row">
												<div class="col-md-12 col-sm-12">
													<div class="row">
														<div class="col-sm-6 col-md-6 col-lg-6">
															<b>Actions</b>
														</div>

														<div class="col-sm-6 col-md-6 col-lg-6">
															<a type="button" href="<?=$this->controller->config->getFileRootPath()?>seo/editGlobal?globalId=<?=$globalData['seo_global_id']?>" class="btn btn-warning"><i class="fa fa-pen"></i></a>&nbsp;&nbsp;

															<form action="<?=$this->controller->config->getFileRootPath()?>seo/deleteGlobal" method="POST" id="deleteForm">
																<input type="hidden" name="recordId" value="<?=$globalData['seo_global_id']?>">
																<a onclick="confirmDelete(event)"
																	href="#"
																	class="btn btn-danger"><i class="fa fa-trash"></i>
																</a>
															</form>

															<a href="<?=$this->controller->config->getFileRootPath()?>seo" class="btn btn-primary btn-sm">Back to all pages</a>
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
									 <b>No global SEO data was found for that record!</b>
								</p>
								<h3 style="font-weight:bold;color:#3d78d8;">
									 <a href="<?=$this->controller->config->getFileRootPath()?>seo" class="btn btn-primary btn-sm">Back to all pages</a>
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

