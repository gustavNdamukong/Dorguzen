<?php

namespace Dorguzen\Views;

class search extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        $keyword = $viewModel['keyword'] ?? '';
        $results = $viewModel['results'] ?? ['news' => [], 'portfolio' => [], 'blog' => []];
        $base    = $this->controller->config->getFileRootPath();

        $totalCount = count($results['news']) + count($results['portfolio']) + count($results['blog']);
        ?>

        <!-- Hero Header Start -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn">Search Results</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>">Home</a></li>
                                <li class="breadcrumb-item text-white active" aria-current="page">Search</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero Header End -->

        <div class="container-xxl py-5">
            <div class="container px-lg-5">

                <!-- Search bar (repeat on results page for easy re-search) -->
                <form method="get" action="<?= $base ?>search" class="mb-5">
                    <div class="input-group" style="max-width:600px; margin:0 auto;">
                        <input type="text" name="search_keyword" class="form-control border p-3"
                               value="<?= htmlspecialchars($keyword) ?>" placeholder="Search keyword...">
                        <button type="submit" class="btn btn-primary px-4"><i class="fa fa-search me-1"></i> Search</button>
                    </div>
                </form>

                <?php if ($keyword === ''): ?>
                    <div class="text-center py-5">
                        <i class="fa fa-search fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">Enter a keyword above to search across news, portfolio, and blog posts.</p>
                    </div>
                <?php elseif ($totalCount === 0): ?>
                    <div class="text-center py-5">
                        <i class="fa fa-search fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted fs-5">No results for "<strong><?= htmlspecialchars($keyword) ?></strong>".</p>
                        <p class="text-muted small">Try different or broader keywords.</p>
                    </div>
                <?php else: ?>

                    <div class="mb-4">
                        <h5>Results for "<strong><?= htmlspecialchars($keyword) ?></strong>"
                            <span class="badge bg-secondary ms-2"><?= $totalCount ?> found</span>
                        </h5>
                    </div>

                    <?php if (!empty($results['news'])): ?>
                    <div class="mb-5">
                        <h6 class="text-uppercase fw-bold mb-3" style="color:var(--site-theme, #fd7e14); letter-spacing:1px;">
                            <i class="fa fa-newspaper me-2"></i>News (<?= count($results['news']) ?>)
                        </h6>
                        <div class="row g-3">
                            <?php foreach ($results['news'] as $item): ?>
                            <div class="col-md-6">
                                <a href="<?= $base ?>news/article?newsId=<?= (int)$item['news_id'] ?>" class="text-decoration-none">
                                    <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                                        <div class="card-body">
                                            <h6 class="card-title fw-semibold text-dark"><?= htmlspecialchars($item['news_title'] ?? '') ?></h6>
                                            <p class="card-text text-muted small mb-0"><?= htmlspecialchars(substr(strip_tags($item['news_description'] ?? ''), 0, 120)) ?>...</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($results['portfolio'])): ?>
                    <div class="mb-5">
                        <h6 class="text-uppercase fw-bold mb-3" style="color:var(--site-theme, #fd7e14); letter-spacing:1px;">
                            <i class="fa fa-briefcase me-2"></i>Portfolio (<?= count($results['portfolio']) ?>)
                        </h6>
                        <div class="row g-3">
                            <?php foreach ($results['portfolio'] as $item): ?>
                            <div class="col-md-6">
                                <a href="<?= $base ?>portfolio" class="text-decoration-none">
                                    <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                                        <div class="card-body">
                                            <h6 class="card-title fw-semibold text-dark"><?= htmlspecialchars($item['portfolio_title'] ?? '') ?></h6>
                                            <p class="card-text text-muted small mb-0"><?= htmlspecialchars(substr(strip_tags($item['portfolio_description'] ?? ''), 0, 120)) ?>...</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($results['blog'])): ?>
                    <div class="mb-5">
                        <h6 class="text-uppercase fw-bold mb-3" style="color:var(--site-theme, #fd7e14); letter-spacing:1px;">
                            <i class="fa fa-pencil-square me-2"></i>Blog Posts (<?= count($results['blog']) ?>)
                        </h6>
                        <div class="row g-3">
                            <?php foreach ($results['blog'] as $item): ?>
                            <div class="col-md-6">
                                <a href="<?= $base ?>blog/post?slug=<?= urlencode($item['slug'] ?? '') ?>" class="text-decoration-none">
                                    <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                                        <div class="card-body">
                                            <h6 class="card-title fw-semibold text-dark"><?= htmlspecialchars($item['title'] ?? '') ?></h6>
                                            <p class="card-text text-muted small mb-0"><?= htmlspecialchars(substr(strip_tags($item['excerpt'] ?? ''), 0, 120)) ?>...</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                <?php endif; ?>

            </div>
        </div>

        <?php
    }
}
