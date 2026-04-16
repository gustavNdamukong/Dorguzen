<?php

namespace Dorguzen\Views\Admin;



class resetPw extends \Dorguzen\Core\DGZ_AdminHtmlView
{



     function show(array $viewModel = [])
     {
          extract($viewModel);
        //Pull in the PHP file that has the JS validation codes
        $jsValidation = \Dorguzen\Core\DGZ_View::getInsideView('jsValidationPartial', $this->controller);
        $jsValidation->show();
        ?>
        <div>
            <section class="hgroup">
                <div class="container">
                    <br />
                    <ul class="breadcrumb pull-right">
                        <li><a href="<?=$this->controller->config->getFileRootPath()?>Home/home">Home</a> </li>
                    </ul>
                </div>
            </section>
            <section>
                <div class="container">

                    <!-- START SIDE SLIDE-IN MENU -->
                    <?php
                    //Pull in the PHP file that has the JS code that handles all the JS to do with placing an ad
                    $slideInMenu = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                    $slideInMenu->show();
                    ?>
                    <!-- END OF SIDE SLIDE-IN MENU --> 

                    <div class="row">
                        <div class="col-sm-6 col-md-12">
                            <div class="signin">
                                <h2 class="sign_title">Reset your password</h2>
                                <div class="row">
                                    <div class="col-lg-2"></div>
                                    <div class="form col-lg-8">
                                        <form action="<?=$this->controller->config->getFileRootPath()?>admin/resetPw" method="post">
                                            <input type="hidden" name="_csrf_token" value="<?=getCsrfToken()?>">
                                            <input id="reset_user_id" name="reset_user_id" class="form-control" type="hidden" value="<?=$userId?>" />
                                            <input id="reset_email" name="reset_email" class="form-control" type="hidden" value="<?=$userEmail?>" />

                                            <p>
                                                <label for="reset_pwd">Enter a new password (at least 6 characters long)</label>
                                                <input placeholder="Password" id="reset_pwd" name="reset_pwd" class="form-control" type="password">
                                            </p>

                                            <p>
                                                <label for="reset_conf_pwd">Confirm password</label>
                                                <input placeholder="Password" id="reset_conf_pwd" name="reset_conf_pwd" class="form-control" type="password">
                                            </p>

                                            <input type="reset" class="btn btn-warning btn-sm"
                                                    value="Clear entry"/>
                                            <button type="submit" class="btn btn-primary btn-sm">Submit</button>
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