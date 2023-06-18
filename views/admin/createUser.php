<?php

namespace views\admin;



class createUser extends \DGZ_library\DGZ_HtmlView
{



     function show()
     { ?>

         <!-- ==========================
         BREADCRUMB - START
         =========================== -->
         <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                        <div class="col-12 text-center">
                            <h1 class="text-white animated zoomIn">Create user</h1>
                            <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-content-center">
                                    <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>"><i class="fa fa-home"></i> Home</a></li>
                                    <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>admin/dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a class="text-white" href="<?=$this->controller->config->getFileRootPath()?>admin/manageUsers"><i class="fa fa-user"></i> Manage users</a></li>
                                    <li class="breadcrumb-item text-white active" aria-current="page">Create User</li>
                                </ol>
                            </nav>
                        </div>
                </div>
            </div>
        </div>
         <!-- ==========================
             BREADCRUMB - END
         =========================== -->

         <script src="http://code.jquery.com/jquery-latest.min.js"></script>
         <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
         <?php
         if ((isset($_SESSION['authenticated'])) && ($_SESSION['authenticated'] == 'Let Go-'.$this->controller->config->getConfig()['appName'])) {
         //Pull in the PHP file that has the JS validation codes
         //$jsValidation = \DGZ_library\DGZ_View::getInsideView('jsCreateUserValidationPartial', $this->controller);
         //$jsValidation->show();
         ?>

         <!-- ==========================
             PAGE CONTENT - START
         =========================== -->
         <section>
             <div class="container">
                 <div class="row">
                     <div class="col-sm-12 col-md-12">
                        
                        <!-- START SIDE SLIDE-IN MENU -->
                        <?php
                        //Pull in the PHP file that has the JS code that handles all the JS to do with placing an ad
                        $slideInMenu = \DGZ_library\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                        $slideInMenu->show();
                        ?>
                        <!-- END OF SIDE SLIDE-IN MENU --> 

                        <div class="row">
                            <div class="col-lg-2"></div>
                            <div class="form col-md-10 col-lg--10">
                                <form id="createUserForm" action="<?=$this->controller->config->getFileRootPath()?>admin/doCreateUser" method="post">

                                <label for="user_type">User Type</label>
                                    <select id="new_user_type" name="new_user_type" class="form-control" <?=($_SESSION['user_type'] != 'super_admin') ? "disabled='true' title='Only a super admin can change user types'":''?>>
                                    <option value="">Choose user type</option>
                                    <option value="member">Member</option>
                                    <option value="admin">Admin</option>
                                    <option value="admin_gen">Admin Gen (general admin)</option>
                                    <option value="superadmin">Super admin</option>
                                    </select>

                                    <input placeholder="First name" id="new_user_fn" name="new_user_fn" class="form-control" type="text" <?=isset($_SESSION['postBack'])? 'value="' . htmlentities($_SESSION['postBack']['new_user_fn'], ENT_COMPAT, 'UTF-8') . '"':''?> />
                                    <input placeholder="Last name" id="new_user_ln" name="new_user_ln" class="form-control" type="text" <?=isset($_SESSION['postBack'])? 'value="' . htmlentities($_SESSION['postBack']['new_user_ln'], ENT_COMPAT, 'UTF-8') . '"':''?> />
                                    <label for="new_user_un">Email</label><span id='info'></span></p>
                                    <input placeholder="Email address" id="new_user_un" name="new_user_un" 
                                    class="form-control" 
                                    type="text" <?=isset($_SESSION['postBack'])? 'value="' . htmlentities($_SESSION['postBack']['new_user_un'], ENT_COMPAT, 'UTF-8') . '"':''?> />

                                    <input placeholder="Phone number (Optional)" id="new_user_phone" name="new_user_phone" class="form-control" type="text">
                                    <input placeholder="Password" id="new_user_pwd" name="new_user_pwd" class="form-control" type="password">
                                    <input placeholder="Confirm password" id="new_user_pwd_confirm" name="new_user_pwd_confirm" class="form-control" type="password">

                                    <button type="reset" class="btn btn-warning btn-sm">Clear entry</button>
                                    <button type="submit" id="createUser" class="btn btn-primary btn-sm">Submit</button>
                                </form>

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
                                   <h3 style="color:red;">Sorry! You have no access to this page <a href="<?=$this->controller->config->getFileRootPath()?>auth" class="btn btn-default">Login</a><a href="<?=$this->controller->config->getFileRootPath()?>home" class="btn btn-default">Home</a></h3>
                              </div>
                         </div>
                    </div>
               </section>
          </div>
        <?php
         }
     }

} ?>