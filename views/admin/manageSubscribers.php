<?php

namespace Dorguzen\Views\Admin;

use Dorguzen\Core\DGZ_View;

class manageSubscribers extends \Dorguzen\Core\DGZ_AdminHtmlView
{
    public function show(array $viewModel = []): void
    {
        $subscribers       = $viewModel['subscribers']       ?? [];
        $activeSubscribers = $viewModel['activeSubscribers'] ?? [];
        $newSubscribers    = $viewModel['newSubscribers']    ?? [];
        $newsletters       = $viewModel['newsletters']       ?? [];

        $base        = $this->controller->config->getFileRootPath();
        $totalCount  = count($subscribers);
        $activeCount = count($activeSubscribers);
        $newCount    = count($newSubscribers);
        ?>

        <!-- Hero Header Start -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn">Manage Subscribers</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>">Home</a></li>
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item text-white active" aria-current="page">Manage Subscribers</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero Header End -->

        <style>
            .sub-table-wrap { border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.08); overflow: hidden; }
            .sub-table-wrap table { margin-bottom: 0; }
            .sub-table-wrap thead th {
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
            .sub-table-wrap tbody td {
                vertical-align: middle;
                padding: 10px 14px;
                font-size: .88rem;
                border-color: #f0f0f0;
                color: #343a40;
            }
            .sub-table-wrap tbody tr:hover { background: #fff8f2; }
        </style>

        <div class="container-xxl py-5">
            <div class="container px-lg-5">

                <!-- START SIDE SLIDE-IN MENU -->
                <?php
                $slideInMenu = DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                $slideInMenu->show();
                ?>
                <!-- END OF SIDE SLIDE-IN MENU -->

                <!-- Stats row -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card border-0 shadow-sm text-center py-3" style="border-radius:12px;">
                            <div class="card-body">
                                <div style="font-size:2rem; font-weight:700; color:#fd7e14;"><?= $totalCount ?></div>
                                <div class="text-muted small">Total Subscribers</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card border-0 shadow-sm text-center py-3" style="border-radius:12px;">
                            <div class="card-body">
                                <div style="font-size:2rem; font-weight:700; color:#0d9488;"><?= $newCount ?></div>
                                <div class="text-muted small">New (Unwelcomed)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action buttons row -->
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <a href="<?= $base ?>admin/newsletters" class="btn btn-outline-secondary btn-sm me-2">Manage Newsletters</a>
                        <a href="<?= $base ?>admin/dashboard" class="btn btn-outline-secondary btn-sm">← Dashboard</a>
                    </div>
                    <div class="d-flex gap-2">
                        <?php if ($newCount > 0 && count($newsletters) > 0) { ?>
                            <button class="btn btn-success btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#sendWelcomeModal">
                                Send Welcome Emails (<?= $newCount ?>)
                            </button>
                        <?php } ?>
                        <?php if ($activeCount > 0 && count($newsletters) > 0) { ?>
                            <button class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#sendBulkModal">
                                Send Bulk Email (<?= $activeCount ?>)
                            </button>
                        <?php } ?>
                    </div>
                </div>

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-0" id="subTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-tab" data-bs-toggle="tab"
                                data-bs-target="#allSubs" type="button" role="tab">
                            All Subscribers
                            <span class="badge bg-secondary ms-1"><?= $totalCount ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="new-tab" data-bs-toggle="tab"
                                data-bs-target="#newSubs" type="button" role="tab">
                            New (Unwelcomed)
                            <span class="badge bg-success ms-1"><?= $newCount ?></span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content border border-top-0 rounded-bottom p-3 bg-white shadow-sm">

                    <!-- Tab 1: All Subscribers -->
                    <div class="tab-pane fade show active" id="allSubs" role="tabpanel">
                        <?php if (!empty($subscribers)) { ?>
                            <div class="sub-table-wrap mt-3">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($subscribers as $sub) {
                                            $sid      = (int) $sub['subscriber_id'];
                                            $isActive = !empty($sub['subscriber_active']);
                                            $isNew    = $isActive && empty($sub['subscriber_welcomed']);
                                            $date     = !empty($sub['subscriber_created'])
                                                ? date('Y-m-d', strtotime($sub['subscriber_created'])) : '—';
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($sub['subscriber_firstname'] ?: '—') ?></td>
                                                <td><?= htmlspecialchars($sub['subscriber_email'] ?? '') ?></td>
                                                <td>
                                                    <?php if (!$isActive) { ?>
                                                        <span class="badge bg-danger">Unsubscribed</span>
                                                    <?php } elseif ($isNew) { ?>
                                                        <span class="badge bg-success">New</span>
                                                    <?php } else { ?>
                                                        <span class="badge bg-secondary">Active</span>
                                                    <?php } ?>
                                                </td>
                                                <td style="white-space:nowrap; font-size:.82rem; color:#6c757d;">
                                                    <?= $date ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?= $base ?>admin/subscribers/delete?subscriber_id=<?= $sid ?>"
                                                       class="btn btn-sm btn-danger"
                                                       onclick="return confirm('Delete this subscriber?')">Delete</a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php } else { ?>
                            <p class="text-muted mt-3">No subscribers yet.</p>
                        <?php } ?>
                    </div>

                    <!-- Tab 2: New Subscribers -->
                    <div class="tab-pane fade" id="newSubs" role="tabpanel">
                        <?php if (!empty($newSubscribers)) { ?>
                            <div class="sub-table-wrap mt-3">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Date</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($newSubscribers as $sub) {
                                            $sid  = (int) $sub['subscriber_id'];
                                            $date = !empty($sub['subscriber_created'])
                                                ? date('Y-m-d', strtotime($sub['subscriber_created'])) : '—';
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($sub['subscriber_firstname'] ?: '—') ?></td>
                                                <td><?= htmlspecialchars($sub['subscriber_email'] ?? '') ?></td>
                                                <td style="white-space:nowrap; font-size:.82rem; color:#6c757d;">
                                                    <?= $date ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?= $base ?>admin/subscribers/delete?subscriber_id=<?= $sid ?>"
                                                       class="btn btn-sm btn-danger"
                                                       onclick="return confirm('Delete this subscriber?')">Delete</a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php } else { ?>
                            <p class="text-muted mt-3">No new subscribers at the moment.</p>
                        <?php } ?>
                    </div>

                </div><!-- end tab-content -->

            </div>
        </div>


        <!-- ===============================================================
             SEND WELCOME EMAILS MODAL
        =============================================================== -->
        <div class="modal fade" id="sendWelcomeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius:12px; overflow:hidden;">
                    <div class="modal-header text-white border-0"
                         style="background: linear-gradient(135deg, #198754, #20c997);">
                        <h5 class="modal-title fw-bold">Send Welcome Emails</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <p class="text-muted">Choose a newsletter template to send as the welcome email to all <strong><?= $newCount ?></strong> new subscriber(s).</p>
                        <form action="<?= $base ?>admin/subscribers/sendWelcome" method="post">
                            <input type="hidden" name="_csrf_token" value="<?= getCsrfToken() ?>">

                            <?php foreach ($newSubscribers as $sub) { ?>
                                <input type="hidden" name="subscriber_ids[]"
                                       value="<?= (int) $sub['subscriber_id'] ?>">
                            <?php } ?>

                            <div class="mb-3">
                                <label for="wl_newsletter_id" class="form-label fw-semibold">Select Newsletter</label>
                                <select name="newsletter_id" id="wl_newsletter_id" class="form-select" required>
                                    <option value="">— Choose —</option>
                                    <?php foreach ($newsletters as $nl) { ?>
                                        <option value="<?= (int) $nl['newsletter_id'] ?>">
                                            <?= htmlspecialchars($nl['newsletter_subject'] ?? '') ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success fw-semibold">Send Now</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- ===============================================================
             SEND BULK EMAIL MODAL
        =============================================================== -->
        <div class="modal fade" id="sendBulkModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius:12px; overflow:hidden;">
                    <div class="modal-header text-white border-0"
                         style="background: linear-gradient(135deg, #0d6efd, #3a9bd5);">
                        <h5 class="modal-title fw-bold">Send Bulk Email</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <p class="text-muted">Send a newsletter to all <strong><?= $activeCount ?></strong> active subscriber(s). Inactive and unsubscribed users are excluded automatically.</p>
                        <form action="<?= $base ?>admin/subscribers/sendBulk" method="post">
                            <input type="hidden" name="_csrf_token" value="<?= getCsrfToken() ?>">

                            <?php foreach ($activeSubscribers as $sub) { ?>
                                <input type="hidden" name="subscriber_ids[]"
                                       value="<?= (int) $sub['subscriber_id'] ?>">
                            <?php } ?>

                            <div class="mb-3">
                                <label for="bulk_newsletter_id" class="form-label fw-semibold">Select Newsletter</label>
                                <select name="newsletter_id" id="bulk_newsletter_id" class="form-select" required>
                                    <option value="">— Choose —</option>
                                    <?php foreach ($newsletters as $nl) { ?>
                                        <option value="<?= (int) $nl['newsletter_id'] ?>">
                                            <?= htmlspecialchars($nl['newsletter_subject'] ?? '') ?>
                                            (<?= htmlspecialchars($nl['newsletter_template'] ?? '') ?>)
                                        </option>
                                    <?php } ?>
                                </select>
                                <div class="form-text">
                                    This list shows your saved newsletter records. Each record has its own subject,
                                    body and email template. To send a regular bulk newsletter you want a
                                    <strong>newsletter</strong> template, so be sure to create one first. To create a 
                                    newsletter template, go here: <a href="<?= $base ?>admin/newsletters/create">Manage Newsletters</a> 
                                    and when asked to choose a template, select <strong>newsletter</strong> and save it. 
                                    Then when you come back here, you will see that newsletter in this list. You can then 
                                    choose to use it as the template for any bulk emails you are sending.
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary fw-semibold">Send Now</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }
}
