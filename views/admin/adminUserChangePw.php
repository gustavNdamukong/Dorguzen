<?php

namespace views\admin;



class adminUserChangePw extends \DGZ_library\DGZ_HtmlView
{ 

    function show($user, $userId)
     { ?>
         <script src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>


        <!-- ==========================
         Hero Header & Breadcrumb - START
         =========================== -->
         <div class="container-xxl py-5 bg-primary hero-header mb-5">
                 <div class="container my-5 py-5 px-lg-5">
                     <div class="row g-5 py-5">
                         <div class="col-12 text-center">
                             <h1 class="text-white animated zoomIn">Change your email & password</h1>
                             <hr class="bg-white mx-auto mt-0" style="width: 90px;">

                             <nav aria-label="breadcrumb">
                                 <ol class="breadcrumb justify-content-center">
                                     <li class="breadcrumb-item"><a class="text-white" href="<?=$this->controller->config->getFileRootPath()?>admin/dashboard"><i class="fa fa-home"></i>Dashboard</a></li>
                                     <li class="breadcrumb-item text-white active" aria-current="page">Change password</li>
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


        <?php
         if ((isset($_SESSION['authenticated'])) && ($_SESSION['authenticated'] == 'Let Go-'.$this->controller->config->getConfig()['appName'])) {

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
                        $jsValidation = \DGZ_library\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                        $jsValidation->show();
                        ?>
                        <!-- END OF SIDE SLIDE-IN MENU -->       
                        <div class="row">
                            <div class="form">
                                <form id="editUserForm" action="<?=$this->controller->config->getFileRootPath()?>admin/adminUserChangePw?change=1" method="post">

                                    <input placeholder="Username" id="new_user_un" name="new_user_un" class="form-control" type="text" value="<?=$user[0]['users_email']?>" />

                                    <input placeholder="Password" id="new_user_pwd" name="new_user_pwd" class="form-control" type="text" value="<?=$user[0]['pass']?>">

                                    <input type="hidden" name="userId" value="<?=$userId?>" />

                                    <a href="<?=$this->controller->config->getFileRootPath()?>admin/dashboard" class="btn btn-warning btn-sm" id="cancel" >Cancel</a>
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
             <section>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6 col-md-12">
                            <div>
                                <div class="row">
                                    <div class="col-lg-2"></div>
                                    <div class="form col-lg-8">
                                        <h3 style="color:red;">Sorry! You have no access to this page 
										<a href="<?=$this->controller->config->getFileRootPath()?>auth" class="btn btn-info">Login</a>
										<a href="<?=$this->controller->config->getFileRootPath()?>" class="btn btn-info">Home</a></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php
        }
    }

} ?>