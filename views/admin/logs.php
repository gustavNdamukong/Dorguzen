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

          <div class="main">
               <section class="content account" style="margin-top: 100px;">
                    <div class="container">
                         <div class="row">
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