<?php

namespace Dorguzen\Views\Admin;

use Dorguzen\Core\DGZ_View;

class manageNewsletters extends \Dorguzen\Core\DGZ_AdminHtmlView
{
    public function show(array $viewModel = []): void
    {
        $newsletters = $viewModel['newsletters'] ?? [];
        $base        = $this->controller->config->getFileRootPath();
        ?>

        <!-- Hero Header Start -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn">Manage Newsletters</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>">Home</a></li>
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item text-white active" aria-current="page">Manage Newsletters</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero Header End -->

        <style>
            .nl-table-wrap { border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.08); overflow: hidden; }
            .nl-table-wrap table { margin-bottom: 0; }
            .nl-table-wrap thead th {
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
            .nl-table-wrap tbody td {
                vertical-align: middle;
                padding: 10px 14px;
                font-size: .88rem;
                border-color: #f0f0f0;
                color: #343a40;
            }
            .nl-table-wrap tbody tr:hover { background: #fff8f2; }
            .nl-truncate {
                max-width: 220px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
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

                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h2 class="mb-0">Newsletters
                        <small class="text-muted fs-6 fw-normal ms-2">
                            <?= count($newsletters) ?> item<?= count($newsletters) != 1 ? 's' : '' ?>
                        </small>
                    </h2>
                    <div class="d-flex gap-2">
                        <a href="<?= $base ?>admin/newsletters/create" class="btn btn-primary btn-sm">+ Create Newsletter</a>
                        <a href="<?= $base ?>admin/subscribers" class="btn btn-outline-success btn-sm">Manage Subscribers</a>
                        <a href="<?= $base ?>admin/dashboard" class="btn btn-outline-secondary btn-sm">← Dashboard</a>
                    </div>
                </div>

                <?php if (!empty($newsletters)) { ?>
                <div class="nl-table-wrap">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Template</th>
                                    <th>Image</th>
                                    <th>Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($newsletters as $nl) {
                                $nid      = (int) $nl['newsletter_id'];
                                $subject  = htmlspecialchars($nl['newsletter_subject'] ?? '');
                                $template = htmlspecialchars($nl['newsletter_template'] ?? '—');
                                $date     = !empty($nl['newsletter_created'])
                                    ? date('Y-m-d', strtotime($nl['newsletter_created'])) : '—';
                                $imgSrc   = !empty($nl['newsletter_image'])
                                    ? $base . htmlspecialchars($nl['newsletter_image']) : '';
                                ?>
                                <tr>
                                    <td class="nl-truncate" title="<?= $subject ?>">
                                        <?= $subject ?: '—' ?>
                                    </td>
                                    <td>
                                        <code style="font-size:.8rem; background:#f8f9fa; padding:2px 6px; border-radius:4px;">
                                            <?= $template ?>
                                        </code>
                                    </td>
                                    <td>
                                        <?php if ($imgSrc) { ?>
                                            <img src="<?= $imgSrc ?>" alt=""
                                                 style="width:48px; height:36px; object-fit:cover; border-radius:4px;">
                                        <?php } else { ?>
                                            <span class="text-muted">—</span>
                                        <?php } ?>
                                    </td>
                                    <td style="white-space:nowrap; font-size:.82rem; color:#6c757d;">
                                        <?= $date ?>
                                    </td>
                                    <td class="text-center" style="white-space:nowrap;">
                                        <a href="<?= $base ?>admin/newsletters/create?edit=1&newsletterId=<?= $nid ?>"
                                           class="btn btn-sm btn-primary">Edit</a>
                                        <a href="<?= $base ?>admin/newsletters/delete?newsletter_id=<?= $nid ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Delete this newsletter?')">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php } else { ?>
                    <div class="text-muted py-4">
                        <p>No newsletters yet. <a href="<?= $base ?>admin/newsletters/create">Create the first one.</a></p>
                    </div>
                <?php } ?>

            </div>
        </div>

        <?php
    }
}
