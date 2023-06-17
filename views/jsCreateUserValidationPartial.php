<?php

namespace views;


/**
 * This class exposes various JS code snippets which you can reuse across many view files in your application.
 * Just use it by pulling it into any view files where u need it. Place the code to include it above in the show()
 * method as it is done in login.php
 *          $jsValidation = \DGZ_library\DGZ_View::getInsideView('jsCreateUserValidationPartial', $this->controller);
 *           $jsValidation->show();
 *
 * It is essentially a partial (a piece of code that is included), and a good example of how a view can be used
 *  inside of another in Dorguzen
 *
 * Class jsValidationPartial
 * @package views
 */
class jsCreateUserValidationPartial extends \DGZ_library\DGZ_HtmlView
{

    public function show()
    { ?>


        <script type="text/javascript">

            $(document).ready(function () { 
                //make an ajax call-this calls the function 'checkEmail()' below // #regis_form #regis_panel 
                $(document).on('blur', '#regis_form #email', function(e)
                {  
                    e.preventDefault(); 
                    checkEmail(this);
                });

                /**
                 * Code to get you started making ajax calls form your application.
                 * Create a controller called AuthController with a method checkEmail()
                 * You pass it a email from a form, and it calls the checkEmail() method
                 * The checkEmail() method checks in the DB if that email is already in use.
                 * It returns some text like 'email available' or 'email already taken' which you can display in a span
                 *    element next to the email input field

                 * @param email
                 */
                function checkEmail(email) {
                    if (email.value == '') {
                        document.getElementById('info').innerHTML = '';
                        return
                    }

                    params = "email=" + email.value
                    request = new ajaxRequest()
                    request.open("POST", "auth/checkEmail", true)
                    request.setRequestHeader("Content-type",
                        "application/x-www-form-urlencoded")

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
                            var request = new XMLHttpRequest()
                        }
                        catch (e1) {
                            try {
                                request = new ActiveXObject("Msxml2.XMLHTTP")
                            }
                            catch (e2) {
                                try {
                                    request = new ActiveXObject("Microsoft.XMLHTTP")
                                }
                                catch (e3) {
                                    request = false
                                }
                            }
                        }
                        return request
                    }
            });

        </script>
        <?php
    }
}