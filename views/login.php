<?php

namespace views;



class login extends \DGZ_library\DGZ_HtmlView
{



     function show($login_errors = [], $username = '', $password = '')
     { ?>
         <script src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
    <?php

         //Pull in the PHP file that has the JS validation codes
         $jsValidation = \DGZ_library\DGZ_View::getView('jsValidationPartial', $this->controller);
         $jsValidation->show();

         ?>
         <div>
         <section class="hgroup">
             <div class="container">
                 <br />
                 <br />
                 <!--<h1>Register or Sign in</h1>-->
                 <ul class="breadcrumb pull-right">
                     <li><a href="<?=$this->controller->settings->getFileRootPath()?>Home/home">Home</a> </li>
                     <!--<li class="active">Register or Sign in</li>-->
                 </ul>
             </div>
         </section>
         <section>
             <div class="container">
                 <div class="row">
                     <div class="col-sm-6 col-md-12">
                         <div class="signin">
                             <?php /* <div class="social_sign">
                                 <!--<h3>Sign in with your social account</h3>-->
                                 <!--<h1>Register or Sign in</h1>-->
                                 <a class="fb" href="#facebook"><i class="fa fa-facebook"></i></a> <a class="tw" href="#twitter"><i class="fa fa-twitter"></i></a> <a class="gp" href="#googleplus"><i class="fa fa-google-plus"></i></a> </div>
                             <div class="or">
                                 <div class="or_l"></div>
                                 <span>or</span>
                                 <div class="or_r"></div>
                             </div> */?>
                             <p class="sign_title">Admin Login</p>
                             <div class="row">
                                 <h4><?php //if ($econfirm) { echo "Congratulations, your account has been activated, you may now login"; }?></h4>
                                 <br />


                                 <?php /*if ((isset($fail)) && ($fail != '')) { echo "<p style='color: red; font-size: 14px; font-weight: bold;'>Oops, there was an error</p><br /><p style='color: red; background-color: white;'><i>$fail</i></p>"; } else if (isset($success)) { echo "<p style='background-color: white;'><font color=green size=3><br />$success</font></p>"; }

                                 if (isset($login_errors) && !empty($login_errors)) //these are errors coming from grabbing the login details from the db.
                                 { ?>
                                     <div class="well">
                                     <?php
                                     foreach ($login_errors as $loginerrs)
                                     {
                                         echo "<p style='color: red; background-color: white;'>$loginerrs</p> ";
                                     } ?>
                                     </div>
                                 <?php
                                 } */ ?>


                                 <div class="col-lg-2"></div>
                                 <div class="form col-lg-8">
                                     <form id="loginForm" action="<?=$this->controller->settings->getFileRootPath()?>admin/login" method="post" onSubmit="return validateLoginForm(this)">

                                         <input placeholder="Email" id="login_email" name="login_email" class="form-control loginfieldinput" type="text" <?php if ((isset($fail)) && ($fail != '') || ((isset($errors)) && count($errors) > 0 )) { echo 'value="' . htmlentities($username, ENT_COMPAT, 'UTF-8') . '"'; } ?>></p><span id='info'></span>

                                         <input placeholder="Password" id="login_pwd" name="login_pwd" class="form-control loginfieldinput" type="password">

                                             <div class="checkbox loginfieldinput">
                                                 <input type="checkbox" id="signin-remember" name="rem_me" class="form-control" />
                                                 <label for="signin-remember">Remember me </label>
                                             </div>

                                         <div class="forgot">
                                             <a id="forgot_pass" class="btn btn-default btn-sm" href="#">Forgot password?</a>
                                             <br />
                                             <input type="email" maxlength="40" class="form-control loginfieldinput" id="forgot_pass_input" name="forgot_pass_input" placeholder="Email address here" style="display: none; margin-top: 5px;" /><span style="display: none; margin-top: 5px;" class="loginfieldinput" id="forgot_pass_info">&nbsp;&larr;<font color=blue>Enter your email address to receive your password</font></span>

                                             <input id="forgotstatus" type="hidden" name="forgotstatus" value="no" />
                                         </div>

                                         <input type="reset" class="btn btn-warning btn-sm"
                                                value="Clear entry"/>
                                         <button type="submit" id="login" name="login" class="btn btn-primary btn-sm">Submit</button>
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

} ?>