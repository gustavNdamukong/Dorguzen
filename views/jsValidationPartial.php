<?php

namespace views;


/**
 * This class exposes various JS code snippets which you can reuse across many view files in your application.
 * Just use it by pulling it into any view files where u need it. Place the code to include it above in the show()
 * method as it is done in login.php
 *          $jsValidation = \DGZ_library\DGZ_View::getView('jsValidationPartial', $this->controller);
            $jsValidation->show();
 *
 * It is essencially a partial and is meant to give you an idea of how code can be reused in Dorguzen
 *
 * Class jsValidationPartial
 * @package views
 */
class jsValidationPartial extends \DGZ_library\DGZ_HtmlView
{

    public function show()
    { ?>


        <script type="text/javascript">

            $(document).ready(function () {

                //validate the registration form
                $(document).on('submit', '#regis_form', function (e) {
                    e.preventDefault();

                    if (validate(this))
                    {
                        this.submit();
                    }
                });


                //validate the login form
                $(document).on('submit', '#loginForm', function (e) {
                    e.preventDefault();

                    if (validateLoginForm(this))
                    {
                        this.submit();
                    }
                });
                


                $(document).on('blur', '#regis_form #username', function()
                {
                    checkUsername(this);
                });




                //func to validate the registration form
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





                //func to validate the login form
                function validateLoginForm(form)
                { //alert("You have submitted the login form");
                    //return false
                    if ($('#forgotstatus').val() == 'yes')
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



                //Handle the login forgot password feature
                $('#forgot_pass').click(function(e) {
                    e.preventDefault();
                    $('.loginfieldinput').toggle();

                    $("#forgotstatus").val('yes');

                    if ($('#forgot_pass').html() == 'Reset form')
                    {
                        location.reload(true);
                    }
                    else
                    {
                        $('#forgot_pass').html('Reset form')
                    }
                });









                function validateFirstname(field) {
                        if (field == "") { alert('please enter firstname!!!'); return false;
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
                        else if (field.length < 6) {"
                            return 'The password must be at least 6 characters';
                        }

                        //
                        <!--WE CAN MAKE THE PW EVEN STRONGER AND ACCEPT IT ONLY IF IT CONTAINS AT LEAST ONE SMALL LETTER, ONE UPPERCASE LETTER, AND ONE NUMBER, BUT IN THIS CASE WE WILL NOT BECAUSE OUR PHP CODE WILL STILL VALIDATE THE PW PROPERLY. HENCE WE COMMENT OUT THE FOLLOWING 4 LINES-->
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








                    //code to get you started making ajax calls form your application.
                    //create a controller called AuthController with a method checkUsername()
                    //you pass it the username from a form to and it checks in the DB if that username is already in use.
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



            });

        </script>


        <?php
    }
}