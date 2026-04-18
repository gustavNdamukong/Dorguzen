<?php

/** @var Dorguzen\Core\DGZ_Router $router */

// -----------------------------------------------------------------------
// PUBLIC ROUTES
// -----------------------------------------------------------------------
$router->get('/',          'HomeController@defaultAction')->name('home');
$router->get('/home',      'HomeController@defaultAction');

$router->get('/feedback',          'FeedbackController@contact')->name('contact');
$router->post('/feedback/processContact', 'FeedbackController@processContact');

// -----------------------------------------------------------------------
// AUTH ROUTES
// -----------------------------------------------------------------------
$router->get('/auth/login',    'AuthController@login')->name('login');
$router->post('/auth/doLogin', 'AuthController@doLogin');
$router->get('/auth/logout',   'AuthController@logout')->name('logout');
$router->get('/auth/signup',   'AuthController@signup')->name('signup');
$router->post('/auth/register',  'AuthController@register');
$router->post('/auth/checkEmail','AuthController@checkEmail');
$router->get('/auth/verifyEmail',               'AuthController@verifyEmail');
$router->get('/auth/emailActivationInstructions','AuthController@emailActivationInstructions');
$router->get('/auth/reset',    'AuthController@reset');
$router->post('/auth/resetPw', 'AuthController@resetPw');

// -----------------------------------------------------------------------
// USER ROUTES (authenticated members)
// -----------------------------------------------------------------------
$router->get('/user/dashboard', 'UserController@dashboard');
$router->get('/user/changePw',  'UserController@changePw');
$router->post('/user/changePw', 'UserController@changePw');

// -----------------------------------------------------------------------
// ADMIN ROUTES (protected)
// -----------------------------------------------------------------------
$router->get('/admin',              'AdminController@dashboard')->name('admin.dashboard');
$router->get('/admin/dashboard',    'AdminController@dashboard');

$router->get('/admin/manageUsers',  'AdminController@manageUsers');
$router->get('/admin/createUser',   'AdminController@createUser');
$router->post('/admin/doCreateUser','AdminController@doCreateUser');
$router->get('/admin/editUser',     'AdminController@editUser');
$router->post('/admin/editUser',    'AdminController@editUser');
$router->get('/admin/deleteUser',   'AdminController@deleteUser');

$router->get('/admin/adminUserChangePw',  'AdminController@adminUserChangePw');
$router->post('/admin/adminUserChangePw', 'AdminController@adminUserChangePw');

$router->get('/admin/contactMessages',       'AdminController@contactMessages');
$router->get('/admin/deleteContactMessage',  'AdminController@deleteContactMessage');

$router->get('/admin/baseSettings',  'AdminController@baseSettings');
$router->post('/admin/baseSettings', 'AdminController@baseSettings');

$router->get('/admin/log',             'AdminController@log');
$router->get('/admin/log_errors_only', 'AdminController@log_errors_only');
$router->get('/admin/logAdminLogins',  'AdminController@logAdminLogins');

// -----------------------------------------------------------------------
// EXCEPTION / ERROR ROUTES
// -----------------------------------------------------------------------
$router->get('/exception/error', 'ExceptionController@error')->name('exception.error');

// -----------------------------------------------------------------------
// TEST ROUTES (used by PHPUnit feature tests only — not for production use)
// -----------------------------------------------------------------------
$router->get('/ping',           'TestController@ping');        // PASS
$router->post('/echo',          'TestController@echo');        // PASS
$router->post('/echoJson',      'TestController@echoJson');    // PASS
$router->get('/me',             'TestController@meTest');      // PASS
