<?php

namespace views;


use DGZ_library\DGZ_Translator;


/**
 * This class exposes various JS code snippets which you can reuse across many view files in your application.
 * Just use it by pulling it into any view files where u need it. Place the code to include it above in the show()
 * method as it is done in login.php
 *          $jsValidation = \DGZ_library\DGZ_View::getInsideView('jsValidationPartial', $this->controller);
 *           $jsValidation->show();
 *
 * It is essentially a partial (a piece of code that is included), and a good example of how a view can be used
 *  inside of another in Dorguzen
 *
 * Class jsValidationPartial
 * @package views
 */
class sideSlideInMenuPartial extends \DGZ_library\DGZ_HtmlView
{

    public function show()
    { 
        $langClass = new DGZ_Translator();
        $lang = $langClass::getCurrentLang();
        ?>

        <div id="side-menu" class="container side-nav" style="display:none;">

            <a href="#" class="btn btn-close btn-sm" 
                onclick="closeSlideMenu(event)"><i class="fa fa-times" aria-hidden="true"></i>
            </a>

                <!----NAV BAR STARTS HERE --->
            <ul class="nav navbar-nav navbar-right list-group">
                <li class="title"><?=$langClass->translate($lang, 'menu.php', 'menu-headingGroup-menu')?></li>

                <a title="" href="<?=$this->rootPath()?>home" class="list-group-item list-group-item-action">
                    <i class="fa fa-home"></i> <?=$langClass->translate($lang, 'menu.php', 'menu-home')?>
                </a>
                <a href="<?=$this->rootPath()?>feedback" class="list-group-item list-group-item-action">Contact</a>
                <?php
                if (!isset($_SESSION['authenticated'])) { ?>
                    <a href="<?=$this->rootPath()?>auth/login" class="list-group-item list-group-item-action">Login</a>
                    <?php
                    $config = new \configs\Config();
                    if ($config->getConfig()['allow_registration'] === true)
                    { ?>
                    <a href="<?=$this->rootPath()?>auth/signup" type="button" class="list-group-item list-group-item-action">Register</a>
                    <?php
                    }
                }
                else
                { ?>
                    <a href="<?=$this->rootPath()?>auth/logout" class="list-group-item list-group-item-action">Logout</a>
                    <a href="<?=$this->rootPath()?>admin/dashboard" class="list-group-item list-group-item-action">Dashboard</a>
                    <a href="<?=$this->controller->config->getFileRootPath()?>" class="list-group-item list-group-item-action">Exit Dashboard</a>
                    <?php
                } ?>
            </ul>
        </div>
        
    <?php
    }
}