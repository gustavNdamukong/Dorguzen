<?php

namespace Dorguzen\Views;

class changepw extends \Dorguzen\Core\DGZ_HtmlView
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
                        <h1 class="text-white animated zoomIn">Change Password</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>">Home</a></li>
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>user/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item text-white active" aria-current="page">Change Password</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero Header End -->

        <div class="container-xxl py-5">
            <div class="container px-lg-5">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="wow fadeInUp" data-wow-delay="0.3s">
                            <a href="<?= $this->controller->config->getFileRootPath() ?>user/dashboard" class="btn btn-outline-primary mb-4">&larr; Return to Dashboard</a>

                            <h3>Change your password</h3>
                            <form method="post" action="<?= $this->controller->config->getFileRootPath() ?>user/changePw">
                                <input type="hidden" name="_csrf_token" value="<?= getCsrfToken() ?>">

                                <div class="mb-3">
                                    <label for="old_password" class="form-label">Current password</label>
                                    <input type="password" class="form-control" id="old_password" name="old_password" placeholder="Current password">
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New password (at least 6 characters)</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New password">
                                </div>

                                <div class="mb-3">
                                    <label for="conf_new_password" class="form-label">Confirm new password</label>
                                    <input type="password" class="form-control" id="conf_new_password" name="conf_new_password" placeholder="Confirm new password">
                                </div>

                                <button type="reset"  class="btn btn-warning me-2">Clear</button>
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }
}
