<?php

namespace Dorguzen\Views\Admin;



class adminUserChangePw extends \Dorguzen\Core\DGZ_AdminHtmlView
{ 

    function show(array $viewModel = [])
     {
          extract($viewModel); ?>
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
        <!-- ==========================
             Hero Header & Breadcrumb - End
        =========================== -->   

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
                        $jsValidation = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                        $jsValidation->show();
                        ?>
                        <!-- END OF SIDE SLIDE-IN MENU -->       
                        <div class="row">
                            <div class="form">
                                <form id="editUserForm" action="<?=$this->controller->config->getFileRootPath()?>admin/adminUserChangePw?change=1" method="post">
                                    <input type="hidden" name="_csrf_token" value="<?=getCsrfToken()?>">
                                    <input placeholder="Username" id="new_user_un" name="new_user_un" class="form-control" type="text" value="<?=$user['users_email']?>" />

                                    <input placeholder="Password" id="new_user_pwd" name="new_user_pwd" class="form-control" type="text" value="<?=$user['pass']?>">

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
} ?>