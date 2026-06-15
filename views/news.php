<?php

namespace Dorguzen\Views;

class news extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel); // $newsItems, $latestNews, $totalCount
        ?>

        <!-- Hero Header Start -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn">Latest News</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item">
                                    <a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>">Home</a>
                                </li>
                                <li class="breadcrumb-item text-white active" aria-current="page">News</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero Header End -->

        <!-- START SIDE SLIDE-IN MENU -->
        <?php
        ?>
        <!-- END OF SIDE SLIDE-IN MENU -->

        <div class="container-xxl py-5">
            <div class="container px-lg-5">
                <div class="row g-5">

                    <!-- Main Feed -->
                    <div class="col-lg-8">
                        <h2 class="mb-4">News &amp; Updates
                            <small class="text-muted fs-6 fw-normal ms-2"><?= (int) $totalCount ?> article<?= $totalCount != 1 ? 's' : '' ?></small>
                        </h2>

                        <?php if (!empty($newsItems)) { ?>
                            <?php foreach ($newsItems as $item) {
                                $nid   = (int) $item['news_id'];
                                $title = htmlspecialchars($item['news_title'] ?? '');
                                $desc  = htmlspecialchars($item['news_description'] ?? '');
                                $short = mb_strlen($desc) > 250 ? mb_substr($desc, 0, 250) . '...' : $desc;
                                $date  = date('F j, Y', strtotime($item['news_created'] ?? 'now'));
                                // Use thumbnail for card display; full image is on the article page
                                $imgSrc = '';
                                if (!empty($item['news_image'])) {
                                    $imgPath  = $item['news_image'];
                                    $thumbRel = dirname($imgPath) . '/'
                                        . pathinfo(basename($imgPath), PATHINFO_FILENAME) . '_thb.'
                                        . pathinfo($imgPath, PATHINFO_EXTENSION);
                                    $imgSrc = $this->controller->config->getFileRootPath() . htmlspecialchars($thumbRel);
                                }
                                $articleUrl = $this->controller->config->getFileRootPath() . 'news/article?newsId=' . $nid;
                                ?>
                                <div class="card border-0 shadow-sm mb-4" style="border-radius:12px; overflow:hidden;">
                                    <?php if ($imgSrc) { ?>
                                        <a href="<?= $articleUrl ?>">
                                            <img src="<?= $imgSrc ?>" alt="<?= $title ?>"
                                                 class="card-img-top"
                                                 style="height:220px; object-fit:cover;">
                                        </a>
                                    <?php } else { ?>
                                        <div style="height:120px; background: linear-gradient(135deg, #fd7e14 0%, #e55e00 100%); display:flex; align-items:center; justify-content:center;">
                                            <span style="color:rgba(255,255,255,.4); font-size:2rem;">&#128240;</span>
                                        </div>
                                    <?php } ?>
                                    <div class="card-body p-4">
                                        <p class="text-muted" style="font-size:.8rem; margin-bottom:6px;">
                                            <i class="fa fa-calendar-o me-1"></i><?= $date ?>
                                        </p>
                                        <h5 class="card-title mb-2">
                                            <a href="<?= $articleUrl ?>" class="text-dark text-decoration-none"><?= $title ?></a>
                                        </h5>
                                        <p class="card-text text-muted mb-3"><?= $short ?></p>
                                        <a href="<?= $articleUrl ?>" class="btn btn-primary btn-sm">Read More</a>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="text-center py-5 text-muted">
                                <p class="fs-5">No news articles have been published yet. Check back soon!</p>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <div class="sticky-top" style="top:100px; z-index:1;">
                            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px; overflow:hidden;">
                                <div class="card-header text-white fw-bold" style="background:var(--site-theme, #fd7e14);">
                                    Latest Articles
                                </div>
                                <div class="card-body p-0">
                                    <?php if (!empty($latestNews)) { ?>
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($latestNews as $ln) {
                                                $lnTitle = htmlspecialchars($ln['news_title'] ?? '');
                                                $lnDate  = date('M j, Y', strtotime($ln['news_created'] ?? 'now'));
                                                $lnUrl   = $this->controller->config->getFileRootPath() . 'news/article?newsId=' . (int) $ln['news_id'];
                                                ?>
                                                <li class="list-group-item border-0 py-3">
                                                    <a href="<?= $lnUrl ?>" class="text-dark text-decoration-none fw-semibold d-block mb-1" style="font-size:.9rem; line-height:1.3;"><?= $lnTitle ?></a>
                                                    <span class="text-muted" style="font-size:.78rem;"><?= $lnDate ?></span>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    <?php } else { ?>
                                        <p class="p-3 text-muted mb-0" style="font-size:.9rem;">No articles yet.</p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <?php
    }
}
