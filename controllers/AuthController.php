<?php

namespace controllers;

/*
use Subscribers;
*/
use DGZ_library\DGZ_Translator;
use DGZ_library\DGZ_Messenger;
use DGZ_library\DGZ_CheckPassword;
use DGZ_library\DGZ_Validate;
use Users;
use configs\Config;
use Logs;
use DGZ_library\DGZ_View;
use Password_reset;



class AuthController extends \DGZ_library\DGZ_Controller  {

    public function __construct()
    {
        parent::__construct();

    }


    public function getDefaultAction()
    {
        return 'defaultAction';
    }


    public function defaultAction()
    {
        $this->login();
    }

    public function login($email = '')
    {
        //Establish where they're coming from, so you can refer them back after logging them in
        $this->checkReferral();

        $view = DGZ_View::getView('login', $this, 'html');
        $this->setPageTitle('Login');
        $this->setLayoutDirectory('seoMaster');
        $this->setLayoutView('seoMasterLayout');
        $view->show($email);
    }



    /**
     * See if they are logging in because a route (controller (c)) requires them to do so, so u can send them back there after logging them in.
     * Ideally, you would even
     * send them back to the specific view section aka the anchor segment in that view (v)
     */
    private function checkReferral()
    {
        //if only a controller & method are provided
        if ((isset($_GET['c'])) && (isset($_GET['m'])))
        {
            $_SESSION['referBack']['c'] = $_GET['c'];
            $_SESSION['referBack']['m'] = $_GET['m'];
        }
        //if the developer only provided a controller
        elseif (isset($_GET['c']))
        {
            $_SESSION['referBack']['c'] = $_GET['c'];
            $_SESSION['referBack']['m'] = "";
        }
    }


    public function signup() 
    {
        $view = DGZ_View::getView('register', $this, 'html');
        $this->setPageTitle('Register');

        $this->setLayoutDirectory('seoMaster');
        $this->setLayoutView('seoMasterLayout');
        $view->show();
    }


