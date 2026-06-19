<?php

namespace Dorguzen\Modules\Testimonials\Views;

class adminManageTestimonials extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel);
        // $testimonials, $total, $pendingCount
        $rootPath = $this->controller->config->getFileRootPath();

        $slideMenu = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
        $slideMenu->show();
        ?>

        <div class="container py-4">

            <!-- Flash messages -->
            <?php if (!empty($_SESSION['_dgz_success'])): ?>
                <?php foreach ($_SESSION['_dgz_success'] as $msg): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; unset($_SESSION['_dgz_success']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['_dgz_errors'])): ?>
                <?php foreach ($_SESSION['_dgz_errors'] as $msg): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; unset($_SESSION['_dgz_errors']); ?>
            <?php endif; ?>

            <!-- Page header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="fw-bold mb-0">
                    Testimonial Moderation
                    <?php if ($pendingCount > 0): ?>
                        <span class="badge bg-warning text-dark ms-2" style="font-size:.75rem;">
                            <?= (int) $pendingCount ?> pending
                        </span>
                    <?php endif; ?>
                </h4>
                <span class="text-muted" style="font-size:.88rem;"><?= (int) $total ?> total</span>
            </div>

            <?php if (empty($testimonials)): ?>
                <div class="card border-0 shadow-sm text-center py-5">
                    <p class="text-muted mb-0">
                        <i class="fa fa-comments fa-2x d-block mb-2 opacity-25"></i>
                        No testimonials have been submitted yet.
                    </p>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm" style="border-radius:12px; overflow:hidden;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background:var(--site-theme, #3949ab); color:#fff; font-size:.82rem; text-transform:uppercase; letter-spacing:.5px;">
                                <tr>
                                    <th>Name</th>
                                    <th>Company / Role</th>
                                    <th>Email</th>
                                    <th>Rating</th>
                                    <th>Comment</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody style="font-size:.88rem;">
                                <?php foreach ($testimonials as $t): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($t['testimonial_name']) ?></strong>
                                        </td>
                                        <td style="max-width:130px;">
                                            <?php
                                            $meta = array_filter([
                                                $t['testimonial_role']    ?? '',
                                                $t['testimonial_company'] ?? '',
                                            ]);
                                            echo $meta ? htmlspecialchars(implode(', ', $meta)) : '<span class="text-muted">—</span>';
                                            ?>
                                        </td>
                                        <td style="font-size:.8rem; color:#6c757d; max-width:150px;" class="text-truncate">
                                            <?= !empty($t['testimonial_email']) ? htmlspecialchars($t['testimonial_email']) : '—' ?>
                                        </td>
                                        <td style="color:#ffc107; font-size:1rem; white-space:nowrap;">
                                            <?= $t['stars_html'] ?>
                                        </td>
                                        <td style="max-width:240px;">
                                            <span class="text-muted" style="font-size:.85rem;">
                                                <?= htmlspecialchars(mb_substr($t['testimonial_comment'], 0, 90)) ?>
                                                <?= mb_strlen($t['testimonial_comment']) > 90 ? '&hellip;' : '' ?>
                                            </span>
                                        </td>
                                        <td style="font-size:.78rem; color:#6c757d; white-space:nowrap;">
                                            <?= date('M j, Y', strtotime($t['created_at'])) ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= htmlspecialchars($t['status_class']) ?>">
                                                <?= htmlspecialchars(ucfirst($t['testimonial_status'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-end" style="white-space:nowrap;">
                                            <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                                    style="font-size:.78rem; padding:3px 10px;"
                                                    data-id="<?= (int) $t['testimonial_id'] ?>"
                                                    data-status="<?= htmlspecialchars($t['testimonial_status']) ?>"
                                                    data-name="<?= htmlspecialchars($t['testimonial_name']) ?>"
                                                    data-meta="<?= htmlspecialchars(implode(', ', array_filter([$t['testimonial_role'] ?? '', $t['testimonial_company'] ?? '']))) ?>"
                                                    data-rating="<?= (int) $t['testimonial_rating'] ?>"
                                                    data-comment="<?= htmlspecialchars($t['testimonial_comment']) ?>"
                                                    onclick="openManageModal(this)">
                                                <i class="fa fa-edit me-1"></i>Manage
                                            </button>
                                            <a href="<?= $rootPath ?>admin/testimonials/delete?testimonial_id=<?= (int) $t['testimonial_id'] ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               style="font-size:.78rem; padding:3px 10px;"
                                               onclick="return confirm('Permanently delete this testimonial?')">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Manage modal -->
        <div class="modal fade" id="manageModal" tabindex="-1" aria-labelledby="manageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width:560px;">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background:var(--site-theme,#3949ab);">
                        <h6 class="modal-title fw-bold text-white" id="manageModalLabel">
                            <i class="fa fa-star me-2"></i>Review Testimonial
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="post" action="<?= $rootPath ?>admin/testimonials/approve">
                        <div class="modal-body py-4">
                            <input type="hidden" name="_csrf_token" value="<?= getCsrfToken() ?>">
                            <input type="hidden" name="testimonial_id" id="modalTestimonialId" value="">

                            <!-- Testimonial preview -->
                            <div class="p-3 mb-4 rounded" style="background:#f8f9fa; border:1px solid #e9ecef;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <strong id="modalName" style="font-size:.95rem;"></strong>
                                    <span id="modalMeta" class="text-muted" style="font-size:.8rem;"></span>
                                </div>
                                <div id="modalStars" style="color:#ffc107; font-size:1.1rem; margin-bottom:10px;"></div>
                                <p id="modalComment" class="mb-0" style="font-size:.9rem; line-height:1.7; white-space:pre-wrap; color:#333;"></p>
                            </div>

                            <p class="fw-semibold mb-3" style="font-size:.9rem;">Set status:</p>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="status" id="statusApproved" value="approved">
                                <label class="form-check-label" for="statusApproved">
                                    <span class="badge bg-success me-1">Approved</span>
                                    <span class="text-muted" style="font-size:.82rem;">— will appear publicly</span>
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="status" id="statusPending" value="pending">
                                <label class="form-check-label" for="statusPending">
                                    <span class="badge bg-warning text-dark me-1">Pending</span>
                                    <span class="text-muted" style="font-size:.82rem;">— under review</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusRejected" value="rejected">
                                <label class="form-check-label" for="statusRejected">
                                    <span class="badge bg-danger me-1">Rejected</span>
                                    <span class="text-muted" style="font-size:.82rem;">— hidden from public</span>
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-primary">Save Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
        function openManageModal(btn) {
            var id      = btn.getAttribute('data-id');
            var status  = btn.getAttribute('data-status');
            var name    = btn.getAttribute('data-name');
            var meta    = btn.getAttribute('data-meta');
            var rating  = parseInt(btn.getAttribute('data-rating'), 10) || 0;
            var comment = btn.getAttribute('data-comment');

            document.getElementById('modalTestimonialId').value = id;
            document.getElementById('modalName').textContent    = name;
            document.getElementById('modalMeta').textContent    = meta ? '— ' + meta : '';
            document.getElementById('modalComment').textContent = comment;
            document.getElementById('modalStars').textContent   = '★'.repeat(rating) + '☆'.repeat(5 - rating);

            var radios = document.querySelectorAll('#manageModal input[name="status"]');
            radios.forEach(function (r) { r.checked = (r.value === status); });

            var modal = new bootstrap.Modal(document.getElementById('manageModal'));
            modal.show();
        }
        </script>

        <?php
    }
}
