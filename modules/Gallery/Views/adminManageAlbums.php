<?php

namespace Dorguzen\Modules\Gallery\Views;

class adminManageAlbums extends \Dorguzen\Core\DGZ_AdminHtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel); // $albums
        $base = $this->controller->config->getFileRootPath();
        ?>
        <style>
            .gallery-table-wrap { border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.08); overflow: hidden; }
            .gallery-table-wrap thead th { background: var(--site-theme, #fd7e14) !important; color: #fff !important; font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; border: none !important; white-space: nowrap; }
            .gallery-table-wrap tbody tr:hover { background: #fff8f2; }
            .cover-thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; }
            .cover-placeholder { width: 50px; height: 50px; border-radius: 6px; background: #eee; display: flex; align-items: center; justify-content: center; color: #bbb; }
            .badge-active { background: #28a745; }
            .badge-inactive { background: #6c757d; }
        </style>

        <!-- Hero -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn">Manage Gallery</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item text-white active">Gallery Albums</li>
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
                    <a href="<?= $base ?>admin/gallery/create" class="btn btn-sm text-white"
                       style="background: var(--site-theme, #fd7e14);">
                        <i class="fa fa-plus me-1"></i> New Album
                    </a>
                </div>

                <?php if (empty($albums)): ?>
                    <div class="alert alert-info">No albums yet. <a href="<?= $base ?>admin/gallery/create">Create your first album.</a></div>
                <?php else: ?>
                    <div class="gallery-table-wrap">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Cover</th>
                                        <th>Album Name</th>
                                        <th>Status</th>
                                        <th class="text-center">Images</th>
                                        <th>Created</th>
                                        <th>Home Slider</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($albums as $album): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($album['album_cover'])): ?>
                                                    <img class="cover-thumb"
                                                         src="<?= $base ?>assets/images/gallery/<?= (int) $album['album_id'] ?>/<?= htmlspecialchars(\Dorguzen\Core\DGZ_Uploader\DGZ_Upload::thumbName($album['album_cover'])) ?>"
                                                         alt="">
                                                <?php else: ?>
                                                    <div class="cover-placeholder"><i class="fa fa-image"></i></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($album['album_name']) ?></strong>
                                                <?php if (!empty($album['album_description'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars(mb_substr($album['album_description'], 0, 60)) ?>…</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge <?= $album['album_status'] === 'active' ? 'badge-active' : 'badge-inactive' ?>">
                                                    <?= ucfirst(htmlspecialchars($album['album_status'])) ?>
                                                </span>
                                            </td>
                                            <td class="text-center"><?= (int) $album['image_count'] ?></td>
                                            <td><small class="text-muted"><?= date('d M Y', strtotime($album['created_at'])) ?></small></td>
                                            <td>
                                                <?php if (!empty($album['album_is_featured'] ?? null)): ?>
                                                    <span class="badge" style="background: var(--site-theme, #fd7e14);" title="This album powers the homepage slider">
                                                        <i class="fa fa-star me-1"></i> Featured
                                                    </span>
                                                <?php else: ?>
                                                    <form method="post" action="<?= $base ?>admin/gallery/setFeatured" style="margin:0;">
                                                        <input type="hidden" name="_csrf_token" value="<?= getCsrfToken() ?>">
                                                        <input type="hidden" name="albumId" value="<?= (int) $album['album_id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Use this album for the homepage slider">
                                                            <i class="fa fa-star me-1"></i> Make home
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1 flex-wrap">
                                                    <a href="<?= $base ?>admin/gallery/images?albumId=<?= (int) $album['album_id'] ?>"
                                                       class="btn btn-sm btn-outline-primary" title="Manage Images">
                                                        <i class="fa fa-images"></i> Images
                                                    </a>
                                                    <a href="<?= $base ?>admin/gallery/create?edit=1&albumId=<?= (int) $album['album_id'] ?>"
                                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="<?= $base ?>admin/gallery/delete?albumId=<?= (int) $album['album_id'] ?>"
                                                       class="btn btn-sm btn-outline-danger"
                                                       title="Delete Album"
                                                       onclick="return confirm('Delete this album and all its images? This cannot be undone.');">
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
