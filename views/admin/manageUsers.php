<?php

namespace views\admin;


use Users;

class manageUsers extends \DGZ_library\DGZ_HtmlView
{



     function show()
     { ?>
          <script src="http://code.jquery.com/jquery-latest.min.js"></script>
          <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

          <?php
          if ((isset($_SESSION['authenticated'])) && ($_SESSION['authenticated'] == 'Let Go-'.$this->controller->config->getConfig()['appName'])) {


               $users = new Users();
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
               Hero Header & Breadcrumb - START
               =========================== -->
               <div class="container-xxl py-5 bg-primary hero-header mb-5">
                         <div class="container my-5 py-5 px-lg-5">
                              <div class="row g-5 py-5">
                                   <div class="col-12 text-center">
                                   <h1 class="text-white animated zoomIn">Manage Users</h1>
                                   <hr class="bg-white mx-auto mt-0" style="width: 90px;">

                                   <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb justify-content-center">
                                             <li class="breadcrumb-item"><a class="text-white" href="<?=$this->controller->config->getFileRootPath()?>admin/dashboard"><i class="fa fa-home"></i>Dashboard</a></li>
                                             <li class="breadcrumb-item text-white active" aria-current="page">Manage Users</li>
                                        </ol>
                                   </nav>
                                   </div>
                              </div>
                         </div>
                    </div>
                    </div>
               <!-- ==========================
                    Hero Header & Breadcrumb - End
               =========================== -->



               <!-- ==========================
                   PAGE CONTENT - START
               =========================== -->

          <div class="main">
               <section class="content account" style="margin-top: 100px;">
                    <div class="container">
                         <div class="row">

                              <div class="jumbotron">

                                   <div class="well pr-2 pt-1" style="text-align: center; color: white; background: black;">
                                        <a href='<?=$this->controller->config->getFileRootPath()?>admin/dashboard' class="btn btn-md btn-primary pull-right">Back to Dashboard</a>
                                        <div style="clear:both;"></div>
                                   </div>

                                   <div class="well mb-2" style="text-align: center; color: white; background: black;"><h3 class="text-primary">Total Users</h3>
                                        <p class="badge"><span><?=$numOfAllUsers?></span></p>
                                        <div style="clear:both;"></div>
                                   </div>
                                   <?php if ($_SESSION['user_type'] == 'admin_gen') { ?>
                                        <a href='<?=$this->controller->config->getFileRootPath()?>admin/createUser'
                                           class="btn btn-lg btn-primary">Create new User</a>
                                        <?php
                                   }
                                   else
                                   { ?>
                                        <a href="#" class="btn btn-lg btn-primary" title="You dont have permission create a user" disabled>Create new User</a>
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
                                                  <th class="col-xs-1">Action</th>
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
                                                                      <a href="<?=$this->controller->config->getFileRootPath()?>user/editUser?userId=<?=$user['users_id']?>"
                                                                           title="Edit this user"
                                                                           class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
                                                                      <a onclick="return confirm('Are you sure you wish to delete this user?')" 
                                                                           href="<?=$this->controller->config->getFileRootPath()?>admin/deleteUser?userId=<?=$user['users_id']?>"
                                                                           title="Delete this user"
                                                                           class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                                                 <?php
                                                                 }
                                                                 elseif($_SESSION['user_type'] == 'admin_gen') { ?>
                                                                      <a href="#" class="btn btn-primary btn-sm" title="You cannot edit yourself from here" disabled><i class="fa fa-pencil"></i></a>
                                                                      <a href="#" class="btn btn-danger btn-sm" title="You cannot delete yourself" disabled><i class="fa fa-trash"></i></a>
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
                                                  <th class="col-xs-2">email</th>
                                                  <th class="col-xs-2">Created</th>
                                                  <th class="col-xs-1">Action</th>
                                             </tr>
                                             <?php
                                             foreach($allUsers as $user) { ?>
                                                  <?php if ($user['users_type'] == 'admin') { ?>
                                                       <tr>
                                                            <td><?= $user['users_first_name'] ?></td>
                                                            <td><?= $user['users_last_name'] ?></td>
                                                            <td class="hidden-xs"><?= $user['users_email'] ?></td>
                                                            <td class="hidden-xs"><?= $dateClass->YYYYMMDDtoDDMMYYYY($user['users_created']) ?></td>
                                                            <td>
                                                                 <?php
                                                                 if ($_SESSION['user_type'] == 'super_admin' || $_SESSION['user_type'] == 'admin_gen')
                                                                 { ?>
                                                                      <a href="<?=$this->controller->config->getFileRootPath()?>admin/editUser?userId=<?=$user['users_id']?>&edit=0"
                                                                           title="Edit this user"  
                                                                           class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i>
                                                                      </a>

                                                                      <a onclick="return confirm('Are you sure you wish to delete this user?')" 
                                                                           href="<?=$this->controller->config->getFileRootPath()?>admin/deleteUser?userId=<?=$user['users_id']?>"
                                                                           title="Delete this user"
                                                                           class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>
                                                                      </a>
                                                                 <?php
                                                                 }
                                                                 else { ?>
                                                                      <a href="#" class="btn btn-primary btn-sm" title="You dont have permission to edit" disabled><i class="fa fa-pencil"></i></a>
                                                                      <a href="#" class="btn btn-danger btn-sm" title="You dont have permission to delete" disabled><i class="fa fa-trash"></i></a>
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