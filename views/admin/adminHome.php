<?php

namespace views\admin;



class adminHome extends \DGZ_library\DGZ_HtmlView
{



function show()
{ ?>



          <?php /*<!-- ==========================
         BREADCRUMB - START
         =========================== -->
          <section class="breadcrumb-wrapper">
               <div class="container">
                    <div class="row">
                         <div class="col-xs-6">
                              <h3 class="text-center">Admin Dashboard</h3>
                         </div>
                         <div class="col-xs-6">
                              <ol class="breadcrumb">
                                   <li title="Go back to website"><a href="<?=$this->controller->settings->getFileRootPath()?>Home/home">Exit Dashboard</a></li>
                                   <li class="active">Dashboard</li>
                              </ol>
                         </div>
                    </div>
               </div>
          </section>
          <!-- ==========================
              BREADCRUMB - END
          =========================== -->*/ ?>

     <!-- Hero Header Start -->
     <div class="container-xxl py-5 bg-primary hero-header mb-5">
          <div class="container my-5 py-5 px-lg-5">
               <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                         <h1 class="text-white animated zoomIn">Dashboard</h1>
                         <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                         <nav aria-label="breadcrumb">
                              <ol class="breadcrumb justify-content-center">
                                   <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->settings->getFileRootPath() ?>">Home</a></li>
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
     if ((isset($_SESSION['authenticated'])) && ($_SESSION['authenticated'] == 'Let Go-'.$this->controller->settings->getSettings()['appName'])) {
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

                                        <!-- WIDGET:CATEGORIES - START -->
                                        <div class="widget widget-navigation">
                                             <ul class="nav nav-pills nav-stacked">
                                                  <li class="active"><a href="<?=$this->controller->settings->getFileRootPath()?>admin/dashboard">Account Dashboard</a>
                                                  </li>
                                                  <li><a href="<?=$this->controller->settings->getFileRootPath()?>home/home" title="Go to the home page">Exit Dashboard</a></li>
                                                  <li><a href="<?=$this->controller->settings->getFileRootPath()?>admin/logout" title="This will take you back to the home page">Logout</a></li>
                                             </ul>
                                        </div>
                                        <!-- WIDGET:CATEGORIES - END -->

                                   </aside>
                              </div>




                              <div class="col-sm-9">
                                   <article class="account-content">
                                        <!--USE THIS WIDGET BELOW TO DISPLAY ANY DISMISSABLE NOTIFICATION - GET RID OF IT IF YOU DON'T NEED IT-->
                                        <div class="alert alert-warning alert-dismissible" role="alert">
                                             <button type="button" class="close" data-dismiss="alert"
                                                     aria-label="Close"><i class="fa fa-times"></i></button>
                                             <strong>Warning!</strong> You can display an alert to the user here.
                                        </div>

                                        <h3>Welcome <span><?= $_SESSION['first_name'] ?></span></h3>
                                        <p>This is where you can manage everything on your site, like uploading images, uploading videos, manage your blog or news page,
                                             manage user accounts, and much more.</p>
                                        <div class="icon-nav row">
                                             <div class="col-xs-6 col-md-3"><a><i
                                                           class="fa fa-newspaper-o"></i> Manage News</a></div>
                                             <div class="col-xs-6 col-md-3"><a><i
                                                           class="fa fa-video-camera"></i> Manage Videos</a></div>
                                             <div class="col-xs-6 col-md-3"><a><i
                                                           class="fa fa-envelope-o"></i> Newsletters</a></div>
                                             <div class="col-xs-6 col-md-3"><a><i
                                                           class="fa fa-thumbs-up"></i> Manage Testimonials</a></div>
                                             <div class="col-xs-6 col-md-3"><a href="<?=$this->controller->settings->getFileRootPath()?>admin/baseSettings?change=0"><i
                                                           class="fa fa-gears"></i> Settings</a></div>
                                             <div class="col-xs-6 col-md-3"><a><i
                                                           class="fa fa-heart"></i> Portfolio</a></div>
                                             <div class="col-xs-6 col-md-3"><a
                                                      href="<?=$this->controller->settings->getFileRootPath()?>admin/manageUsers"><i
                                                           class="fa fa-users"></i>Manage Users</a></div>

                                             <div class="col-xs-6 col-md-3"><a
                                                      href="<?=$this->controller->settings->getFileRootPath()?>admin/contactMessages"><i
                                                           class="fa fa-envelope-o"></i>Contact Messages</a></div>


                                             <div class="card dashboard-cards" style="width: 12rem;">
                                                  <a
                                                      href="<?=$this->controller->settings->getFileRootPath()?>admin/contactMessages">
                                                  <div class="card-body">

                                                            <h5 class="card-title text-center" style="background:darkblue;border-radius:4px;color:#fff;font-weight:bold;"><i
                                                                     class="fa fa-envelope-o"></i></h5>
                                                            <p class="card-text">Contact Messages</p>
                                                            <!--<a href="#" class="btn btn-primary">Go somewhere</a>-->
                                                  </div>
                                                  </a>
                                             </div>


                                             <div class="col-xs-6 col-md-3"><a
                                                      href="<?=$this->controller->settings->getFileRootPath()?>admin/adminUserChangePw?userId=<?=$_SESSION['custo_id']?>&change=0"><i
                                                           class="fa fa-shield"></i>Change Password</a></div>
                                             <div class="col-xs-6 col-md-3"><a><i
                                                           class="fa fa-cc-diners-club"></i> Manage Blog</a></div>

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
                                   <h3 style="color:red;">Sorry! You have no access to this page <a href="<?=$this->controller->settings->getFileRootPath()?>admin" class="btn btn-default">Login</a><a href="<?=$this->controller->settings->getFileRootPath()?>home" class="btn btn-default">Home</a></h3>
                              </div>
                         </div>
                    </div>
               </section>
          </div>
     <?php
     }
}

} ?>