<?php

namespace Dorguzen\Views\Admin;



class manageSettings extends \Dorguzen\Core\DGZ_AdminHtmlView
{
     function show(array $viewModel = [])
     {
          extract($viewModel); ?>
         <script src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

         <!-- ==========================
         Hero Header & Breadcrumb - START
         =========================== -->
         <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn">Edit Base Settings</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?=$this->controller->config->getFileRootPath()?>admin/dashboard"><i class="fa fa-home"></i>Dashboard</a></li>
                                <li class="breadcrumb-item text-white active" aria-current="page">Edit settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- ==========================
             Hero Header & Breadcrumb - End
        =========================== -->



         <!-- ==========================
             PAGE CONTENT - START
         =========================== -->
         <section>
             <div class="container">
                 <div class="row">

                    <!-- START SIDE SLIDE-IN MENU -->
                    <?php
                    //Pull in the PHP file that has the JS code that handles all the JS to do with placing an ad
                    $jsValidation = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                    $jsValidation->show();
                    ?>
                    <!-- END OF SIDE SLIDE-IN MENU -->

                     <div class="col-sm-12 col-md-12">

                         <article class="account-content" style="height: auto;padding-bottom: 50%;">
                             <div style="margin-top: 100px;">
                                 <form id="settings_form" method="post" action="<?=$this->controller->config->getFileRootPath()?>admin/baseSettings?change=1">
                                     <input type="hidden" name="_csrf_token" value="<?=getCsrfToken()?>">
                                     <div id="regis_panel" class="panel panel-primary">

                                         <div class="panel-heading">
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

                                                     }
                                                 } ?>
                                         </div><!--END OF BODY OF PANEL HOLDING REGIS FORM-->

                                         <div class="panel-footer clearfix">

                                             <div class="pull-right">
                                                 <a href="<?=$this->controller->config->getFileRootPath()?>admin/dashboard" class="btn btn-warning" role="button">Cancel</a>
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
} ?>