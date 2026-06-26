<?php

/** @var Dorguzen\Core\DGZ_Router $router */

// -----------------------------------------------------------------------
// PUBLIC ROUTES
// -----------------------------------------------------------------------
$router->get('/',          'HomeController@defaultAction')->name('home');
$router->get('/terms',     'PagesController@terms')->name('terms');
$router->get('/privacy',   'PagesController@privacy')->name('privacy');
$router->get('/home',      'HomeController@homeRedirect');   // 301 -> canonical "/" (avoids duplicate homepage URL)

$router->get("/portfolio",                "PortfolioController@portfolio");
$router->get('/search',                   'SearchController@search');

// Gallery module (public)
$router->get('/gallery',              'GalleryController@index',  'gallery')->name('gallery');
$router->get('/gallery/album',        'GalleryController@album',  'gallery');
$router->get('/news',              'NewsController@news')->name('news');
$router->get('/news/article',      'NewsController@article');

$router->post('/subscribe',                          'NewsletterController@subscribe');
$router->get('/unsubscribe',                         'NewsletterController@unsubscribe');

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
$router->get('/admin/login',        'AdminController@login');   // public — redirects to auth/login
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

$router->get('/admin/subscribers',                   'NewsletterController@manageSubscribers');
$router->get('/admin/subscribers/delete',            'NewsletterController@deleteSubscriber');
$router->post('/admin/subscribers/sendWelcome',      'NewsletterController@sendWelcomeEmails');
$router->post('/admin/subscribers/sendBulk',         'NewsletterController@sendBulkEmail');

$router->get('/admin/newsletters',                   'NewsletterController@manageNewsletters');
$router->get('/admin/newsletters/create',            'NewsletterController@createNewsletter');
$router->post('/admin/newsletters/create',           'NewsletterController@createNewsletter');
$router->get('/admin/newsletters/delete',            'NewsletterController@deleteNewsletter');

$router->get('/admin/portfolio',           'PortfolioController@managePortfolio');
$router->get('/admin/portfolio/create',   'PortfolioController@createPortfolio');
$router->post('/admin/portfolio/create',  'PortfolioController@createPortfolio');
$router->get('/admin/portfolio/delete',   'PortfolioController@deletePortfolio');

$router->get('/videos',                       'VideosController@index',          'videos');
$router->get('/videos/album',                 'VideosController@album',           'videos');

$router->get('/admin/videos',                 'VideosController@manageAlbums',    'videos');
$router->get('/admin/videos/create',          'VideosController@createAlbum',     'videos');
$router->post('/admin/videos/create',         'VideosController@createAlbum',     'videos');
$router->get('/admin/videos/delete',          'VideosController@deleteAlbum',     'videos');
$router->get('/admin/videos/videos',          'VideosController@manageVideos',    'videos');
$router->post('/admin/videos/addVideo',       'VideosController@addVideo',        'videos');
$router->get('/admin/videos/deleteVideo',     'VideosController@deleteVideo',     'videos');

$router->get('/admin/gallery',                'GalleryController@manageAlbums',  'gallery');
$router->get('/admin/gallery/create',         'GalleryController@createAlbum',   'gallery');
$router->post('/admin/gallery/create',        'GalleryController@createAlbum',   'gallery');
$router->get('/admin/gallery/delete',         'GalleryController@deleteAlbum',   'gallery');
$router->get('/admin/gallery/images',         'GalleryController@manageImages',  'gallery');
$router->post('/admin/gallery/upload',        'GalleryController@uploadImages',  'gallery');
$router->get('/admin/gallery/deleteImage',    'GalleryController@deleteImage',   'gallery');
$router->post('/admin/gallery/setCover',      'GalleryController@setCover',      'gallery');
$router->post('/admin/gallery/setFeatured',   'GalleryController@setFeatured',   'gallery');

// Blog module
$router->get('/blog',                         'BlogController@index',          'blog');
$router->get('/blog/post',                    'BlogController@post',           'blog');
$router->post('/blog/comment',                'BlogController@comment',        'blog');

$router->get('/admin/blog',                   'BlogController@managePosts',    'blog');
$router->get('/admin/blog/create',            'BlogController@createPost',     'blog');
$router->post('/admin/blog/create',           'BlogController@createPost',     'blog');
$router->get('/admin/blog/edit',              'BlogController@editPost',       'blog');
$router->post('/admin/blog/edit',             'BlogController@editPost',       'blog');
$router->get('/admin/blog/delete',            'BlogController@deletePost',     'blog');
$router->post('/admin/blog/saveCategory',     'BlogController@saveCategory',   'blog');
$router->get('/admin/blog/deleteCategory',    'BlogController@deleteCategory', 'blog');
$router->get('/admin/blog/comments',          'BlogController@manageComments', 'blog');
$router->get('/admin/blog/approveComment',    'BlogController@approveComment', 'blog');
$router->get('/admin/blog/deleteComment',     'BlogController@deleteComment',  'blog');

// -----------------------------------------------------------------------
// TESTIMONIALS MODULE ROUTES
// -----------------------------------------------------------------------
$router->get('/testimonials',                   'TestimonialsController@index',   'testimonials');
$router->post('/testimonials/submit',           'TestimonialsController@submit',  'testimonials');
$router->get('/admin/testimonials',             'TestimonialsController@manage',  'testimonials');
$router->post('/admin/testimonials/approve',    'TestimonialsController@approve', 'testimonials');
$router->get('/admin/testimonials/delete',      'TestimonialsController@delete',  'testimonials');

$router->get('/admin/news',            'NewsController@manageNews');
$router->get('/admin/news/create',    'NewsController@createNews');
$router->post('/admin/news/create',   'NewsController@createNews');
$router->get('/admin/news/delete',    'NewsController@deleteNews');

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
