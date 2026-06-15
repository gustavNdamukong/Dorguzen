<?php

namespace Dorguzen\Views\Admin;

use Dorguzen\Core\DGZ_View;

class manageNews extends \Dorguzen\Core\DGZ_AdminHtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel); // $newsItems
        $base = $this->controller->config->getFileRootPath();
        ?>

        <!-- Hero Header Start -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn">Manage News</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>">Home</a></li>
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item text-white active" aria-current="page">Manage News</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero Header End -->

        <style>
            .mn-table-wrap { border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.08); overflow: hidden; }
            .mn-table-wrap .table-responsive { border-radius: 12px; }
            .mn-table-wrap table { margin-bottom: 0; }
            .mn-table-wrap thead th {
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
            .mn-table-wrap tbody td {
                vertical-align: middle;
                padding: 10px 14px;
                font-size: .88rem;
                border-color: #f0f0f0;
                color: #343a40;
            }
            .mn-table-wrap tbody tr:hover { background: #fff8f2; }
            .mn-table-wrap tbody tr:nth-child(even) { background: #fafafa; }
            .mn-table-wrap tbody tr:nth-child(even):hover { background: #fff8f2; }

            .mn-truncate {
                max-width: 200px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .mn-badge {
                display: inline-block;
                padding: 3px 10px;
                border-radius: 20px;
                font-size: .75rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .4px;
            }
            .mn-badge-published { background: #d1fae5; color: #065f46; }
            .mn-badge-draft     { background: #fef3c7; color: #92400e; }

            .mn-thumb {
                width: 48px;
                height: 36px;
                object-fit: cover;
                border-radius: 4px;
            }
        </style>

        <div class="container-xxl py-5">
            <div class="container px-lg-5">

                <!-- START SIDE SLIDE-IN MENU -->
                <?php
                $slideInMenu = DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                $slideInMenu->show();
                ?>
                <!-- END OF SIDE SLIDE-IN MENU -->

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="mb-0">News Items
                        <small class="text-muted fs-6 fw-normal ms-2">
                            <?= count($newsItems) ?> item<?= count($newsItems) != 1 ? 's' : '' ?>
                        </small>
                    </h2>
                    <div>
                        <a href="<?= $base ?>admin/news/create" class="btn btn-primary btn-sm me-2">+ Add News Item</a>
                        <a href="<?= $base ?>admin/dashboard" class="btn btn-outline-secondary btn-sm">← Dashboard</a>
                    </div>
                </div>

                <?php if (!empty($newsItems)) { ?>
                <div class="mn-table-wrap">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Image</th>
                                    <th>Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($newsItems as $item) {
                                $nid    = (int) $item['news_id'];
                                $nTitle = htmlspecialchars($item['news_title'] ?? '');
                                $status = $item['news_status'] ?? 'draft';
                                $badgeClass = $status === 'published' ? 'mn-badge-published' : 'mn-badge-draft';
                                $date   = !empty($item['news_created']) ? date('Y-m-d', strtotime($item['news_created'])) : '—';
                                $imgSrc = !empty($item['news_image'])
                                    ? $base . htmlspecialchars($item['news_image'])
                                    : '';
                                ?>
                                <tr>
                                    <td class="mn-truncate" title="<?= $nTitle ?>">
                                        <?= $nTitle ?: '—' ?>
                                    </td>
                                    <td>
                                        <span class="mn-badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($imgSrc) { ?>
                                            <img src="<?= $imgSrc ?>" alt="" class="mn-thumb">
                                        <?php } else { ?>
                                            <span class="text-muted">—</span>
                                        <?php } ?>
                                    </td>
                                    <td style="white-space:nowrap; font-size:.82rem; color:#6c757d;">
                                        <?= $date ?>
                                    </td>
                                    <td class="text-center" style="white-space:nowrap;">
                                        <a href="<?= $base ?>admin/news/create?edit=1&newsId=<?= $nid ?>"
                                           class="btn btn-sm btn-primary">Edit</a>
                                        <a href="<?= $base ?>admin/news/delete?news_id=<?= $nid ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Are you sure you want to delete this news item?')">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php } else { ?>
                    <div class="text-muted py-4">
                        <p>There are no news items yet. <a href="<?= $base ?>admin/news/create">Add the first one.</a></p>
                    </div>
                <?php } ?>

            </div>
        </div>

        <?php
    }
}
