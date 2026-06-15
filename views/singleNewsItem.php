<?php

namespace Dorguzen\Views;

class singleNewsItem extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel); // $newsItem, $latestNews

        $title    = htmlspecialchars($newsItem['news_title']       ?? 'Article');
        $desc     = nl2br(htmlspecialchars($newsItem['news_description'] ?? ''));
        $date     = isset($newsItem['news_created']) ? date('F j, Y', strtotime($newsItem['news_created'])) : '';
        // Full image on detail view (not thumbnail)
        $imgSrc   = !empty($newsItem['news_image'])
                    ? $this->controller->config->getFileRootPath() . htmlspecialchars($newsItem['news_image'])
                    : '';
        $newsUrl  = $this->controller->config->getFileRootPath() . 'news';

        // Embed URL is stored clean by the controller on save — use directly
        $videoEmbedUrl = trim($newsItem['news_video_url'] ?? '');

        $audioUrl = trim($newsItem['news_audio_url'] ?? '');
        ?>

        <!-- Hero Header Start -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn"><?= $title ?></h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item">
                                    <a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a class="text-white" href="<?= $newsUrl ?>">News</a>
                                </li>
                                <li class="breadcrumb-item text-white active" aria-current="page">Article</li>
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

                    <!-- Article Content -->
                    <div class="col-lg-8">
                        <?php if ($newsItem) { ?>
                            <?php if ($imgSrc) { ?>
                                <img src="<?= $imgSrc ?>" alt="<?= $title ?>"
                                     class="img-fluid rounded mb-4"
                                     style="width:100%; max-height:400px; object-fit:cover;">
                            <?php } ?>

                            <p class="text-muted mb-3" style="font-size:.85rem;">
                                <i class="fa fa-calendar-o me-1"></i><?= $date ?>
                            </p>

                            <div class="article-body" style="font-size:1rem; line-height:1.8; color:#343a40;">
                                <?= $desc ?>
                            </div>

                            <?php if ($videoEmbedUrl !== '') { ?>
                                <div class="ratio ratio-16x9 mt-4 mb-4 rounded overflow-hidden shadow-sm">
                                    <iframe src="<?= htmlspecialchars($videoEmbedUrl) ?>"
                                            allowfullscreen
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            title="Article video"></iframe>
                                </div>
                            <?php } ?>

                            <?php if ($audioUrl !== '') { ?>
                                <div class="mt-4 mb-4">
                                    <audio controls style="width:100%;">
                                        <source src="<?= htmlspecialchars($audioUrl) ?>">
                                        Your browser does not support the audio element.
                                    </audio>
                                </div>
                            <?php } ?>

                            <div class="mt-5">
                                <a href="<?= $newsUrl ?>" class="btn btn-outline-primary">
                                    &larr; Back to News
                                </a>
                            </div>
                        <?php } else { ?>
                            <div class="text-center py-5 text-muted">
                                <p class="fs-5">The article you requested could not be found.</p>
                                <a href="<?= $newsUrl ?>" class="btn btn-primary mt-2">Browse All News</a>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <div class="sticky-top" style="top:100px; z-index:1;">
                            <div class="card border-0 shadow-sm" style="border-radius:12px; overflow:hidden;">
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
