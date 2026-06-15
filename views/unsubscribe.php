<?php

namespace Dorguzen\Views;

class unsubscribe extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        $base  = $this->controller->config->getFileRootPath();
        $email = htmlspecialchars($viewModel['email'] ?? '');
        ?>

        <!-- Hero Header Start -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn">Unsubscribed</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>">Home</a></li>
                                <li class="breadcrumb-item text-white active" aria-current="page">Unsubscribe</li>
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
                    <div class="col-md-6 text-center">
                        <div class="card border-0 shadow-sm p-5" style="border-radius:16px;">
                            <div style="font-size:3rem; margin-bottom:16px;">✅</div>
                            <h3 class="fw-bold mb-3">You have been unsubscribed</h3>
                            <?php if ($email !== ''): ?>
                                <p class="text-muted mb-4">
                                    <strong><?= $email ?></strong> has been removed from our newsletter list.
                                    You will no longer receive emails from us.
                                </p>
                            <?php else: ?>
                                <p class="text-muted mb-4">
                                    You have been removed from our newsletter list.
                                </p>
                            <?php endif; ?>
                            <a href="<?= $base ?>" class="btn btn-primary px-4">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }
}
