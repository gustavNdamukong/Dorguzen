<?php

namespace views;




use controllers\FeedbackController;

class contact extends \DGZ_library\DGZ_HtmlView
{


	function show()
	{
		$this->addMetadata(
			[
				'<meta name="description" content="The DorguzApp (replace this with your application name) contact us page. Send us a quick message and we will get back 
					to you as soon as possible.">',
				'<meta name="keywords" content="DorguzApp (replace this with your application name) contact form, get quote, more information.">',
				'<meta name="author" content="Your name here">'
			]);
		?>

		<!-- ==========================
        BREADCRUMB - START
		=========================== -->
		<section class="breadcrumb-wrapper" xmlns="http://www.w3.org/1999/html">
			<div class="container">
				<div class="row">
					<div class="col-xs-6">
						<h3 class="text-center">Contact Us</h3>
					</div>
					<div class="col-xs-6">
						<ol class="breadcrumb">
							<li><a href="<?=$this->controller->config->getFileRootPath()?>home/home"><i class="fa fa-home"></i>Home</a></li>
							<li class="active">Contact us</li>
						</ol>
					</div>
				</div>
			</div>
		</section>
		<!-- ==========================
            BREADCRUMB - END
        =========================== -->



		<!-- ==========================
            PAGE CONTENT - START
        =========================== -->
		<section class="content news">
			<div class="container">
				<h2 class="hidden">Contact</h2>
				<div class="row">


					<!------------------------------------
                    GOOGLE MAPS START
                    -------------------------------------->
					<div class="col-md-12">
						<div class="attachment">
							<!--REPLACE THE MAP URL BELOW WITH YOUR OWN GOOGLE MAPS URL-->
							<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2482.0536078009377!2d-0.09573408401648491!3d51.530576579638975!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x48761b5f583930e5%3A0x5a7e590f7325e26f!2s20-22%20Wenlock%20Rd%2C%20Hoxton%2C%20London%20N1%207GU!5e0!3m2!1sen!2suk!4v1594140277944!5m2!1sen!2suk" width="100%" height="400" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
							</iframe>
						</div>
					</div>
					<!------------------------------------
                    GOOGLE MAPS END
                    -------------------------------------->





					<!------------------------------------
                    MAIN PAGE SECTION START
                    -------------------------------------->
					<div class="col-sm-9" style="margin-top: 200px;">
						<h2>Let us give you a quote!</h2>

						<form action="<?=$this->controller->config->getFileRootPath()?>feedback/processContact" method="post">
							<div class="row">
								<div class="col-md-6">
									<label class="label-control" for="name">Name</label>
									<input type="text" id="name" name="name" class="form-control" required />
								</div>
								<div class="col-md-6">
									<label class="label-control" for="email">Email</label>
									<input type="email" id="email" name="email" class="form-control" required />
								</div>
								<!--</div>-->

								<div class="col-md-6">
									<label class="label-control" for="subject">Phone number</label>
									<input type="text" class="form-control" id="phone" name="phone" />
								</div>

								<div class="col-md-6">
									<label class="label-control" for="message">Tell us more about your requirement</label>
									<textarea class="form-control" id="message" name="message" rows="8"></textarea>
								</div>

								<div class="col-md-6" style="margin-top:10px;">
									<label></label>
									<input type="submit" id='sendMessage' name="sendMessage" value="Get Quote" class="btn btn-primary" />
								</div>
							</div>
						</form>

					</div>
					<!------------------------------------
                    MAIN PAGE END
                    -------------------------------------->


					<!------------------------------------
                    START OF SIDEBAR
                    -------------------------------------->
					<div class="col-sm-3">
						<aside class="sidebar right">

							<!-- WIDGET: LATEST NEWS - START -->
							<div class="widget widget-post-categories">
								<h3 style="color: #FFF; font-weight: bold;" class="text-center">Contact Info</h3>
								<p>Phone: +447111222333</p>
								<p><strong><a href="mailto:dorguzen@dorguzen.com">Send us an email</a></strong></p>






								<hr />
							</div>
							<!-- WIDGET: LATEST NEWS - END -->


							<!-- WIDGET: FACEBOOK NEWS - START -->
							<div>
								<h3>Facebook</h3>
								<div class="widget widget-facebook-news">
									<div class="fb-page" data-height="800" data-href="https://www.facebook.com/yourAppName" data-tabs="timeline" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true">
										<blockquote cite="https://www.facebook.com/yourAppName/yourCity" class="fb-xfbml-parse-ignore">
											<a href="https://www.facebook.com/yourAppName">Facebook</a>
										</blockquote>
									</div>
								</div>
							</div>
							<!-- WIDGET: FACEBOOK NEWS - END -->



							<!-- WIDGET: FACEBOOK NEWS - START -->
							<div>
								<h3>Twitter</h3>
								<div class="widget" id="twitter-news">
									<a class="twitter-timeline" data-width="265" data-height="800" href="https://twitter.com/yourAppName">Tweets by yourAppName</a>
									<script async src="http://platform.twitter.com/widgets.js" charset="utf-8"></script>
								</div>
							</div>
							<!-- WIDGET: FACEBOOK NEWS - END -->



						</aside>
					</div><!--END OF COL-SM-3 DIV CONTAINING THE SIDEBAR 'ASIDE' ELEMENT-->
				</div><!--END OF ROW DIV, THE FIRST ELEMENT INSIDE THE CONTAINER DIV - WH WRAPS AROUND, OR IS FOLLOWED (INSIDE OF IT) BY THE COL-SM-9 DIV THAT HOLDS THE MAIN BODY OF THE PAGE-->
			</div><!--END OF CONTAINER DIV-->
		</section>
		<!-- ==========================
            BLOG - END
        =========================== -->
		<?php
	}
}

