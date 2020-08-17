<?php

namespace views\admin;



class resetPw extends \DGZ_library\DGZ_HtmlView
{



     function show($userId, $userEmail)
     {

         //Pull in the PHP file that has the JS validation codes
         $jsValidation = \DGZ_library\DGZ_View::getView('jsValidationPartial', $this->controller);
         $jsValidation->show();

         ?>
         <div>
         <section class="hgroup">
             <div class="container">
                 <br />
                 <ul class="breadcrumb pull-right">
                     <li><a href="<?=$this->controller->settings->getFileRootPath()?>Home/home">Home</a> </li>
                 </ul>
             </div>
         </section>
         <section>
             <div class="container">
                 <div class="row">
                     <div class="col-sm-6 col-md-12">
                         <div class="signin">
                             <h2 class="sign_title">Reset your password</h2>
                             <div class="row">
                                 <div class="col-lg-2"></div>
                                 <div class="form col-lg-8">
                                     <form action="<?=$this->controller->settings->getFileRootPath()?>admin/resetPw" method="post">

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