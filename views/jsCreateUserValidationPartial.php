<?php

namespace Dorguzen\Views;


/**
 * This class exposes various JS code snippets which you can reuse across many view files in your application.
 * Just use it by pulling it into any view files where u need it. Place the code to include it above in the show()
 * method as it is done in login.php
 *          $jsValidation = \Dorguzen\Core\DGZ_View::getInsideView('jsCreateUserValidationPartial', $this->controller);
 *           $jsValidation->show();
 *
 * It is essentially a partial (a piece of code that is included), and a good example of how a view can be used
 *  inside of another in Dorguzen
 *
 * Class jsValidationPartial
 * @package views
 */
class jsCreateUserValidationPartial extends \Dorguzen\Core\DGZ_HtmlView
{

    public function show()
    { ?>
        <script type="text/javascript">
            $(document).ready(function () {
                var dgzCsrfToken = "<?= getCsrfToken() ?>";

                // Only fire after the user has explicitly focused the field themselves.
                // Guards against browsers that auto-focus email fields for autofill,
                // which would otherwise trigger the check on every subsequent click.
                var emailFocusedByUser = false;

                $('#regis_form #email').on('focus', function() {
                    emailFocusedByUser = true;
                }).on('blur', function() {
                    if (emailFocusedByUser) {
                        checkEmail(this);
                    }
                    emailFocusedByUser = false;
                });

                function checkEmail(email) {
                    if (email.value == '') {
                        document.getElementById('info').innerHTML = '';
                        return;
                    }

                    var params = "email=" + encodeURIComponent(email.value) +
                                 "&_csrf_token=" + encodeURIComponent(dgzCsrfToken);
                    var request = new ajaxRequest();
                    request.open("POST", "<?=$this->rootPath()?>auth/checkEmail", true);
                    request.setRequestHeader("Content-type",
                        "application/x-www-form-urlencoded");

                    request.onreadystatechange = function () {
                        if (this.readyState == 4) {
                            if (this.status == 200) {
                                if (this.responseText != null) {
                                    document.getElementById('info').innerHTML =
                                        this.responseText
                                }
                                else alert("Ajax error: No data received")
                            }
                            else alert("Ajax error: " + this.statusText)
                        }
                    }
                    request.send(params)
                }


                function ajaxRequest() {
                    try {
                        return new XMLHttpRequest();
                    } catch (e) {
                        return false;
                    }
                }
            });

        </script>
        <?php
    }
}