<?php

namespace Dorguzen\Modules\Blog\Views;

class adminManagePosts extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel);
        // $posts, $categories
        $rootPath   = $this->controller->config->getFileRootPath();
        $themeColor = 'var(--site-theme, #3949ab)';

        // Pull the admin slide-in menu partial
        $slideMenu = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
        $slideMenu->show();
        ?>

        <div class="container py-4">

            <!-- Flash messages -->
            <?php if (!empty($_SESSION['_dgz_success'])): ?>
                <?php foreach ($_SESSION['_dgz_success'] as $msg): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; unset($_SESSION['_dgz_success']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['_dgz_errors'])): ?>
                <?php foreach ($_SESSION['_dgz_errors'] as $msg): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; unset($_SESSION['_dgz_errors']); ?>
            <?php endif; ?>

            <div class="row g-4">

                <!-- Main: posts table -->
                <div class="col-lg-8">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="fw-bold mb-0">Blog Posts</h4>
                        <a href="<?= $rootPath ?>admin/blog/create" class="btn btn-sm"
                           style="background:<?= $themeColor ?>; color:#fff; border:none; border-radius:8px;">
                            <i class="fa fa-plus me-1"></i> New Post
                        </a>
                    </div>

                    <?php if (empty($posts)): ?>
                        <div class="card border-0 shadow-sm text-center py-5">
                            <div class="text-muted">
                                <i class="fa fa-pencil-square-o fa-3x mb-3 d-block opacity-25"></i>
                                No posts yet. <a href="<?= $rootPath ?>admin/blog/create">Create your first post</a>.
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card border-0 shadow-sm" style="border-radius:12px; overflow:hidden;">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead style="background:#f8f9fa; font-size:.82rem; text-transform:uppercase; letter-spacing:.5px;">
                                        <tr>
                                            <th style="width:60px;"></th>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size:.9rem;">
                                        <?php foreach ($posts as $p):
                                            $cover = !empty($p['cover_image'])
                                                ? $rootPath . 'assets/images/blog/' . htmlspecialchars($p['cover_image'])
                                                : null;
                                            $date = date('M j, Y', strtotime($p['created_at']));
                                        ?>
                                            <tr>
                                                <td>
                                                    <?php if ($cover): ?>
                                                        <img src="<?= $cover ?>" alt=""
                                                             style="width:50px; height:40px; object-fit:cover; border-radius:6px;">
                                                    <?php else: ?>
                                                        <div class="d-flex align-items-center justify-content-center rounded"
                                                             style="width:50px;height:40px;background:#e9ecef;">
                                                            <i class="fa fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($p['title']) ?></strong>
                                                    <div class="text-muted" style="font-size:.78rem;">by <?= htmlspecialchars($p['author']) ?></div>
                                                </td>
                                                <td>
                                                    <?php if (!empty($p['category_name'])): ?>
                                                        <span class="badge" style="background:<?= $themeColor ?>; font-size:.72rem;">
                                                            <?= htmlspecialchars($p['category_name']) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted" style="font-size:.8rem;">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($p['status'] === 'published'): ?>
                                                        <span class="badge bg-success" style="font-size:.72rem;">Published</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark" style="font-size:.72rem;">Draft</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="font-size:.8rem; color:#6c757d;"><?= $date ?></td>
                                                <td class="text-end">
                                                    <a href="<?= $rootPath ?>admin/blog/edit?postId=<?= (int) $p['post_id'] ?>"
                                                       class="btn btn-xs btn-outline-secondary me-1" style="font-size:.78rem; padding:3px 10px;">
                                                        <i class="fa fa-pencil me-1"></i>Edit
                                                    </a>
                                                    <a href="<?= $rootPath ?>admin/blog/delete?postId=<?= (int) $p['post_id'] ?>"
                                                       class="btn btn-xs btn-outline-danger"
                                                       style="font-size:.78rem; padding:3px 10px;"
                                                       onclick="return confirm('Delete this post and all its comments?')">
                                                        <i class="fa fa-trash me-1"></i>Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Comment moderation link -->
                    <div class="mt-3 text-end">
                        <a href="<?= $rootPath ?>admin/blog/comments" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-comments me-1"></i> Manage Comments
                        </a>
                    </div>
                </div>

                <!-- Sidebar: categories -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm" style="border-radius:12px; overflow:hidden;">
                        <div class="card-header fw-bold text-white" style="background:<?= $themeColor ?>;">
                            <i class="fa fa-tags me-2"></i>Categories
                        </div>
                        <div class="card-body">
                            <!-- Add category -->
                            <form method="post" action="<?= $rootPath ?>admin/blog/saveCategory" class="mb-3">
                                <input type="hidden" name="_csrf_token" value="<?= getCsrfToken() ?>">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="category_name" class="form-control"
                                           placeholder="New category name…" required maxlength="150">
                                    <button class="btn btn-sm text-white" type="submit"
                                            style="background:<?= $themeColor ?>; border:none;">
                                        Add
                                    </button>
                                </div>
                            </form>

                            <?php if (empty($categories)): ?>
                                <p class="text-muted mb-0" style="font-size:.85rem;">No categories yet.</p>
                            <?php else: ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($categories as $cat): ?>
                                        <li class="list-group-item border-0 py-2 px-0 d-flex align-items-center justify-content-between">
                                            <span style="font-size:.88rem;">
                                                <?= htmlspecialchars($cat['name']) ?>
                                                <span class="text-muted ms-1" style="font-size:.75rem;">
                                                    (<?= (int) $cat['post_count'] ?>)
                                                </span>
                                            </span>
                                            <a href="<?= $rootPath ?>admin/blog/deleteCategory?categoryId=<?= (int) $cat['category_id'] ?>"
                                               class="btn btn-xs btn-outline-danger"
                                               style="font-size:.72rem; padding:2px 8px;"
                                               onclick="return confirm('Delete this category?')">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php
    }
}