    public function register()
    {
        $val = new DGZ_Validate();
        $users = new Users();
        $config = new Config();
        $logs = new Logs();
        if ($this->config->getConfig()['allow_registration']) {
            $words = [
                'chokochohilarious', 'jammijamjim', 'tolambomanulo', 'kilabakula', 'jamborayla', 
                'kingkong', 'camerooncom', 'camerooncomcm', 'bayofbiscay', 'camprocol', 'tuxedo', 'camgas', 
                'manyolo', 'geomasso', 'ndipakem', 'jamesbond', 'camerooncomcom', 'camerooncomnet', 
                'camerooncominfo', 'nolimit', 'chopman', 'builders', 'jackstraw', 'colgate', 'jimreeves', 
                'popol', 'bamenda', 'buea', 'bafoussam', 'nkongsamba', 'ahidjo', 'douala', 'yaounde', 
                'bertoua', 'ebolowa', 'ngaoundere', 'maroua', 'foumban', 'bafang', 'lavoir', 'brancher', 
                'sicia', 'achana', 'francais', 'anglais', 'french', 'english', 'business', 'bosco', 'shokoloko', 
                'bangoshay', 'papou', 'wembley', 'hausa'
            ];

            $randomnumber = rand(0, 53);
            $randword = rand() . rand(0, 32000);
            $randCode = "$words[$randomnumber]" . "$randword";
            $activationCode = md5(trim($randCode));

            $firstname = $surname = $username = $password = $phone = $email = $fail = $success = $error = $mailresult = false;

            $googleId = 'null';
            if ((isset($_POST)) && ($_POST != '')) {
                //They must agree to our terms & conditions
                if (!array_key_exists('agreeToTerms', $_POST)) {
                    $this->addErrors('Please accept our Terms and Conditions', 'Alert!');
                    $this->redirect('auth', 'signup');
                }

                //reject spam bots
                if (isset($_POST['captcha_hidden'])) {
                    if (trim($_POST['captcha_hidden']) != '') {
                        //log dodgy hidden captcha completion & do not even bother to display an error message
                        $registrant = implode('|', $_POST);
                        $logs->log('Dodgy hidden captcha completion', 'This user '.$registrant.' failed to register because they dodgily completed the hidden captcha'); 
                        $this->redirect('home');
                    }
                }

                if (isset($_POST['captcha_addition']) && ($_POST['captcha_addition'] != 4)) {
                    //log failed captcha addition
                    $registrant = implode('|', $_POST);
                    $logs->log('Failed captcha addition', 'This user '.$registrant.' failed to register because of the wrong captcha addition value');

                    $this->addErrors('Something went wrong, try again or contact us for help', 'Error!');
                    $this->redirect('auth', 'signup');
                }

                $user_type = "member";

                $emailverified = "no";

                //sanitize the submitted values
                if (isset($_POST['firstname'])) {
                    $firstname = $val->fix_string($_POST['firstname']);
                }

                if (isset($_POST['surname'])) {
                    $surname = $val->fix_string($_POST['surname']);
                }

                /*if (isset($_POST['username'])) {
                    $username = $val->fix_string($_POST['username']);
                }*/

                if (isset($_POST['pwd'])) {
                    $password = $val->fix_string($_POST['pwd']);
                    $retyped = $val->fix_string($_POST['conf_pwd']);
                }

                if (isset($_POST['phone'])) {
                    $phone = $val->fix_string($_POST['phone']);
                }

                if (isset($_POST['email'])) {
                    $email = $val->fix_string($_POST['email']);;
                }

                //validate the submitted values
                $fail  = $val->validate_firstname($firstname);
                $fail .= $val->validate_surname($surname);
                //$fail .= $val->validate_username($username);
                $fail .= $val->validate_password($password);
                $fail .= $val->validate_phonenumber($phone);
                $fail .= $val->validate_email($email);

                if ($fail == "") {
                    $checkPwd = new DGZ_CheckPassword($password, 6);
                    //IF WE WANT TO MAKE THE PASSWORD STRONGER, WE WILL UNCOMMENT THE FOLLOWING 3 LINES SO THAT THE THE PW WILL ONLY BE
                    //ALLOWED IF IT HAS MIXED LETTER CASES, OR CONTAINS NUMBERS, OR CONTAINS SYMBOLS, OR CONTAINS ALL THE ABOVE, DEPENDING
                    //ON UR CHOICE
                    //$checkPwd->requireMixedCase();
                    //$checkPwd->requireNumbers(2);
                    //$checkPwd->requireSymbols();
                    $passwordOK = $checkPwd->check();
                    if (!$passwordOK) {
                        //$errors = array_merge($errors, $checkPwd->getErrors());
                        //$fail .= array_merge($fail, $checkPwd->getErrors());
                        foreach ($checkPwd->getErrors() as $error) {
                            //$fail .= $fail . $checkPwd->getErrors(); THIS LINE LEAVES THE WORD 'ARRAY' IN THE VARIABLE $fail; To fix
                            //the problem, THIS LOOP WILL EMPTY THE CONTENTS OF THE errors property (which is an array) as loose strings
                            ////into $fail rather than the whole array itself. This is because if the whole array gets it, $fail will
                            //still contain something even though it's empty, and the insertion into the database will not happen, as
                            //$fail has to be empty (free of all errors for that to happen.

                            $fail .= $error;
                        }
                    }
                    if ($password != $retyped) {
                        $fail .= "Your passwords don't match.";

                        $this->addErrors($fail);
                        $this->redirect(
                            'auth', 
                            'signup',
                            [
                                'firstname' => $firstname, 
                                'surname' => $surname,  
                                'phone' => $phone, 
                                'email' => $email, 
                                'fail' => $fail
                            ]
                        );
                    }

                    if (!$fail) {
                        $users->users_type = $user_type;
                        $users->users_email = $email;
                        $users->users_pass = $password;
                        $users->users_first_name = $firstname;
                        $users->users_last_name = $surname;
                        $users->users_phone_number = $phone;
                        $users->users_emailverified = $emailverified;
                        $users->users_created = $users->timeNow();
                        $users->users_eactivationcode = $activationCode;
                        $saved = $users->save();

                        if ($saved == 1062) {
                            $fail .= "<p>That username already exists</p>";

                            $this->addErrors($fail);
                            $this->redirect(
                                'auth', 
                                'signup', 
                                [
                                    'firstname' => $firstname, 
                                    'surname' => $surname, 
                                    'username' => $username, 
                                    'phone' => $phone, 
                                    'email' => $email, 
                                    'fail' => $fail
                                ]
                            );
                            exit();
                        } else if ($saved) {
                            $messenger = new DGZ_Messenger();

                            // Add your own subject below
                            $subject = "Activate your account";

                            $appName = $this->config->getConfig()['appName'];
                            $appUrl = $config->getConfig()['live']?$config->getConfig()['liveUrl']:$config->getConfig()['localUrl'];

                            $message = "<h1>Congratulations</h1>
                                    <h2>Your account has been created on " . $appName . "</h2>
                                    <br />
	                                <p>Click on this link to activate your account</p>
                                    <p><a href='" . $this->config->getConfig()['appURL'] . "auth/verifyEmail?em=" . $activationCode . "'>Activate account</a> 
                                    If the above link does not work, copy and paste this in your browser:</p>
                                    <p>" . $this->config->getConfig()['appURL'] . "auth/verifyEmail?em=" . $activationCode . "</p>
                                    <br />
                                    
                                    <p><img width='100' height='100' src='" . $this->config->getConfig()['appURL'] . "assets/images/logos/logo.svg' /></p>";

                            $messenger->sendEmailActivationEmail($username, $email, $subject, $message);
                            $_SESSION['activationCode'] = $activationCode;

                            $this->redirect('auth', 'email-activation-instructions'); 
                        } else {
                            $fail .= "Something went wrong. Please make sure all fields are completed, then try again or contact us for help. Thanks";
                            $this->addErrors($fail);
                            $this->redirect('auth', 'signup');
                        }
                    } else {
                        $this->addErrors($fail);
                        $this->redirect('auth', 'signup');
                    }
                } else {
                    $this->addErrors($fail);
                    $this->redirect('auth', 'signup');
                }
            } else {
                $this->addErrors('You did not fill in the form');
                $this->redirect('auth', 'signup');
                exit();
            }
        }
        else
        {
            $this->addErrors('Registration is not allowed!');
            $this->redirect('admin');
        }
    }


