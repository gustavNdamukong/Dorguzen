<?php

namespace Dorguzen\Views\Admin;



class createUser extends \Dorguzen\Core\DGZ_AdminHtmlView
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

         <!-- ==========================
             PAGE CONTENT - START
         =========================== -->
         <style>
             .cu-card {
                 background: #fff;
                 border-radius: 16px;
                 box-shadow: 0 4px 24px rgba(0,0,0,.08);
                 padding: 36px 40px 32px;
                 max-width: 620px;
                 margin: 0 auto 60px;
             }
             .cu-card h2 {
                 font-size: 1.4rem;
                 font-weight: 700;
                 color: #1a1a1a;
                 text-align: center;
                 margin-bottom: 28px;
                 padding-bottom: 16px;
                 border-bottom: 2px solid #f0f0f0;
             }
             .cu-card h2 span {
                 display: block;
                 font-size: .8rem;
                 font-weight: 400;
                 color: #6c757d;
                 margin-top: 4px;
             }
             .cu-group { margin-bottom: 18px; }
             .cu-group label {
                 display: block;
                 font-size: .82rem;
                 font-weight: 600;
                 text-transform: uppercase;
                 letter-spacing: .4px;
                 color: #495057;
                 margin-bottom: 6px;
             }
             .cu-group label .cu-optional {
                 font-weight: 400;
                 text-transform: none;
                 color: #adb5bd;
                 font-size: .78rem;
                 letter-spacing: 0;
             }
             .cu-group .form-control {
                 border-radius: 8px;
                 border-color: #dee2e6;
                 padding: 10px 14px;
                 font-size: .92rem;
                 transition: border-color .15s, box-shadow .15s;
             }
             .cu-group .form-control:focus {
                 border-color: var(--site-theme, #fd7e14);
                 box-shadow: 0 0 0 3px rgba(253,126,20,.15);
             }
             .cu-divider {
                 border: none;
                 border-top: 1px solid #f0f0f0;
                 margin: 24px 0;
             }
             .cu-actions {
                 display: flex;
                 gap: 10px;
                 justify-content: flex-end;
                 margin-top: 28px;
             }
             .cu-actions .btn { border-radius: 8px; padding: 9px 24px; font-size: .92rem; }
         </style>

         <section>
             <div class="container">
                 <div class="row">
                     <div class="col-sm-12 col-md-12">

                        <!-- START SIDE SLIDE-IN MENU -->
                        <?php
                        $slideInMenu = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                        $slideInMenu->show();
                        ?>
                        <!-- END OF SIDE SLIDE-IN MENU -->

                        <div class="cu-card">
                            <h2>Create New User
                                <span>Fill in the details below to add a user to the system</span>
                            </h2>

                            <form id="createUserForm" action="<?=$this->controller->config->getFileRootPath()?>admin/doCreateUser" method="post">
                                <input type="hidden" name="_csrf_token" value="<?=getCsrfToken()?>">

                                <!-- Role -->
                                <div class="cu-group">
                                    <label for="new_user_type">User Role</label>
                                    <select id="new_user_type" name="new_user_type" class="form-control"
                                        <?= ($_SESSION['user_type'] != 'super_admin') ? "disabled title='Only a super admin can change user types'" : '' ?>>
                                        <option value="">Choose a role…</option>
                                        <option value="member">Member</option>
                                        <option value="admin">Admin</option>
                                        <option value="admin_gen">General Admin</option>
                                        <option value="superadmin">Super Admin</option>
                                    </select>
                                </div>

                                <hr class="cu-divider">

                                <!-- Name row -->
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="cu-group">
                                            <label for="new_user_fn">First Name</label>
                                            <input id="new_user_fn" name="new_user_fn" type="text"
                                                class="form-control" placeholder="e.g. Jane"
                                                value="<?= isset($_SESSION['postBack']) ? htmlentities($_SESSION['postBack']['new_user_fn'], ENT_COMPAT, 'UTF-8') : '' ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="cu-group">
                                            <label for="new_user_ln">Last Name</label>
                                            <input id="new_user_ln" name="new_user_ln" type="text"
                                                class="form-control" placeholder="e.g. Smith"
                                                value="<?= isset($_SESSION['postBack']) ? htmlentities($_SESSION['postBack']['new_user_ln'], ENT_COMPAT, 'UTF-8') : '' ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="cu-group">
                                    <label for="new_user_un">Email Address <span id="info"></span></label>
                                    <input id="new_user_un" name="new_user_un" type="text"
                                        class="form-control" placeholder="e.g. jane@example.com"
                                        value="<?= isset($_SESSION['postBack']) ? htmlentities($_SESSION['postBack']['new_user_un'], ENT_COMPAT, 'UTF-8') : '' ?>">
                                </div>

                                <!-- Phone -->
                                <div class="cu-group">
                                    <label for="new_user_phone">Phone Number <span class="cu-optional">(optional)</span></label>
                                    <input id="new_user_phone" name="new_user_phone" type="text"
                                        class="form-control" placeholder="e.g. +1 555 000 1234">
                                </div>

                                <hr class="cu-divider">

                                <!-- Password -->
                                <div class="cu-group">
                                    <label for="new_user_pwd">Password</label>
                                    <input id="new_user_pwd" name="new_user_pwd" type="password"
                                        class="form-control" placeholder="Min. 8 characters">
                                </div>

                                <div class="cu-group">
                                    <label for="new_user_pwd_confirm">Confirm Password</label>
                                    <input id="new_user_pwd_confirm" name="new_user_pwd_confirm" type="password"
                                        class="form-control" placeholder="Repeat password">
                                </div>

                                <div class="cu-actions">
                                    <a href="<?= $this->controller->config->getFileRootPath() ?>admin/manageUsers" class="btn btn-outline-secondary">Cancel</a>
                                    <button type="reset" class="btn btn-outline-secondary">Clear</button>
                                    <button type="submit" id="createUser" class="btn btn-primary">Create User</button>
                                </div>
                            </form>
                        </div>

                     </div>
                 </div>
            </div>
        </section>
    <?php
     }

} ?>
