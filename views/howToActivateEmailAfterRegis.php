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