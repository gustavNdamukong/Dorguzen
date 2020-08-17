<?php

namespace views\admin;



class manageSettings extends \DGZ_library\DGZ_HtmlView
{



     function show($baseSettings)
     { ?>
         <script src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
    <?php
         if (isset($_SESSION['authenticated'])) {
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
                             <li><a href="<?=$this->controller->settings->getFileRootPath()?>admin/adminHome"><i class="fa fa-home"></i>Dashboard</a></li>
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


                                                         if ($val['settings_name'] == 'brand_slider_source') { ?>
                                                             <label for="brand_slider_source">Brand slider source (If gallery, please ensure you have a
                                                                 gallery album named <b>brand_slider</b>)</label>
                                                             <select id="brand_slider_source" name="brand_slider_source" class="form-control" onchange="alert('Please be aware that if you choose gallery, you must have a gallery album called brand_slider')">
                                                                 <option
                                                                     value="gallery" <?= $val['settings_value'] == 'gallery' ? 'selected="true"' : '' ?>>
                                                                     Gallery
                                                                 </option>
                                                             </select>
                                                             <hr />
                                                             <?php
                                                         }

                                                         if ($val['settings_name'] == 'default_language') { ?>
                                                             <label for="default_language">Choose your app's default language</label>
                                                             <select id="default_language" name="default_language" class="form-control">
                                                                 <option
                                                                     value="english" <?= $val['settings_value'] == 'english' ? 'selected="true"' : '' ?>>
                                                                     English
                                                                 </option>
                                                                 <option
                                                                     value="french" <?= $val['settings_value'] == 'french' ? 'selected="true"' : '' ?>>
                                                                     French
                                                                 </option>
                                                                 <option
                                                                     value="spanish" <?= $val['settings_value'] == 'spanish' ? 'selected="true"' : '' ?>>
                                                                     Spanish
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
                                                 <a href="<?=$this->controller->settings->getFileRootPath()?>admin/adminHome" class="btn btn-warning" role="button">Cancel</a>
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