<?php

namespace views\admin;



class manageSettings extends \DGZ_library\DGZ_HtmlView
{



     function show($baseSettings)
     { ?>
         <script src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
    <?php
         if ((isset($_SESSION['authenticated'])) && ($_SESSION['authenticated'] == 'Let Go-'.$this->controller->settings->getSettings()['appName'])) {
         ?>
         <!-- ==========================
         BREADCRUMB - START
         =========================== -->
         <section class="breadcrumb-wrapper">
             <div class="container">
                 <div class="row">
                     <div class="col-xs-6">
                         <h3 class="text-center">Edit Base Settings</h3>
                     </div>
                     <div class="col-xs-6">
                         <ol class="breadcrumb">
                             <li><a href="<?=$this->controller->settings->getFileRootPath()?>admin/dashboard"><i class="fa fa-home"></i>Dashboard</a></li>
                             <li class="active">Edit settings</li>
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
         <section>
             <div class="container">
                 <div class="row">
                     <div class="col-sm-6 col-md-12">

                         <article class="account-content" style="height: auto;padding-bottom: 50%;">
                             <div style="margin-top: 100px;">
                                 <form id="settings_form" method="post" action="<?=$this->controller->settings->getFileRootPath()?>admin/baseSettings?change=1"><!--THIS IS WHERE THE JS FUNCTIONS ARE CALLED WHEN THE FORM IS SUBMITTED. You put it inside the opening <form> tag, its a listening function 'onSubmit',
                                                                                                                                            but then u use it as an attribute with = symbols, and obviously the action to be taken in quotes. validate(this) where validate(form) is the function to be run, while this refers to the current object(form).-->


                                     <div id="regis_panel" class="panel panel-primary">

                                         <div class="panel-heading">

                                             <h3 class="panel-title text-center" style="color: #FFFFFF;">SETTINGS</h3>

                                         </div>

                                         <div class="panel-body">

                                             <?php
                                                 if ($baseSettings) {
                                                     foreach ($baseSettings as $key => $val) {
                                                         if ($val['settings_name'] == 'show_brand_slider') { ?>
                                                             <label for="show_brand_slider">Activate brand slider</label>
                                                             <select id="show_brand_slider" name="show_brand_slider" class="form-control">
                                                                 <option
                                                                     value="true" <?= $val['settings_value'] == 'true' ? 'selected="true"' : '' ?>>
                                                                     Yes
                                                                 </option>
                                                                 <option
                                                                     value="false" <?= $val['settings_value'] == 'false' ? 'selected="true"' : '' ?>>
                                                                     No
                                                                 </option>

                                                             </select>
                                                             <hr/>
                                                             <?php
                                                         }


                                                         if ($val['settings_name'] == 'default_language') { ?>
                                                             <label for="default_language">Choose your app's default language</label>
                                                             <select id="default_language" name="default_language" class="form-control">
                                                                 <option
                                                                     value="en" <?= $val['settings_value'] == 'en' ? 'selected="true"' : '' ?>>
                                                                     English
                                                                 </option>
                                                                 <option
                                                                     value="fre" <?= $val['settings_value'] == 'fre' ? 'selected="true"' : '' ?>>
                                                                     French
                                                                 </option>
                                                                 <option
                                                                     value="nl" <?= $val['settings_value'] == 'nl' ? 'selected="true"' : '' ?>>
                                                                     Dutch
                                                                 </option>
                                                             </select>
                                                             <hr/>
                                                             <?php
                                                         }

                                                         if ($val['settings_name'] == 'app_color_theme') { ?>
                                                             <label for="app_color_theme">Choose your app's color theme</label>
                                                             <select id="app_color_theme" name="app_color_theme" class="form-control">
                                                                 <option
                                                                     value="blue" <?= $val['settings_value'] == 'blue' ? 'selected="true"' : '' ?>>
                                                                     Blue
                                                                 </option>
                                                                 <option
                                                                     value="brown" <?= $val['settings_value'] == 'brown' ? 'selected="true"' : '' ?>>
                                                                     Brown
                                                                 </option>
                                                                 <option
                                                                     value="dark-blue" <?= $val['settings_value'] == 'dark-blue' ? 'selected="true"' : '' ?>>
                                                                     Dark-blue
                                                                 </option>
                                                                 <option
                                                                     value="dark-red" <?= $val['settings_value'] == 'dark-red' ? 'selected="true"' : '' ?>>
                                                                     Dark-red
                                                                 </option>
                                                                 <option
                                                                     value="green" <?= $val['settings_value'] == 'green' ? 'selected="true"' : '' ?>>
                                                                     Green
                                                                 </option>
                                                                 <option
                                                                     value="light-green" <?= $val['settings_value'] == 'light-green' ? 'selected="true"' : '' ?>>
                                                                     Light-green
                                                                 </option>
                                                                 <option
                                                                     value="orange" <?= $val['settings_value'] == 'orange' ? 'selected="true"' : '' ?>>
                                                                     Orange
                                                                 </option>
                                                                 <option
                                                                     value="purple" <?= $val['settings_value'] == 'purple' ? 'selected="true"' : '' ?>>
                                                                     Purple
                                                                 </option>
                                                                 <option
                                                                     value="red" <?= $val['settings_value'] == 'red' ? 'selected="true"' : '' ?>>
                                                                     Red
                                                                 </option>
                                                                 <option
                                                                     value="yellow" <?= $val['settings_value'] == 'yellow' ? 'selected="true"' : '' ?>>
                                                                     Yellow
                                                                 </option>
                                                             </select>
                                                             <hr/>
                                                             <?php
                                                         }

                                                         if ($val['settings_name'] == 'allow_registration') { ?>
                                                             <label for="allow_registration">Allow registration</label>
                                                             <select id="allow_registration" name="allow_registration" class="form-control">
                                                                 <option
                                                                         value="true" <?= $val['settings_value'] == 'true' ? 'selected="true"' : '' ?>>
                                                                     Yes
                                                                 </option>
                                                                 <option
                                                                         value="false" <?= $val['settings_value'] == 'false' ? 'selected="true"' : '' ?>>
                                                                     No
                                                                 </option>

                                                             </select>
                                                             <hr/>
                                                             <?php
                                                         }
                                                     }
                                                 } ?>
                                         </div><!--END OF BODY OF PANEL HOLDING REGIS FORM-->

                                         <div class="panel-footer clearfix">

                                             <div class="pull-right">
                                                 <a href="<?=$this->controller->settings->getFileRootPath()?>admin/dashboard" class="btn btn-warning" role="button">Cancel</a>
                                                 <input type="submit" name="" class="btn btn-primary" value="Save changes" />

                                             </div><!--end of div holding form button(s)-->

                                         </div><!--end of panel footer-->
                                     </div><!--END OF PANEL-->

                                 </form>
                             </div>
                         </article>

                     </div>
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
                                 <h3 style="color:red;">Sorry! You have no access to this page</h3>
                             </div>
                         </div>
                     </div>
                 </section>
             </div>
             <?php
         }
     }

} ?>