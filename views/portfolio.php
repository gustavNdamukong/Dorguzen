<?php

namespace Dorguzen\Views;

class portfolio extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        $items = $viewModel['portfolioItems'] ?? [];
        $base  = $this->controller->config->getFileRootPath();
        ?>

        <!-- Hero Header Start -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn">Portfolio</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>">Home</a></li>
                                <li class="breadcrumb-item text-white active" aria-current="page">Portfolio</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero Header End -->

        <style>
            .pf-card {
                border-radius: 14px;
                overflow: hidden;
                box-shadow: 0 3px 16px rgba(0,0,0,.10);
                cursor: pointer;
                transition: transform .18s ease, box-shadow .18s ease;
                background: #fff;
            }
            .pf-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 28px rgba(0,0,0,.18);
            }
            .pf-card-img-wrap {
                position: relative;
                overflow: hidden;
                height: 210px;
                background: #f0f0f0;
            }
            .pf-card-img-wrap img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform .35s ease;
            }
            .pf-card:hover .pf-card-img-wrap img {
                transform: scale(1.06);
            }
            .pf-card-img-wrap .pf-overlay {
                position: absolute;
                inset: 0;
                background: rgba(0,0,0,.35);
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: opacity .25s ease;
            }
            .pf-card:hover .pf-overlay { opacity: 1; }
            .pf-overlay i { font-size: 2.2rem; color: #fff; }
            .pf-card-body { padding: 16px 18px 20px; }
            .pf-card-title {
                font-size: 1rem;
                font-weight: 700;
                color: #212529;
                margin-bottom: 4px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .pf-card-company {
                font-size: .82rem;
                color: #6c757d;
                margin-bottom: 6px;
            }
            .pf-card-excerpt {
                font-size: .85rem;
                color: #495057;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .pf-placeholder {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #f0f4ff, #e8f0ff);
            }
            .pf-placeholder i { font-size: 3rem; color: #c5d0e8; }
        </style>

        <div class="container-xxl py-5">
            <div class="container px-lg-5">

                <div class="text-center mb-5">
                    <h6 class="text-secondary text-uppercase fw-bold ls-2">Our Work</h6>
                    <h2 class="mb-0">Portfolio</h2>
                    <hr class="mx-auto mt-2" style="width:60px; border-top:3px solid var(--site-theme,#fd7e14);">
                    <p class="text-muted mt-3" style="max-width:560px; margin:0 auto;">
                        A selection of projects we have worked on. Click any item to learn more.
                    </p>
                </div>

                <?php if (!empty($items)) { ?>
                    <div class="row g-4">
                        <?php foreach ($items as $item) {
                            $id      = (int) $item['portfolio_id'];
                            $title   = htmlspecialchars($item['portfolio_title'] ?? '');
                            $company = htmlspecialchars($item['portfolio_company_name'] ?? '');
                            $website = htmlspecialchars($item['portfolio_website'] ?? '');
                            $desc    = htmlspecialchars($item['portfolio_description'] ?? '');

                            // Full image for modal; thumbnail for card display
                            $fullImgSrc = !empty($item['portfolio_image'])
                                ? $base . htmlspecialchars($item['portfolio_image'])
                                : '';
                            $imgSrc = '';
                            if (!empty($item['portfolio_image'])) {
                                $imgPath  = $item['portfolio_image'];
                                $thumbRel = dirname($imgPath) . '/'
                                    . pathinfo(basename($imgPath), PATHINFO_FILENAME) . '_thb.'
                                    . pathinfo($imgPath, PATHINFO_EXTENSION);
                                $imgSrc = $base . htmlspecialchars($thumbRel);
                            }
                            ?>
                            <div class="col-sm-6 col-lg-4">
                                <div class="pf-card"
                                     data-bs-toggle="modal"
                                     data-bs-target="#portfolioModal"
                                     data-title="<?= $title ?>"
                                     data-company="<?= $company ?>"
                                     data-website="<?= $website ?>"
                                     data-desc="<?= $desc ?>"
                                     data-img="<?= $fullImgSrc ?>">

                                    <div class="pf-card-img-wrap">
                                        <?php if ($imgSrc) { ?>
                                            <img src="<?= $imgSrc ?>" alt="<?= $title ?>">
                                        <?php } else { ?>
                                            <div class="pf-placeholder">
                                                <i class="bi bi-briefcase"></i>
                                            </div>
                                        <?php } ?>
                                        <div class="pf-overlay">
                                            <i class="bi bi-zoom-in"></i>
                                        </div>
                                    </div>

                                    <div class="pf-card-body">
                                        <div class="pf-card-title"><?= $title ?></div>
                                        <?php if ($company) { ?>
                                            <div class="pf-card-company">
                                                <i class="bi bi-building me-1"></i><?= $company ?>
                                            </div>
                                        <?php } ?>
                                        <?php if ($item['portfolio_description']) { ?>
                                            <div class="pf-card-excerpt">
                                                <?= htmlspecialchars(substr($item['portfolio_description'], 0, 120)) ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-briefcase" style="font-size:3rem; opacity:.3;"></i>
                        <p class="mt-3">No portfolio items yet. Check back soon!</p>
                    </div>
                <?php } ?>

            </div>
        </div>

        <!-- Portfolio Detail Modal -->
        <div class="modal fade" id="portfolioModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius:14px; overflow:hidden;">
                    <div class="modal-header border-0 text-white py-3 px-4"
                         style="background: var(--site-theme, #fd7e14);">
                        <h5 class="modal-title fw-bold" id="pf-modal-title">Portfolio Item</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div id="pf-modal-img-wrap" style="display:none;">
                            <img id="pf-modal-img" src="" alt=""
                                 style="width:100%; height:auto; display:block;">
                        </div>
                        <div class="p-4">
                            <div id="pf-modal-company" class="text-muted mb-2" style="display:none;">
                                <i class="bi bi-building me-1"></i>
                                <span id="pf-modal-company-text"></span>
                            </div>
                            <div id="pf-modal-website-wrap" class="mb-3" style="display:none;">
                                <i class="bi bi-globe me-1"></i>
                                <a id="pf-modal-website" href="#" target="_blank" rel="noopener"
                                   style="color: var(--site-theme, #fd7e14); word-break:break-all;"></a>
                            </div>
                            <p id="pf-modal-desc" class="text-secondary" style="white-space:pre-line; line-height:1.75;"></p>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modal = document.getElementById('portfolioModal');
            modal.addEventListener('show.bs.modal', function (e) {
                var card = e.relatedTarget;

                document.getElementById('pf-modal-title').textContent    = card.dataset.title    || '';
                document.getElementById('pf-modal-desc').textContent     = card.dataset.desc     || '';

                // Company
                var companyWrap = document.getElementById('pf-modal-company');
                var companyText = document.getElementById('pf-modal-company-text');
                if (card.dataset.company) {
                    companyText.textContent = card.dataset.company;
                    companyWrap.style.display = '';
                } else {
                    companyWrap.style.display = 'none';
                }

                // Website
                var websiteWrap = document.getElementById('pf-modal-website-wrap');
                var websiteLink = document.getElementById('pf-modal-website');
                if (card.dataset.website) {
                    websiteLink.href        = card.dataset.website;
                    websiteLink.textContent = card.dataset.website;
                    websiteWrap.style.display = '';
                } else {
                    websiteWrap.style.display = 'none';
                }

                // Image
                var imgWrap = document.getElementById('pf-modal-img-wrap');
                var img     = document.getElementById('pf-modal-img');
                if (card.dataset.img) {
                    img.src = card.dataset.img;
                    imgWrap.style.display = '';
                } else {
                    imgWrap.style.display = 'none';
                }
            });
        });
        </script>

        <?php
    }
}
