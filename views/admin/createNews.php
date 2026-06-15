<?php

namespace Dorguzen\Views\Admin;

use Dorguzen\Core\DGZ_View;

class createNews extends \Dorguzen\Core\DGZ_AdminHtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel); // $newsItemData

        $isEdit      = !empty($newsItemData) && isset($newsItemData[0]);
        $item        = $isEdit ? $newsItemData[0] : [];
        $pageTitle   = $isEdit ? 'Edit News Item' : 'Add News Item';
        $newsId      = (int) ($item['news_id'] ?? 0);
        $base        = $this->controller->config->getFileRootPath();
        $formAction  = $base . 'admin/news/create' . ($isEdit ? '?edit=1&newsId=' . $newsId : '');

        $titleVal    = htmlspecialchars($item['news_title']       ?? '');
        $descVal     = htmlspecialchars($item['news_description'] ?? '');
        $statusVal   = $item['news_status']   ?? 'draft';
        $videoVal    = htmlspecialchars($item['news_video_url']   ?? '');
        $audioVal    = htmlspecialchars($item['news_audio_url']   ?? '');
        $currentImg  = !empty($item['news_image']) ? $base . htmlspecialchars($item['news_image']) : '';
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
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>admin/news">Manage News</a></li>
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
                                        <input type="hidden" name="edit"   value="1">
                                        <input type="hidden" name="newsId" value="<?= $newsId ?>">
                                    <?php } ?>

                                    <!-- Title -->
                                    <div class="mb-4">
                                        <label for="news_title" class="form-label fw-semibold">
                                            Title <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               id="news_title"
                                               name="news_title"
                                               class="form-control"
                                               value="<?= $titleVal ?>"
                                               required
                                               placeholder="Enter news title">
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-4">
                                        <label for="news_description" class="form-label fw-semibold">
                                            Description <span class="text-danger">*</span>
                                        </label>
                                        <textarea id="news_description"
                                                  name="news_description"
                                                  class="form-control"
                                                  rows="8"
                                                  required
                                                  placeholder="Enter the full article content..."><?= $descVal ?></textarea>
                                    </div>

                                    <!-- Status -->
                                    <div class="mb-4">
                                        <label for="news_status" class="form-label fw-semibold">Status</label>
                                        <select id="news_status" name="news_status" class="form-select">
                                            <option value="draft"     <?= $statusVal === 'draft'     ? 'selected' : '' ?>>Draft</option>
                                            <option value="published" <?= $statusVal === 'published' ? 'selected' : '' ?>>Published</option>
                                        </select>
                                        <div class="form-text">Draft items are not visible to the public.</div>
                                    </div>

                                    <!-- Featured Image -->
                                    <div class="mb-4">
                                        <label for="news_image" class="form-label fw-semibold">Featured Image</label>
                                        <?php if ($currentImg) { ?>
                                            <div class="mb-2">
                                                <img src="<?= $currentImg ?>" alt="Current image"
                                                     style="max-height:120px; border-radius:6px; object-fit:cover;">
                                                <p class="form-text mt-1">Current image. Upload a new file to replace it.</p>
                                            </div>
                                        <?php } ?>
                                        <input type="file"
                                               id="news_image"
                                               name="news_image"
                                               accept="image/*"
                                               style="display:block; width:100%; padding:6px 0;">
                                        <div class="form-text">JPEG, PNG, GIF, WebP. Uploaded to <code>assets/images/news/</code>.</div>
                                    </div>

                                    <!-- Video URL -->
                                    <div class="mb-4">
                                        <label for="news_video_url" class="form-label fw-semibold">
                                            Video URL
                                        </label>
                                        <input type="text"
                                               id="news_video_url"
                                               name="news_video_url"
                                               class="form-control"
                                               value="<?= $videoVal ?>"
                                               placeholder="Paste a YouTube or Vimeo URL (e.g. https://www.youtube.com/watch?v=...)">
                                        <div class="form-text">Supported: YouTube and Vimeo URLs. Leave blank if not applicable.</div>
                                    </div>

                                    <!-- Audio URL -->
                                    <div class="mb-4">
                                        <label for="news_audio_url" class="form-label fw-semibold">
                                            Audio URL
                                        </label>
                                        <input type="text"
                                               id="news_audio_url"
                                               name="news_audio_url"
                                               class="form-control"
                                               value="<?= $audioVal ?>"
                                               placeholder="Direct link to an audio file (MP3, OGG, WAV, etc.)">
                                        <div class="form-text">Must be a direct URL to an audio file. Leave blank if not applicable.</div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="d-flex gap-2 mt-3">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <?= $isEdit ? 'Update Item' : 'Create Item' ?>
                                        </button>
                                        <a href="<?= $base ?>admin/news" class="btn btn-outline-secondary">Cancel</a>
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
