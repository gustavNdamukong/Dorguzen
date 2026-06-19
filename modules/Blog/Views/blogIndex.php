<?php

namespace Dorguzen\Modules\Blog\Views;

class blogIndex extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel);
        // $posts, $categories, $recentPosts, $currentPage, $totalPages, $total, $categoryId, $search
        $rootPath  = $this->controller->config->getFileRootPath();
        $themeColor = 'var(--site-theme, #1565C0)';
        ?>

        <!-- Hero -->
        <div style="background:linear-gradient(135deg, <?= $themeColor ?> 0%, #0d47a1 100%); padding:60px 0 40px; margin-bottom:0;">
            <div class="container text-center text-white">
                <h1 class="fw-bold mb-2" style="font-size:2.4rem; letter-spacing:-0.5px;">Our Blog</h1>
                <p class="mb-0 opacity-75" style="font-size:1.05rem;">Insights, news and stories from our team</p>
            </div>
        </div>

        <!-- Filter bar -->
        <div style="background:#f8f9fa; border-bottom:1px solid #e9ecef; padding:14px 0; margin-bottom:32px;">
            <div class="container">
                <form method="get" action="<?= $rootPath ?>blog" class="row g-2 align-items-center">
                    <div class="col-auto">
                        <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int) $cat['category_id'] ?>"
                                    <?= (int) ($categoryId ?? 0) === (int) $cat['category_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                    (<?= (int) $cat['post_count'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control" placeholder="Search posts…"
                                   value="<?= htmlspecialchars($search ?? '') ?>" style="min-width:200px;">
                            <button class="btn btn-sm" type="submit"
                                    style="background:<?= $themeColor ?>; color:#fff; border:none;">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <?php if ($search || $categoryId): ?>
                        <div class="col-auto">
                            <a href="<?= $rootPath ?>blog" class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-times me-1"></i>Clear
                            </a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="container pb-5">
            <?php if ($search || $categoryId): ?>
                <p class="text-muted mb-4" style="font-size:.9rem;">
                    <?php if ($search): ?>
                        Showing <strong><?= $total ?></strong> result<?= $total !== 1 ? 's' : '' ?> for
                        <strong>"<?= htmlspecialchars($search) ?>"</strong>
                    <?php else:
                        $activeCat = array_filter($categories, fn($c) => (int)$c['category_id'] === (int)$categoryId);
                        $activeCat = array_values($activeCat)[0] ?? null;
                    ?>
                        Showing <strong><?= $total ?></strong> post<?= $total !== 1 ? 's' : '' ?> in
                        <strong><?= htmlspecialchars($activeCat['name'] ?? '') ?></strong>
                    <?php endif; ?>
                </p>
            <?php endif; ?>

            <div class="row">
                <!-- Posts grid -->
                <div class="col-lg-8">
                    <?php if (empty($posts)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fa fa-pencil-square-o fa-3x mb-3 d-block opacity-25"></i>
                            <p>No posts found.</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-4">
                            <?php foreach ($posts as $post):
                                $cover    = !empty($post['cover_image'])
                                    ? $rootPath . 'assets/images/blog/' . htmlspecialchars($post['cover_image'])
                                    : null;
                                $postDate = !empty($post['published_at'])
                                    ? date('M j, Y', strtotime($post['published_at']))
                                    : date('M j, Y', strtotime($post['created_at']));
                                $excerpt  = htmlspecialchars($post['excerpt'] ?? '');
                                if ($excerpt === '') {
                                    $excerpt = htmlspecialchars(mb_substr(strip_tags($post['body']), 0, 130));
                                    if (mb_strlen(strip_tags($post['body'])) > 130) $excerpt .= '…';
                                }
                            ?>
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm" style="border-radius:12px; overflow:hidden; transition:transform .2s, box-shadow .2s;"
                                         onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.12)'"
                                         onmouseout="this.style.transform='';this.style.boxShadow=''">
                                        <?php if ($cover): ?>
                                            <a href="<?= $rootPath ?>blog/post?slug=<?= urlencode($post['slug']) ?>">
                                                <img src="<?= $cover ?>" alt="<?= htmlspecialchars($post['title']) ?>"
                                                     class="card-img-top"
                                                     style="height:200px; object-fit:cover;">
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= $rootPath ?>blog/post?slug=<?= urlencode($post['slug']) ?>"
                                               class="d-block d-flex align-items-center justify-content-center"
                                               style="height:200px; background:linear-gradient(135deg,<?= $themeColor ?>,#0d47a1);">
                                                <i class="fa fa-pencil-square-o fa-3x text-white opacity-50"></i>
                                            </a>
                                        <?php endif; ?>
                                        <div class="card-body d-flex flex-column p-4">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <?php if (!empty($post['category_name'])): ?>
                                                    <a href="<?= $rootPath ?>blog?category=<?= (int) $post['category_id'] ?>"
                                                       class="badge text-decoration-none"
                                                       style="background:<?= $themeColor ?>; font-size:.7rem; font-weight:600;">
                                                        <?= htmlspecialchars($post['category_name']) ?>
                                                    </a>
                                                <?php endif; ?>
                                                <span style="font-size:.78rem; color:var(--nlm-text-muted, #555);"><?= $postDate ?></span>
                                            </div>
                                            <h5 class="card-title fw-bold mb-2" style="font-size:1rem; line-height:1.4;">
                                                <a href="<?= $rootPath ?>blog/post?slug=<?= urlencode($post['slug']) ?>"
                                                   class="text-dark text-decoration-none">
                                                    <?= htmlspecialchars($post['title']) ?>
                                                </a>
                                            </h5>
                                            <p class="card-text mb-3" style="font-size:.88rem; flex:1; color:var(--nlm-text-muted, #444);">
                                                <?= $excerpt ?>
                                            </p>
                                            <a href="<?= $rootPath ?>blog/post?slug=<?= urlencode($post['slug']) ?>"
                                               class="btn btn-sm mt-auto"
                                               style="background:<?= $themeColor ?>; color:#fff; border:none; border-radius:6px;">
                                                Read more <i class="fa fa-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav class="mt-5">
                                <ul class="pagination justify-content-center">
                                    <?php
                                    $qBase = '';
                                    if ($search)     $qBase .= '&search=' . urlencode($search);
                                    if ($categoryId) $qBase .= '&category=' . $categoryId;
                                    ?>
                                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="<?= $rootPath ?>blog?page=<?= $currentPage - 1 ?><?= $qBase ?>">
                                            <i class="fa fa-chevron-left"></i>
                                        </a>
                                    </li>
                                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                        <li class="page-item <?= $p === $currentPage ? 'active' : '' ?>">
                                            <a class="page-link" href="<?= $rootPath ?>blog?page=<?= $p ?><?= $qBase ?>"
                                               <?php if ($p === $currentPage): ?>
                                                   style="background:<?= $themeColor ?>; border-color:<?= $themeColor ?>;"
                                               <?php endif; ?>>
                                                <?= $p ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                        <a class="page-link" href="<?= $rootPath ?>blog?page=<?= $currentPage + 1 ?><?= $qBase ?>">
                                            <i class="fa fa-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="sticky-top" style="top:100px; z-index:1;">

                        <!-- Recent posts -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius:12px; overflow:hidden;">
                            <div class="card-header fw-bold text-white" style="background:<?= $themeColor ?>;">
                                <i class="fa fa-clock-o me-2"></i>Recent Posts
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($recentPosts)): ?>
                                    <p class="p-3 text-muted mb-0" style="font-size:.9rem;">No posts yet.</p>
                                <?php else: ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($recentPosts as $rp):
                                            $rpDate = !empty($rp['published_at']) ? date('M j, Y', strtotime($rp['published_at'])) : '';
                                        ?>
                                            <li class="list-group-item border-0 py-3">
                                                <a href="<?= $rootPath ?>blog/post?slug=<?= urlencode($rp['slug']) ?>"
                                                   class="text-decoration-none fw-semibold d-block mb-1"
                                                   style="font-size:.88rem; line-height:1.3; color:var(--nlm-text, #1a1a1a);">
                                                    <?= htmlspecialchars($rp['title']) ?>
                                                </a>
                                                <span style="font-size:.75rem; color:var(--nlm-text-muted, #555);"><?= $rpDate ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius:12px; overflow:hidden;">
                            <div class="card-header fw-bold text-white" style="background:<?= $themeColor ?>;">
                                <i class="fa fa-tags me-2"></i>Categories
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($categories)): ?>
                                    <p class="p-3 text-muted mb-0" style="font-size:.9rem;">No categories yet.</p>
                                <?php else: ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($categories as $cat): ?>
                                            <li class="list-group-item border-0 py-2 px-3">
                                                <a href="<?= $rootPath ?>blog?category=<?= (int) $cat['category_id'] ?>"
                                                   class="d-flex justify-content-between align-items-center text-decoration-none"
                                                   style="font-size:.88rem; color:var(--nlm-text, #1a1a1a); font-weight:500;">
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
