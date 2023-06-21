<?php

namespace views\admin;

use DGZ_library\DGZ_functions\DateConversion;
use DGZ_library\DGZ_Dates;


class logs_errors_only extends \DGZ_library\DGZ_HtmlView
{

     function show($runtime_error_logs_raw = false, $totalRecs = 0, $max_no_perpage = 10, $no_pages = 1, $pageNum = 1, $first_item_onpage = 1, $last_item_onpage = 10)
     {
          if ((isset($_SESSION['authenticated'])) && ($_SESSION['authenticated'] == 'Let Go-'.$this->controller->config->getConfig()['appName'])) {

               $langClass = new \DGZ_library\DGZ_Translator();
               $lang = $langClass::getCurrentLang();
               $dates = new DGZ_Dates(); ?>

               <div class="main">
                    <section>
                         <div class="py-5">

                              <div class="container">

                                   <!-- START SIDE SLIDE-IN MENU -->
                                   <?php
                                   //Pull in the PHP file that has the JS code that handles all the JS to do with placing an ad
                                   $jsValidation = \DGZ_library\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                                   $jsValidation->show();
                                   ?>
                                   <!-- END OF SIDE SLIDE-IN MENU --> 

                                   <div class="row">
                                   <h1 style="color:#000;" class="text-center">Runtime Error Logs</h1>
                                   <h3>Displaying
                                        <?php //lets show the text 'displaying bla to bla of bla adverts only when the items on a page are less than the total no. of items
                                        if ($first_item_onpage == $totalRecs) { ?>

                                             Viewing <?php echo $first_item_onpage . ' of<b> ' . $totalRecs . ' </b>  error log(s)'; ?>
                                        <?php
                                        }
                                        else { ?>

                                             Viewing <?php echo ' <b>'.$first_item_onpage . '</b> to <b>' . $last_item_onpage . '</b> of<b> ' . $totalRecs . ' </b> error log(s)'; ?>

                                        <?php
                                        } ?>
                                   </h3>

                                   <div class="well">
                                        <a class="btn btn-primary" href="<?=$this->controller->config->getFileRootPath()?>user/dashboard">Return to Dashboard</a>
                                        <a class="btn btn-primary" href="<?=$this->controller->config->getFileRootPath()?>admin/log">
                                             <i class="fa fa-undo"></i> Back to all logs</a>
                                   </div>
                                        <?php
                                        if ($runtime_error_logs_raw) {
                                        $rec_counter = 1;
                                        $runtime_error_logs = array_slice($runtime_error_logs_raw, $first_item_onpage - 1);
                                             foreach ($runtime_error_logs as $log) {
                                             if ($rec_counter <= $max_no_perpage)
                                             {
                                                  $title = $log['logs_title'];
                                                  $message = $log['logs_message'];
                                                  $date_created = date("d-m-Y", strtotime($log['logs_created']));
                                                  ?>

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
                                                  $rec_counter++;
                                             }
                                             } ?>


                                             <!------------------------------------------------------------------------------->
                         <div class="text-center">
                              <ul class="pagination">
                                   <?php
                                   if ($pageNum > 1) {
                                        //$case as used here below is from the switch statement above in category.inc.php wh is sent thru w pagination so every subsequent page knows what catalog ad item we are dealing with atm, n grab it to show
                                        ?>
                                   <li><a aria-label="Previous" href="<?=$this->controller->config->getFileRootPath()?>admin/log-errors-only?pageno=<?=($pageNum - 1)?>"><span aria-hidden="true">&laquo;</span></a></li>
                                   <?php
                                   }


                                   // loop starts here
                                   for ($x = 0; $x < $no_pages; $x++) {
                                   if ($pageNum == $x + 1) {
                                        echo "<li class='active'><a>" . ($x + 1) . '</a></li>';
                                   }
                                   else { ?>
                                   <li><a href="<?=$this->controller->config->getFileRootPath()?>admin/log-errors-only?pageno=<?=($x + 1)?>"><?=($x + 1)?></a></li>
                                   <?php
                                   }
                                   } // loop end

                                   if ($last_item_onpage < $totalRecs) { ?>
                                   <li><a aria-label="Next" href="<?=$this->controller->config->getFileRootPath()?>admin/log-errors-only?pageno=<?=($pageNum + 1)?>"><span>&raquo;</span></a></li>
                                   <?php
                                   } // loop ends here ?>
                                   </ul>
                                   </div>
                              <!------------------------------------------------------------------------------->

                                        <?php
                                        }
                                        else
                                        { ?>
                                             <h3>There are no runtime error logs yet</h3>
                                        <?php
                                        } ?>

                                   </div>

                              </div>

                         </div>
                    </section>
               </div>
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
                             <h3 style="color:red;">Sorry! You have no access to this page 
                                <a href="<?=$this->controller->config->getFileRootPath()?>auth" class="btn btn-info">Login</a>
                                <a href="<?=$this->controller->config->getFileRootPath()?>" class="btn btn-info">Home</a></h3>
                             </div>
                         </div>
                     </div>
                 </section>
             </div>
          <?php
         }
     }
} ?>