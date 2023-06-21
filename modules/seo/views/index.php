<?php

namespace modules\seo\views;


use modules\seo\Seo;

class index extends \DGZ_library\DGZ_HtmlView
{

	function show($globalData = [], $seoData = [])
	{ ?>
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

						<!------------------------------------
						MAIN PAGE SECTION START
						-------------------------------------->
						<div class="col-sm-12 col-lg-12">
							<h1 style="text-align:center;">Welcome to the Typhoon SEO module</h1>
							<h4 class="text-center"><b>Super-charge the SEO efforts of your web application</b></h4>

							<h3>Global SEO data</h3>
							<?php
							if ($globalData)
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
									 <b>There are no global SEO settings for your site yet!</b>
								</p>
								<h3 style="font-weight:bold;color:#3d78d8;">
									 <a href="<?=$this->controller->config->getFileRootPath()?>shop/manage-shop?userId=<?=$_SESSION['custo_id']?>" class="btn btn-primary btn-sm">Create Global data</a>
								</h3>
								<?php
						   } ?>	
						   <br />
						   <hr />


							
						   <h2 class="text-center">Pages SEO Data</h2>
							<?php
							if ($seoData)
							{ 
								foreach ($seoData as $sdata) 
								{ ?>
									<div class="col-md-12 well">
										<div class="card mt-5">
											<div class="card-header bg-white">
												<h4>
													<i class="fa fa-bullhorn section-title-icon"></i>&nbsp;<span
														class="col-form-label"><b><?=$sdata['seo_page_name']?></b></span>
												</h4>
												<p class="card-text"><b>Title en</b><?= $sdata['seo_meta_title_en'] ?? '---' ?></p>
												<p class="card-text"><b>Title fre </b><?= $sdata['seo_meta_title_fre'] ?? '---' ?></p>
												<p class="card-text"><b>Title es </b><?= $sdata['seo_meta_title_es'] ?? '---' ?></p>
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
															<span class="font-weight-bold section-title">Dynamic <small>If yes, then the page title & description are 
																dynamically generated from content provided by users & not from the title & decription you create here.
															</small></span>
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
															<span class="font-weight-bold section-title">Canonical link <small>This is an href link to the main page 
																that has the authority on the content of this page. On a product seach results page, for example; the 
																canonical link will be the link path to the full product catalog page. DGZ will use this link to generate 
																the canonical tag for you. 
															</small></span>
														</div>
														<div class="col-sm-12 col-md-8 col-lg-8">
															<div class="bg-white rounded-top p1-2">
																<?= $sdata['seo_canonical_href'] ?? '---' ?>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-sm-12 col-md-2 col-lg-2">
															<span class="font-weight-bold section-title">No Index <small>Whether or not you would like this page to be 
																indexed by Search Engines or not
															</small></span>
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


													<h3>Open Graph Settings <small>(Facebook:og tags DONE)</small></h3>
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
													<div class="col-md-6 col-sm-12">
														<div class="row">
															<div class="col-sm-9 col-md-9 col-lg-9">
																<b>Actions</b>
															</div>

															<div class="col-sm-3 col-md-3 col-lg-3">
																<a type="button" href="<?=$this->controller->config->getFileRootPath()?>seo/editPageSeo?pageId=<?=$sdata['seo_id']?>" class="btn btn-warning"><i class="fa fa-pen"></i></a>&nbsp;&nbsp;
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
							} 
							else 
							{ ?>
								<p style="color:red;text-align: center;">
									 <b>There is no SEO data for your site pages yet!</b>
								</p>
								<h3 style="font-weight:bold;color:#3d78d8;">
									 <a href="<?=$this->controller->config->getFileRootPath()?>shop/manage-shop?userId=<?=$_SESSION['custo_id']?>" class="btn btn-primary btn-sm">Add page data</a>
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

