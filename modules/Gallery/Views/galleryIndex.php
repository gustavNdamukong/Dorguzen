<?php

namespace Dorguzen\Modules\Gallery\Views;

use Dorguzen\Core\DGZ_Uploader\DGZ_Upload;

class galleryIndex extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel); // $albums
        $base = $this->controller->config->getFileRootPath();
        ?>
        <style>
            .gallery-hero { background: linear-gradient(135deg, var(--site-theme, #fd7e14) 0%, #c75000 100%); }
            .album-card { border: none; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 18px rgba(0,0,0,.1); transition: transform .2s, box-shadow .2s; }
            .album-card:hover { transform: translateY(-5px); box-shadow: 0 8px 30px rgba(0,0,0,.18); }
            .album-card .cover-wrap { position: relative; height: 200px; overflow: hidden; background: #eee; }
            .album-card .cover-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; }
            .album-card:hover .cover-wrap img { transform: scale(1.06); }
            .album-card .cover-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #f5f5f5; color: #bbb; font-size: 3rem; }
            .album-badge { position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,.55); color: #fff; border-radius: 20px; padding: 3px 12px; font-size: .78rem; }
            .album-name { font-weight: 700; font-size: 1rem; margin-bottom: 4px; color: #222; }
            .album-desc { font-size: .83rem; color: #777; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        </style>

        <!-- Hero -->
        <div class="container-xxl py-5 gallery-hero hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn">Gallery</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>">Home</a></li>
                                <li class="breadcrumb-item text-white active" aria-label="page">Gallery</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Albums grid -->
        <section class="py-5">
            <div class="container">
                <h2 class="text-center fw-bold mb-4" style="font-size:1.8rem; letter-spacing:.3px;">Gallery</h2>
                <?php if (empty($albums)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-images fa-3x mb-3"></i>
                        <p class="fs-5">No albums yet. Check back soon!</p>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($albums as $album): ?>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <a href="<?= $base ?>gallery/album?albumId=<?= (int) $album['album_id'] ?>"
                                   class="text-decoration-none">
                                    <div class="album-card card h-100">
                                        <div class="cover-wrap">
                                            <?php if (!empty($album['album_cover'])): ?>
                                                <img src="<?= $base ?>assets/images/gallery/<?= (int) $album['album_id'] ?>/<?= htmlspecialchars(DGZ_Upload::thumbName($album['album_cover'])) ?>"
                                                     alt="<?= htmlspecialchars($album['album_name']) ?>">
                                            <?php else: ?>
                                                <div class="cover-placeholder"><i class="fas fa-images"></i></div>
                                            <?php endif; ?>
                                            <span class="album-badge">
                                                <?= (int) $album['image_count'] ?> photo<?= $album['image_count'] != 1 ? 's' : '' ?>
                                            </span>
                                        </div>
                                        <div class="card-body p-3">
                                            <p class="album-name"><?= htmlspecialchars($album['album_name']) ?></p>
                                            <?php if (!empty($album['album_description'])): ?>
                                                <p class="album-desc"><?= htmlspecialchars($album['album_description']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
}
