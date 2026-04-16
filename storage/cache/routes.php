<?php

return array (
  0 => 
  array (
    'method' => 'GET',
    'uri' => '/',
    'action' => 'HomeController@defaultAction',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => 'home',
  ),
  1 => 
  array (
    'method' => 'GET',
    'uri' => '/home',
    'action' => 'HomeController@defaultAction',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  2 => 
  array (
    'method' => 'GET',
    'uri' => '/feedback',
    'action' => 'FeedbackController@contact',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => 'contact',
  ),
  3 => 
  array (
    'method' => 'POST',
    'uri' => '/feedback/processContact',
    'action' => 'FeedbackController@processContact',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  4 => 
  array (
    'method' => 'GET',
    'uri' => '/auth/login',
    'action' => 'AuthController@login',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => 'login',
  ),
  5 => 
  array (
    'method' => 'POST',
    'uri' => '/auth/doLogin',
    'action' => 'AuthController@doLogin',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  6 => 
  array (
    'method' => 'GET',
    'uri' => '/auth/logout',
    'action' => 'AuthController@logout',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => 'logout',
  ),
  7 => 
  array (
    'method' => 'GET',
    'uri' => '/auth/signup',
    'action' => 'AuthController@signup',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => 'signup',
  ),
  8 => 
  array (
    'method' => 'POST',
    'uri' => '/auth/register',
    'action' => 'AuthController@register',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  9 => 
  array (
    'method' => 'GET',
    'uri' => '/auth/verifyEmail',
    'action' => 'AuthController@verifyEmail',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  10 => 
  array (
    'method' => 'GET',
    'uri' => '/auth/emailActivationInstructions',
    'action' => 'AuthController@emailActivationInstructions',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  11 => 
  array (
    'method' => 'GET',
    'uri' => '/auth/reset',
    'action' => 'AuthController@reset',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  12 => 
  array (
    'method' => 'POST',
    'uri' => '/auth/resetPw',
    'action' => 'AuthController@resetPw',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  13 => 
  array (
    'method' => 'GET',
    'uri' => '/admin',
    'action' => 'AdminController@dashboard',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => 'admin.dashboard',
  ),
  14 => 
  array (
    'method' => 'GET',
    'uri' => '/admin/dashboard',
    'action' => 'AdminController@dashboard',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  15 => 
  array (
    'method' => 'GET',
    'uri' => '/admin/manageUsers',
    'action' => 'AdminController@manageUsers',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  16 => 
  array (
    'method' => 'GET',
    'uri' => '/admin/createUser',
    'action' => 'AdminController@createUser',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  17 => 
  array (
    'method' => 'POST',
    'uri' => '/admin/doCreateUser',
    'action' => 'AdminController@doCreateUser',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  18 => 
  array (
    'method' => 'GET',
    'uri' => '/admin/editUser',
    'action' => 'AdminController@editUser',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  19 => 
  array (
    'method' => 'POST',
    'uri' => '/admin/editUser',
    'action' => 'AdminController@editUser',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  20 => 
  array (
    'method' => 'GET',
    'uri' => '/admin/deleteUser',
    'action' => 'AdminController@deleteUser',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  21 => 
  array (
    'method' => 'GET',
    'uri' => '/admin/adminUserChangePw',
    'action' => 'AdminController@adminUserChangePw',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  22 => 
  array (
    'method' => 'POST',
    'uri' => '/admin/adminUserChangePw',
    'action' => 'AdminController@adminUserChangePw',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  23 => 
  array (
    'method' => 'GET',
    'uri' => '/admin/contactMessages',
    'action' => 'AdminController@contactMessages',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  24 => 
  array (
    'method' => 'GET',
    'uri' => '/admin/deleteContactMessage',
    'action' => 'AdminController@deleteContactMessage',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  25 => 
  array (
    'method' => 'GET',
    'uri' => '/admin/baseSettings',
    'action' => 'AdminController@baseSettings',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  26 => 
  array (
    'method' => 'POST',
    'uri' => '/admin/baseSettings',
    'action' => 'AdminController@baseSettings',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  27 => 
  array (
    'method' => 'GET',
    'uri' => '/admin/log',
    'action' => 'AdminController@log',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  28 => 
  array (
    'method' => 'GET',
    'uri' => '/admin/log_errors_only',
    'action' => 'AdminController@log_errors_only',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  29 => 
  array (
    'method' => 'GET',
    'uri' => '/admin/logAdminLogins',
    'action' => 'AdminController@logAdminLogins',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  30 => 
  array (
    'method' => 'GET',
    'uri' => '/exception/error',
    'action' => 'ExceptionController@error',
    'apiVersion' => '',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => 'exception.error',
  ),
  31 => 
  array (
    'method' => 'POST',
    'uri' => '/api/v1/auth/register',
    'action' => 'AuthApi@register',
    'apiVersion' => 'v1',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  32 => 
  array (
    'method' => 'POST',
    'uri' => '/api/v1/auth/login',
    'action' => 'AuthApi@login',
    'apiVersion' => 'v1',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  33 => 
  array (
    'method' => 'POST',
    'uri' => '/api/v1/auth/refresh',
    'action' => 'AuthApi@refresh',
    'apiVersion' => 'v1',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  34 => 
  array (
    'method' => 'GET',
    'uri' => '/api/v1/docs',
    'action' => 'Docs@index',
    'apiVersion' => 'v1',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  35 => 
  array (
    'method' => 'GET',
    'uri' => '/api/v1/docs/spec',
    'action' => 'Docs@spec',
    'apiVersion' => 'v1',
    'module' => '',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
);
