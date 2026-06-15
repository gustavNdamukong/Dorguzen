<?php

namespace Dorguzen\Modules\Blog\Views;

class blogPost extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel);
        // $post, $comments, $recentPosts, $categories, $captchaN1, $captchaN2
        $rootPath   = $this->controller->config->getFileRootPath();
        $themeColor = 'var(--site-theme, #fd7e14)';

        $postDate = !empty($post['published_at'])
            ? date('F j, Y', strtotime($post['published_at']))
            : date('F j, Y', strtotime($post['created_at']));

        $hasCover = !empty($post['cover_image']);
        ?>

        <!-- Hero / cover -->
        <?php if ($hasCover): ?>
            <div style="position:relative; height:480px; overflow:hidden; margin-bottom:0;">
                <img src="<?= $rootPath ?>assets/images/blog/<?= htmlspecialchars($post['cover_image']) ?>"
                     alt="<?= htmlspecialchars($post['title']) ?>"
                     style="width:100%; height:100%; object-fit:cover; object-position:center 30%;">
                <div style="position:absolute;inset:0;background:rgba(0,0,0,.45);"></div>
                <div class="container" style="position:absolute;bottom:32px;left:50%;transform:translateX(-50%);width:100%;">
                    <div class="col-lg-8">
                        <?php if (!empty($post['category_name'])): ?>
                            <a href="<?= $rootPath ?>blog?category=<?= (int) $post['category_id'] ?>"
                               class="badge text-decoration-none mb-2 d-inline-block"
                               style="background:<?= $themeColor ?>; font-size:.75rem;">
                                <?= htmlspecialchars($post['category_name']) ?>
                            </a>
                        <?php endif; ?>
                        <h1 class="text-white fw-bold" style="font-size:2rem; line-height:1.25; text-shadow:0 2px 8px rgba(0,0,0,.4);">
                            <?= htmlspecialchars($post['title']) ?>
                        </h1>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div style="background:linear-gradient(135deg,<?= $themeColor ?> 0%,#c75000 100%); padding:60px 0 40px;">
                <div class="container">
                    <?php if (!empty($post['category_name'])): ?>
                        <a href="<?= $rootPath ?>blog?category=<?= (int) $post['category_id'] ?>"
                           class="badge text-decoration-none mb-2 d-inline-block"
                           style="background:rgba(255,255,255,.25); font-size:.75rem;">
                            <?= htmlspecialchars($post['category_name']) ?>
                        </a>
                    <?php endif; ?>
                    <h1 class="text-white fw-bold" style="font-size:2rem; line-height:1.3;">
                        <?= htmlspecialchars($post['title']) ?>
                    </h1>
                </div>
            </div>
        <?php endif; ?>

        <div class="container py-5">
            <div class="row">

                <!-- Article body -->
                <div class="col-lg-8">

                    <!-- Meta bar -->
                    <div class="d-flex align-items-center flex-wrap gap-3 mb-4 pb-3" style="border-bottom:1px solid #e9ecef; font-size:.85rem; color:#444;">
                        <span><i class="fa fa-user me-1"></i><?= htmlspecialchars($post['author']) ?></span>
                        <span><i class="fa fa-calendar me-1"></i><?= $postDate ?></span>
                        <a href="#comment-form" class="text-decoration-none" style="color:<?= $themeColor ?>;">
                            <i class="fa fa-comments me-1"></i><?= count($comments) ?> comment<?= count($comments) !== 1 ? 's' : '' ?> — Leave one
                        </a>
                        <a href="<?= $rootPath ?>blog" class="ms-auto text-decoration-none" style="color:<?= $themeColor ?>;">
                            <i class="fa fa-arrow-left me-1"></i>Back to blog
                        </a>
                    </div>

                    <!-- Body -->
                    <div class="blog-body" style="font-size:1.05rem; line-height:1.85; color:#1a1a1a;">
                        <?= nl2br(htmlspecialchars($post['body'])) ?>
                    </div>

                    <!-- Comments -->
                    <div class="mt-5 pt-4" style="border-top:2px solid #e9ecef;">
                        <h4 class="fw-bold mb-4">
                            <i class="fa fa-comments me-2" style="color:<?= $themeColor ?>;"></i>
                            Comments (<?= count($comments) ?>)
                        </h4>

                        <?php
                        // Flash messages
                        if (!empty($_SESSION['_dgz_success'])) {
                            foreach ($_SESSION['_dgz_success'] as $msg): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?= htmlspecialchars($msg) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endforeach;
                            unset($_SESSION['_dgz_success']);
                        }
                        if (!empty($_SESSION['_dgz_errors'])) {
                            foreach ($_SESSION['_dgz_errors'] as $msg): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?= htmlspecialchars($msg) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endforeach;
                            unset($_SESSION['_dgz_errors']);
                        }
                        ?>

                        <?php if (empty($comments)): ?>
                            <p class="text-muted mb-4">No comments yet. Be the first to share your thoughts!</p>
                        <?php else: ?>
                            <?php foreach ($comments as $c): ?>
                                <div class="d-flex gap-3 mb-4 pb-3" style="border-bottom:1px solid #f0f0f0;">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                                         style="width:44px;height:44px;background:<?= $themeColor ?>;font-size:.9rem;">
                                        <?= strtoupper(mb_substr($c['author_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-semibold mb-1" style="font-size:.9rem;">
                                            <?= htmlspecialchars($c['author_name']) ?>
                                            <span class="text-muted fw-normal ms-2" style="font-size:.78rem;">
                                                <?= date('M j, Y \a\t g:i a', strtotime($c['created_at'])) ?>
                                            </span>
                                        </div>
                                        <p class="mb-0" style="font-size:.92rem; line-height:1.6; color:#333;">
                                            <?= nl2br(htmlspecialchars($c['body'])) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Comment form -->
                        <div id="comment-form" class="card border-0 shadow-sm p-4 mt-4" style="border-radius:12px;">
                            <h5 class="fw-bold mb-3">Leave a Comment</h5>
                            <p class="text-muted mb-3" style="font-size:.85rem;">
                                Your comment will be reviewed before it appears. All fields are required.
                            </p>
                            <form method="post" action="<?= $rootPath ?>blog/comment">
                                <input type="hidden" name="_csrf_token" value="<?= getCsrfToken() ?>">
                                <input type="hidden" name="post_id" value="<?= (int) $post['post_id'] ?>">
                                <input type="hidden" name="post_slug" value="<?= htmlspecialchars($post['slug']) ?>">

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" style="font-size:.85rem;">Your Name</label>
                                        <input type="text" name="author_name" class="form-control" required maxlength="150">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" style="font-size:.85rem;">Email Address</label>
                                        <input type="email" name="author_email" class="form-control" required maxlength="255">
                                        <div class="form-text">Never displayed publicly.</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="font-size:.85rem;">Comment</label>
                                    <textarea name="body" class="form-control" rows="4" required maxlength="3000"></textarea>
                                </div>

                                <!-- Math captcha -->
                                <div class="mb-4 p-3 rounded" style="background:#f8f9fa; border:1px dashed #dee2e6;">
                                    <label class="form-label fw-semibold mb-1" style="font-size:.85rem;">
                                        <i class="fa fa-shield me-1" style="color:<?= $themeColor ?>;"></i>
                                        Security Check — What is <?= (int) $captchaN1 ?> + <?= (int) $captchaN2 ?>?
                                    </label>
                                    <input type="number" name="captcha_answer" class="form-control" style="max-width:120px;"
                                           required placeholder="Your answer">
                                </div>

                                <button type="submit" class="btn px-4"
                                        style="background:<?= $themeColor ?>; color:#fff; border:none; border-radius:8px;">
                                    <i class="fa fa-paper-plane me-2"></i>Submit Comment
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4 mt-5 mt-lg-0">
                    <div class="sticky-top" style="top:100px; z-index:1;">

                        <!-- Recent posts -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius:12px; overflow:hidden;">
                            <div class="card-header fw-bold text-white" style="background:<?= $themeColor ?>;">
                                <i class="fa fa-clock-o me-2"></i>Recent Posts
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($recentPosts as $rp): ?>
                                        <li class="list-group-item border-0 py-3 <?= $rp['slug'] === $post['slug'] ? 'bg-light' : '' ?>">
                                            <a href="<?= $rootPath ?>blog/post?slug=<?= urlencode($rp['slug']) ?>"
                                               class="text-decoration-none fw-semibold d-block mb-1"
                                               style="font-size:.88rem; line-height:1.3; color:#1a1a1a;">
                                                <?= htmlspecialchars($rp['title']) ?>
                                            </a>
                                            <?php if (!empty($rp['published_at'])): ?>
                                                <span style="font-size:.75rem; color:#555;">
                                                    <?= date('M j, Y', strtotime($rp['published_at'])) ?>
                                                </span>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius:12px; overflow:hidden;">
                            <div class="card-header fw-bold text-white" style="background:<?= $themeColor ?>;">
                                <i class="fa fa-tags me-2"></i>Categories
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($categories)): ?>
                                    <p class="p-3 text-muted mb-0" style="font-size:.9rem;">No categories.</p>
                                <?php else: ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($categories as $cat): ?>
                                            <li class="list-group-item border-0 py-2 px-3">
                                                <a href="<?= $rootPath ?>blog?category=<?= (int) $cat['category_id'] ?>"
                                                   class="d-flex justify-content-between align-items-center text-decoration-none"
                                                   style="font-size:.88rem; color:#1a1a1a; font-weight:500;">
                                                    <span><?= htmlspecialchars($cat['name']) ?></span>
                                                    <span class="badge rounded-pill" style="background:<?= $themeColor ?>; font-size:.7rem;">
                                                        <?= (int) $cat['post_count'] ?>
                                                    </span>
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
        </div>

        <?php
    }
}
