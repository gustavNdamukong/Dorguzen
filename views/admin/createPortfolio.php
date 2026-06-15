<?php

namespace Dorguzen\Views\Admin;

use Dorguzen\Core\DGZ_View;

class createPortfolio extends \Dorguzen\Core\DGZ_AdminHtmlView
{
    public function show(array $viewModel = []): void
    {
        $item         = $viewModel['portfolioItem'] ?? null;
        $isEdit       = $item !== null;
        $portfolioId  = (int) ($item['portfolio_id'] ?? 0);
        $pageTitle    = $isEdit ? 'Edit Portfolio Item' : 'Add Portfolio Item';
        $base         = $this->controller->config->getFileRootPath();
        $formAction   = $base . 'admin/portfolio/create' . ($isEdit ? '?edit=1&portfolioId=' . $portfolioId : '');

        $titleVal   = htmlspecialchars($item['portfolio_title']        ?? '');
        $companyVal = htmlspecialchars($item['portfolio_company_name'] ?? '');
        $websiteVal = htmlspecialchars($item['portfolio_website']      ?? '');
        $descVal    = htmlspecialchars($item['portfolio_description']  ?? '');
        $currentImg = !empty($item['portfolio_image'])
            ? $base . htmlspecialchars($item['portfolio_image']) : '';
        ?>

        <!-- Hero Header Start -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn"><?= $pageTitle ?></h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>">Home</a></li>
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>admin/portfolio">Portfolio</a></li>
                                <li class="breadcrumb-item text-white active" aria-current="page"><?= $pageTitle ?></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero Header End -->

        <div class="container-xxl py-5">
            <div class="container px-lg-5">

                <!-- START SIDE SLIDE-IN MENU -->
                <?php
                $slideInMenu = DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                $slideInMenu->show();
                ?>
                <!-- END OF SIDE SLIDE-IN MENU -->

                <div class="row justify-content-center">
                    <div class="col-lg-8">

                        <div class="card border-0 shadow-sm" style="border-radius:12px; overflow:hidden;">
                            <div class="card-header text-white fw-bold py-3 px-4"
                                 style="background:var(--site-theme, #fd7e14); font-size:1rem;">
                                <?= $pageTitle ?>
                            </div>
                            <div class="card-body p-4">

                                <form action="<?= $formAction ?>"
                                      method="post"
                                      enctype="multipart/form-data">

                                    <input type="hidden" name="_csrf_token" value="<?= getCsrfToken() ?>">
                                    <?php if ($isEdit) { ?>
                                        <input type="hidden" name="edit"        value="1">
                                        <input type="hidden" name="portfolioId" value="<?= $portfolioId ?>">
                                    <?php } ?>

                                    <!-- Title -->
                                    <div class="mb-4">
                                        <label for="portfolio_title" class="form-label fw-semibold">
                                            Title <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               id="portfolio_title"
                                               name="portfolio_title"
                                               class="form-control"
                                               value="<?= $titleVal ?>"
                                               required
                                               placeholder="Project or work title">
                                    </div>

                                    <!-- Company -->
                                    <div class="mb-4">
                                        <label for="portfolio_company_name" class="form-label fw-semibold">
                                            Company / Client
                                            <span class="text-muted fw-normal">(optional)</span>
                                        </label>
                                        <input type="text"
                                               id="portfolio_company_name"
                                               name="portfolio_company_name"
                                               class="form-control"
                                               value="<?= $companyVal ?>"
                                               placeholder="Company or client name">
                                    </div>

                                    <!-- Website -->
                                    <div class="mb-4">
                                        <label for="portfolio_website" class="form-label fw-semibold">
                                            Website / URL
                                            <span class="text-muted fw-normal">(optional)</span>
                                        </label>
                                        <input type="url"
                                               id="portfolio_website"
                                               name="portfolio_website"
                                               class="form-control"
                                               value="<?= $websiteVal ?>"
                                               placeholder="https://example.com">
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-4">
                                        <label for="portfolio_description" class="form-label fw-semibold">
                                            Description
                                            <span class="text-muted fw-normal">(optional)</span>
                                        </label>
                                        <textarea id="portfolio_description"
                                                  name="portfolio_description"
                                                  class="form-control"
                                                  rows="6"
                                                  placeholder="Describe the project..."><?= $descVal ?></textarea>
                                    </div>

                                    <!-- Image -->
                                    <div class="mb-4">
                                        <label for="portfolio_image" class="form-label fw-semibold">
                                            Image
                                            <span class="text-muted fw-normal">(optional)</span>
                                        </label>
                                        <?php if ($currentImg) { ?>
                                            <div class="mb-2">
                                                <img src="<?= $currentImg ?>" alt="Current image"
                                                     style="max-height:140px; border-radius:8px; object-fit:cover;">
                                                <p class="form-text mt-1">Current image. Upload a new file to replace it.</p>
                                            </div>
                                        <?php } ?>
                                        <input type="file"
                                               id="portfolio_image"
                                               name="portfolio_image"
                                               accept="image/*"
                                               style="display:block; width:100%; padding:6px 0;">
                                        <div class="form-text">Uploaded to <code>assets/images/portfolio/</code>.</div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="d-flex gap-2 mt-3">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <?= $isEdit ? 'Update Item' : 'Save Item' ?>
                                        </button>
                                        <a href="<?= $base ?>admin/portfolio" class="btn btn-outline-secondary">Cancel</a>
                                    </div>

                                </form>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <?php
    }
}
