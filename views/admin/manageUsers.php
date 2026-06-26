<?php

namespace Dorguzen\Views\Admin;


class manageUsers extends \Dorguzen\Core\DGZ_AdminHtmlView
{

     function show(array $viewModel = [])
     {
          extract($viewModel); ?>
          <script src="https://code.jquery.com/jquery-latest.min.js"></script>
          <script src="https://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

          <?php

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

          $dateClass = new \Dorguzen\Core\DGZ_Dates();
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
          <!-- ==========================
               Hero Header & Breadcrumb - End
          =========================== -->



          <!-- ==========================
               PAGE CONTENT - START
          =========================== -->

          <div class="main">
               <section class="content account" style="margin-top: 100px;">
                    <div class="container">

                         <!-- START SIDE SLIDE-IN MENU -->
                         <?php
                         //Pull in the PHP file that has the JS code that handles all the JS to do with placing an ad
                         $jsValidation = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                         $jsValidation->show();
                         ?>
                         <!-- END OF SIDE SLIDE-IN MENU -->

                         <div class="row">

                              <style>
                                   .mu-hero {
                                        display: flex;
                                        align-items: center;
                                        justify-content: space-between;
                                        background: #1a1a1a;
                                        border-radius: 12px;
                                        padding: 20px 24px;
                                        margin-bottom: 24px;
                                   }
                                   .mu-hero-text h3 { color: var(--site-theme, #fd7e14); margin: 0 0 2px; font-size: 1.4rem; }
                                   .mu-hero-text p  { color: #ccc; margin: 0; font-size: .95rem; }
                                   .mu-create { margin-bottom: 20px; }
                                   .mu-section-heading {
                                        font-size: 1rem;
                                        font-weight: 700;
                                        text-transform: uppercase;
                                        letter-spacing: .6px;
                                        color: #495057;
                                        margin: 28px 0 10px;
                                        display: flex;
                                        align-items: center;
                                        gap: 8px;
                                   }
                                   .mu-section-heading .badge {
                                        background: var(--site-theme, #fd7e14);
                                        color: #fff;
                                        border-radius: 20px;
                                        padding: 2px 10px;
                                        font-size: .75rem;
                                   }
                                   .mu-table { border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.07); margin-bottom: 8px; }
                                   .mu-table table { margin-bottom: 0; }
                                   .mu-table thead th {
                                        background: var(--site-theme, #fd7e14);
                                        color: #fff;
                                        border: none;
                                        font-weight: 600;
                                        font-size: .85rem;
                                        text-transform: uppercase;
                                        letter-spacing: .4px;
                                        padding: 12px 14px;
                                   }
                                   .mu-table tbody tr { transition: background .12s; }
                                   .mu-table tbody tr:hover { background: #fff8f2; }
                                   .mu-table tbody tr:nth-child(even) { background: #fafafa; }
                                   .mu-table tbody tr:nth-child(even):hover { background: #fff8f2; }
                                   .mu-table td { vertical-align: middle; padding: 10px 14px; font-size: .9rem; border-color: #f0f0f0; }
                                   .mu-empty { color: #198754; font-style: italic; }
                              </style>

                              <div class="jumbotron">

                                   <div class="mu-hero">
                                        <div class="mu-hero-text">
                                             <h3>Total Users</h3>
                                             <p><?= $numOfAllUsers ?> registered user<?= $numOfAllUsers != 1 ? 's' : '' ?></p>
                                        </div>
                                        <a href='<?=$this->controller->config->getFileRootPath()?>admin/dashboard'
                                           class="btn btn-primary" style="white-space:nowrap;margin-right:8px;">← Back to Dashboard</a>
                                   </div>

                                   <div class="mu-create">
                                        <?php if (in_array($_SESSION['user_type'], ['admin_gen', 'super_admin'])) { ?>
                                             <a href='<?=$this->controller->config->getFileRootPath()?>admin/createUser'
                                                class="btn btn-lg btn-primary">Create new User</a>
                                        <?php } else { ?>
                                             <a href="#" class="btn btn-lg btn-primary" title="You don't have permission to create a user" disabled>Create new User</a>
                                        <?php } ?>
                                   </div>

                                   <p class="mu-section-heading">Super Admin Users <span class="badge"><?= $superAdminUserCounter ?></span></p>
                                   <div class="table-responsive mu-table">
                                        <table class="table table-bordered">
                                             <thead><tr>
                                                  <th>First Name</th>
                                                  <th>Last Name</th>
                                                  <th>Username</th>
                                                  <th>Created</th>
                                                  <th>Action</th>
                                             </tr></thead><tbody>
                                             <?php
                                             foreach($allUsers as $user) { ?>
                                                  <?php if ($user['users_type'] == 'super_admin') { ?>
                                                       <tr>
                                                            <td>
                                                                 <a><?= $user['users_first_name'] ?></a>
                                                            </td>
                                                            <td><?= $user['users_last_name'] ?></td>
                                                            <td class="hidden-xs"><?= $user['users_email'] ?></td>
                                                            <td><?=$dateClass->YYYYMMDDtoDDMMYYYY($user['users_created']) ?></td>
                                                            <td><?php
                                                                 if (
                                                                      ($_SESSION['user_type'] == 'super_admin') &&
                                                                      ($user['users_id'] != $_SESSION['custo_id'])
                                                                 )
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
                                                                      <a href="#" class="btn btn-primary btn-sm" title="You cannot edit a super admin user" disabled><i class="fa fa-pencil"></i></a>
                                                                      <a href="#" class="btn btn-danger btn-sm" title="You cannot delete a super admin user" disabled><i class="fa fa-trash"></i></a>
                                                                      <?php
                                                                 } ?>
                                                            </td>
                                                       </tr>
                                                       <?php
                                                  }
                                             } ?>

                                             <?php if ($superAdminUserCounter == 0) { ?>
                                                  <tr><td colspan="5" class="mu-empty">There are no Super Admin Users</td></tr>
                                             <?php } ?>
                                             </tbody></table>
                                   </div>

                                   <p class="mu-section-heading">General Admin Users <span class="badge"><?= $adminGenUserCounter ?></span></p>
                                   <div class="table-responsive mu-table">
                                        <table class="table table-bordered">
                                             <thead><tr>
                                                  <th>First Name</th>
                                                  <th>Last Name</th>
                                                  <th>Username</th>
                                                  <th>Created</th>
                                                  <th>Action</th>
                                             </tr></thead><tbody>
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

                                             <?php if ($adminGenUserCounter == 0) { ?>
                                                  <tr><td colspan="5" class="mu-empty">There are no General Admin Users</td></tr>
                                             <?php } ?>
                                             </tbody></table>
                                   </div>

                                   <p class="mu-section-heading">Admin Users <span class="badge"><?= $adminUserCounter ?></span></p>
                                   <div class="table-responsive mu-table">
                                        <table class="table table-bordered">
                                             <thead><tr>
                                                  <th>First Name</th>
                                                  <th>Last Name</th>
                                                  <th>Email</th>
                                                  <th>Created</th>
                                                  <th>Action</th>
                                             </tr></thead><tbody>
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

                                             <?php if ($adminUserCounter == 0) { ?>
                                                  <tr><td colspan="5" class="mu-empty">There are no Admin Users</td></tr>
                                             <?php } ?>
                                             </tbody></table>
                                   </div>

                                   <p class="mu-section-heading">Member Users <span class="badge"><?= $userCounter ?></span></p>
                                   <div class="table-responsive mu-table">
                                        <table class="table table-bordered">
                                             <thead><tr>
                                                  <th>First Name</th>
                                                  <th>Last Name</th>
                                                  <th>Email</th>
                                                  <th>Created</th>
                                                  <th>Action</th>
                                             </tr></thead><tbody>
                                             <?php
                                             foreach($allUsers as $user) { ?>
                                                  <?php if ($user['users_type'] == 'member') { ?>
                                                       <tr>
                                                            <td><?= $user['users_first_name'] ?></td>
                                                            <td><?= $user['users_last_name'] ?></td>
                                                            <td class="hidden-xs"><?= $user['users_email'] ?></td>
                                                            <td class="hidden-xs"><?= $dateClass->YYYYMMDDtoDDMMYYYY($user['users_created']) ?></td>
                                                            <td>
                                                                 <?php
                                                                 if ($_SESSION['user_type'] == 'super_admin' || $_SESSION['user_type'] == 'admin_gen' || $_SESSION['user_type'] == 'admin')
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

                                             <?php if ($userCounter == 0) { ?>
                                                  <tr><td colspan="5" class="mu-empty">There are no Member Users</td></tr>
                                             <?php } ?>
                                             </tbody></table>
                                   </div>

                              </div>

                         </div>
                    </div>
               </section>
          </div>
     <?php
     }
} ?>
