<?php

namespace Dorguzen\Views;


/**
 * This class exposes various JS code snippets which you can reuse across many view files in your application.
 * Just use it by pulling it into any view files where u need it. Place the code to include it above in the show()
 * method as it is done in login.php
 *          $jsValidation = \Dorguzen\Core\DGZ_View::getInsideView('jsValidationPartial', $this->controller);
            $jsValidation->show();
 *
 * It is essentially a partial (a piece of code that is included), and a good example of how a view can be used
 *  inside of another in Dorguzen
 *
 * Class jsValidationPartial
 * @package views
 */
class jsValidationPartial extends \Dorguzen\Core\DGZ_HtmlView
{

    public function show()
    { ?>


        <script type="text/javascript">

            // -------------------------------------------------------------------
            // Plain JavaScript — no jQuery. The validator functions below are GLOBAL,
            // so each form calls them directly through its inline submit handler, e.g.
            //     <form ... onSubmit="return validateLoginForm(this)">
            // (a handler that returns false cancels the submit). DOM event handlers that
            // need elements — the forgot-password toggle and the username check — are
            // attached lower down inside DOMContentLoaded listeners.
            // -------------------------------------------------------------------




                //function to validate the registration form
                function validate(form) {
                    fail = validateFirstname(form.firstname.value)
                    fail += validateSurname(form.surname.value)
                    fail += validateUsername(form.username.value)
                    fail += validatePassword(form.pwd.value)
                    fail += validatePhone(form.phone.value)
                    fail += validateEmail(form.email.value)
                    if (fail == "") {
                        return true;
                    }
                    else {
                        alert(fail);
                        return false;
                    }
                }





                //function to validate the login form
                function validateLoginForm(form)
                {
                    if (document.getElementById('forgotstatus').value == 'yes')
                    {
                        fail = validateEmail(form.forgot_pass_input.value);
                        if (fail == "")
                        {
                            return true;
                        }
                        else
                        {
                            alert(fail);
                            return false;
                        }
                    }
                    else
                    {
                        fail = validateEmail(form.login_email.value);
                        fail += validatePassword(form.login_pwd.value);

                        if (fail == "")
                        {
                            return true;
                        }
                        else
                        {
                            alert(fail);
                            return false;
                        }
                    }
                }



                //Handle the login forgot-password toggle (plain JS, no jQuery)
                document.addEventListener('DOMContentLoaded', function () {
                    var forgotLink = document.getElementById('forgot_pass');
                    if (!forgotLink) { return; }

                    forgotLink.addEventListener('click', function (e) {
                        e.preventDefault();

                        // Reveal the forgot-password email field and hide the login fields
                        // (and vice-versa) — every element carrying .loginfieldinput is flipped.
                        var fields = document.querySelectorAll('.loginfieldinput');
                        for (var i = 0; i < fields.length; i++) {
                            var el = fields[i];
                            var isHidden = el.style.display === 'none' || getComputedStyle(el).display === 'none';
                            el.style.display = isHidden ? '' : 'none';
                        }

                        document.getElementById('forgotstatus').value = 'yes';

                        if (forgotLink.innerHTML.trim() === 'Reset form') {
                            location.reload();
                        } else {
                            forgotLink.innerHTML = 'Reset form';
                        }
                    });
                });









                function validateFirstname(field) {
                        if (field == "") {
                            return "firstname\n";
                        }
                        return "";
                    }


                    function validateSurname(field) {
                        if (field == "") {
                            return "No Surname was entered.\n";
                        }
                        return ""
                    }


                    function validateUsername(field) {
                        if (field == "") {
                            return "username\n"
                        }
                        else if (field.length < 5) {
                            return "username\n"
                        }
                        else if (/[^a-zA-Z0-9_-]/.test(field)) {
                            return ""
                        }

                        //if none of the checks above match; return a blank string for no errors
                        return ""
                    }


                    function validatePassword(field) {
                        if (field == "") {
                            return "No password was entered"
                        }
                        else if (field.length < 6) {
                            return 'The password must be at least 6 characters';
                        }

                        // WE CAN MAKE THE PW EVEN STRONGER AND ACCEPT IT ONLY IF IT CONTAINS AT LEAST ONE SMALL 
                        // LETTER, ONE UPPERCASE LETTER, AND ONE NUMBER, BUT IN THIS CASE WE WILL NOT BECAUSE OUR 
                        // PHP CODE WILL STILL VALIDATE THE PW PROPERLY. HENCE WE COMMENT OUT THE FOLLOWING 4 LINES
                        //else if (! /[a-z]/.test(field) ||
                        //! /[A-Z]/.test(field) ||
                        //! /[0-9]/.test(field))
                        //return "Passwords require one each of a-z, A-Z and 0-9.\\n"
                        return ""
                    }


                    function validateAge(field) {
                        if (isNaN(field)) return "No Age was entered.\n"
                        else if (field < 18 || field > 110)
                            return "Age must be between 18 and 110.\n"
                        return "";
                    }


                    function validatePhone(field) {
                        if (isNaN(field)) return "No Phone number was entered.\n"
                        else if (field.length > 15)
                            return "Phone number must not be more than 15 digits.\n"
                        return "";
                    }


                    function validateEmail(field) {
                        if (field == "") return "No Email was entered.\n"
                        else if (!((field.indexOf(".") > 0) &&
                            (field.indexOf("@") > 0)) ||
                            /[^a-zA-Z0-9.@_-]/.test(field))
                            return "The Email address is invalid.\n"
                        return "";
                    }



                //make an ajax call-this calls the function 'checkUsername()' below
                document.addEventListener('DOMContentLoaded', function () {
                    var regUsername = document.querySelector('#regis_form #username');
                    if (regUsername) {
                        regUsername.addEventListener('blur', function () {
                            checkUsername(this);
                        });
                    }
                });



                /**
                 * Code to get you started making ajax calls form your application.
                 * Create a controller called AuthController with a method checkUsername()
                 * You pass it a username from a form, and it calls the checkUsername() method
                 * The checkUsername() method checks in the DB if that username is already in use.
                 * It returns some text like 'username available' or 'username already taken' which you can display in a span
                 *    element next to the username input field

                 * @param username
                 */
                    function checkUsername(username) {
                        if (username.value == '') {
                            document.getElementById('info').innerHTML = '';
                            return
                        }

                        params = "username=" + username.value
                        request = new ajaxRequest()
                        request.open("POST", "auth/checkUsername", true)
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

        </script>
        <?php
    }
}