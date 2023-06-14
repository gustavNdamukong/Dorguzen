<?php

namespace views\admin;



class createUser extends \DGZ_library\DGZ_HtmlView
{



     function show()
     { ?>
         <script src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
    <?php
         if ((isset($_SESSION['authenticated'])) && ($_SESSION['authenticated'] == 'Let Go-'.$this->controller->config->getConfig()['appName'])) {
         //Pull in the PHP file that has the JS validation codes
         $jsValidation = \DGZ_library\DGZ_View::getInsideView('jsValidationPartial', $this->controller);
         $jsValidation->show();
         ?>
         <!-- ==========================
         BREADCRUMB - START
         =========================== -->
         <section class="breadcrumb-wrapper">
             <div class="container">
                 <div class="row">
                     <div class="col-xs-6">
                         <h3 class="text-center">Create a user</h3>
                     </div>
                     <div class="col-xs-6">
                         <ol class="breadcrumb">
                             <li><a href="<?=$this->controller->config->getFileRootPath()?>admin/dashboard"><i class="fa fa-home"></i>Dashboard</a></li>
                             <li><a href="<?=$this->controller->config->getFileRootPath()?>admin/manageUsers"><i class="fa fa-user"></i>Manage users</a></li>
                             <li class="active">Create user</li>
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
                                 <div class="col-lg-2"></div>
                                 <div class="form col-lg-8">
                                     <form id="createUserForm" action="<?=$this->controller->config->getFileRootPath()?>admin/doCreateUser" method="post">

                                         <input placeholder="First name" id="new_user_fn" name="new_user_fn" class="form-control" type="text" <?=isset($_SESSION['postBack'])? 'value="' . htmlentities($_SESSION['postBack']['new_user_fn'], ENT_COMPAT, 'UTF-8') . '"':''?> />
                                         <input placeholder="Last name" id="new_user_ln" name="new_user_ln" class="form-control" type="text" <?=isset($_SESSION['postBack'])? 'value="' . htmlentities($_SESSION['postBack']['new_user_ln'], ENT_COMPAT, 'UTF-8') . '"':''?> />
                                         <input placeholder="Email address" id="new_user_un" name="new_user_un" class="form-control" type="text" <?=isset($_SESSION['postBack'])? 'value="' . htmlentities($_SESSION['postBack']['new_user_un'], ENT_COMPAT, 'UTF-8') . '"':''?> />

                                         <input placeholder="Password" id="new_user_pwd" name="new_user_pwd" class="form-control" type="password">
                                         <input placeholder="Confirm password" id="new_user_pwd_confirm" name="new_user_pwd_confirm" class="form-control" type="password">

                                         <input type="reset" class="btn btn-warning btn-sm"
                                                value="Clear entry"/>
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