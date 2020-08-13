<?php

namespace views;



class adminHome extends \DGZ_library\DGZ_HtmlView
{



function show()
{ ?>



          <!-- ==========================
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
          =========================== -->



     <?php
     if (isset($_SESSION['authenticated'])) {
     ?>


          <!-- ==========================
              PAGE CONTENT - START
          =========================== -->
          <div class="main">
               <?php /*
               <section class="article-text">
               <div class="container">
                    <div class="row">
                         <div class="col-sm-8 col-md-8">
                              <p>Use this admin section of the site to do lots of awesome stuff, like add a new user (or users) and make them admin users to be able to login
                                            and manage newsletters, upload new images to the site, delete old images, and define various other settings.</p>
                              <div class="thumbnail">Upload Videos</div>
                              <div class="thumbnail">Upload Videos</div>
                              <div class="thumbnail">Upload Videos</div>
                              <div class="thumbnail">Upload Videos</div>
                               <!--<blockquote>
                                  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
                                   <small>Clients are talking about us</small>
                               </blockquote>-->
                         </div>
                         <div class="col-sm-4 col-md-4">
                              <div class="service_teaser vertical">
                                   <div class="service_photo">
                                        <figure style="background-image:url(images/bull_dog_engineer.jpg)"></figure>
                                        <img src="images/bull_dog_engineer.jpg" /><!--just added this here, not sure if i should keep it here-->
                                   </div>
                                   <div class="service_details">
                                        <h2 class="section_header skincolored"><b>Is there something you would like to be able to manage on your site which is not available here?</b>
                                            <small>We are constantly working on this admin section of our sites and adding new features. Please suggest any ideas you have and we will work on it and make sure you
                                                have an update of your admin page with the new functionality. Thank you.</small></h2>
                              </div>
                         </div>
                    </div>
               </div>
               </section> */ ?>
               <section class="content account">
                    <div class="container">
                         <div class="row">
                              <div class="col-sm-3">
                                   <aside class="sidebar">

                                        <!-- WIDGET:CATEGORIES - START -->
                                        <div class="widget widget-navigation">
                                             <ul class="nav nav-pills nav-stacked">
                                                  <li class="active"><a href="<?=$this->controller->settings->getFileRootPath()?>admin/adminHome">Account Dashboard</a>
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
                                             <strong>Warning!</strong> You have one unpaid order.
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

                                             <div class="col-xs-6 col-md-3"><a href="<?=$this->controller->settings->getFileRootPath()?>components"><i
                                                           class="fa fa-space-shuttle"></i>Build Components</a></div>

                                             <div class="col-xs-6 col-md-3"><a><i
                                                           class="fa fa-heart"></i> Portfolio</a></div>
                                             <div class="col-xs-6 col-md-3"><a
                                                      href="<?=$this->controller->settings->getFileRootPath()?>admin/manageUsers"><i
                                                           class="fa fa-users"></i>Manage Users</a></div>

                                             <div class="col-xs-6 col-md-3"><a
                                                      href="<?=$this->controller->settings->getFileRootPath()?>admin/contactMessages"><i
                                                           class="fa fa-envelope-o"></i>Contact Messages</a></div>

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