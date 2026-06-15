<?php

namespace Dorguzen\Views\Admin;

class logs_admin_logins extends \Dorguzen\Core\DGZ_AdminHtmlView
{
     function show(array $viewModel = [])
     {
          extract($viewModel); ?>

          <div class="main">
               <section>
                    <div class="py-5">
                         <div class="container">
                              <!-- START SIDE SLIDE-IN MENU -->
                              <?php
                              $jsValidation = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                              $jsValidation->show();
                              ?>
                              <!-- END OF SIDE SLIDE-IN MENU -->

                              <div class="row">
                                   <div class="col-12 d-flex align-items-center justify-content-between mb-3">
                                        <h1 style="color:#000;" class="mb-0">Admin Login Logs</h1>
                                        <?php if ($totalCount > 0) { ?>
                                        <small class="text-muted">
                                             Showing <?= (($pageNum - 1) * $perPage) + 1 ?>–<?= min($pageNum * $perPage, $totalCount) ?> of <?= $totalCount ?> entries
                                        </small>
                                        <?php } ?>
                                   </div>

                                   <div class="col-12 mb-3">
                                        <a class="btn btn-primary" href="<?=$this->controller->config->getFileRootPath()?>admin/dashboard">Return to Dashboard</a>
                                        <a class="btn btn-primary" href="<?=$this->controller->config->getFileRootPath()?>admin/log">
                                             <i class="fa fa-undo"></i> Back to all logs</a>
                                   </div>

                                   <?php if ($logs) {
                                        foreach ($logs as $log) {
                                             $title        = $log['logs_title'];
                                             $message      = $log['logs_message'];
                                             $date_created = date("d-m-Y", strtotime($log['logs_created'])); ?>

                                             <div class="col-md-12 well">
                                                  <div class="card mt-5">
                                                       <div class="card-header">
                                                            <h3>
                                                                 <i class="fa fa-bullhorn section-title-icon"></i>&nbsp;<span
                                                                      class="col-form-label"><b><?= $title ?></b> (<?= $date_created ?>)</span>
                                                            </h3>
                                                       </div>
                                                       <div class="card-body">
                                                            <div class="container bg-light p-2">
                                                                 <div class="row">
                                                                      <div class="col-md-2 col-sm-12">
                                                                           <span class="font-weight-bold section-title">Message:</span>
                                                                      </div>
                                                                      <div class="col-md-10 col-sm-12">
                                                                           <div class="bg-white rounded-top p1-2">
                                                                                <p><?= $message ?></p>
                                                                           </div>
                                                                      </div>
                                                                 </div>
                                                                 <div class="row mt-2">
                                                                      <div class="col-md-2 col-sm-12">
                                                                           <span class="font-weight-bold section-title">Date:</span>
                                                                      </div>
                                                                      <div class="col-md-10 col-sm-12">
                                                                           <small><?= $date_created ?></small>
                                                                      </div>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                        <?php }
                                   } else { ?>
                                        <h3>There are no admin login logs yet</h3>
                                   <?php } ?>

                                   <?php if ($numPages > 1) {
                                        $baseUrl = $this->controller->config->getFileRootPath() . 'admin/log-admin-logins'; ?>
                                   <div class="col-12 mt-4">
                                        <nav aria-label="Admin login pagination">
                                             <ul class="pagination justify-content-center flex-wrap">
                                                  <li class="page-item <?= $isFirstPage ? 'disabled' : '' ?>">
                                                       <a class="page-link" href="<?= $baseUrl ?>?pageno=<?= $pageNum - 1 ?>">&#8249; Prev</a>
                                                  </li>
                                                  <?php for ($p = 1; $p <= $numPages; $p++) { ?>
                                                  <li class="page-item <?= $p === $pageNum ? 'active' : '' ?>">
                                                       <a class="page-link" href="<?= $baseUrl ?>?pageno=<?= $p ?>"><?= $p ?></a>
                                                  </li>
                                                  <?php } ?>
                                                  <li class="page-item <?= $isLastPage ? 'disabled' : '' ?>">
                                                       <a class="page-link" href="<?= $baseUrl ?>?pageno=<?= $pageNum + 1 ?>">Next &#8250;</a>
                                                  </li>
                                             </ul>
                                        </nav>
                                   </div>
                                   <?php } ?>

                              </div>
                         </div>
                    </div>
               </section>
          </div>
     <?php
     }
} ?>
