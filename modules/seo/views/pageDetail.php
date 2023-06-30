<?php

namespace modules\seo\views;


use modules\seo\Seo;

class pageDetail extends \DGZ_library\DGZ_HtmlView
{

	function show($sdata = [])
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
										<li class="breadcrumb-item text-white active" aria-current="page">Page Detail</li>
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
						   <h2 class="text-center">SEO data for the '<?=$sdata['seo_page_name']?>' page</h2>
						   <div class="well">
						      <a href="<?=$this->controller->config->getFileRootPath()?>seo" class="btn btn-primary btn-sm">Back to all pages</a>
						   </div>
							<?php
							if ($sdata)
							{ ?>
								<div class="col-md-12 well">
									<div class="card mt-5 current-data-card">
										<div class="card-header bg-white">
											<h4>
												<i class="fa fa-bullhorn section-title-icon"></i>&nbsp;<span
													class="col-form-label"><b><?=$sdata['seo_page_name']?></b></span>
											</h4>
											<p class="card-text"><span class="label">Title en</span> <?= $sdata['seo_meta_title_en'] ?? '---' ?></p>
											<p class="card-text"><span class="label">Title fre</span> <?= $sdata['seo_meta_title_fre'] ?? '---' ?></p>
											<p class="card-text"><span class="label">Title es</span> <?= $sdata['seo_meta_title_es'] ?? '---' ?></p>
										</div>

										<div class="card-body">
											<div class="container bg-light p-2">
												<h3>Page specifics></h3>
												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title">Meta Description</span>
													</div>

													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $sdata['seo_meta_desc_en'] ?? '---' ?><br /><hr />
															<?= $sdata['seo_meta_desc_fre'] ?? '---' ?><br /><hr />
															<?= $sdata['seo_meta_desc_es'] ?? '---' ?>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title">Dynamic (titles & description?)</span>
													</div>
													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $sdata['seo_dynamic'] ?? '---' ?>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title">Meta Keywords</span>
													</div>
													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $sdata['seo_keywords_en'] ?? '---' ?><br /><hr />
															<?= $sdata['seo_keywords_fre'] ?? '---' ?><br /><hr />
															<?= $sdata['seo_keywords_es'] ?? '---' ?>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title"><small>Canonical link (references another more authoritative page?)</small></span>
													</div>
													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $sdata['seo_canonical_href'] ?? '---' ?>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title"><small>No Index (to index or not to index)</small></span>
													</div>
													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $sdata['seo_no_index'] ?? '---' ?>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title">H1 Text</span>
													</div>
													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $sdata['seo_h1_text_en'] ?? '---' ?><br /><hr />
															<?= $sdata['seo_h1_text_fre'] ?? '---' ?><br /><hr />
															<?= $sdata['seo_h1_text_es'] ?? '---' ?>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title">H2 Text</span>
													</div>
													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $sdata['seo_h2_text_en'] ?? '---' ?><br /><hr />
															<?= $sdata['seo_h2_text_fre'] ?? '---' ?><br /><hr />
															<?= $sdata['seo_h2_text_es'] ?? '---' ?>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-12 col-md-2 col-lg-2">
														<span class="font-weight-bold section-title">Page Content</span>
													</div>
													<div class="col-sm-12 col-md-8 col-lg-8">
														<div class="bg-white rounded-top p1-2">
															<?= $sdata['seo_page_content_en'] ?? '---' ?><br /><hr />
															<?= $sdata['seo_page_content_fre'] ?? '---' ?><br /><hr />
															<?= $sdata['seo_page_content_es'] ?? '---' ?>
														</div>
													</div>
												</div>


												<h3>Open Graph Settings <small>(Facebook:og tags)</small></h3>
												<div class="row">
													<div class="col-md-12 col-sm-12">
														<div class="row">
															<div class="col-sm-12 col-md-2 col-lg-2">
																<span class="font-weight-bold section-title">OG:title</span>
															</div>
															<div class="col-sm-12 col-md-8 col-lg-8">
																<div class="bg-white rounded-top p1-2">
																	<?= $sdata['seo_og_title_en'] ?? '---' ?><br /><hr />
																	<?= $sdata['seo_og_title_fre'] ?? '---' ?><br /><hr />
																	<?= $sdata['seo_og_title_es'] ?? '---' ?>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-sm-12 col-md-2 col-lg-2">
																<span class="font-weight-bold section-title">OG:description</span>
															</div>
															<div class="col-sm-12 col-md-8 col-lg-8">
																<div class="bg-white rounded-top p1-2">
																	<?= $sdata['seo_og_desc_en'] ?? '---' ?><br /><hr />
																	<?= $sdata['seo_og_desc_fre'] ?? '---' ?><br /><hr />
																	<?= $sdata['seo_og_desc_es'] ?? '---' ?>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-sm-12 col-md-2 col-lg-2">
																<span class="font-weight-bold section-title">Og:Image</span>
															</div>
															<div class="col-sm-12 col-md-8 col-lg-8">
																<div class="bg-white rounded-top p1-2">
																	<?= $sdata['seo_og_image'] ?? '---' ?>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-sm-12 col-md-2 col-lg-2">
																<span class="font-weight-bold section-title">Og:Image width</span>
															</div>
															<div class="col-sm-12 col-md-8 col-lg-8">
																<div class="bg-white rounded-top p1-2">
																	<?= $sdata['seo_og_image_width'] ?? '---' ?>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-sm-12 col-md-2 col-lg-2">
																<span class="font-weight-bold section-title">Og:Image height</span>
															</div>
															<div class="col-sm-12 col-md-8 col-lg-8">
																<div class="bg-white rounded-top p1-2">
																	<?= $sdata['seo_og_image_height'] ?? '---' ?>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-sm-12 col-md-2 col-lg-2">
																<span class="font-weight-bold section-title">OG:Type <small>e.g. article, profile etc</small></span>
															</div>
															<div class="col-sm-12 col-md-8 col-lg-8">
																<div class="bg-white rounded-top p1-2">
																	<?= $sdata['seo_og_type_en'] ?? '---' ?><br /><hr />
																	<?= $sdata['seo_og_type_fre'] ?? '---' ?><br /><hr />
																	<?= $sdata['seo_og_type_es'] ?? '---' ?>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-sm-12 col-md-2 col-lg-2">
																<span class="font-weight-bold section-title">Og:URL <small>The full path to this page</small></span>
															</div>
															<div class="col-sm-12 col-md-8 col-lg-8">
																<div class="bg-white rounded-top p1-2">
																	<?= $sdata['seo_og_url'] ?? '---' ?>
																</div>
															</div>
														</div>
													</div>
												</div>

												<h3>Twitter Card settings <small>(Twitter tags)</small></h3>
												<div class="row">
													<div class="col-md-12 col-sm-12">
														<div class="row">
															<div class="col-sm-12 col-md-4 col-lg-4">
																<span class="font-weight-bold section-title">Twitter Title</span>
															</div>
															<div class="col-sm-12 col-md-8 col-lg-8">
																<div class="bg-white rounded-top p1-2">
																<?= $sdata['seo_twitter_title_en'] ?? '---' ?><br /><hr />
																<?= $sdata['seo_twitter_title_fre'] ?? '---' ?><br /><hr />
																<?= $sdata['seo_twitter_title_es'] ?? '---' ?>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-sm-12 col-md-4 col-lg-4">
																<span class="font-weight-bold section-title">Twitter Description</span>
															</div>
															<div class="col-sm-12 col-md-8 col-lg-8">
																<div class="bg-white rounded-top p1-2">
																<?= $sdata['seo_twitter_desc_en'] ?? '---' ?><br /><hr />
																<?= $sdata['seo_twitter_desc_fre'] ?? '---' ?><br /><hr />
																<?= $sdata['seo_twitter_desc_es'] ?? '---' ?>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-sm-12 col-md-4 col-lg-4">
																<span class="font-weight-bold section-title">Twitter Image</span>
															</div>
															<div class="col-sm-12 col-md-8 col-lg-8 ">
																<div class="bg-white rounded-top p1-2">
																	<?= $sdata['seo_twitter_image'] ?? '---' ?>
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
															<a type="button" href="<?=$this->controller->config->getFileRootPath()?>seo/editPageSeo?pageId=<?=$sdata['seo_id']?>" class="btn btn-warning"><i class="fa fa-pen"></i></a>
															
															<form action="<?=$this->controller->config->getFileRootPath()?>seo/deletePage" method="POST" id="deleteForm">
																<input type="hidden" name="pageId" value="<?=$sdata['seo_id']?>">
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
									 <b>No SEO data was found for that page!</b>
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