     /**
     * After successful user registration, display a view to them with clear instructions about the next step,
     * which is to check their email and click on the email verification link to activate
     * their account in order to be able to log in.
     *
     * @throws \DGZ_library\DGZ_Exception
     */
    public function emailActivationInstructions() 
    {
        $view = \DGZ_library\DGZ_View::getView('howToActivateEmailAfterRegis', $this, 'html');
        $view->show();
    }


    //Activate account via email link after registration
    public function verifyEmail() 
    {
        $langClass = new DGZ_Translator();
        $lang = $langClass->getCurrentLang();

        if (isset($_GET['em'])) {
            $user_model = new Users();
            $val = new DGZ_Validate();
            
            $code = $val->fix_string($_GET['em']);
            $yes = 'yes';

            //we need to match their activation code
            $selectCriteria = ["users_eactivationcode" => $code];  
            $fields = ['users_id', 'users_first_name', 'users_email'];
            $user = $user_model->selectWhere($fields, $selectCriteria);

            if ($user) {
                /////$userId = $user[0]['users_id'];
                $user_model->users_emailverified = $yes;
                $user_model->users_eactivationcode = NULL;
                $updateCriteria = ['users_eactivationcode' => $code];
                $updated = $user_model->updateObject($updateCriteria);

                if ($updated) {
                    //if this is an API call, send the success response now
                    if ($this->isApiRequest()) {
                        $message = $langClass->translate($lang, 'register.php', 'auth-emailActivated');
                        return $this->apiResponse(true, $message);
                    }

                    $this->addSuccess($langClass->translate($lang, 'register.php', 'auth-emailActivated'), $langClass->translate($lang, 'register.php', 'auth-great'));
                    $this->redirect('auth', 'login');
                    exit();

                }
                else {
                    //if this is an API call, send the error response now
                    if ($this->isApiRequest()) {
                        $message = $langClass->translate($lang, 'register.php', 'auth-couldNotActivateEmail');
                        return $this->apiResponse(false, $message);
                    }

                    $this->addErrors($langClass->translate($lang, 'register.php', 'auth-couldNotActivateEmail'), $langClass->translate($lang, 'register.php', 'auth-sorry'));
                    $this->redirect('home');
                    exit;
                }
            }
            else {
                //if this is an API call, send the error response now
                if ($this->isApiRequest()) {
                    $message = $langClass->translate($lang, 'register.php', 'auth-couldNotFindYourDetails');
                    return $this->apiResponse(false, $message);
                }

                $this->addErrors($langClass->translate($lang, 'register.php', 'auth-couldNotFindYourDetails'), $langClass->translate($lang, 'register.php', 'auth-sorry'));
                $this->redirect('home');
                exit;
            }
        }
    }



