<?php

namespace Dorguzen\Views\Admin;

use Dorguzen\Core\DGZ_View;

class managePortfolio extends \Dorguzen\Core\DGZ_AdminHtmlView
{
    public function show(array $viewModel = []): void
    {
        $items = $viewModel['portfolioItems'] ?? [];
        $base  = $this->controller->config->getFileRootPath();
        $count = count($items);
        ?>

        <!-- Hero Header Start -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn">Manage Portfolio</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>">Home</a></li>
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item text-white active" aria-current="page">Manage Portfolio</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero Header End -->

        <style>
            .pf-table-wrap { border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.08); overflow: hidden; }
            .pf-table-wrap table { margin-bottom: 0; }
            .pf-table-wrap thead th {
                background: var(--site-theme, #fd7e14) !important;
                color: #fff !important;
                font-size: .8rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .5px;
                padding: 12px 14px;
                border: none !important;
                white-space: nowrap;
            }
            .pf-table-wrap tbody td {
                vertical-align: middle;
                padding: 10px 14px;
                font-size: .88rem;
                border-color: #f0f0f0;
                color: #343a40;
            }
            .pf-table-wrap tbody tr:hover { background: #fff8f2; }
            .pf-truncate { max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        </style>

        <div class="container-xxl py-5">
            <div class="container px-lg-5">

                <!-- START SIDE SLIDE-IN MENU -->
                <?php
                $slideInMenu = DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                $slideInMenu->show();
                ?>
                <!-- END OF SIDE SLIDE-IN MENU -->

                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h2 class="mb-0">Portfolio
                        <small class="text-muted fs-6 fw-normal ms-2">
                            <?= $count ?> item<?= $count !== 1 ? 's' : '' ?>
                        </small>
                    </h2>
                    <div class="d-flex gap-2">
                        <a href="<?= $base ?>admin/portfolio/create" class="btn btn-primary btn-sm">+ Add Item</a>
                        <a href="<?= $base ?>portfolio" class="btn btn-outline-success btn-sm" target="_blank">View Public Page</a>
                        <a href="<?= $base ?>admin/dashboard" class="btn btn-outline-secondary btn-sm">← Dashboard</a>
                    </div>
                </div>

                <?php if (!empty($items)) { ?>
                    <div class="pf-table-wrap">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Company</th>
                                        <th>Website</th>
                                        <th>Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($items as $item) {
                                    $id      = (int) $item['portfolio_id'];
                                    $title   = htmlspecialchars($item['portfolio_title'] ?? '');
                                    $company = htmlspecialchars($item['portfolio_company_name'] ?? '');
                                    $website = htmlspecialchars($item['portfolio_website'] ?? '');
                                    $date    = !empty($item['portfolio_created'])
                                        ? date('Y-m-d', strtotime($item['portfolio_created'])) : '—';
                                    $imgSrc  = !empty($item['portfolio_image'])
                                        ? $base . htmlspecialchars($item['portfolio_image']) : '';
                                    ?>
                                    <tr>
                                        <td>
                                            <?php if ($imgSrc) { ?>
                                                <img src="<?= $imgSrc ?>" alt=""
                                                     style="width:56px; height:42px; object-fit:cover; border-radius:6px;">
                                            <?php } else { ?>
                                                <span class="text-muted">—</span>
                                            <?php } ?>
                                        </td>
                                        <td class="pf-truncate" title="<?= $title ?>"><?= $title ?: '—' ?></td>
                                        <td class="pf-truncate"><?= $company ?: '—' ?></td>
                                        <td>
                                            <?php if ($website) { ?>
                                                <a href="<?= $website ?>" target="_blank" rel="noopener"
                                                   style="font-size:.82rem; color:var(--site-theme,#fd7e14);">
                                                    <?= strlen($website) > 30 ? substr($website, 0, 30) . '…' : $website ?>
                                                </a>
                                            <?php } else { ?>
                                                <span class="text-muted">—</span>
                                            <?php } ?>
                                        </td>
                                        <td style="white-space:nowrap; font-size:.82rem; color:#6c757d;"><?= $date ?></td>
                                        <td class="text-center" style="white-space:nowrap;">
                                            <a href="<?= $base ?>admin/portfolio/create?edit=1&portfolioId=<?= $id ?>"
                                               class="btn btn-sm btn-primary">Edit</a>
                                            <a href="<?= $base ?>admin/portfolio/delete?portfolio_id=<?= $id ?>"
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Delete this portfolio item?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="text-muted py-4">
                        <p>No portfolio items yet. <a href="<?= $base ?>admin/portfolio/create">Add the first one.</a></p>
                    </div>
                <?php } ?>

            </div>
        </div>

        <?php
    }
}
