<?php

namespace views\admin;

use DGZ_library\DGZ_functions\DateConversion;
use DGZ_library\DGZ_Dates;


class logs_admin_logins extends \DGZ_library\DGZ_HtmlView
{

     function show($adminLoginData)
     {

          $langClass = new \DGZ_library\DGZ_Translator();
          $lang = $langClass::getCurrentLang();
          $dates = new DGZ_Dates(); ?>

          <div class="main">
               <section>
                    <div class="py-5">

                         <div class="container">

                              <div class="row">
                                  <h1 style="color:#000;" class="text-center">Admin Login Logs</h1>
                                  <div class="well">
                                      <a class="btn btn-primary" href="<?=$this->controller->config->getFileRootPath()?>admin/dashboard">Return to Dashboard</a>
                                      <a class="btn btn-primary" href="<?=$this->controller->config->getFileRootPath()?>admin/log">
                                          <i class="fa fa-undo"></i> Back to all logs</a>
                                  </div>
                                   <?php
                                   if ($adminLoginData) {
                                        foreach ($adminLoginData as $log) {

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
                                        <h3>There are no admin login logs yet</h3>
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