    public function checkUsername()
    {
        $langClass = new DGZ_Translator();
        $lang = $langClass->getCurrentLang();
        $val = new DGZ_Validate();
        $users = new Users();

        if (isset($_POST['username']))
        {
            $username = $val->fix_string($_POST['username']);
        }

        $fail = $val->validate_surname($username);

        if ($fail == "")
        {
            $query = "SELECT * FROM users WHERE users_username = '$username'";

            $user = $users->query($query);

            if ($user)
            {
                die("<b style='color:red'>&nbsp;&larr;
                ".$langClass->translate($lang, 'register.php', 'usernameAlreadyTaken')."</b>");
            }
            else
            {
                die("<b  style='color:green'>&nbsp;&larr;
                ".$langClass->translate($lang, 'register.php', 'usernameAvailable')."</b>");
            }
        }
        else
        {
            die("<b  style='color:red'>&nbsp;&larr;
			 ".$langClass->translate($lang, 'register.php', 'usernameInvalid')."</b>");
        }
    }




    public function checkEmail()
    {
        $langClass = new DGZ_Translator();
        $lang = $langClass->getCurrentLang();
        $val = new DGZ_Validate();
        $users = new Users();

        if (isset($_POST['email']))
        {
            $email = $val->fix_string($_POST['email']);
        }

        $fail = $val->validate_email($email);

        if ($fail == "")
        {
            $query = "SELECT * FROM users WHERE users_email = '$email'";

            $user = $users->query($query);

            if ($user)
            {
                die("<b style='color:red'>&nbsp;&larr;
                ".$langClass->translate($lang, 'register.php', 'emailAlreadyExists')."</b>");
            }
            else
            {
                //We have to return something, but because in this case we want to take no action 
                //on the form if the email is unique, we return a null.
                die(null);
            }
        }
        else
        {
            die("<b  style='color:red'>&nbsp;&larr;
			 ".$langClass->translate($lang, 'register.php', 'emailInvalid')."</b>");
        }
    }



    public function doLogin()
    {
        $password = $email = $rem_me = false;
        $fail = "";
        $callerOrigin = ((isset($_POST['caller-origin'])) && ($_POST['caller-origin'] == 'api'))?'api':'';
        //'status' has a value of either 'true' or 'false', while 'message' has the error msg
        $returnMessage = [
            'status' => '',
            'message' => ''
        ];

        $val = new DGZ_Validate();

        if ((isset($_POST['login_email'])) && (($_POST['forgotstatus']) == 'no'))
        {
            if(isset($_POST['login_email']))
            {
                $email = $val->fix_string($_POST['login_email']);
            }

            if (isset($_POST['login_pwd']))
            {
                $password = $val->fix_string($_POST['login_pwd']);
            }

            if (isset($_POST['rem_me']))
            {

                $rem_me = ($_POST['rem_me']);
            }


            $fail .= $val->validate_email($email);

            $fail .= $val->validate_password($password);

            if ($fail == "")
            {
                $authenticated = $this->authenticate($email, $password);

                if ($authenticated)
                {
                    if (!session_id()) { session_start(); }
                    $_SESSION['authenticated'] = 'Let Go-'.$this->config->getConfig()['appName'];
                    $_SESSION['start'] = time();
                    session_regenerate_id();

                    $_SESSION['custo_id'] = $authenticated['users_id'];
                    $_SESSION['user_type'] = $authenticated['users_type'];
                    $_SESSION['first_name'] = $authenticated['users_first_name'];
                    $_SESSION['last_name'] = $authenticated['users_last_name'];
                    $_SESSION['email'] = $authenticated['users_email'];
                    $_SESSION['phone'] = $authenticated['users_phone_number'];
                    $_SESSION['created'] = $authenticated['users_created'];

                    session_write_close();

                    //if this is an API call, send the success response now
                    if ($callerOrigin == 'api') {
                        $returnMessage['status'] = 'true';
                        $returnMessage['message'] = 'Login was successful';
                        return $returnMessage;
                    }

                    //log admin login activity here
                    if (in_array($authenticated['users_type'], ['admin', 'admin_gen', 'super_admin'])) {
                        $logs = new Logs();
                        $logTitle = 'Admin login';
                        $logData = 'User type: '. $authenticated['users_type'].
                            '| Firstname: '.$authenticated['users_first_name'].
                            '| Surname: ' . $authenticated['users_last_name'].
                            '| Time: ' . date("d-m-y h:i:s");
                        $logs->log($logTitle, $logData);
                    }

                    //We only set a cookie if the user chose to be remembered
                    if ($rem_me)
                    {
                        setcookie('rem_me', $email, time() + 172800); //48 hours
                    }

                    //see if they are logging in because a route (controller (c)) requires them to do so, & send them back there, & optionally to a specific
                    // method (v). If it's a shop, build the redirection path
                    if ((isset($_SESSION['referBack']['c'])) && (isset($_SESSION['referBack']['m'])) && (isset($_SESSION['referBack']['m2'])))
                    {
                        $controller = $_SESSION['referBack']['c'];
                        $method = $_SESSION['referBack']['m'].'/'.$_SESSION['referBack']['m2'];
                    }
                    else if ((isset($_SESSION['referBack']['c'])) && (isset($_SESSION['referBack']['m'])))
                    {
                        $controller = $_SESSION['referBack']['c'];
                        $method = $_SESSION['referBack']['m'];
                    }
                    else if (isset($_GET['c']))
                    {
                        $controller = $_SESSION['referBack']['c'];
                        $method = "";
                    }
                    else
                    {
                        //send the user to the default authenticated view
                        $controller = 'admin';
                        $method = 'dashboard';
                    }

                    unset($_SESSION['referBack']);
                    $this->addSuccess('Welcome Admin!', 'Hey');
                    $this->redirect($controller, $method);
                }
                else
                {
                    //if this is an API call, send the failed response now
                    if ($callerOrigin == 'api') {
                        $returnMessage['status'] = 'false';
                        $returnMessage['message'] = "Either the email address or the password you provided was wrong";
                        return $returnMessage;
                    }

                    // if no match, prepare error message
                    $this->addErrors('Either the email address or the password you provided was wrong, try again or contact us for help. Thank you.','Oops, something went wrong!');
                    $this->redirect('admin');
                }
            }
            else
            {
                //if this is an API call, send the failed response now
                if ($callerOrigin == 'api') {
                    $returnMessage['status'] = 'false';
                    $returnMessage['message'] = $fail;
                    return $returnMessage;
                }

                $this->addErrors($fail, "Error!");
                $this->redirect('admin');
            }
        }
        elseif ((isset($_POST['forgotstatus'])) && (($_POST['forgotstatus']) == 'yes'))
        {
            if(isset($_POST['forgot_pass_input']))
            {
                $email = $val->fix_string($_POST['forgot_pass_input']);
                $fail .= $val->validate_email($email);
                if ($fail == "")
                {
                    $user_model = new Users();

                    $found = $user_model->recoverLostPw($email);


                    if ($found)
                    {
                        $resetCode = $this->generateCode();
                        $resetModel = new Password_reset();

                        $query = "INSERT INTO password_reset (password_reset_users_id, password_reset_firstname, password_reset_email, password_reset_date, password_reset_reset_code)
                                  VALUES ($found[userId], '$found[firstname]', '$found[email]', '".date('Y-m-d H:i:s')."', '$resetCode')";
                        $saved = $resetModel->query($query);

                        if ($saved) {
                            //if this is an API call, send a response with the reset link now
                            $resetLinkPath = "%sauth/reset?em=%s";
                            $resetLink = sprintf(
                                $resetLinkPath,
                                $this->config->getHomePage(),
                                $resetCode
                            );

                            if ($callerOrigin == 'api') {
                                $returnMessage['status'] = 'true';
                                $returnMessage['message'] = 'Here is the link for the user to reset their password';

                                $returnMessage['resetLink'] = $resetLink;
                                return $returnMessage;
                            }

                            $mailer = new DGZ_Messenger();

                            $mailresult = $mailer->sendPasswordResetEmail($found['email'], $found['firstname'], $resetCode);
                            if ($mailresult) {
                                $this->addSuccess('We have sent a link to reset your password to your email address', "Thank you");
                                $this->redirect('admin');
                            }
                        }
                    }
                    else
                    {
                        //if this is an API call, the failed response now
                        if ($callerOrigin == 'api') {
                            $returnMessage['status'] = 'false';
                            $returnMessage['message'] = "Either the email address or the password you provided was wrong";
                            return $returnMessage;
                        }

                        $fail .= 'Sorry, there was a problem with the database! Try again later, or contact us';
                        $this->addErrors($fail);
                        $this->redirect('admin');
                    }
                }
            }
        }
    }


    /**
     * Users click on a 'Forgot my password' link sent to them via email to reset their password and they 
     * land on this script. We need to generate a view file, verify their activation code, then if good we 
     * show a form for them to reset their password. If the code fails validation either because it's expired 
     * or is simply invalid, we display an error on that view and kick them out to the log in page.
     */
    public function reset($em)
    {
        if (isset($_GET['em'])) {
            $val = new DGZ_Validate();
            $resetCode = $val->fix_string($_GET['em']);
            if ($resetCode != '') {
                $model = new Password_reset();
                $sql = "SELECT * FROM password_reset 
                        WHERE password_reset_reset_code = '$resetCode'";

                $resetDetails = $model->query($sql);

                $sql = "DELETE FROM password_reset
                    WHERE password_reset_id = ".$resetDetails[0]['password_reset_id'].
                    " AND password_reset_reset_code = '$resetCode'";

                $deleted = $model->query($sql);

                if ($resetDetails[0]['password_reset_date'] <= strtotime('-2 hours'))
                {
                    $view = DGZ_View::getAdminView('resetPw', $this, 'html');
                    $view->show($resetDetails[0]['password_reset_users_id'], $resetDetails[0]['password_reset_email']);
                }
                else{
                    $this->addWarning('You waited too long and your reset code expired, request for one again');
                    $this->redirect('admin');
                }
            }
        }
    }


    
    


    public function resetPw()
    {
        $reset_email = $reset_user_id = $reset_pwd = $reset_conf_pwd = '';
        $fail = "";

        $val = new DGZ_Validate();

        if(isset($_POST['reset_user_id']))
        {
            $reset_user_id = $val->fix_string($_POST['reset_user_id']);
        }else{ $fail .= '<p>Something went wrong! Try requesting for a reset again or contact us.</p>'; }

        if(isset($_POST['reset_email']))
        {
            $reset_email = $val->fix_string($_POST['reset_email']);
        }else{ $fail .= '<p>Sorry! We could not identify your account.</p>'; }

        if (isset($_POST['reset_pwd']))
        {
            $reset_pwd = $val->fix_string($_POST['reset_pwd']);
        }


        if (isset($_POST['reset_conf_pwd']))
        {
            $reset_conf_pwd = $val->fix_string($_POST['reset_conf_pwd']);
        }


        $fail .= $val->validate_password($reset_pwd);

        if ($reset_pwd !== $reset_conf_pwd)
        {
            $fail .= "<p>Both passwords did not match!</p>";
        }


        if ($fail == "")
        {
            $user_model = new Users();

            $userCreated = $user_model->resetUserPassword($reset_user_id, $reset_email, $reset_pwd);

            if($userCreated)
            {
                $this->addSuccess('Your password was successfully updated, now you can login');
                $this->redirect('admin');
            }
            elseif($userCreated == false)
            {
                $this->addErrors('Try not to use the same old password. If it fails again, contact us', 'Error!');
                $this->redirect('admin');
            }

        }
        else
        {
            $this->addErrors($fail);
            $view = DGZ_View::getAdminView('resetPw', $this, 'html');
            $view->show($reset_user_id, $reset_email);
        }
    }





    /**
     * @param $email the email to authenticate the user with
     * @param $password the password to authenticate the user with
     * @return array|bool It returns false if the login fails, or an array of all fields in your users table
     */
    public function authenticate($email, $password)
    {
        $user_model = new Users();

        $loginData = ['users_email' => $email, 'users_pass' => $password];

        return $user_model->authenticateUser($loginData);

    }




    public function logout()
    {
        $_SESSION = array();

        if (isset($_COOKIE[session_name()]))
        {
            setcookie(session_name(), '', time() - 86400, '/');
        }

        if (isset($_COOKIE['rem_me']))
        {
            setcookie('rem_me', '', time()-86400);
        }

        session_destroy();

        $this->redirect('home');
        exit();
    }

}

