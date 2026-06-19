<?php

namespace Dorguzen\Modules\Blog\Views;

class adminManageComments extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel);
        // $comments, $pendingCount
        $rootPath   = $this->controller->config->getFileRootPath();
        $themeColor = 'var(--site-theme, #3949ab)';

        $slideMenu = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
        $slideMenu->show();

        $pending  = array_filter($comments, fn($c) => $c['status'] === 'pending');
        $approved = array_filter($comments, fn($c) => $c['status'] === 'approved');
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

            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="fw-bold mb-0">
                    Comment Moderation
                    <?php if ($pendingCount > 0): ?>
                        <span class="badge bg-warning text-dark ms-2" style="font-size:.75rem;">
                            <?= $pendingCount ?> pending
                        </span>
                    <?php endif; ?>
                </h4>
                <a href="<?= $rootPath ?>admin/blog" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i>Back to Posts
                </a>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" id="commentTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-pending">
                        Pending
                        <?php if ($pendingCount > 0): ?>
                            <span class="badge bg-warning text-dark ms-1" style="font-size:.7rem;"><?= $pendingCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-approved">
                        Approved (<?= count($approved) ?>)
                    </a>
                </li>
            </ul>

            <div class="tab-content">

                <!-- Pending tab -->
                <div class="tab-pane fade show active" id="tab-pending">
                    <?php if (empty($pending)): ?>
                        <div class="card border-0 shadow-sm text-center py-5">
                            <p class="text-muted mb-0"><i class="fa fa-check-circle fa-2x d-block mb-2 text-success opacity-50"></i>No pending comments.</p>
                        </div>
                    <?php else: ?>
                        <div class="card border-0 shadow-sm" style="border-radius:12px; overflow:hidden;">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead style="background:var(--site-theme, #3949ab); font-size:.82rem; text-transform:uppercase; letter-spacing:.5px;">
                                        <tr>
                                            <th>Post</th>
                                            <th>Author</th>
                                            <th>Comment</th>
                                            <th>Date</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size:.88rem;">
                                        <?php foreach ($pending as $c): ?>
                                            <tr>
                                                <td style="max-width:140px;">
                                                    <span class="d-block text-truncate fw-semibold" title="<?= htmlspecialchars($c['post_title'] ?? '') ?>">
                                                        <?= htmlspecialchars($c['post_title'] ?? '—') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($c['author_name']) ?></strong>
                                                    <div class="text-muted" style="font-size:.75rem;"><?= htmlspecialchars($c['author_email']) ?></div>
                                                </td>
                                                <td style="max-width:260px;">
                                                    <span class="text-muted" style="font-size:.85rem;">
                                                        <?= htmlspecialchars(mb_substr($c['body'], 0, 100)) ?><?= mb_strlen($c['body']) > 100 ? '…' : '' ?>
                                                    </span>
                                                </td>
                                                <td style="font-size:.78rem; color:#6c757d; white-space:nowrap;">
                                                    <?= date('M j, Y', strtotime($c['created_at'])) ?>
                                                </td>
                                                <td class="text-end" style="white-space:nowrap;">
                                                    <a href="<?= $rootPath ?>admin/blog/approveComment?commentId=<?= (int) $c['comment_id'] ?>"
                                                       class="btn btn-xs btn-success me-1" style="font-size:.78rem; padding:3px 10px;">
                                                        <i class="fa fa-check me-1"></i>Approve
                                                    </a>
                                                    <a href="<?= $rootPath ?>admin/blog/deleteComment?commentId=<?= (int) $c['comment_id'] ?>"
                                                       class="btn btn-xs btn-outline-danger"
                                                       style="font-size:.78rem; padding:3px 10px;"
                                                       onclick="return confirm('Delete this comment?')">
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

                <!-- Approved tab -->
                <div class="tab-pane fade" id="tab-approved">
                    <?php if (empty($approved)): ?>
                        <div class="card border-0 shadow-sm text-center py-5">
                            <p class="text-muted mb-0">No approved comments yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="card border-0 shadow-sm" style="border-radius:12px; overflow:hidden;">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead style="background:var(--site-theme, #3949ab); font-size:.82rem; text-transform:uppercase; letter-spacing:.5px;">
                                        <tr>
                                            <th>Post</th>
                                            <th>Author</th>
                                            <th>Comment</th>
                                            <th>Date</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size:.88rem;">
                                        <?php foreach ($approved as $c): ?>
                                            <tr>
                                                <td style="max-width:140px;">
                                                    <span class="d-block text-truncate fw-semibold" title="<?= htmlspecialchars($c['post_title'] ?? '') ?>">
                                                        <?= htmlspecialchars($c['post_title'] ?? '—') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($c['author_name']) ?></strong>
                                                    <div class="text-muted" style="font-size:.75rem;"><?= htmlspecialchars($c['author_email']) ?></div>
                                                </td>
                                                <td style="max-width:260px;">
                                                    <span class="text-muted" style="font-size:.85rem;">
                                                        <?= htmlspecialchars(mb_substr($c['body'], 0, 100)) ?><?= mb_strlen($c['body']) > 100 ? '…' : '' ?>
                                                    </span>
                                                </td>
                                                <td style="font-size:.78rem; color:#6c757d; white-space:nowrap;">
                                                    <?= date('M j, Y', strtotime($c['created_at'])) ?>
                                                </td>
                                                <td class="text-end">
                                                    <a href="<?= $rootPath ?>admin/blog/deleteComment?commentId=<?= (int) $c['comment_id'] ?>"
                                                       class="btn btn-xs btn-outline-danger"
                                                       style="font-size:.78rem; padding:3px 10px;"
                                                       onclick="return confirm('Delete this comment?')">
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

            </div>
        </div>

        <?php
    }
}
