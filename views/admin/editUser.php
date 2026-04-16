<?php

namespace Dorguzen\Views\Admin;



class editUser extends \Dorguzen\Core\DGZ_AdminHtmlView
{
     function show(array $viewModel = [])
     {
          extract($viewModel); ?>
         <script src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

         <!-- ==========================
         BREADCRUMB - START
         =========================== -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                        <div class="col-12 text-center">
                            <h1 class="text-white animated zoomIn">Edit user</h1>
                            <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-content-center">
                                    <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>">Home</a></li>
                                    <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>admin/dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item text-white active" aria-current="page">Edit user</li>
                                </ol>
                            </nav>
                        </div>
                </div>
            </div>
        </div>
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

                        <!-- START SIDE SLIDE-IN MENU -->
                        <?php
                        //Pull in the PHP file that has the JS code that handles all the JS to do with placing an ad
                        $jsValidation = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                        $jsValidation->show();
                        ?>
                        <!-- END OF SIDE SLIDE-IN MENU --> 

                        <div class="row">
                            <div class="col-lg-2"></div>
                            <div class="form col-lg-8">
                                <form id="editUserForm" action="<?=$this->controller->config->getFileRootPath()?>admin/editUser?edit=1" method="post">
                                    <input type="hidden" name="_csrf_token" value="<?=getCsrfToken()?>">
                                    <label for="user_type">User Type (<small>Only a super admin can change user types</small>)</label>
                                    <select id="new_user_type" name="new_user_type" class="form-control" <?=($_SESSION['user_type'] != 'super_admin') ? "disabled='true' title='Only a super admin can change user types'":''?>>
                                    <option value="">Choose user type</option>
                                    <option <?=($user['users_type'] == 'member')?"selected='true'":''?> value="member">Member</option>
                                    <option <?=($user['users_type'] == 'admin')?"selected='true'":''?> value="admin">Admin</option>
                                    <option <?=($user['users_type'] == 'admin_gen')?"selected='true'":''?> value="admin_gen">Admin Gen (general admin)</option>
                                    <option <?=($user['users_type'] == 'superadmin')?"selected='true'":''?> value="superadmin">Super admin</option>
                                    </select>

                                    <input placeholder="First name" id="new_user_fn" name="new_user_fn" class="form-control" type="text" value="<?=$user['users_first_name']?>" />
                                    <input placeholder="Last name" id="new_user_ln" name="new_user_ln" class="form-control" type="text" value="<?=$user['users_last_name']?>" />
                                    <input placeholder="Username" id="new_user_un" name="new_user_un" class="form-control" type="text" value="<?=$user['users_email']?>" />
                                    <input placeholder="Phone number" id="new_user_phone" name="new_user_phone" class="form-control" type="text" value="<?=$user['users_phone_number']?>">
                                    <input placeholder="Password" id="new_user_pwd" name="new_user_pwd" class="form-control" type="text" value="<?=$user['pass']?>">

                                    <input type="hidden" name="userId" value="<?=$userId?>" />

                                    <a href="<?=$this->controller->config->getFileRootPath()?>admin/manageUsers" class="btn btn-warning btn-sm" id="cancel" >Cancel</a>
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
} ?>