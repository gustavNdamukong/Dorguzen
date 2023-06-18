<?php

namespace views;

use DGZ_library\DGZ_View;

class login extends \DGZ_library\DGZ_HtmlView
{
     function show($email = '')
     {
         if ((isset($_SESSION['authenticated'])) && ($_SESSION['authenticated'] == 'Let Go-' . $this->controller->config->getConfig()['appName'])) {
             //user is already logged in
             $this->controller->redirect('admin/dashboard');
         }
         else {
             ?>
             <script src="http://code.jquery.com/jquery-latest.min.js"></script>
             <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

             <?php
             $jsValidation = DGZ_View::getInsideView('jsValidationPartial', $this->controller);
             $jsValidation->show();
             ?>

             <!-- Hero Header Start -->
             <div class="container-xxl py-5 bg-primary hero-header mb-5">
                 <div class="container my-5 py-5 px-lg-5">
                     <div class="row g-5 py-5">
                         <div class="col-12 text-center">
                             <h1 class="text-white animated zoomIn">Admin Login</h1>
                             <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                             <nav aria-label="breadcrumb">
                                 <ol class="breadcrumb justify-content-center">
                                     <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>">Home</a></li>
                                     <li class="breadcrumb-item text-white active" aria-current="page">Login</li>
                                 </ol>
                             </nav>
                         </div>
                     </div>
                 </div>
             </div>
             </div>
             <!-- Hero Header End -->

             <!-- Contact Start -->
             <div class="container-xxl py-5">
                 <div class="container px-lg-5">

                    <!-- START SIDE SLIDE-IN MENU -->
                    <?php
                    //Pull in the PHP file that has the JS code that handles all the JS to do with placing an ad
                    $slideInMenu = \DGZ_library\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                    $slideInMenu->show();
                    ?>
                    <!-- END OF SIDE SLIDE-IN MENU --> 

                     <div class="row justify-content-center">
                         <div class="col-lg-7">
                             <div class="wow fadeInUp" data-wow-delay="0.3s">
                                 <form id="loginForm"
                                       action="<?= $this->controller->config->getFileRootPath() ?>auth/doLogin"
                                       method="post" onSubmit="return validateLoginForm(this)">
                                     <div class="row g-3">
                                         <div class="col-md-12">
                                             <div class="form-floating">
                                                 <input placeholder="Email" id="login_email" name="login_email"
                                                        class="form-control loginfieldinput"
                                                        type="text" <?php if ((isset($fail)) && ($fail != '') || ((isset($errors)) && count($errors) > 0)) {
                                                     echo 'value="' . htmlentities($email, ENT_COMPAT, 'UTF-8') . '"';
                                                 } ?>>
                                                 <label class="loginfieldinput" for="login_email">Your Email</label>
                                             </div>
                                         </div>
                                         <div class="col-md-12">
                                             <div class="form-floating">
                                                 <input placeholder="Password" id="login_pwd" name="login_pwd"
                                                        class="form-control loginfieldinput" type="password">
                                                 <label class="loginfieldinput" for="login_pwd">Your Password</label>
                                             </div>
                                         </div>

                                         <div class="checkbox loginfieldinput">
                                             <input type="checkbox" id="signin-remember" name="rem_me"/>
                                             <label for="signin-remember">Remember me </label>
                                         </div>

                                         <div class="forgot form-floating">
                                             <a id="forgot_pass" class="btn btn-info btn-sm py-2 mb-1" href="#">Forgot
                                                 password?</a>
                                             <br/>
                                             <input type="email" maxlength="40"
                                                    class="form-control loginfieldinput" id="forgot_pass_input"
                                                    name="forgot_pass_input" placeholder="Email address here"
                                                    style="display: none; margin-top: 5px;"/><span
                                                 style="display: none; margin-top: 5px;" class="loginfieldinput"
                                                 id="forgot_pass_info">&nbsp;&larr;<font color=blue>Enter your
                                                     email address to receive your password</font></span>

                                             <input id="forgotstatus" type="hidden" name="forgotstatus"
                                                    value="no"/>
                                         </div>

                                         <div class="col-12">
                                             <button type="reset" class="btn btn-warning btn-sm py-2 mb-1">Clear entry</button>
                                             <button id="login" name="login" class="btn btn-primary w-100 py-3" type="submit">Submit</button>
                                         </div>
                                     </div>
                                 </form>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>

             <?php
         }
     }

} ?>