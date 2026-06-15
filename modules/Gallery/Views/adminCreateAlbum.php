<?php

namespace Dorguzen\Modules\Gallery\Views;

class adminCreateAlbum extends \Dorguzen\Core\DGZ_AdminHtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel); // $albumData (null = create mode, array = edit mode)
        $base    = $this->controller->config->getFileRootPath();
        $isEdit  = !empty($albumData) && isset($albumData[0]);
        $album   = $isEdit ? $albumData[0] : [];
        $albumId = $isEdit ? (int) $album['album_id'] : 0;
        $action  = $isEdit
            ? $base . 'admin/gallery/create?edit=1&albumId=' . $albumId
            : $base . 'admin/gallery/create';
        ?>

        <!-- Hero -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn"><?= $isEdit ? 'Edit Album' : 'New Album' ?></h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>admin/gallery">Gallery</a></li>
                                <li class="breadcrumb-item text-white active"><?= $isEdit ? 'Edit Album' : 'New Album' ?></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <section class="py-4">
            <div class="container">
                <?php
                $slideInMenu = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                $slideInMenu->show();
                ?>

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="card shadow-sm border-0 rounded-3">
                            <div class="card-body p-4">
                                <form method="POST" action="<?= $action ?>">
                                    <input type="hidden" name="_csrf_token" value="<?= getCsrfToken() ?>">
                                    <?php if ($isEdit): ?>
                                        <input type="hidden" name="albumId" value="<?= $albumId ?>">
                                    <?php endif; ?>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Album Name <span class="text-danger">*</span></label>
                                        <input type="text" name="album_name" class="form-control"
                                               value="<?= htmlspecialchars($album['album_name'] ?? '') ?>"
                                               placeholder="e.g. Summer 2025" required maxlength="150">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Description</label>
                                        <textarea name="album_description" class="form-control" rows="3"
                                                  placeholder="Optional short description of this album..."
                                                  maxlength="1000"><?= htmlspecialchars($album['album_description'] ?? '') ?></textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Status</label>
                                        <select name="album_status" class="form-select">
                                            <option value="active" <?= (($album['album_status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active (visible on site)</option>
                                            <option value="inactive" <?= (($album['album_status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive (hidden)</option>
                                        </select>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn text-white px-4"
                                                style="background: var(--site-theme, #fd7e14);">
                                            <?= $isEdit ? 'Save Changes' : 'Create Album' ?>
                                        </button>
                                        <a href="<?= $base ?>admin/gallery" class="btn btn-outline-secondary">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}
