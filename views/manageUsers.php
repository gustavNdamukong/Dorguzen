<?php

namespace views;




class manageUsers extends \DGZ_library\DGZ_HtmlView
{



     function show()
     { ?>
          <script src="http://code.jquery.com/jquery-latest.min.js"></script>
          <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

          <?php
          //$langClass = new \DGZ_library\DGZ_Translator();
          //$lang = $langClass::getCurrentLang();
          if (isset($_SESSION['authenticated'])) {


               $users = new \Users();
               $allUsers = $users->getAll();
               $numOfAllUsers = $users->getCount();

               //get the count of super admin users
               $superAdminUserCounter = 0;
               foreach($allUsers as $user) { ?>
                    <?php if ($user['users_type'] == 'super_admin') {
                         $superAdminUserCounter++;
                    }
               }

               //get the count of general admin users
               $adminGenUserCounter = 0;
               foreach($allUsers as $user) { ?>
                    <?php if ($user['users_type'] == 'admin_gen') {
                         $adminGenUserCounter++;
                    }
               }

               //get the count of admin users
               $adminUserCounter = 0;
               foreach($allUsers as $user) { ?>
                    <?php if ($user['users_type'] == 'admin') {
                         $adminUserCounter++;
                    }
               }

               //get the count of regular users (members)
               $userCounter = 0;
               foreach($allUsers as $user) { ?>
                    <?php if ($user['users_type'] == 'member') {
                         $userCounter++;
                    }
               }

               $dateClass = new \DGZ_library\DGZ_Dates();
          ?>
               <!-- ==========================
               BREADCRUMB - START
               =========================== -->
               <section class="breadcrumb-wrapper">
                    <div class="container">
                         <div class="row">
                              <div class="col-xs-6">
                                   <h3 class="text-center">Manage Users</h3>
                              </div>
                              <div class="col-xs-6">
                                   <ol class="breadcrumb">
                                        <li><a href="<?=$this->controller->settings->getFileRootPath()?>admin/adminHome"><i class="fa fa-home"></i>Dashboard</a></li>
                                        <li class="active">Manage users</li>
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

          <div class="main">
               <section class="content account" style="margin-top: 100px;">
                    <div class="container">
                         <div class="row">

                              <div class="jumbotron">

                                   <div class="well" style="text-align: center; color: white; background: black;"><h3>Manage Users</h3>
                                        <a href='<?=$this->controller->settings->getFileRootPath()?>admin/adminHome' class="btn btn-lg btn-primary pull-right">Return to Dashboard</a>
                                        <div style="clear:both;"></div>
                                   </div>

                                   <div class="well" style="text-align: center; color: white; background: black;"><h3>Total Users</h3>
                                        <p class="badge"><span><?=$numOfAllUsers?></span></p>
                                        <div style="clear:both;"></div>
                                   </div>
                                   <?php if ($_SESSION['user_type'] == 'admin_gen') { ?>
                                        <a href='<?=$this->controller->settings->getFileRootPath()?>admin/createUser'
                                           class="btn btn-lg btn-primary">Create new User</a>
                                        <?php
                                   }
                                   else
                                   { ?>
                                        <a class="btn btn-lg btn-primary" title="Youn dont have permission create a user" disabled>Create new User</a>
                                   <?php
                                   } ?>
                                   <h4>General Admin Users <span class="badge"><?=$adminGenUserCounter?></span></h4>
                                   <div class="table-responsive border">
                                        <table class="table table-bordered">
                                             <tr>
                                                  <th class="col-xs-2">First Name</th>
                                                  <th class="col-xs-2">Last Name</th>
                                                  <th class="col-xs-2">Username</th>
                                                  <th class="col-xs-5">Created</th>
                                                  <th class="col-xs-1">Edit User</th>
                                             </tr>
                                             <?php
                                             foreach($allUsers as $user) { ?>
                                                  <?php if ($user['users_type'] == 'admin_gen') { ?>
                                                       <tr>
                                                            <td>
                                                                 <a><?= $user['users_first_name'] ?></a>
                                                            </td>
                                                            <td><?= $user['users_last_name'] ?></td>
                                                            <td class="hidden-xs"><?= $user['users_email'] ?></td>
                                                            <td><?=$dateClass->YYYYMMDDtoDDMMYYYY($user['users_created']) ?></td>
                                                            <td><?php
                                                                 if ($_SESSION['user_type'] == 'super_admin')
                                                                 { ?>
                                                                      <a href="<?=$this->controller->settings->getFileRootPath()?>user/editUser?userId=<?=$user['users_id']?>"
                                                                         class="btn btn-primary btn-sm">Edit</a>
                                                                      <a onclick="return confirm('Are you sure you wish to delete this user?')" href="<?=$this->controller->settings->getFileRootPath()?>admin/deleteUser?userId=<?=$user['users_id']?>"
                                                                         class="btn btn-danger btn-sm">Delete</a>
                                                                 <?php
                                                                 }
                                                                 elseif($_SESSION['user_type'] == 'admin_gen') { ?>
                                                                      <a class="btn btn-primary btn-sm" title="You cannot edit yourself from here" disabled>Edit</a>
                                                                      <a class="btn btn-danger btn-sm" title="You cannot delete yourself" disabled>Delete</a>
                                                                      <?php
                                                                 } ?>
                                                            </td>
                                                       </tr>
                                                       <?php
                                                  }
                                             } ?>

                                             <?php
                                             if ($adminGenUserCounter == 0)
                                             {
                                                  echo '<tr style="color:green;"><td  colspan="5">There are no General Admin Users</td></tr>';
                                             } ?>
                                        </table>
                                   </div>





                                   <h4>Admin Users <span class="badge"><?=$adminUserCounter?></span></h4>
                                   <div class="table-responsive border">
                                        <table class="table table-bordered">
                                             <tr>
                                                  <th class="col-xs-2">First name</th>
                                                  <th class="col-xs-2">Last Name</th>
                                                  <th class="col-xs-2">Username</th>
                                                  <th class="col-xs-2">Created</th>
                                                  <th class="col-xs-1">Edit User Type</th>
                                             </tr>
                                             <?php
                                             foreach($allUsers as $user) { ?>
                                                  <?php if ($user['users_type'] == 'admin') { ?>
                                                       <tr>
                                                            <td>
                                                                 <a href="single-order.html"><?= $user['users_first_name'] ?></a>
                                                            </td>
                                                            <td><?= $user['users_last_name'] ?></td>
                                                            <td class="hidden-xs"><?= $user['users_email'] ?></td>
                                                            <td class="hidden-xs"><?= $dateClass->YYYYMMDDtoDDMMYYYY($user['users_created']) ?></td>
                                                            <td>
                                                                 <?php
                                                                 if ($_SESSION['user_type'] == 'super_admin' || $_SESSION['user_type'] == 'admin_gen')
                                                                 { ?>
                                                                      <a href="<?=$this->controller->settings->getFileRootPath()?>admin/editUser?userId=<?=$user['users_id']?>&edit=0"
                                                                         class="btn btn-primary btn-sm">Edit</a>
                                                                      <a onclick="return confirm('Are you sure you wish to delete this user?')" href="<?=$this->controller->settings->getFileRootPath()?>admin/deleteUser?userId=<?=$user['users_id']?>"
                                                                         class="btn btn-danger btn-sm">Delete</a>
                                                                 <?php
                                                                 }
                                                                 else { ?>
                                                                      <a class="btn btn-primary btn-sm" title="You dont have permission to edit" disabled>Edit</a>
                                                                      <a class="btn btn-danger btn-sm" title="You dont have permission to delete" disabled>Delete</a>
                                                                      <?php
                                                                 } ?>
                                                            </td>
                                                       </tr>
                                                       <?php
                                                  }
                                             } ?>

                                             <?php
                                             if ($adminUserCounter == 0)
                                             {
                                                  echo '<tr style="color:green;"><td  colspan="5">There are no Admin Users</td></tr>';
                                             } ?>
                                        </table>
                                   </div>

                                   <?php /*if ($userCounter == 0)
                                   {
                                        echo '<p style="color:green;">There are no Member Users</p>';
                                   } */?>
                              </div>

                         </div>
                    </div>
               </section>
          </div>
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