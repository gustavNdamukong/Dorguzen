<?php

namespace views\admin;

use Users;

class adminHome extends \DGZ_library\DGZ_HtmlView
{



function show()
{ 
     $users = new Users(); 
     ?>

     <!-- Hero Header Start -->
     <div class="container-xxl py-5 bg-primary hero-header mb-5">
          <div class="container my-5 py-5 px-lg-5">
               <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                         <h1 class="text-white animated zoomIn">Dashboard</h1>
                         <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                         <nav aria-label="breadcrumb">
                              <ol class="breadcrumb justify-content-center">
                                   <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>">Home</a></li>
                                   <li class="breadcrumb-item text-white active" aria-current="page">Dashboard</li>
                              </ol>
                         </nav>
                    </div>
               </div>
          </div>
     </div>
     </div>
     <!-- Hero Header End -->



     <?php
     if ((isset($_SESSION['authenticated'])) && ($_SESSION['authenticated'] == 'Let Go-'.$this->controller->config->getConfig()['appName'])) {
     ?>


          <!-- ==========================
              PAGE CONTENT - START
          =========================== -->
          <div class="main">
               <section class="content account bg-white">
                    <div class="container">
                         <div class="row">
                              <div class="col-sm-3">
                                   <aside class="sidebar">
                                        <!-- WIDGET:SIDEBAR - START -->
                                        <div class="card" style="background-color:#4777F5;">
                                             <div class="card-body">
                                             <h5 class="card-title text-white">Quick links</h5>
                                             <div class="list-group">
                                                  <a href="<?=$this->controller->config->getFileRootPath()?>admin/dashboard" class="list-group-item list-group-item-action">Account Dashboard</a>
                                                  <a href="<?=$this->controller->config->getFileRootPath()?>" class="list-group-item list-group-item-action">Exit Dashboard</a>
                                                  <a href="<?=$this->controller->config->getFileRootPath()?>admin/logout" class="list-group-item list-group-item-action">Logout</a>
                                             </div>
                                             </div>
                                        </div>
                                        <!-- WIDGET:SIDEBAR - END -->
                                   </aside>
                              </div>

                              <div class="col-sm-9">
                                   <article class="account-content">

                                        <!-- START SIDE SLIDE-IN MENU -->
                                        <?php
                                        //Pull in the PHP file that has the JS code that handles all the JS to do with placing an ad
                                        $jsValidation = \DGZ_library\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                                        $jsValidation->show();
                                        ?>
                                        <!-- END OF SIDE SLIDE-IN MENU -->

                                        <h3>Welcome <span><?= $_SESSION['first_name'] ?></span></h3>
                                        <p>This is where you can manage everything on your site, like uploading images, uploading videos, manage your blog or news page,
                                             manage user accounts, and much more.</p>
                                        <div class="icon-nav row">

                                             <div class="col-xs-6 col-md-3 card dashboard-cards">
                                                  <a
                                                  href="<?=$this->controller->config->getFileRootPath()?>admin/adminUserChangePw?userId=<?=$_SESSION['custo_id']?>&change=0">
                                                       <div class="card-body">
                                                            <h5 class="card-title text-center"><i
                                                                 class="fa fa-shield dash-icons"></i></h5>
                                                            <p class="card-text">Change Password</p>
                                                       </div>
                                                  </a>     
                                             </div>

                                             <?php
                                             if ($users->isAdmin($_SESSION['custo_id']))
                                             { ?>

                                                  <div class="col-xs-6 col-md-3 card dashboard-cards">
                                                       <a
                                                       href="#">
                                                            <div class="card-body">
                                                                 <h5 class="card-title text-center"><i
                                                                      class="fa fa-newspaper-o dash-icons"></i></h5>
                                                                 <p class="card-text">News</p>
                                                            </div>
                                                       </a>     
                                                  </div>

                                                  <div class="col-xs-6 col-md-3 card dashboard-cards">
                                                       <a
                                                       href="#">
                                                            <div class="card-body">
                                                                 <h5 class="card-title text-center"><i
                                                                      class="fa fa-video-camera dash-icons"></i></h5>
                                                                 <p class="card-text">Videos</p>
                                                            </div>
                                                       </a>     
                                                  </div>

                                                  <div class="col-xs-6 col-md-3 card dashboard-cards">
                                                       <a
                                                       href="#">
                                                            <div class="card-body">
                                                                 <h5 class="card-title text-center"><i
                                                                      class="fa fa-envelope-o dash-icons"></i></h5>
                                                                 <p class="card-text">Newsletters</p>
                                                            </div>
                                                       </a>     
                                                  </div>

                                                  <div class="col-xs-6 col-md-3 card dashboard-cards">
                                                       <a
                                                       href="#">
                                                            <div class="card-body">
                                                                 <h5 class="card-title text-center"><i
                                                                      class="fa fa-thumbs-up dash-icons"></i></h5>
                                                                 <p class="card-text">Manage Testimonials</p>
                                                            </div>
                                                       </a>     
                                                  </div>

                                                  <div class="col-xs-6 col-md-3 card dashboard-cards">
                                                       <a
                                                       href="<?=$this->controller->config->getFileRootPath()?>admin/baseSettings?change=0">
                                                            <div class="card-body">
                                                                 <h5 class="card-title text-center"><i
                                                                      class="fa fa-gears dash-icons"></i></h5>
                                                                 <p class="card-text">Settings</p>
                                                            </div>
                                                       </a>     
                                                  </div>

                                                  <?php
                                                  if ((isset($_SESSION['authenticated'])) && ($users->isAdmin($_SESSION['custo_id']))) { ?>
                                                       <div class="col-xs-6 col-md-3 card dashboard-cards">
                                                            <a
                                                            href="<?=$this->controller->config->getFileRootPath()?>admin/log">
                                                                 <div class="card-body">
                                                                      <h5 class="card-title text-center"><i
                                                                           class="fa fa-eye dash-icons"></i></h5>
                                                                      <p class="card-text">System logs</p>
                                                                 </div>
                                                            </a>     
                                                       </div>
                                                  <?php
                                                  } ?>

                                                  <div class="col-xs-6 col-md-3 card dashboard-cards">
                                                       <a
                                                       href="#">
                                                            <div class="card-body">
                                                                 <h5 class="card-title text-center"><i
                                                                      class="fa fa-list dash-icons"></i></h5>
                                                                 <p class="card-text">Portfolio</p>
                                                            </div>
                                                       </a>     
                                                  </div>

                                                  <div class="col-xs-6 col-md-3 card dashboard-cards">
                                                       <a
                                                       href="<?=$this->controller->config->getFileRootPath()?>admin/manageUsers">
                                                            <div class="card-body">
                                                                 <h5 class="card-title text-center"><i
                                                                      class="fa fa-users dash-icons"></i></h5>
                                                                 <p class="card-text">Manage Users</p>
                                                            </div>
                                                       </a>     
                                                  </div>

                                                  <div class="col-xs-6 col-md-3 card dashboard-cards">
                                                       <a
                                                       href="<?=$this->controller->config->getFileRootPath()?>admin/contactMessages">
                                                            <div class="card-body">
                                                                 <h5 class="card-title text-center"><i
                                                                      class="fa fa-envelope-o dash-icons"></i></h5>
                                                                 <p class="card-text">Messages</p>
                                                            </div>
                                                       </a>     
                                                  </div>

                                                  <div class="col-xs-6 col-md-3 card dashboard-cards">
                                                       <a
                                                       href="#">
                                                            <div class="card-body">
                                                                 <h5 class="card-title text-center"><i
                                                                      class="fa fa-cc-diners-club dash-icons"></i></h5>
                                                                 <p class="card-text">Manage Blog</p>
                                                            </div>
                                                       </a>     
                                                  </div>

                                                  <hr />
                                                  <h2>Modules</h2>
                                                  <div class="icon-nav row">
                                                       <div class="col-xs-6 col-md-3 card dashboard-cards">
                                                            <a
                                                            href="<?=$this->controller->config->getFileRootPath()?>seo">
                                                                 <div class="card-body">
                                                                      <h5 class="card-title text-center"><i
                                                                           class="fa fa-bolt"></i></h5>
                                                                      <p class="card-text">Typhoon SEO</p>
                                                                 </div>
                                                            </a>     
                                                       </div>
                                                       <div class="col-xs-6 col-md-3 card dashboard-cards">
                                                            <a
                                                            href="<?=$this->controller->config->getFileRootPath()?>payments">
                                                                 <div class="card-body">
                                                                      <h5 class="card-title text-center"><i
                                                                           class="fa fa-money"></i></h5>
                                                                      <p class="card-text">Payments</p>
                                                                 </div>
                                                            </a>     
                                                       </div>
                                                  </div>
                                             <?php
                                             } ?>          
                                        </div>
                                   </article>
                              </div>
                         </div>
                    </div>
               </section>

          </div>
          <!-- ==========================
              PAGE CONTENT - END
          =========================== -->
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
                                   <a href="<?=$this->controller->config->getFileRootPath()?>auth" class="btn btn-default">Login</a>
                                   <a href="<?=$this->controller->config->getFileRootPath()?>home" class="btn btn-default">Home</a></h3>
                              </div>
                         </div>
                    </div>
               </section>
          </div>
     <?php
     }
}

} ?>