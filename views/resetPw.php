<?php

namespace Dorguzen\Views;

class resetPw extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = [])
    {
        $userId    = $viewModel['userId']    ?? '';
        $userEmail = $viewModel['userEmail'] ?? '';

        $jsValidation = \Dorguzen\Core\DGZ_View::getInsideView('jsValidationPartial', $this->controller);
        $jsValidation->show();
        ?>

        <!-- Hero Header Start -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn">Reset Password</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>">Home</a></li>
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>auth/login">Login</a></li>
                                <li class="breadcrumb-item text-white active" aria-current="page">Reset Password</li>
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
                            <h2>Choose a new password</h2>
                            <form method="post" action="<?= $this->controller->config->getFileRootPath() ?>auth/resetPw">
                                <input type="hidden" name="_csrf_token" value="<?= getCsrfToken() ?>">
                                <input type="hidden" name="reset_user_id" value="<?= htmlspecialchars($userId, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="reset_email"   value="<?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') ?>">

                                <div class="mb-3">
                                    <label for="reset_pwd" class="form-label">New password (at least 6 characters)</label>
                                    <input type="password" class="form-control" id="reset_pwd" name="reset_pwd" placeholder="New password">
                                </div>

                                <div class="mb-3">
                                    <label for="reset_conf_pwd" class="form-label">Confirm new password</label>
                                    <input type="password" class="form-control" id="reset_conf_pwd" name="reset_conf_pwd" placeholder="Confirm password">
                                </div>

                                <button type="reset"  class="btn btn-warning me-2">Clear</button>
                                <button type="submit" class="btn btn-primary">Set New Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }
}
