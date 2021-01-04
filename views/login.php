<?php

namespace views;



class login extends \DGZ_library\DGZ_HtmlView
{



     function show($login_errors = [], $username = '', $password = '')
     {
         if ((isset($_SESSION['authenticated'])) && ($_SESSION['authenticated'] == 'Let Go-' . $this->controller->settings->getSettings()['appName'])) {
             //user is already logged in
             $this->controller->redirect('admin/dashboard');
         }
         else {
             ?>
             <script src="http://code.jquery.com/jquery-latest.min.js"></script>
             <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

             <?php
             $jsValidation = \DGZ_library\DGZ_View::getInsideView('jsValidationPartial', $this->controller);
             $jsValidation->show();

             ?>
             <div>
                 <section class="hgroup">
                     <div class="container">
                         <br/>
                         <br/>
                         <ul class="breadcrumb pull-right">
                             <li><a href="<?= $this->controller->settings->getFileRootPath() ?>Home">Home</a></li>
                         </ul>
                     </div>
                 </section>
                 <section>
                     <div class="container">
                         <div class="row">
                             <div class="col-sm-6 col-md-12">
                                 <div class="signin">
                                     <p class="sign_title">Admin Login</p>
                                     <div class="row">
                                         <div class="col-lg-2"></div>
                                         <div class="form col-lg-8">
                                             <form id="loginForm"
                                                   action="<?= $this->controller->settings->getFileRootPath() ?>admin/login"
                                                   method="post" onSubmit="return validateLoginForm(this)">

                                                 <input placeholder="Email" id="login_email" name="login_email"
                                                        class="form-control loginfieldinput"
                                                        type="text" <?php if ((isset($fail)) && ($fail != '') || ((isset($errors)) && count($errors) > 0)) {
                                                     echo 'value="' . htmlentities($username, ENT_COMPAT, 'UTF-8') . '"';
                                                 } ?>></p><span id='info'></span>

                                                 <input placeholder="Password" id="login_pwd" name="login_pwd"
                                                        class="form-control loginfieldinput" type="password">

                                                 <div class="checkbox loginfieldinput">
                                                     <input type="checkbox" id="signin-remember" name="rem_me"
                                                            class="form-control"/>
                                                     <label for="signin-remember">Remember me </label>
                                                 </div>

                                                 <div class="forgot">
                                                     <a id="forgot_pass" class="btn btn-default btn-sm" href="#">Forgot
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

                                                 <input type="reset" class="btn btn-warning btn-sm"
                                                        value="Clear entry"/>
                                                 <button type="submit" id="login" name="login"
                                                         class="btn btn-primary btn-sm">Submit
                                                 </button>
                                             </form>

                                         </div>
                                         <div class="col-lg-2"></div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </section>
             </div>
             <?php
         }
     }

} ?>