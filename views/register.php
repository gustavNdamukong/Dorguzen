<?php

namespace views;

use DGZ_library\DGZ_View;

class register extends \DGZ_library\DGZ_HtmlView
{
     function show($firstname = '', $lastname = '', $phone = '', $email = '', $fail = '')
     {
         if ((isset($_SESSION['authenticated'])) && ($_SESSION['authenticated'] == 'Let Go-' . $this->controller->config->getConfig()['appName'])) {
             //user is already logged in
             $this->controller->addNotice('You are already logged in');
             $this->controller->redirect('admin/dashboard');
         }
         else 
         { ?>
             <script src="http://code.jquery.com/jquery-latest.min.js"></script>
             <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

             <?php
             //Pull in the PHP file that has the JS validation codes
             $jsValidation = \DGZ_library\DGZ_View::getInsideView('jsCreateUserValidationPartial', $this->controller);
             $jsValidation->show();
             ?>

             <!-- Hero Header Start -->
             <div class="container-xxl py-5 bg-primary hero-header mb-5">
                 <div class="container my-5 py-5 px-lg-5">
                     <div class="row g-5 py-5">
                         <div class="col-12 text-center">
                             <h1 class="text-white animated zoomIn">Register</h1>
                             <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                             <nav aria-label="breadcrumb">
                                 <ol class="breadcrumb justify-content-center">
                                     <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>">Home</a></li>
                                     <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>auth">Login</a></li>
                                     <li class="breadcrumb-item text-white active" aria-current="page">Sign up</li>
                                 </ol>
                             </nav>
                         </div>
                     </div>
                 </div>
             </div>
             <!-- Hero Header End -->

             <!-- Contact Start -->
             <div class="container-xxl py-5">

                <!-- START SIDE SLIDE-IN MENU -->
                <?php
                //Pull in the PHP file that has the JS code that handles all the JS to do with placing an ad
                $slideInMenu = \DGZ_library\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                $slideInMenu->show();
                ?>
                <!-- END OF SIDE SLIDE-IN MENU --> 

                 <div class="container px-lg-5">
                     <div class="row justify-content-center">
                         <div class="col-lg-7">
                             <div class="wow fadeInUp" data-wow-delay="0.3s">

                             <form id="regis_form" method="post"
                                      action="<?=$this->route('auth/register')?>">

                                    <div id="regis_panel" class="panel panel-primary">
                                        <a href="<?= $this->controller->config->getFileRootPath()?>auth/login" class="btn btn-success btn-lg pull-right">Already a member? Login</a>

                                        <div class="panel-heading">

                                            <h3 class="panel-title text-center"
                                                style="color: #000;">Register</h3>

                                        </div>

                                        <div class="panel-body">
                                            <p>
                                                <label for="firstname">Enter your first name</label>
                                                <input type="text" class="form-control regisforminput"
                                                       placeholder="First name"
                                                       maxlength="32"
                                                       name="firstname" id="firstname"
                                                       value="<?=htmlentities($firstname, ENT_COMPAT, 'UTF-8')?>"/>
                                            </p>
                                            <hr/>

                                            <p>
                                                <label for="surname">Enter your last name</label>
                                                <input type="text" class="form-control regisforminput"
                                                       placeholder="Surname"
                                                       maxlength="32"
                                                       name="surname" id="surname"
                                                       value="<?=htmlentities($lastname, ENT_COMPAT, 'UTF-8')?>"/>
                                            </p>
                                            <hr/>

                                            <p>
                                                <label for="email">Your email</label>
                                                <span id='info'></span></p>
                                                <input 
                                                    type="text"
                                                    class="form-control regisforminput"
                                                    placeholder="Email"
                                                    maxlength="30"
                                                    name="email"
                                                    id="email"
                                                    value="<?=htmlentities($email, ENT_COMPAT, 'UTF-8')?>" 
                                                />
                                            <hr/>

                                            <p>
                                                <label for="password">Enter password (Max 12 characters)</label>
                                                <input type="password"
                                                       class="form-control regisforminput"
                                                       placeholder="Password"
                                                       maxlength="12"
                                                       name="pwd"
                                                       id="pwd" />
                                            </p>
                                            <hr/>

                                            <p>
                                                <label for="conf_pwd">Confirm your password</label>
                                                <input type="password"
                                                       class="form-control regisforminput"
                                                       maxlength="12" name="conf_pwd"
                                                       placeholder="Confirm password"
                                                       id="conf_pwd" required>
                                            </p>
                                            <hr/>

                                            <p>
                                                <label for="phone">Phone number</label>
                                                <input type="text"
                                                       class="form-control regisforminput"
                                                       maxlength="16"
                                                       name="phone"
                                                       placeholder="Phone number"
                                                       id="phone"
                                                       value="<?=htmlentities($phone, ENT_COMPAT, 'UTF-8')?>"
                                                />
                                            </p>
                                            <hr/>

                                            <p>
                                                <label for="captcha_addition">
                                                    If you are not a robot, what is 3 + 1?</label>
                                                <input type="number"
                                                       class="form-control regisforminput"
                                                       name="captcha_addition"
                                                       id="captcha_addition"
                                                       min="0" />
                                            </p>
                                            <hr/>

                                            <p>
                                                <label for="agreeToTerms">
                                                    Terms & Conditions
                                                </label>
                                                <input type="checkbox"
                                                       class="regisforminput"
                                                       name="agreeToTerms"
                                                       id="agreeToTerms"
                                                       value="on"
                                                /><a href="#" target="_blank">
                                                    Agree to our Terms & Conditions
                                                </a>&nbsp;<small>(Opens TC page in a different tab)</small>
                                            </p>
                                            <hr/>

                                            <input type="hidden" name="captcha_hidden" />
                                        </div>

                                        <div class="panel-footer clearfix">

                                            <div class="pull-right">
                                                <input type="reset" class="btn btn-warning"
                                                       role="button"
                                                       value="Reset"/>
                                                <input type="submit"
                                                       class="btn btn-primary"
                                                       value="Register"/>
                                            </div>
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