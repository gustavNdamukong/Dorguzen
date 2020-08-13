<?php

namespace views;



class editUser extends \DGZ_library\DGZ_HtmlView
{



     function show($user, $userId)
     { ?>
         <script src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
    <?php
         if (isset($_SESSION['authenticated'])) {
         //Pull in the PHP file that has the JS validation codes
         //$jsValidation = \DGZ_library\DGZ_View::getView('jsValidationPartial', $this->page);
         //$jsValidation->show();

         ?>
         <!-- ==========================
         BREADCRUMB - START
         =========================== -->
         <section class="breadcrumb-wrapper">
             <div class="container">
                 <div class="row">
                     <div class="col-xs-6">
                         <h3 class="text-center">Edit user</h3>
                     </div>
                     <div class="col-xs-6">
                         <ol class="breadcrumb">
                             <li><a href="<?=$this->controller->settings->getFileRootPath()?>admin/adminHome"><i class="fa fa-home"></i>Dashboard</a></li>
                             <li><a href="<?=$this->controller->settings->getFileRootPath()?>admin/manageUsers"><i class="fa fa-user"></i>Manage users</a></li>
                             <li class="active">Edit user</li>
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
                                     <form id="editUserForm" action="<?=$this->controller->settings->getFileRootPath()?>admin/editUser?edit=1'?>" method="post">

                                         <input placeholder="First name" id="new_user_fn" name="new_user_fn" class="form-control" type="text" value="<?=$user[0]['users_first_name']?>" />
                                         <input placeholder="Last name" id="new_user_ln" name="new_user_ln" class="form-control" type="text" value="<?=$user[0]['users_last_name']?>" />
                                         <input placeholder="Username" id="new_user_un" name="new_user_un" class="form-control" type="text" value="<?=$user[0]['users_email']?>" />

                                         <input placeholder="Password" id="new_user_pwd" name="new_user_pwd" class="form-control" type="text" value="<?=$user[0]['pass']?>">

                                         <input type="hidden" name="userId" value="<?=$userId?>" />

                                         <a href="<?=$this->controller->settings->getFileRootPath()?>admin/manageUsers" class="btn btn-warning btn-sm" id="cancel" >Cancel</a>
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