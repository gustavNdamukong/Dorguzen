<?php

namespace Dorguzen\Modules\Blog\Views;

class adminCreatePost extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel);
        // $postData (null = create, array = edit), $categories
        $rootPath   = $this->controller->config->getFileRootPath();
        $themeColor = 'var(--site-theme, #3949ab)';

        $isEdit  = !empty($postData['post_id']);
        $postId  = (int) ($postData['post_id'] ?? 0);
        $action  = $isEdit
            ? $rootPath . 'admin/blog/edit?postId=' . $postId
            : $rootPath . 'admin/blog/create';

        $slideMenu = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
        $slideMenu->show();
        ?>

        <div class="container py-4" style="max-width:860px;">

            <!-- Flash messages -->
            <?php if (!empty($_SESSION['_dgz_errors'])): ?>
                <?php foreach ($_SESSION['_dgz_errors'] as $msg): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; unset($_SESSION['_dgz_errors']); ?>
            <?php endif; ?>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="fw-bold mb-0">
                    <?= $isEdit ? 'Edit Post' : 'Create New Post' ?>
                </h4>
                <a href="<?= $rootPath ?>admin/blog" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i>Back
                </a>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius:14px; overflow:hidden;">
                <div class="card-body p-4">
                    <form method="post" action="<?= $action ?>" enctype="multipart/form-data">
                        <input type="hidden" name="_csrf_token" value="<?= getCsrfToken() ?>">
                        <?php if ($isEdit): ?>
                            <input type="hidden" name="post_id" value="<?= $postId ?>">
                        <?php endif; ?>

                        <!-- Title + Author row -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Post Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required maxlength="255"
                                       value="<?= htmlspecialchars($postData['title'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Author</label>
                                <input type="text" name="author" class="form-control" maxlength="150"
                                       value="<?= htmlspecialchars($postData['author'] ?? 'Admin') ?>">
                            </div>
                        </div>

                        <!-- Category + Status row -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">— Uncategorised —</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= (int) $cat['category_id'] ?>"
                                            <?= (int) ($postData['category_id'] ?? 0) === (int) $cat['category_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="draft" <?= ($postData['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>
                                        Draft
                                    </option>
                                    <option value="published" <?= ($postData['status'] ?? '') === 'published' ? 'selected' : '' ?>>
                                        Published
                                    </option>
                                </select>
                                <div class="form-text">Drafts are not visible to the public.</div>
                            </div>
                        </div>

                        <!-- Excerpt -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Excerpt / Summary</label>
                            <textarea name="excerpt" class="form-control" rows="2" maxlength="500"
                                      placeholder="Short summary shown on the blog listing page (optional)…"><?= htmlspecialchars($postData['excerpt'] ?? '') ?></textarea>
                        </div>

                        <!-- Body -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Body <span class="text-danger">*</span></label>
                            <textarea name="body" class="form-control" rows="14" required
                                      placeholder="Write your post content here…"><?= htmlspecialchars($postData['body'] ?? '') ?></textarea>
                            <div class="form-text">Plain text. Line breaks are preserved when displayed.</div>
                        </div>

                        <!-- Cover image -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Cover Image</label>
                            <?php if ($isEdit && !empty($postData['cover_image'])): ?>
                                <div class="mb-2">
                                    <img src="<?= $rootPath ?>assets/images/blog/<?= htmlspecialchars($postData['cover_image']) ?>"
                                         alt="Current cover"
                                         style="height:100px; border-radius:8px; object-fit:cover;">
                                    <div class="form-text">Upload a new image to replace the current one.</div>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="cover_image" class="form-control" accept="image/*">
                            <div class="form-text">Recommended: landscape image, min 800px wide. JPG/PNG/WebP.</div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn px-4"
                                    style="background:<?= $themeColor ?>; color:#fff; border:none; border-radius:8px;">
                                <i class="fa fa-save me-2"></i><?= $isEdit ? 'Update Post' : 'Create Post' ?>
                            </button>
                            <a href="<?= $rootPath ?>admin/blog" class="btn btn-outline-secondary px-4" style="border-radius:8px;">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php
    }
}
