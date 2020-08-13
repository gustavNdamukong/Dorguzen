<?php

namespace views;



class adminUserChangePw extends \DGZ_library\DGZ_HtmlView
{



     function show($user, $userId)
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
                         <h3 class="text-center">Change your email & password</h3>
                     </div>
                     <div class="col-xs-6">
                         <ol class="breadcrumb">
                             <li><a href="<?=$this->controller->settings->getFileRootPath()?>admin/adminHome"><i class="fa fa-home"></i>Dashboard</a></li>
                             <li><a href="<?=$this->controller->settings->getFileRootPath()?>admin/manageUsers"><i class="fa fa-user"></i>Manage users</a></li>
                             <li class="active">Change password</li>
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
                         <div>
                             <div class="row">
                                 <h4><?php //if ($econfirm) { echo "Congratulations, your account has been activated, you may now login"; }?></h4>
                                 <br />




                                 <div class="col-lg-2"></div>
                                 <div class="form col-lg-8">
                                     <form id="editUserForm" action="<?=$this->controller->settings->getFileRootPath()?>admin/adminUserChangePw?change=1'?>" method="post">

                                         <input placeholder="Username" id="new_user_un" name="new_user_un" class="form-control" type="text" value="<?=$user[0]['users_email']?>" />

                                         <input placeholder="Password" id="new_user_pwd" name="new_user_pwd" class="form-control" type="text" value="<?=$user[0]['pass']?>">

                                         <input type="hidden" name="userId" value="<?=$userId?>" />

                                         <a href="<?=$this->controller->settings->getFileRootPath()?>admin/adminHome" class="btn btn-warning btn-sm" id="cancel" >Cancel</a>
                                         <button type="submit" id="createUser" class="btn btn-primary btn-sm">Submit</button>
                                     </form>

                                 </div>
                             </div>
                         </div>
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