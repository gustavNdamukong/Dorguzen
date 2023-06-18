<?php

namespace DGZ_library\DGZ_views;


use configs\Config;
use controllers\ExceptionController;


/**
 * Displays a DGZ_library/Exception in HTML format
 *
 * @author Gustav Ndamukong
 */
class DGZExceptionView  extends \DGZ_library\DGZ_View {
	


	public function show($exceptions = []) {
		$config = new Config();
        $controller = new ExceptionController();
		$langClass = new \DGZ_library\DGZ_Translator();
        $lang = $langClass::getCurrentLang(); 
		?>
	
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
                        <div class="jumbotron" style="">
                            <div class="well" style="">
                                <h3>Sorry, there was an error</h3>
								<?php
								if ($exceptions)
								{ ?>
									<ul>
									<?php
									foreach ($exceptions as $exception)
									{ ?>
										<li><?=$exception?></li>
									<?php
									} ?>
									</ul>
								<?php
								} ?>
                                <h3><?=$langClass->translate($lang, 'errors.php', 'sorry-about-this')?>:
                                    <i><small>The error was logged and we will fix it ASAP</small></i></h3>
                                <a href="<?=$config->getFileRootPath()?>" class="btn btn-outline-info btn-lg">
                                    <?=$langClass->translate($lang, 'menu.php', 'menu-home')?>
                                </a>
                                <a href="<?=$config->getFileRootPath()?>admin/dashboard" class="btn btn-outline-info btn-lg">
                                    <?=$langClass->translate($lang, 'menu.php', 'menu-dashboard')?>
                                </a>
                                <a target="_blank" class="btn btn-primary btn-lg" href="<?=$config->getFileRootPath()?>mall/">
                                    <i class="fa fa-building"> <?=$langClass->translate($lang, 'menu.php', 'menu-mall')?></i></a>

                                <?php
                                if($config->getConfig()['live'] == true)
                                {
                                    if ((isset($_SESSION['authenticated'])) &&
                                        (in_array($_SESSION['user_type'], ['admin', 'admin_gen', 'super_admin']))
                                    ) { ?>
                                        <hr style="border:solid 1px goldenrod;">
                                        <h3><?=$langClass->translate($lang, 'errors.php', 'only-you-can-see')?></h3>

                                        <h3><b style="color:goldenrod;"><?=$langClass->translate($lang, 'errors.php', 'click-below-to-view-logs')?></b></h3>
                                        <a class="btn btn-primary" href="<?=$config->getFileRootPath()?>admin/log">
                                            <i class="fa fa-eye"></i> <?=$langClass->translate($lang, 'errors.php', 'view-logs')?></a>
                                    <?php
                                    }
                                }
                                else
                                { ?>
                                    <hr style="border:solid 1px goldenrod;">
                                    <h3><?=$langClass->translate($lang, 'errors.php', 'not-shown-on-live')?></h3>

                                    <h3><b style="color:goldenrod;"><?=$langClass->translate($lang, 'errors.php', 'click-below-to-view-logs')?></b></h3>
                                    <a class="btn btn-primary" href="<?=$config->getFileRootPath()?>admin/log" target="_blank">
                                        <i class="fa fa-eye"></i> <?=$langClass->translate($lang, 'errors.php', 'view-logs')?></a>
                                <?php
                                } ?>
                                <div style="clear:both;"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        </div>

	<?php
	
	}
}
