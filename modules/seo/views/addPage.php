<?php

namespace modules\seo\views;


use DGZ_library\DGZ_Form;
use modules\seo\Seo;

class addPage extends \DGZ_library\DGZ_HtmlView
{


	function show()
	{ 
		$form = new DGZ_Form();
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
										<li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath()?>seo">Seo</a></li>
										<li class="breadcrumb-item text-white active" aria-current="page">Create</li>
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

					<?php
					//Pull in the PHP file that has the JS validation codes
					$jsValidation = \DGZ_library\DGZ_View::getModuleInsideView('seo', 'jsSeoValidationPartial', $this->controller);
					$jsValidation->show(); ?>

					<div class="row">
						<div class="col-sm-12 col-lg-12">
							<h2 style="text-align:center;">Create SEO data for a webpage</h2>
                                <div class="panel panel-primary">
									<div class="panel-heading">
										<h4>
											<i class="fa fa-bullhorn section-title-icon"></i>&nbsp;
											<span class="col-form-label">
												<small>See label text above form fields for hints on how these SEO elements work</small>
											</span>
										</h4>
									</div>
									<?php
									$form::open('addPage', $this->controller->config->getFileRootPath().'seo/saveNewPage', 'post', ['enctype' => 'multipart/form-data']); ?>
										<div class="panel-body panel-primary">
											<div class="container bg-light p-2">
												<fieldset>
													<legend>Page specifics</legend>
													    <div class="col-sm-12 col-md-12 col-lg-12 form-group">
															<span class="font-weight-bold section-title"><small>The page name is the page for which you are creating the SEO attributes.
																The name you enter here MUST therefore match the exact same spelling of the view file. eg for a view about-us.blade.php
																the page name you enter here should be 'about-us'.  
															</small></span>
															<?php 
															$form::label('seo_page_name', 'Page Name');
															echo "<span id='info'></span>";
									                        $form::input('seo_page_name', 'text', ['class' => 'form-control']); 
															?>
														</div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group">
														    <span class="font-weight-bold section-title"><small>Meta titles should take a MAX of 60 characters. Try & put in the most important 
																keywords of this page in there. A great tip is to split keywords/phrases/categories/sections (depending on the type of 
																content of this page) by pipe characters e.g. for a car sales details page for a specific car, you can use the pipe 
																technique wisely & pack into this title, vital keywords relating to the category, make, model, plus the actual description of 
																the specific car like so: "Car | Mercedes-Benz | Mercedes G Wagon | Nice car for sale"  
															</small></span>
															<?php
															$form::label('seo_meta_title_en', 'Title (en)');
									                        $form::input('seo_meta_title_en', 'text', ['class' => 'form-control', 'maxlength' => '60']); 

															$form::label('seo_meta_title_fre', 'Title (fre)');
									                        $form::input('seo_meta_title_fre', 'text', ['class' => 'form-control', 'maxlength' => '60']);

															$form::label('seo_meta_title_es', 'Title (es)');
									                        $form::input('seo_meta_title_es', 'text', ['class' => 'form-control', 'maxlength' => '60']);
															?>
														</div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group">
														    <span class="font-weight-bold section-title"><small>Meta descriptions should take a MAX of 150 characters. Try to get into this 
																section, this page's important keywords. Just like titles, meta descriptions are very powerful SEO influencers. Again, just like 
																with meta titles, you can use the pipe technique here really wisely to pack vital keywords into this description relating to the 
																content of this page. Just make substr_replace you do not literally repeat what is already in the title tag.
															</small></span>
															<?php 
															$form::label('seo_meta_desc_en', 'Meta description (en)');
									                        $form::input('seo_meta_desc_en', 'text', ['class' => 'form-control', 'maxlength' => '150']); 

															$form::label('seo_meta_desc_fre', 'Meta description (fre)');
									                        $form::input('seo_meta_desc_fre', 'text', ['class' => 'form-control', 'maxlength' => '150']);

															$form::label('seo_meta_desc_es', 'Meta description (es)');
									                        $form::input('seo_meta_desc_es', 'text', ['class' => 'form-control', 'maxlength' => '150']);
															?>
														</div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group">
														    <span class="font-weight-bold section-title"><small>By default this is 0 (false), but if it is true, then the page title 
																& description would be dynamically generated from content provided by users & not from the title & decription you 
																create here. You will have to make sure you go into the code of the view file of this page and capture the TITLE & 
																DESCRIPTION text and generate these metatags using the <b>addMetadata()</b> view function and manually pass into it 
																these (title & description) metatags. This is for pages whose title & description data you know will be provided by 
																user-submitted data e.g. a classified ads site. In such a case, you cannot guess what those values will be, so you set 
																'SEO Dynamic' here below to 1.  
															</small></span>
															<?php 
															$form::label('seo_dynamic', 'SEO Dynamic');
									                        $form::select('seo_dynamic', [0, 1], [0], false, ['class' => 'form-control']);
															?>
														</div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group">
														    <span class="font-weight-bold section-title">Meta Keywords</span>
															<?php 
															$form::label('seo_keywords_en', 'Meta keywords (en)');
									                        $form::input('seo_keywords_en', 'text', ['class' => 'form-control', 'maxlength' => '200']); 

															$form::label('seo_keywords_fre', 'Meta keywords (fre)');
									                        $form::input('seo_keywords_fre', 'text', ['class' => 'form-control', 'maxlength' => '200']);

															$form::label('seo_keywords_es', 'Meta keywords (es)');
									                        $form::input('seo_keywords_es', 'text', ['class' => 'form-control', 'maxlength' => '200']);
															?>
														</div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group">
														    <span class="font-weight-bold section-title">Canonical <small>This is an href link to the main page 
																that has the authority on the content of this page. On a product seach results page, for example; the 
																canonical link will be the link path to the full product catalog page. DGZ will use this link to generate 
																the canonical tag for you. Just provide the full URL path to the target parent page like so:
																'https://yoursite/products/catalog'.
															</small></span>
															<?php 
															$form::label('seo_canonical_href', 'Canonical link');
									                        $form::input('seo_canonical_href', 'text', ['class' => 'form-control']); 
															?>
														</div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group">
														    <span class="font-weight-bold section-title">No Index (0 or 1)<small>Whether or not you would like this page NOT 
																to be indexed by Search Engines. The default is NO (0) which means all pages are indexed. But if you do not 
																want this page indexed, then just set it to '1'. An example of a page where you would set this to no would be a search 
																results page which makes sense not to index it because the content which will be search result is user-determnined 
																and will always be different. 
															</small></span>
															<?php  
															$form::label('seo_no_index', 'No index');
									                        $form::select('seo_no_index', [0, 1], [0], false, ['class' => 'form-control']);
															?>
														</div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group">
														    <span class="font-weight-bold section-title">H1 Text. You only need one h1 tag per page. That h1 should describe the 
																content of the page. An h1 tag should contain characters from 20 to a MAX of 70.</span>
															<?php 
															$form::label('seo_h1_text_en', 'H1 Text (en)');
									                        $form::input('seo_h1_text_en', 'text', ['class' => 'form-control', 'maxlength' => '70']); 

															$form::label('seo_h1_text_fre', 'H1 Text (fre)');
									                        $form::input('seo_h1_text_fre', 'text', ['class' => 'form-control', 'maxlength' => '70']);

															$form::label('seo_h1_text_es', 'H1 Text (es)');
									                        $form::input('seo_h1_text_es', 'text', ['class' => 'form-control', 'maxlength' => '70']);
															?>
														</div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group">
														    <span class="font-weight-bold section-title">H2 Text</span>
															<?php 
															$form::label('seo_h2_text_en', 'H2 Text (en)');
									                        $form::input('seo_h2_text_en', 'text', ['class' => 'form-control', 'maxlength' => '70']); 

															$form::label('seo_h2_text_fre', 'H2 Text (fre)');
									                        $form::input('seo_h2_text_fre', 'text', ['class' => 'form-control', 'maxlength' => '70']);

															$form::label('seo_h2_text_es', 'H2 Text (es)');
									                        $form::input('seo_h2_text_es', 'text', ['class' => 'form-control', 'maxlength' => '70']);
															?>
														</div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group">
														    <span class="font-weight-bold section-title">Page Content</span>
															<?php 
															$form::label('seo_page_content_en', 'Page content (en)');
									                        $form::input('seo_page_content_en', 'textarea', ['class' => 'form-control']); 

															$form::label('seo_page_content_fre', 'Page content (fre)');
									                        $form::input('seo_page_content_fre', 'textarea', ['class' => 'form-control']);

															$form::label('seo_page_content_es', 'Page content (es)');
									                        $form::input('seo_page_content_es', 'textarea', ['class' => 'form-control']);
															?>
														</div>
							                        </fieldset>

												    <fieldset>
													    <legend>Open Graph Settings <small>(Facebook:og tags)</small></legend>
                                                        <div class="col-sm-12 col-md-12 col-lg-12 form-group">
														   <span class="font-weight-bold section-title">OG:title. This can be, and is usually the same as the text in 
															your meta title tag. </span>
														   <?php 
														    $form::label('seo_og_title_en', 'Og title (en)');
									                        $form::input('seo_og_title_en', 'text', ['class' => 'form-control']); 

														    $form::label('seo_og_title_fre', 'Og title (fre)');
									                        $form::input('seo_og_title_fre', 'text', ['class' => 'form-control']);

															$form::label('seo_og_title_es', 'Og title (es)');
									                        $form::input('seo_og_title_es', 'text', ['class' => 'form-control']);
															?>
														</div>

                                                        <div class="col-sm-12 col-md-12 col-lg-12 form-group">
														   <span class="font-weight-bold section-title">OG:description. This can be, and is usually the same as the text in 
															your meta desciption. </span>
														   <?php 
														    $form::label('seo_og_desc_en', 'Og description (en)');
									                        $form::input('seo_og_desc_en', 'text', ['class' => 'form-control']); 

														    $form::label('seo_og_desc_fre', 'Og description (fre)');
									                        $form::input('seo_og_desc_fre', 'text', ['class' => 'form-control']);

															$form::label('seo_og_desc_es', 'Og description (es)');
									                        $form::input('seo_og_desc_es', 'text', ['class' => 'form-control']);
															?>
														</div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group">
														    <span class="font-weight-bold section-title">
																<small>Facebook image dimensions are 1200 by 630 pixels. You would create and place the image e.g. in your direcxtory 
																	system and then provide the fully qualified URL to the image here like so:
																	'https://yoursite/assets/images/socials/og-image-img.png'	
																	Note: you MUST only provide a secure image path (using 'https') otherwise Facebook will reject it. For security 
																	reasons, Facebook only accepts image paths via SSL URLs. 
																</small>
															</span>
															<?php 
															$form::label('seo_og_image', 'Og:Image (Fully qualified image path eg https://yourSite/assets/images/social/og-image.png)');
									                        $form::input('seo_og_image', 'text', ['class' => 'form-control']); 
															?>
														</div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group">
														    <span class="font-weight-bold section-title"><small>Always include the width and height so that FB will render the image 
																with speed.	The recommended width is 1200, stick to it. Only provide the number e.g. 1200, nothing else.
															</small></span>
															<?php 
															$form::label('seo_og_image_width', 'Og:Image width');
									                        $form::input('seo_og_image_width', 'text', ['class' => 'form-control', 'placeholder' => 'recommended: 1200']); 
															?>
														</div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group">
														    <span class="font-weight-bold section-title"><small>Always include the width and height so that FB will render the image 
																with speed. The recommended height is 630, stick to it. Only provide the number e.g. 630, nothing else.
															</small></span>
															<?php 
															$form::label('seo_og_image_height', 'Og:Image height');
									                        $form::input('seo_og_image_height', 'text', ['class' => 'form-control', 'placeholder' => 'recommended: 630']); 
															?>
														</div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group">
														    <span class="font-weight-bold section-title">OG:Image secure/SSL link with https 
																<small>e.g. https://yourSite/assets/images/social/image.svg</small></span>
															<?php 
															$form::label('seo_og_image_secure_url', 'Og:Image secure/SSL link');
									                        $form::input('seo_og_image_secure_url', 'text', ['class' => 'form-control']); 
															?>
														</div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group">
														   <span class="font-weight-bold section-title">OG:Type <small>e.g. article, profile etc</small></span>
														   <?php 
														    $form::label('seo_og_type_en', 'Og type (en)');
									                        $form::input('seo_og_type_en', 'text', ['class' => 'form-control']); 

														    $form::label('seo_og_type_fre', 'Og type (fre)');
									                        $form::input('seo_og_type_fre', 'text', ['class' => 'form-control']);

															$form::label('seo_og_type_es', 'Og type (es)');
									                        $form::input('seo_og_type_es', 'text', ['class' => 'form-control']);
															?>
														</div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group">
															<?php 
															$form::label('seo_og_url', 'Og:URL (The full path to this page)');
									                        $form::input('seo_og_url', 'text', ['class' => 'form-control']); 
															?>
														</div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group"> 
														    <span class="font-weight-bold section-title">
																<small>Fully qualified URL path to a video if there is one, for the content of this page</small></span>
															<?php 
															$form::label('seo_og_video', 'Og:video (e.g. https://yoursite/assets/videos/myVideo.mp4)');
									                        $form::input('seo_og_video', 'text', ['class' => 'form-control', 'value' => $pageData['seo_og_video'] ?? ' ']); 
															?>
														</div>
							                        </fieldset>	

												    <fieldset>
													    <legend>Twitter Card settings <small>(Twitter tags)</small></legend>
													    <div class="col-sm-12 col-md-12 col-lg-12 form-group">
														   <span class="font-weight-bold section-title">Twitter Title. This can be, and is usually the same as the text in 
															your meta title tag. </span>
														   <?php 
														    $form::label('seo_twitter_title_en', 'Twitter Title (en)');
									                        $form::input('seo_twitter_title_en', 'text', ['class' => 'form-control']); 

														    $form::label('seo_twitter_title_fre', 'Twitter Title (fre)');
									                        $form::input('seo_twitter_title_fre', 'text', ['class' => 'form-control']);

															$form::label('seo_twitter_title_es', 'Twitter Title (es)');
									                        $form::input('seo_twitter_title_es', 'text', ['class' => 'form-control']);
															?>
													    </div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group">
														   <span class="font-weight-bold section-title">Twitter Description. This can be, and is usually the same as the text in 
															your meta desciption. </span>
														   <?php 
														    $form::label('seo_twitter_desc_en', 'Twitter description (en)');
									                        $form::input('seo_twitter_desc_en', 'text', ['class' => 'form-control']); 

														    $form::label('seo_twitter_desc_fre', 'Twitter description (fre)');
									                        $form::input('seo_twitter_desc_fre', 'text', ['class' => 'form-control']);

															$form::label('seo_twitter_desc_es', 'Twitter description (es)');
									                        $form::input('seo_twitter_desc_es', 'text', ['class' => 'form-control']);
															?>
													    </div>

														<div class="col-sm-12 col-md-12 col-lg-12 form-group">
														    <span class="font-weight-bold section-title">
																<small>You do not really need images for twitter because Twitter's search engine bots will 
																	default to Open Graph (og:metatags) if they cannot find Twitter Cards. However, bear in mind that 
																    an OG image does not fit perfectly within a tweet, so content can get cut off. To be on the safe 
																	side, create a separate image for the Twitter Card image. Twitter Card image dimensions are 
																	1024 by 512 pixels. You would create and place the image e.g. in your direcxtory system and 
																	provide the fully qualified URL to the image here like so:
																	'https://yoursite/assets/images/socials/twitter-page-img.png'	
																	Note: you MUST only provide a secure image path (using 'https') otherwise it will be ignored. 
																</small>
															</span>
															<?php 
															$form::label('seo_twitter_image', 'Twitter Image');
									                        $form::input('seo_twitter_image', 'text', ['class' => 'form-control']); 
															?>
														</div>
							                        </fieldset>
												</div>
											</div>
											<div class="panel-footer">
											    <div class="form-group">
													<?php
									                $form::submit('button', 'Cancel', ['class' => 'btn btn-warning btn-sm', 'href' => $this->controller->config->getFileRootPath().'seo']);
									                $form::submit('submit', 'Create Page', ['class' => 'btn btn-primary btn-sm ml-3']);
													?>
									            </div>		
											</div>		
								        </div>
								    <?php
								    $form::close();
									?>
								</div>
						</div>
					</div>
				</div>
			</section>
			<!-- ==========================
				PAGE CONTENT - END
			=========================== -->
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

