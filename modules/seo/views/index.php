<?php

namespace modules\seo\views;


use modules\seo\Seo;

class index extends \DGZ_library\DGZ_HtmlView
{

	function show($globalDataSet = [], $seoData = [])
	{ 
		$this->addStyle('seo.css');
		$globalRecordCount = (count($globalDataSet) ? count($globalDataSet) : '');
		$pageCount = (count($seoData) ? count($seoData) : '')?>
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

							<h3>
								<i class="fa fa-bullhorn section-title-icon"></i>&nbsp;<span
									class="col-form-label"><b>Global SEO Settings (<?=$globalRecordCount?>)</b></span>
							</h3>
							<div class="well">
							<a href="<?=$this->controller->config->getFileRootPath()?>seo/addGlobal" class="btn btn-primary btn-sm">Create Global data</a>
							<h5 class="text-center"><b>Note: </b>You may have multiple global sets, but only the first in the list will ever be used</h5>
						   </div>
							<?php
							if ($globalDataSet)
							{ ?>
								<div class="table-responsive">
                                    <table class="table">
                                      	<thead>
											<tr>
											<th scope="col">Record ID</th>
											<th scope="col">Place</th>
											<th scope="col">Region</th>
											<th>Action</th>
											</tr>
                                      	</thead>
                                      	<tbody>
											<div class="list-group list-group-horizontal">
												<?php foreach ($globalDataSet as $globalData) 
												{ ?>
													<tr>
														<a href="">
															<td><?=$globalData['seo_global_id']?></td>
															<td><?=$globalData['seo_global_geo_placename']?></td>
															<td><?=$globalData['seo_global_geo_region']?></td>
															<td>
																<a class="clickable-record" 
																	title="View Global SEO Data" 
																	href="<?=$this->controller->config->getFileRootPath()?>seo/globalDetail?recordId=<?=$globalData['seo_global_id']?>"><i 
																	class="fa fa-eye"></i>
																</a>
															</td>
														</a>
													</tr> 
												<?php
												} ?>
											</div>	
										</tbody>
									</table> 
								</div>
							<?php
							} ?>	
								
						   <br />
						   <hr />

						   <h3>
								<i class="fa fa-bullhorn section-title-icon"></i>&nbsp;<span
									class="col-form-label"><b>Pages SEO Data (<?=$pageCount?>)</b></span>
							</h3>
						   <div class="well">
						      <a href="<?=$this->controller->config->getFileRootPath()?>seo/addPage" class="btn btn-primary btn-sm">Create page data</a>
						   </div>
							<?php
							if ($seoData)
							{ ?>
							   <div class="table-responsive">
                                    <table class="table">
                                      	<thead>
											<tr>
											<th scope="col">Page name</th>
											<th scope="col">Title</th>
											<th>Action</th>
											</tr>
                                      	</thead>
                                      	<tbody>
											<div class="list-group list-group-horizontal">
												<?php foreach ($seoData as $sdata) 
												{ ?>
													<tr>
														<a href="">
															<td><?=$sdata['seo_page_name']?></td>
															<td><?=$sdata['seo_meta_title_en']?></td>
															<td>
																<a class="clickable-record" 
																	title="View Page SEO Data" 
																	href="<?=$this->controller->config->getFileRootPath()?>seo/pageDetail?pageId=<?=$sdata['seo_id']?>"><i 
																	class="fa fa-eye"></i>
																</a>
															</td>
														</a>
													</tr> 
												<?php
												} ?>
											</div>	
										</tbody>
									</table> 
								</div>
							<?php
							} 
							else 
							{ ?>
								<p style="color:red;text-align: center;">
									 <b>There is no SEO data for your site pages yet!</b>
								</p>
								<h3 style="font-weight:bold;color:#3d78d8;">
									 <a href="<?=$this->controller->config->getFileRootPath()?>seo/addPage" class="btn btn-primary btn-sm">Create page data</a>
								</h3>
								<?php
						   } ?>	
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
		}
	}
}

