<?php

namespace Dorguzen\Views;

class dashboard extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show()
    {
        if (!Auth()->check()) {
            header('Location: ' . $this->controller->config->getFileRootPath() . 'auth/login');
            exit;
        }
        ?>

        <!-- Hero Header Start -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn">Dashboard</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>">Home</a></li>
                                <li class="breadcrumb-item text-white active" aria-current="page">Dashboard</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero Header End -->

        <!-- PAGE CONTENT - START -->
        <div class="main">
            <section class="content account bg-white">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-3">
                            <aside class="sidebar">
                                <div class="card" style="background-color:#4777F5;">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Quick links</h5>
                                        <div class="list-group">
                                            <a href="<?= $this->controller->config->getFileRootPath() ?>user/dashboard" class="list-group-item list-group-item-action">My Dashboard</a>
                                            <a href="<?= $this->controller->config->getFileRootPath() ?>" class="list-group-item list-group-item-action">Home</a>
                                            <a href="<?= $this->controller->config->getFileRootPath() ?>auth/logout" class="list-group-item list-group-item-action">Logout</a>
                                        </div>
                                    </div>
                                </div>
                            </aside>
                        </div>

                        <div class="col-sm-9">
                            <article class="account-content">

                                <!-- START SIDE SLIDE-IN MENU -->
                                <?php
                                $slideInMenu = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                                $slideInMenu->show();
                                ?>
                                <!-- END OF SIDE SLIDE-IN MENU -->

                                <h3>Welcome <span><?= htmlspecialchars($_SESSION['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></h3>
                                <p>Manage your account settings below.</p>

                                <div class="icon-nav row">
                                    <div class="col-xs-6 col-md-3 card dashboard-cards">
                                        <a href="<?= $this->controller->config->getFileRootPath() ?>user/changePw?userId=<?= $_SESSION['custo_id'] ?>&change=0">
                                            <div class="card-body">
                                                <h5 class="card-title text-center"><i class="fa fa-shield dash-icons"></i></h5>
                                                <p class="card-text">Change Password</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <br>

                            </article>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- PAGE CONTENT - END -->

        <?php
    }
}
