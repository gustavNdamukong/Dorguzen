<?php

namespace Dorguzen\Views;


use Dorguzen\Core\DGZ_Translator;


/**
 * This class exposes various JS code snippets which you can reuse across many view files in your application.
 * Just use it by pulling it into any view files where u need it. Place the code to include it above in the show()
 * method as it is done in login.php
 *          $jsValidation = \Dorguzen\Core\DGZ_View::getInsideView('jsValidationPartial', $this->controller);
 *           $jsValidation->show();
 *
 * It is essentially a partial (a piece of code that is included), and a good example of how a view can be used
 *  inside of another in Dorguzen
 *
 * Class jsValidationPartial
 * @package views
 */
class sideSlideInMenuPartial extends \Dorguzen\Core\DGZ_HtmlView
{

    public function show()
    { 
        $langClass = new DGZ_Translator();
        $lang = $langClass::getCurrentLang();
        ?>

        <div id="side-menu" class="container side-nav" style="display:none;">

            <div class="d-flex align-items-center justify-content-between px-3 py-2 text-white fw-bold"
                 style="background:var(--site-theme, #3949ab); font-size:1rem;">
                <span>Menu</span>
                <a href="#" class="btn btn-close btn-close-white btn-sm" onclick="closeSlideMenu(event)"></a>
            </div>

                <!----NAV BAR STARTS HERE --->
            <ul class="nav navbar-nav navbar-right list-group">

                <a title="" href="<?=$this->rootPath()?>home" class="list-group-item list-group-item-action"><i class="fa fa-home fa-fw me-2"></i> <?=$langClass->translate($lang, 'menu.php', 'menu-home')?></a>
                <a href="<?=$this->rootPath()?>news" class="list-group-item list-group-item-action"><i class="fa fa-newspaper-o fa-fw me-2"></i> News</a>
                <a href="<?=$this->rootPath()?>portfolio" class="list-group-item list-group-item-action"><i class="fa fa-briefcase fa-fw me-2"></i> Portfolio</a>
                <?php if (config('app.modules.gallery') === 'on'): ?>
                <a href="<?=$this->rootPath()?>gallery" class="list-group-item list-group-item-action"><i class="fa fa-picture-o fa-fw me-2"></i> Gallery</a>
                <?php endif; ?>
                <?php if (config('app.modules.videos') === 'on'): ?>
                <a href="<?=$this->rootPath()?>videos" class="list-group-item list-group-item-action"><i class="fa fa-film fa-fw me-2"></i> Videos</a>
                <?php endif; ?>
                <?php if (config('app.modules.blog') === 'on'): ?>
                <a href="<?=$this->rootPath()?>blog" class="list-group-item list-group-item-action"><i class="fa fa-pencil-square fa-fw me-2"></i> Blog</a>
                <?php endif; ?>
                <a href="<?=$this->rootPath()?>feedback" class="list-group-item list-group-item-action"><i class="fa fa-envelope-o fa-fw me-2"></i> Contact</a>
                <?php
                if (!isset($_SESSION['authenticated'])) { ?>
                    <a href="<?=$this->rootPath()?>auth/login" class="list-group-item list-group-item-action"><i class="fa fa-sign-in fa-fw me-2"></i> Login</a>
                    <?php
                    if (config('app.allow_registration') === true)
                    { ?>
                    <a href="<?=$this->rootPath()?>auth/signup" class="list-group-item list-group-item-action"><i class="fa fa-user-plus fa-fw me-2"></i> Register</a>
                    <?php
                    }
                }
                else
                { ?>
                    <a href="<?=$this->rootPath()?>auth/logout" class="list-group-item list-group-item-action"><i class="fa fa-sign-out fa-fw me-2"></i> Logout</a>
                    <a href="<?=$this->rootPath()?>admin/dashboard" class="list-group-item list-group-item-action"><i class="fa fa-tachometer fa-fw me-2"></i> Dashboard</a>
                    <a href="<?=$this->controller->config->getFileRootPath()?>" class="list-group-item list-group-item-action"><i class="fa fa-external-link fa-fw me-2"></i> Exit Dashboard</a>
                    <?php
                } ?>
            </ul>
        </div>
        
    <?php
    }
}