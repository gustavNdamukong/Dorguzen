<?php

namespace Dorguzen\Core;


use Dorguzen\Models\Users;
use Dorguzen\Config\Config;
use Dorguzen\Models\Logs;
use Dorguzen\Core\DGZ_Validate;
use Dorguzen\Controllers\HomeController;

class DGZ_Auth
{
    protected $user = null;

    protected $id = null;

    protected Config $config;

    protected Logs $logs;

    public function __construct(Config $config, Logs $logs)  
    {
        $this->config = $config;
        $this->logs = $logs;

        $this->loadUserFromSession();
    } 

    private function clearUser()
    {
        $this->user = null;
        $this->id = null;
    }

    protected function loadUserFromSession() 
    {
        if (isset($_SESSION['authenticated']) && isset($_SESSION['custo_id']))
        {
            $userModel = new Users($this->config);
            $this->user = $userModel->loadData($_SESSION['custo_id']); 
            $this->id = $_SESSION['custo_id'];
        }
    } 



    public function check(): bool  
    {
        return $this->user !== null && !empty($this->user->getData());
    } 



    public function guest(): bool 
    {
        return !$this->check();
    }



    public function id(): ?int  
    {
        return $this->id ?? null;
    }



    /**
     * @return object Users
     */
    public function user(): object|null  
    {
        return $this->user;
    }



    /**
     * login()
     * @param string $username
     * @param string $assword
     */
    public function login($un, $pwd, $rememberMe = false): bool
    {
        $username = $password = $fail = '';

        $val = new DGZ_Validate();
        $username = $val->fix_string($un);
        $password = $val->fix_string($pwd);

        $fail .= $val->validate_username($username);
        $fail .= $val->validate_password($password);

        if ($fail == "") 
        {
            $user_model = new Users($this->config);
            $loginData = ['users_username' => $username, 'users_pass' => $password];
            $authenticated = $user_model->authenticateUser($loginData);

            if ($authenticated)
            {
                if (!session_id()) { session_start(); } 

                //Let Go-yourAppName is the secret string u'll check to confirm that a user is logged in. You can change that.
                $_SESSION['authenticated'] = 'Let Go-'.$this->config->getConfig()['appName']; 
                $_SESSION['start'] = time();
                session_regenerate_id();
                $_SESSION['custo_id'] = $authenticated['users_id'];
                $_SESSION['user_type'] = $authenticated['users_type'];
                $_SESSION['username'] = $authenticated['users_username'];
                $_SESSION['email'] = $authenticated['users_email'];
                $_SESSION['first_name'] = $authenticated['users_first_name'];
                $_SESSION['last_name'] = $authenticated['users_last_name'];
                $_SESSION['google_id'] = $authenticated['users_google_id'];
                $_SESSION['phone_number'] = $authenticated['users_phone_number'];
                $_SESSION['mm_account'] = $authenticated['users_mobile_money_account'];
                $_SESSION['emailverified'] = $authenticated['users_emailverified'];
                $_SESSION['created'] = $authenticated['users_created'];

                session_write_close();

                $this->user = $user_model->loadData($authenticated['users_id']); 
                $this->id = $authenticated['users_id'];

                // log admin activity
                if (in_array($authenticated['users_type'], ['admin', 'admin_gen', 'super_admin'])) 
                {
                    $logTitle = 'Admin login';
                    $logData = 'User type: '. $authenticated['users_type'].
                        ' | Firstname: '.$authenticated['users_first_name'].
                        ' | Surname: ' . $authenticated['users_last_name'].
                        ' | Username: ' . $authenticated['users_username'].
                        ' | Time: ' . date("d-m-y h:i:s");
                    $this->logs->log($logTitle, $logData);
                }

                if ($rememberMe)
                {
                    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
                    setcookie(
                        'rem_me',
                        $username,
                        [
                            'expires' => time() + 345600, // 96 hours (4 days)
                            'path' => '/',
                            'domain' => '',  
                            'secure' => $isSecure, 
                            'httponly' => true, 
                            'samesite' => 'Lax'
                        ]
                    );
                }

                return true;
            }
        }

        return false;
    }



    public function logout()
    {
        $_SESSION = array();

        // invalidate the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 86400, '/');
        }

        //This is the cookie i set with rem_me at log in, we delete it coz the user wants to be logged out.
        if (isset($_COOKIE['rem_me']))
        {
            setcookie('rem_me', '', time()-86400, '/');
        }

        // end session and redirect
        if (session_status() != PHP_SESSION_NONE) {
            session_destroy();
        }

        $this->clearUser();

        // redirect user
        $controller = new HomeController();
        $controller->redirect('auth', 'login'); 
    }




    public function isAdmin(): bool 
    {
        if ($this->check())
        {
            if (
                    ($this->user->getData()['users_type'] == 'admin') || 
                    ($this->user->getData()['users_type'] == 'admin_gen') || 
                    ($this->user->getData()['users_type'] == 'super_admin')
            ) 
            {
                return true;
            }
            else {
                return false;
            }
        }
        else 
        {
            return false;
        }
    }



    public function hasRole($role): bool 
    {
        return $this->user && $this->user->getData()['users_type'] === $role; 
    }



    public function role(): string|null  
    {
        return $this->user->getData()['users_type'] ?? null;
    }



    public function isEmailVerified(): bool  
    {
        return $this->user && $this->user->getData()['users_emailverified'] == 'yes';
    }



    public function username(): ?string  
    {
        return $this->user->getData()['users_username'] ?? null;
    }
}

