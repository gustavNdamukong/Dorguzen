<?php

namespace controllers;



use DGZ_library\DGZ_Validate;
use Users;
use Password_reset;
use BaseSettings;
use ContactFormMessage;
use DGZ_library\DGZ_View;
use DGZ_library\DGZ_Messenger;


class AdminController extends \DGZ_library\DGZ_Controller  {



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
        $view = DGZ_View::getView('login', $this, 'html');
        $this->setPageTitle('login');
        //$this->setLayoutDirectory('admin');
        //$this->setLayoutView('adminLayout');
        $this->setLayoutDirectory('seoMaster');
        $this->setLayoutView('seoMasterLayout');

        $view->show();
    }





    public function dashboard()
    {
        $view = DGZ_View::getAdminView('adminHome', $this, 'html');
        $this->setPageTitle('Admin');

        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show();
    }





                                
    public function login()
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
                    $_SESSION['email'] = $authenticated['users_email'];
                    $_SESSION['first_name'] = $authenticated['users_first_name'];
                    $_SESSION['last_name'] = $authenticated['users_last_name'];
                    $_SESSION['created'] = $authenticated['users_created'];

                    session_write_close();

                    //if this is an API call, send the success response now
                    if ($callerOrigin == 'api') {
                        $returnMessage['status'] = 'true';
                        $returnMessage['message'] = 'Login was successful';
                        return $returnMessage;
                    }

                    if ($rem_me)
                    {
                        setcookie('rem_me', $email, time() + 172800);
                    }

                    $this->addSuccess('Welcome Admin!', 'Hey');
                    $this->redirect('admin','dashboard');
                    exit();
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
                            $resetLinkPath = "%sadmin/verifyEmail?em=%s";
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
     * Users click on a link sent to them via email to reset their password and they land on this script.
     * We need to generate a view file, verify their activation code, then if good we show a form for them
     * to reset their password. If the code fails validation either because it's expired or is simply invalid,
     * we display an error on that view and kick them out to the log in page.
     */
    public function verifyEmail($em)
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



    /**
     * Get vital settings for the application that have been specified by the administrator through the admin dashboard,
     * and stored in the DB settings table. Note that BaseSettings refers to settings in the DB, while the settings class is
     * just a file in settngs/Settings.php containing other minor settings for the app like DB connection credentials etc
     *
     */
    public function getBaseSettings()
    {
        $settings = new BaseSettings();
        $allSettings = $settings->getAll('settings_id');
        return $allSettings;
    }






    public function manageUsers()
    {
        $view = DGZ_View::getAdminView('manageUsers', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show();
    }





    public function createUser()
    {
        $view = DGZ_View::getAdminView('createUser', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show();
    }




    public function doCreateUser()
    {
        $fn = $ln = $un = $newUserPw = $econfirm = false;
        $fail = "";

        $val = new DGZ_Validate();

        if(isset($_POST['new_user_fn']))
        {
            $fn = $val->fix_string($_POST['new_user_fn']);
        }

        if(isset($_POST['new_user_ln']))
        {
            $ln = $val->fix_string($_POST['new_user_ln']);
        }

        if(isset($_POST['new_user_un']))
        {
            $email = $val->fix_string($_POST['new_user_un']);
        }

        if (isset($_POST['new_user_pwd']))
        {
            $password = $val->fix_string($_POST['new_user_pwd']);
        }


        if (isset($_POST['new_user_pwd_confirm']))
        {
            $econfirm = $val->fix_string($_POST['new_user_pwd_confirm']);
        }


        $fail .= $val->validate_firstname($fn);
        $fail .= $val->validate_surname($ln);
        $fail .= $val->validate_email($email);

        $fail .= $val->validate_password($password);

        if ($password !== $econfirm)
        {
            $fail .= "Both passwords did not match!";
        }


        if ($fail == "")
        {
            $user_model = new Users();

            $userCreated = $user_model->createUser($fn, $ln, $email, $password);

            if ($userCreated === 1062)
            {
                $this->addErrors('That email address is already registered on this system');
                $this->redirect('admin', 'createUser');
            }
            elseif($userCreated == true)
            {
                $this->addSuccess('The new admin was created!', 'Great');
                $this->redirect('admin', 'manageUsers');
                exit();
            }
            elseif($userCreated == false)
            {
                $this->addErrors('The user could not be created. Try again later!', 'Sorry!');
                $this->redirect('admin', 'manageUsers');
                exit();
            }

        }
        else
        {
            $this->addErrors($fail);
            $this->postBack($_POST);
            $this->redirect('admin', 'createUser');
        }
    }







    public function editUser()
    {
        if ((isset($_GET['userId'])) && ($_GET['edit'] == 0))
        {
            $userId = $_GET['userId'];
            $user = new Users();
            $userForEdit = $user->getUserById($userId);

            $view = DGZ_View::getAdminView('editUser', $this, 'html');
            $this->setLayoutDirectory('admin');
            $this->setLayoutView('adminLayout');
            $view->show($userForEdit, $userId);
        }
        elseif((isset($_GET['edit'])) && ($_GET['edit'] == 1))
        {
            $fn = $ln = $un = $newUserPw = false;
            $fail = "";

            $userId = $_POST['userId'];
            $user = new Users();

            $userForEdit = $user->getUserById($userId);

            $val = new DGZ_Validate();


            if(isset($_POST['new_user_fn']))
            {
                $fn = $val->fix_string($_POST['new_user_fn']);
            }

            if(isset($_POST['new_user_ln']))
            {
                $ln = $val->fix_string($_POST['new_user_ln']);
            }

            if(isset($_POST['new_user_un']))
            {
                $email = $val->fix_string($_POST['new_user_un']);
            }

            if (isset($_POST['new_user_pwd']))
            {
                $password = $val->fix_string($_POST['new_user_pwd']);
            }

            $fail .= $val->validate_firstname($fn);
            $fail .= $val->validate_surname($ln);
            $fail .= $val->validate_email($email);

            $fail .= $val->validate_password($password);

            if ($fail == "")
            {
                $data = [
                    'users_type' =>  'admin',
                    'users_email' => $email,
                    'users_pass' => $password,
                    'users_first_name' => $fn,
                    'users_last_name' => $ln
                ];

                $where = ['users_id' => $userId];
                $updated = $user->update($data, $where);

                if ($updated)
                {
                    $this->addSuccess('The user was successfully updated!', 'Hooray');
                    $this->redirect('admin', 'manageUsers');
                }
            }
            else
            {
                $this->addErrors($fail);
                $view = DGZ_View::getAdminView('editUser', $this, 'html');
                $view->show($userForEdit, $userId);
            }
        }
    }







    public function adminUserChangePw()
    {
        if ((isset($_GET['userId'])) && ($_GET['change'] == 0))
        {
            $userId = $_GET['userId'];
            $user = new Users();
            $userForEdit = $user->getUserById($userId);

            $view = DGZ_View::getAdminView('adminUserChangePw', $this, 'html');
            $this->setLayoutDirectory('admin');
            $this->setLayoutView('adminLayout');
            $view->show($userForEdit, $userId);
        }
        elseif(
            (isset($_GET['change'])) && 
            ($_GET['change'] == 1)
         ) { 
            $un = $newUserPw = false;
            $fail = "";

            $userId = $_POST['userId'];
            $user = new Users();

            $userForEdit = $user->getUserById($userId);

            $val = new DGZ_Validate();


            if (isset($_POST['new_user_un'])) {
                $email = $val->fix_string($_POST['new_user_un']);
            }

            if (isset($_POST['new_user_pwd'])) {
                $password = $val->fix_string($_POST['new_user_pwd']);
            }

            $fail .= $val->validate_email($email);

            $fail .= $val->validate_password($password);

            if ($fail == "") {
                $data = [
                    'users_email' => $email, 'users_pass' => $password
                ];

                $where = ['users_id' => $userId];

                $updated = $user->update($data, $where);

                if ($updated) {
                    $userForEdit = $user->getUserById($userId);

                    $_SESSION['email'] = $userForEdit[0]['users_email'];
                    $_SESSION['pass'] = $userForEdit[0]['pass'];

                    $this->addSuccess('Your details were successfully updated!', 'Hooray');
                    $this->redirect('admin', 'dashboard');
                    exit();
                }
            }
            else {
                $this->addErrors($fail);
                $view = DGZ_View::getView('adminUserChangePw', $this, 'html');
                $view->show($userForEdit, $userId);
            }
        }
    }








    public function deleteUser()
    {
        $user = new Users();
        $userId = $_GET['userId'];
        $whereClause = ['users_id' => $userId];
        $deleted = $user->deleteWhere($whereClause);

        if ($deleted)
        {
            $this->addSuccess('The user was successfully deleted!');
            $this->redirect('admin', 'manageUsers');
            exit();
        }
    }






    public function contactMessages()
    {
        $settings= new ContactFormMessage();
        $contactMessages = $settings->getAll('contactformmessage_date DESC');

        $view = DGZ_View::getAdminView('manageContactMessages', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        //$this->setLayoutDirectory('seoMaster');
        //$this->setLayoutView('seoMasterLayout');
        $view->show($contactMessages);
    }





    public function deleteContactMessage($contactformmessage_id)
    {
        if ((isset($_GET['contactformmessage_id'])) && ($_GET['contactformmessage_id'] != "")) {
            $recId = $_GET['contactformmessage_id'];
            $contactFormClass = new ContactFormMessage();

            $whereClause = ['contactformmessage_id' => $recId];
            $deleted = $contactFormClass->deleteWhere($whereClause);
            if ($deleted) {
                $this->addSuccess('The contact message was successfully deleted');
                $this->redirect('admin', 'contactMessages');
                exit();
            }
        }

        $this->addErrors('Something went wrong, check the ID of the record you are trying to delete', 'Error');
        $this->redirect('admin', 'contactMessages');
    }






    public function baseSettings()
    {
        if (
            (!isset($_GET['change'])) || 
            (isset($_GET['change']) && $_GET['change'] == 0)
        )
        {
            $settings= new BaseSettings();
            $baseSettings = $settings->getAll();

            $view = DGZ_View::getAdminView('manageSettings', $this, 'html');
            $this->setLayoutDirectory('admin');
            $this->setLayoutView('adminLayout');
            /////$this->setLayoutDirectory('seoMaster');
            /////$this->setLayoutView('seoMasterLayout');
            $view->show($baseSettings);
        }
        elseif((isset($_GET['change'])) && ($_GET['change'] == 1)) {
            $settings= new BaseSettings();
            $table = $settings->getTable();

            foreach ($_POST as $field => $value)
            {
                $sql = "UPDATE ".$table." SET settings_value = '$value' WHERE settings_name = '$field'";
                $settings->query($sql);
            }

            $this->addSuccess('Your settings have been saved!', 'Great!');
            $this->redirect('admin', 'dashboard');
            exit();
        }
    }





}

