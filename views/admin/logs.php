<?php

namespace views\admin;

use DGZ_library\DGZ_functions\DateConversion;
use DGZ_library\DGZ_Dates;


class logs extends \DGZ_library\DGZ_HtmlView
{

     function show($logs)
     {
          $langClass = new \DGZ_library\DGZ_Translator();
          $lang = $langClass::getCurrentLang();
          $dates = new DGZ_Dates(); ?>

          <!-- START SIDE SLIDE-IN MENU -->
          <?php
          //Pull in the PHP file that has the JS code that handles all the JS to do with placing an ad
          $jsValidation = \DGZ_library\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
          $jsValidation->show();
          ?>
          <!-- END OF SIDE SLIDE-IN MENU -->  

          <!-- ==========================
    	     BREADCRUMB - START
		=========================== -->
          <div class="container-xxl py-5 bg-primary hero-header mb-5">
                 <div class="container my-5 py-5 px-lg-5">
                     <div class="row g-5 py-5">
                         <div class="col-12 text-center">
                             <h1 class="text-white animated zoomIn">System Logs</h1>
                             <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                             <nav aria-label="breadcrumb">
                                 <ol class="breadcrumb justify-content-center">
                                     <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath()?>">Home</a></li>
                                     <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath()?>admin/dashboard">Dashboard</a></li>
                                     <li class="breadcrumb-item text-white active" aria-current="page">Logs</li>
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

          <div class="main">
               <section class="content account" style="margin-top: 100px;">
                    <div class="container">
                         <a class="btn btn-primary" href="<?=$this->controller->config->getFileRootPath()?>admin/dashboard">Return to Dashboard</a>
                         <a class="btn btn-primary" href="<?=$this->controller->config->getFileRootPath()?>admin/log-errors-only">
                              <i class="fa fa-eye"></i> Runtime errors only</a>
                         <?php
                         if ((isset($_SESSION['authenticated'])) && ($_SESSION['user_type'] == 'super_admin')) { ?>
                              <a class="btn btn-primary" href="<?=$this->controller->config->getFileRootPath()?>admin/log-admin-logins">
                         <i class="fa fa-eye"></i> Admin logins</a>
                         <?php
                         } ?>
                    </div>
               </section>

               <section>
                    <div class="py-5">
                         <div class="container">

                              <div class="row">
                                  <h2 style="color:#000;" class="text-center">General Log Feed</h2>
                                   <?php
                                   if ($logs) {
                                        foreach ($logs as $log) {
                                             $title = $log['logs_title'];
                                             $message = $log['logs_message'];
                                             $date_created = date("d-m-Y", strtotime($log['logs_created'])); ?>

                                             <div class="col-md-12 well">
                                                  <div class="card mt-5">
                                                       <div class="card-header">
                                                            <h3>
                                                                 <i class="fa fa-bullhorn section-title-icon"></i>&nbsp;<span
                                                                        class="col-form-label"><b><?= $title ?></b> (<?=$date_created?>)</span>
                                                            </h3>
                                                       </div>

                                                       <div class="card-body">
                                                            <div class="container bg-light p-2">
                                                                 <div class="row">
                                                                      <div class="col-md-2 col-sm-12">
                                                                           <span class="font-weight-bold section-title"> Message:</span>
                                                                      </div>

                                                                      <div class="col-md-10 col-sm-12">
                                                                           <div id="policy-number"
                                                                                class="bg-white rounded-top p1-2">
                                                                                <p><?= $message ?></p>
                                                                           </div>
                                                                      </div>
                                                                 </div>
                                                                 <div class="row">
                                                                      <div class="col-md-6 col-sm-12">
                                                                           <div class="row">
                                                                                <div class="col-md-4 col-sm-12">
                                                                                     <span
                                                                                         class="font-weight-bold section-title">Date:</span>
                                                                                </div>

                                                                                <div class="col-md-8 col-sm-12">
                                                                                     <div id="policy-number"
                                                                                          class="bg-white rounded-top p1-2">
                                                                                          <small><?= $date_created ?></small>
                                                                                     </div>
                                                                                </div>
                                                                           </div>
                                                                      </div>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                             <?php
                                        }
                                   }
                                   else
                                   { ?>
                                        <h3>There are no logs yet</h3>     
                                   <?php
                                   } ?>
                              </div>
                         </div>
                    </div>
               </section>
          </div>
          <?php
     }

} ?>