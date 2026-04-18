<?php

namespace Dorguzen\Views;

class home extends \Dorguzen\Core\DGZ_HtmlView
{
    function show()
    {
        $this->addMetadata(
            [
                '<meta name="description" content="Dorguzen — a PHP MVC framework for rapid web development.">',
                '<meta name="keywords" content="PHP, MVC, framework, Dorguzen, web development">',
                '<meta name="author" content="Gustav Ndamukong">'
            ]); ?>

    <!-- Hero Start -->
    <div class="container-xxl py-5 bg-primary hero-header mb-5">
        <div class="container my-5 py-5 px-lg-5">
            <div class="row g-5 py-5">
                <div class="col-12 text-center">
                    <h1 class="text-white animated slideInDown">Build Fast. Build Right.</h1>
                    <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item text-white">
                                Welcome to the <strong>&nbsp;Dorguzen PHP MVC Framework</strong>
                            </li>
                        </ol>
                    </nav>
                    <a href="<?=$this->route('feedback')?>" class="btn btn-light py-sm-3 px-sm-5 rounded-pill mt-3 animated slideInLeft">Contact Us</a>
                    <a href="<?=$this->route('auth/login')?>" class="btn btn-outline-light py-sm-3 px-sm-5 rounded-pill mt-3 animated slideInRight">Admin Login</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->


    <!-- START SIDE SLIDE-IN MENU -->
    <?php
    $slideInMenu = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
    $slideInMenu->show();
    ?>
    <!-- END OF SIDE SLIDE-IN MENU -->

    <!-- Features Start -->
    <div class="container-xxl py-5">
        <div class="container px-lg-5">
            <div class="row g-4">

                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="feature-item bg-light rounded p-4">
                        <div class="feature-icon bg-primary mb-4">
                            <i class="fa fa-code fa-2x text-white"></i>
                        </div>
                        <h5 class="mb-3">MVC Architecture</h5>
                        <p class="m-0">Clean separation of concerns with Controllers, Models, and Views — build maintainable applications from day one.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="feature-item bg-light rounded p-4">
                        <div class="feature-icon bg-primary mb-4">
                            <i class="fa fa-database fa-2x text-white"></i>
                        </div>
                        <h5 class="mb-3">ORM & Multi-DB Support</h5>
                        <p class="m-0">Built-in ORM with support for MySQL (mysqli & PDO), PostgreSQL, and SQLite — switch drivers with a single config change.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="feature-item bg-light rounded p-4">
                        <div class="feature-icon bg-primary mb-4">
                            <i class="fa fa-shield-alt fa-2x text-white"></i>
                        </div>
                        <h5 class="mb-3">Auth & Admin Panel</h5>
                        <p class="m-0">Comes with a ready-made admin panel, user management, login/logout, password reset, and contact message inbox.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="feature-item bg-light rounded p-4">
                        <div class="feature-icon bg-primary mb-4">
                            <i class="fa fa-plug fa-2x text-white"></i>
                        </div>
                        <h5 class="mb-3">Modules System</h5>
                        <p class="m-0">Toggle optional modules (SEO, Payments, SMS) on or off from a single config file. Extend with your own modules.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="feature-item bg-light rounded p-4">
                        <div class="feature-icon bg-primary mb-4">
                            <i class="fa fa-terminal fa-2x text-white"></i>
                        </div>
                        <h5 class="mb-3">CLI Tool</h5>
                        <p class="m-0">Run migrations, generate controllers and models, seed the database, and manage queues — all from the command line.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="feature-item bg-light rounded p-4">
                        <div class="feature-icon bg-primary mb-4">
                            <i class="fa fa-envelope fa-2x text-white"></i>
                        </div>
                        <h5 class="mb-3">Email & Messaging</h5>
                        <p class="m-0">SMTP email via PHPMailer, built-in contact form handling, and an admin inbox to manage messages from your users.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Features End -->


    <!-- Call To Action Start -->
    <div class="container-xxl py-5">
        <div class="container px-lg-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="text-primary-gradient fw-medium">Get Started</h5>
                    <h1 class="mb-4">Your Next App Starts Here</h1>
                    <p class="mb-4">Dorguzen ships with everything you need to build a production-ready web application in PHP — routing, ORM, auth, layouts, modules, and more. No bloat, full control.</p>
                    <a href="<?=$this->route('auth/login')?>" class="btn btn-primary py-sm-3 px-sm-5 rounded-pill me-3 animated slideInLeft">Admin Panel</a>
                    <a href="<?=$this->route('feedback')?>" class="btn btn-outline-primary py-sm-3 px-sm-5 rounded-pill animated slideInRight">Contact Us</a>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="bg-light rounded p-4">
                        <pre class="mb-0" style="font-size:13px; color:#333;"><code>// Define a route
Route::get('home', 'HomeController', 'index');

// Your controller
class HomeController extends DGZ_Controller {
    public function index() {
        $view = DGZ_View::getView('home', $this);
        $this->setPageTitle('Home');
        $view->show();
    }
}

// Your model
class Post extends DGZ_Model {
    public function getLatest() {
        return $this->getAll('created_at');
    }
}</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Call To Action End -->

<?php
    }
}
