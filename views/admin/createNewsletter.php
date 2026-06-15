<?php

namespace Dorguzen\Views\Admin;

use Dorguzen\Core\DGZ_View;

class createNewsletter extends \Dorguzen\Core\DGZ_AdminHtmlView
{
    public function show(array $viewModel = []): void
    {
        $newsletterData = $viewModel['newsletterData'] ?? null;
        $templates      = $viewModel['templates']      ?? [];

        $isEdit       = !empty($newsletterData) && isset($newsletterData[0]);
        $item         = $isEdit ? $newsletterData[0] : [];
        $pageTitle    = $isEdit ? 'Edit Newsletter' : 'Create Newsletter';
        $newsletterId = (int) ($item['newsletter_id'] ?? 0);
        $base         = $this->controller->config->getFileRootPath();
        $formAction   = $base . 'admin/newsletters/create' . ($isEdit ? '?edit=1&newsletterId=' . $newsletterId : '');

        $subjectVal  = htmlspecialchars($item['newsletter_subject']  ?? '');
        $bodyVal     = htmlspecialchars($item['newsletter_body']     ?? '');
        $templateVal = $item['newsletter_template'] ?? 'newsletter-welcome';
        $currentImg  = !empty($item['newsletter_image']) ? $base . htmlspecialchars($item['newsletter_image']) : '';
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
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>admin/newsletters">Newsletters</a></li>
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
                                        <input type="hidden" name="edit"         value="1">
                                        <input type="hidden" name="newsletterId" value="<?= $newsletterId ?>">
                                    <?php } ?>

                                    <!-- Subject -->
                                    <div class="mb-4">
                                        <label for="newsletter_subject" class="form-label fw-semibold">
                                            Subject <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               id="newsletter_subject"
                                               name="newsletter_subject"
                                               class="form-control"
                                               value="<?= $subjectVal ?>"
                                               required
                                               placeholder="Email subject line">
                                    </div>

                                    <!-- Body -->
                                    <div class="mb-4">
                                        <label for="newsletter_body" class="form-label fw-semibold">
                                            Body <span class="text-danger">*</span>
                                        </label>
                                        <textarea id="newsletter_body"
                                                  name="newsletter_body"
                                                  class="form-control"
                                                  rows="10"
                                                  required
                                                  placeholder="Newsletter content..."><?= $bodyVal ?></textarea>
                                        <div class="form-text">HTML is allowed.</div>
                                    </div>

                                    <!-- Template -->
                                    <div class="mb-4">
                                        <label for="newsletter_template" class="form-label fw-semibold">Email Template</label>
                                        <select id="newsletter_template" name="newsletter_template" class="form-select">
                                            <?php if (empty($templates)) { ?>
                                                <option value="newsletter-welcome">newsletter-welcome (default)</option>
                                            <?php } else { ?>
                                                <?php foreach ($templates as $tpl) { ?>
                                                    <option value="<?= htmlspecialchars($tpl) ?>"
                                                        <?= $templateVal === $tpl ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($tpl) ?>
                                                    </option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <div class="form-text">
                                            Options are populated from <code>views/emails/</code> and <code>core/email-views/</code>.
                                            To add a new template, create a <code>.php</code> file in <code>views/emails/</code>
                                            and it will appear here automatically.
                                            Use <strong>newsletter-welcome</strong> for first-time welcome emails
                                            and <strong>newsletter</strong> for regular bulk sends.
                                        </div>
                                    </div>

                                    <!-- Image -->
                                    <div class="mb-4">
                                        <label for="newsletter_image" class="form-label fw-semibold">Header Image</label>
                                        <?php if ($currentImg) { ?>
                                            <div class="mb-2">
                                                <img src="<?= $currentImg ?>" alt="Current image"
                                                     style="max-height:120px; border-radius:6px; object-fit:cover;">
                                                <p class="form-text mt-1">Current image. Upload a new file to replace it.</p>
                                            </div>
                                        <?php } ?>
                                        <input type="file"
                                               id="newsletter_image"
                                               name="newsletter_image"
                                               accept="image/*"
                                               style="display:block; width:100%; padding:6px 0;">
                                        <div class="form-text">Uploaded to <code>assets/images/newsletters/</code>.</div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="d-flex gap-2 mt-3">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <?= $isEdit ? 'Update Newsletter' : 'Create Newsletter' ?>
                                        </button>
                                        <a href="<?= $base ?>admin/newsletters" class="btn btn-outline-secondary">Cancel</a>
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
