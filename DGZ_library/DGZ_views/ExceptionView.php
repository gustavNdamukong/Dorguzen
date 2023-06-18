<?php

namespace DGZ_library\DGZ_views;

use configs\Config;
use controllers\ExceptionController;

/**
 * Description of ExceptionView
 *
 * @author Gustav Ndamukong
 */
class ExceptionView extends \DGZ_library\DGZ_View {
	
	public function show($errorMessage = '') 
	{
		$config = new Config();
        $controller = new ExceptionController();
        $translator = $controller->translator;
        $lang = $controller->translator->getCurrentLang();
		?>

		<!-- Hero Header Start -->
		<div class="container-xxl py-5 bg-primary hero-header mb-5">
			<div class="container my-5 py-5 px-lg-5">
				<div class="row g-5 py-5">
					<div class="col-12 text-center">
						<h1 class="text-white animated zoomIn">Exception</h1>
						<hr class="bg-white mx-auto mt-0" style="width: 90px;">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb justify-content-center">
								<li class="breadcrumb-item"><a class="text-white" href="<?= $config->getFileRootPath() ?>">Home</a></li>
								<li class="breadcrumb-item"><a class="text-white" href="<?= $config->getFileRootPath() ?>auth">Login</a></li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
        </div>
        <!-- Hero Header End --> 

		<div class="main">
            <section class="content account" style="margin-top: 100px;">
                <div class="container">

					<!-- START SIDE SLIDE-IN MENU -->
                    <?php
                    //Pull in the PHP file that has the JS code that handles all the JS to do with placing an ad
                    $slideInMenu = \DGZ_library\DGZ_View::getInsideView('sideSlideInMenuPartial', $controller);
                    $slideInMenu->show();
                    ?>
                    <!-- END OF SIDE SLIDE-IN MENU --> 

                    <div class="row">
                        <div class="jumbotron">
                            <div class="well">

		

        <!--<div class="container-xxl py-5">
            <div class="container px-lg-5">
                <div class="row justify-content-center">
                    <div class="col-lg-7">
                        <div class="wow fadeInUp" data-wow-delay="0.3s">-->

							<h3 style="color: goldenrod;text-align:center;">404</h3>

							<h1><i style="background-color:red;color:white;font-size:3em;font-weight:bold;border-radius:3px;"
									class="fa fa-exclamation-triangle"></i></h1>
							<h3 class="animated bounceInDown">
								<?=$translator->translate($lang, 'errors.php', 'oops')?> <strong>
									<?=$translator->translate($lang, 'errors.php', 'something')?>
								</strong> <?=$translator->translate($lang, 'errors.php', 'went-wrong')?></h3>
							<h3><?=$translator->translate($lang, 'errors.php', 'heading1')?></h3>
							<h3><?=$translator->translate($lang, 'errors.php', 'heading2')?></h3>
							<h3><?=$translator->translate($lang, 'errors.php', 'thankYou')?></h3>

							<a href="<?=$config->getFileRootPath()?>" class="btn btn-outline-info btn-lg">
								<?=$translator->translate($lang, 'menu.php', 'menu-home')?>
							</a>
							<a href="<?=$config->getFileRootPath()?>user/dashboard" class="btn btn-outline-info btn-lg">
								<?=$translator->translate($lang, 'menu.php', 'menu-dashboard')?>
							</a>
							<br>
							<?php
							if($config->getConfig()['live'] == false)
							{
								// Display anything here that you don't want to be shown on live
								?>
								<h3>The following message is only shown locally</h3>
								<?php
								if ($errorMessage != '')
								{                                        
									if (is_array($errorMessage))
									{
										foreach ($errorMessage as $msg)
										{
											var_dump($errorMessage);
										}
									}
									else
									{
										echo "<p>$errorMessage</p>";
									}
								}	
							} ?>

                            <div style="clear:both;"></div>
                            <p style="color:blue;font-weight:bold;"><?=$translator->translate($lang, 'errors.php', 'contactUsText')?>:</p>
                            <a target="_blank" class="btn btn-primary btn-lg" href="<?=$config->getFileRootPath()?>feedback/"><i class="fa fa-envelope">
                                    <?=$translator->translate($lang, 'menu.php', 'menu-contactUs')?></i></a>
							</div>
						</div>
                    </div>
                </div>
			</section>
		</div>
	<?php
	}	
}
