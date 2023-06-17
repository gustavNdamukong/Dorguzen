<?php

namespace views;

use DGZ_library\DGZ_Translator;

class howToActivateEmailAfterRegis extends \DGZ_library\DGZ_HtmlView
{
     function show()
     {
          $langClass = new DGZ_Translator();
          $lang = $langClass::getCurrentLang();
          $activationCode = isset($_SESSION['activationCode'])?$_SESSION['activationCode']:'';
          unset($_SESSION['activationCode']);
          ?>

          <!-- ==========================
          Hero Header & Breadcrumb - START
          =========================== -->
          <div class="container-xxl py-5 bg-primary hero-header mb-5">
               <div class="container my-5 py-5 px-lg-5">
                    <div class="row g-5 py-5">
                         <div class="col-12 text-center">
                              <h1 class="text-white animated zoomIn">Thank you!</h1>
                              <hr class="bg-white mx-auto mt-0" style="width: 90px;">

                              <nav aria-label="breadcrumb">
                                   <ol class="breadcrumb justify-content-center">
                                        <li class="breadcrumb-item"><a class="text-white" href="<?=$this->controller->config->getFileRootPath()?>"><i class="fa fa-home"></i>Home</a></li>
                                        <li class="breadcrumb-item text-white active" aria-current="page">
                                        <a class="text-white" href="<?=$this->controller->config->getFileRootPath()?>feedback"><i class="fa fa-users"></i> Contact us</a></li>
                                   </ol>
                              </nav>
                         </div>
                    </div>
               </div>
          </div>
          <!-- ==========================
          Hero Header & Breadcrumb - End
          =========================== -->

          <div class="main">
               <section class="content account" style="margin-top: 100px;">  
                    <div class="container">
                         <div class="row">
                              
                              <div class="jumbotron">
                                   <div class="well" style="text-align: center;">
                                        <h3 style="color: #0d8609;"><?= $langClass->translate($lang, 'howToActivateEmailAfterRegis.php', 'emailSent') ?></h3>
                                       <h2 style="color: #000;"><?= $langClass->translate($lang, 'howToActivateEmailAfterRegis.php', 'checkSpam') ?></h2>
                                       <h2 style="color: #000;"><?= $langClass->translate($lang, 'howToActivateEmailAfterRegis.php', '24hoursValid') ?></h2>
                                        <p>&nbsp;&nbsp;</p>
                                   </div>
                                   <p style="color:blue;font-weight:bold;"><?= $langClass->translate($lang, 'howToActivateEmailAfterRegis.php', 'anyProblems') ?></p>
                              </div>
                         </div>
                    </div>
               </section>
          </div>
          <?php
     }

} ?>