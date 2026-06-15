<?php

namespace Dorguzen\Modules\Videos\Views;

class adminManageAlbums extends \Dorguzen\Core\DGZ_AdminHtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel); // $albums
        $base = $this->controller->config->getFileRootPath();
        ?>
        <style>
            .videos-table-wrap { border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.08); overflow: hidden; }
            .videos-table-wrap thead th { background: #1a1a2e !important; color: #fff !important; font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; border: none !important; white-space: nowrap; }
            .videos-table-wrap tbody tr:hover { background: #f0f4ff; }
        </style>

        <!-- Hero -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn">Manage Videos</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item text-white active">Video Albums</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <section class="py-4">
            <div class="container">
                <?php
                $slideInMenu = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                $slideInMenu->show();
                ?>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Albums <span class="badge bg-secondary"><?= count($albums) ?></span></h4>
                    <a href="<?= $base ?>admin/videos/create" class="btn btn-sm text-white"
                       style="background: #1a1a2e;">
                        <i class="fa fa-plus me-1"></i> New Album
                    </a>
                </div>

                <?php if (empty($albums)): ?>
                    <div class="alert alert-info">No albums yet. <a href="<?= $base ?>admin/videos/create">Create your first video album.</a></div>
                <?php else: ?>
                    <div class="videos-table-wrap">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Album Name</th>
                                        <th class="text-center">Videos</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($albums as $album): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($album['album_name']) ?></strong>
                                                <?php if (!empty($album['album_description'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars(mb_substr($album['album_description'], 0, 70)) ?>…</small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?= (int) $album['video_count'] ?></td>
                                            <td><small class="text-muted"><?= date('d M Y', strtotime($album['created_at'])) ?></small></td>
                                            <td>
                                                <div class="d-flex gap-1 flex-wrap">
                                                    <a href="<?= $base ?>admin/videos/videos?albumId=<?= (int) $album['album_id'] ?>"
                                                       class="btn btn-sm btn-outline-primary" title="Manage Videos">
                                                        <i class="fa fa-film"></i> Videos
                                                    </a>
                                                    <a href="<?= $base ?>admin/videos/create?edit=1&albumId=<?= (int) $album['album_id'] ?>"
                                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="<?= $base ?>admin/videos/delete?albumId=<?= (int) $album['album_id'] ?>"
                                                       class="btn btn-sm btn-outline-danger"
                                                       title="Delete Album"
                                                       onclick="return confirm('Delete this album and all its videos? This cannot be undone.');">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
}
