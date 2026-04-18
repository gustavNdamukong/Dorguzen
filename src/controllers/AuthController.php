<?php

namespace Dorguzen\Controllers;

use Dorguzen\Core\DGZ_View;
use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Core\DGZ_Validate;
use Dorguzen\Core\DGZ_Messenger;
use Dorguzen\Services\AuthService;

class AuthController extends DGZ_Controller
{
    public function __construct(private AuthService $authService)
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
        $this->checkReferral();
        $view = DGZ_View::getView('login', $this, 'html');
        $this->setPageTitle('Login');
        $this->setLayoutDirectory('seoMaster');
        $this->setLayoutView('seoMasterLayout');
        $view->show($email);
    }

    private function checkReferral()
    {
        if (isset($_GET['c']) && isset($_GET['m'])) {
            $_SESSION['referBack']['c'] = $_GET['c'];
            $_SESSION['referBack']['m'] = $_GET['m'];
        } elseif (isset($_GET['c'])) {
            $_SESSION['referBack']['c'] = $_GET['c'];
            $_SESSION['referBack']['m'] = "";
        }
    }

    public function signup()
    {
        $old = $_SESSION['_old_signup'] ?? [];
        unset($_SESSION['_old_signup']);

        $view = DGZ_View::getView('register', $this, 'html');
        $this->setPageTitle('Register');
        $this->setLayoutDirectory('seoMaster');
        $this->setLayoutView('seoMasterLayout');
        $view->show(
            $old['firstname'] ?? '',
            $old['surname']   ?? '',
            $old['phone']     ?? '',
            $old['email']     ?? ''
        );
    }

    private function flashOldSignupInput(): void
    {
        $val = new DGZ_Validate();
        $_SESSION['_old_signup'] = [
            'firstname' => $val->fix_string($_POST['firstname'] ?? ''),
            'surname'   => $val->fix_string($_POST['surname']   ?? ''),
            'email'     => $val->fix_string($_POST['email']     ?? ''),
            'phone'     => $val->fix_string($_POST['phone']     ?? ''),
        ];
    }

    public function register()
    {
        if (!$this->config->getConfig()['allow_registration']) {
            $this->addErrors('Registration is not allowed!');
            $this->redirect('home');
            return;
        }

        $val = new DGZ_Validate();

        if (empty($_POST)) {
            $this->addErrors('You did not fill in the form');
            $this->redirect('auth', 'signup');
            return;
        }

        if (!array_key_exists('agreeToTerms', $_POST)) {
            $this->flashOldSignupInput();
            $this->addErrors('Please accept our Terms and Conditions', 'Alert!');
            $this->redirect('auth', 'signup');
            return;
        }

        // Honeypot spam protection
        if (!empty($_POST['captcha_hidden'] ?? '')) {
            $this->authService->logBotAttempt('Spam bot registration attempt', implode('|', $_POST));
            $this->redirect('home');
            return;
        }

        $firstname = isset($_POST['firstname']) ? $val->fix_string($_POST['firstname']) : '';
        $surname   = isset($_POST['surname'])   ? $val->fix_string($_POST['surname'])   : '';
        $password  = isset($_POST['pwd'])       ? $val->fix_string($_POST['pwd'])       : '';
        $retyped   = isset($_POST['conf_pwd'])  ? $val->fix_string($_POST['conf_pwd'])  : '';
        $phone     = isset($_POST['phone'])     ? $val->fix_string($_POST['phone'])     : '';
        $email     = isset($_POST['email'])     ? $val->fix_string($_POST['email'])     : '';

        $fail = $this->authService->validateRegistrationInput($firstname, $surname, $password, $retyped, $email);

        if ($fail !== '') {
            $_SESSION['_old_signup'] = compact('firstname', 'surname', 'email', 'phone');
            $this->addErrors($fail);
            $this->redirect('auth', 'signup');
            return;
        }

        $activationCode = md5(uniqid(rand(), true));
        $saved = $this->authService->registerNewUser([
            'user_type'      => 'member',
            'email'          => $email,
            'password'       => $password,
            'firstname'      => $firstname,
            'surname'        => $surname,
            'phone'          => $phone,
            'emailverified'  => 'no',
            'activationCode' => $activationCode,
        ]);

        if ($saved === 1062) {
            $_SESSION['_old_signup'] = compact('firstname', 'surname', 'email', 'phone');
            $this->addErrors('That email address is already registered');
            $this->redirect('auth', 'signup');
            return;
        }

        if ($saved) {
            $appName = $this->config->getConfig()['appName'];
            $appURL  = $this->config->getConfig()['appURL'];
            $subject = "Activate your {$appName} account";
            $message = "<h2>Your account has been created on {$appName}</h2>
                        <p>Click below to activate your account:</p>
                        <p><a href='{$appURL}/auth/verifyEmail?em={$activationCode}'>Activate account</a></p>";

            $messenger = new DGZ_Messenger();
            $messenger->sendEmailActivationEmail($firstname, $email, $subject, $message);
            $_SESSION['activationCode'] = $activationCode;
            $this->redirect('auth', 'emailActivationInstructions');
        } else {
            $this->addErrors('Something went wrong. Please try again or contact us.');
            $this->redirect('auth', 'signup');
        }
    }

    public function checkEmail()
    {
        $val   = new DGZ_Validate();
        $email = isset($_POST['email']) ? $val->fix_string($_POST['email']) : '';

        if ($val->validate_email($email) !== '') {
            die("<b style='color:red'>&nbsp;&larr; Please enter a valid email address.</b>");
        }

        if ($this->authService->emailExists($email)) {
            die("<b style='color:red'>&nbsp;&larr; That email address is already registered.</b>");
        }

        die("<b style='color:green'>&nbsp;&larr; Email is available.</b>");
    }


    public function emailActivationInstructions()
    {
        $view = DGZ_View::getView('howToActivateEmailAfterRegis', $this, 'html');
        $this->setLayoutDirectory('seoMaster');
        $this->setLayoutView('seoMasterLayout');
        $view->show();
    }

    public function verifyEmail()
    {
        if (!isset($_GET['em'])) {
            $this->redirect('home');
            return;
        }

        $val  = new DGZ_Validate();
        $code = $val->fix_string($_GET['em']);

        $user = $this->authService->getUserByActivationCode($code);

        if (!$user) {
            $this->addErrors("We couldn't find your account details.", 'Sorry');
            $this->redirect('home');
            return;
        }

        $activated = $this->authService->activateUserEmail($code);

        if ($activated) {
            $this->addSuccess('Your email has been verified! You can now log in.', 'Great');
            $this->redirect('auth', 'login');
        } else {
            $this->addErrors('Could not activate your account. Please contact us.', 'Sorry');
            $this->redirect('home');
        }
    }

    

    public function doLogin()
    {
        $val  = new DGZ_Validate();
        $fail = '';

        if (!isset($_POST['login_email']) || ($_POST['forgotstatus'] ?? '') !== 'no') {
            if (isset($_POST['forgotstatus']) && $_POST['forgotstatus'] === 'yes') {
                $this->handleForgotPassword($val);
                return;
            }
            $this->redirect('auth', 'login');
            return;
        }

        $email    = $val->fix_string($_POST['login_email'] ?? '');
        $password = $val->fix_string($_POST['login_pwd']   ?? '');
        $rem_me   = $_POST['rem_me'] ?? false;

        $fail .= $this->authService->validateLoginInput($email, $password);

        if ($fail !== '') {
            $this->addErrors($fail, 'Error!');
            $this->redirect('auth', 'login');
            return;
        }

        $authenticated = $this->authService->authenticateUser($email, $password);

        if (!$authenticated) {
            $this->addErrors('Either the email or password was wrong. Please try again.', 'Oops!');
            $this->redirect('auth', 'login');
            return;
        }

        if (!session_id()) { session_start(); }
        $_SESSION['authenticated'] = 'Let Go-' . $this->config->getConfig()['appName'];
        $_SESSION['start']         = time();
        session_regenerate_id();

        $_SESSION['custo_id']   = $authenticated['users_id'];
        $_SESSION['user_type']  = $authenticated['users_type'];
        $_SESSION['first_name'] = $authenticated['users_first_name'];
        $_SESSION['last_name']  = $authenticated['users_last_name'];
        $_SESSION['email']      = $authenticated['users_email'];
        $_SESSION['phone']      = $authenticated['users_phone_number'];
        $_SESSION['created']    = $authenticated['users_created'];
        session_write_close();

        if (in_array($authenticated['users_type'], ['admin', 'admin_gen', 'super_admin'])) {
            $this->authService->logAdminLogin($authenticated);
        }

        if ($rem_me) {
            $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
            setcookie('rem_me', $email, [
                'expires'  => time() + 345600,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        $isAdmin = in_array($authenticated['users_type'], ['admin', 'admin_gen', 'super_admin']);

        $controller = $isAdmin ? 'admin' : 'user';
        $method     = 'dashboard';
        if (isset($_SESSION['referBack']['c'])) {
            $controller = $_SESSION['referBack']['c'];
            $method     = $_SESSION['referBack']['m'] ?? '';
        }
        unset($_SESSION['referBack']);

        $this->addSuccess('Welcome!', 'Hey');
        $this->redirect($controller, $method);
    }

    private function handleForgotPassword(DGZ_Validate $val)
    {
        $email = $val->fix_string($_POST['forgot_pass_input'] ?? '');
        $fail  = $this->authService->validateForgotPasswordInput($email);

        if ($fail !== '') {
            $this->addErrors($fail);
            $this->redirect('auth', 'login');
            return;
        }

        $found = $this->authService->getUserForPasswordReset($email);

        if (!$found) {
            $this->addErrors('Sorry, we could not find an account with that email.', 'Error!');
            $this->redirect('auth', 'login');
            return;
        }

        $resetCode = $this->generateCode();
        $saved     = $this->authService->savePasswordResetRecord($found, $resetCode);

        if ($saved) {
            $mailer = new DGZ_Messenger();
            $mailer->sendPasswordResetEmail($found['users_email'], $found['users_firstname'], $resetCode);
            $this->addSuccess('A password reset link has been sent to your email.', 'Thank you');
        } else {
            $this->addErrors('Something went wrong. Please try again later.');
        }
        $this->redirect('auth', 'login');
    }

    public function reset()
    {
        if (!isset($_GET['em'])) {
            $this->redirect('auth', 'login');
            return;
        }

        $val       = new DGZ_Validate();
        $resetCode = $val->fix_string($_GET['em']);

        $record = $this->authService->fetchAndConsumePasswordReset($resetCode);

        if (!$record) {
            $this->addErrors('Invalid or expired reset link.', 'Error!');
            $this->redirect('auth', 'login');
            return;
        }

        if (strtotime($record['password_reset_date']) <= strtotime('-2 hours')) {
            $this->addWarning('Your reset link has expired. Please request a new one.');
            $this->redirect('auth', 'login');
            return;
        }

        $view = DGZ_View::getView('resetPw', $this, 'html');
        $this->setLayoutDirectory('seoMaster');
        $this->setLayoutView('seoMasterLayout');
        $view->show([
            'userId'    => $record['password_reset_users_id'],
            'userEmail' => $record['password_reset_email'],
        ]);
    }

    public function resetPw()
    {
        $val = new DGZ_Validate();

        $reset_user_id  = isset($_POST['reset_user_id'])  ? $val->fix_string($_POST['reset_user_id'])  : '';
        $reset_email    = isset($_POST['reset_email'])     ? $val->fix_string($_POST['reset_email'])    : '';
        $reset_pwd      = isset($_POST['reset_pwd'])       ? $val->fix_string($_POST['reset_pwd'])      : '';
        $reset_conf_pwd = isset($_POST['reset_conf_pwd'])  ? $val->fix_string($_POST['reset_conf_pwd']) : '';

        $fail = $this->authService->validatePasswordResetInput($reset_user_id, $reset_email, $reset_pwd, $reset_conf_pwd);

        if ($fail === '') {
            $updated = $this->authService->resetUserPassword($reset_user_id, $reset_email, $reset_pwd);

            if ($updated) {
                $this->addSuccess('Your password was successfully updated. You can now log in.');
                $this->redirect('auth', 'login');
            } else {
                $this->addErrors('Try a different password. If it fails again, contact us.', 'Error!');
                $this->redirect('auth', 'login');
            }
        } else {
            $this->addErrors($fail);
            $view = DGZ_View::getView('resetPw', $this, 'html');
            $this->setLayoutDirectory('seoMaster');
            $this->setLayoutView('seoMasterLayout');
            $view->show(['userId' => $reset_user_id, 'userEmail' => $reset_email]);
        }
    }

    public function logout()
    {
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 86400, '/');
        }
        if (isset($_COOKIE['rem_me'])) {
            setcookie('rem_me', '', time() - 86400, '/');
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $this->redirect('home');
    }
}
