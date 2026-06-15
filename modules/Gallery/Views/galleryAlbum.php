<?php

namespace Dorguzen\Modules\Gallery\Views;

class galleryAlbum extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel); // $album, $images
        $base = $this->controller->config->getFileRootPath();

        if (!$album) {
            echo '<div class="container py-5 text-center text-muted"><p>Album not found.</p></div>';
            return;
        }

        $albumId = (int) $album['album_id'];
        $imgPath = $base . 'assets/images/gallery/' . $albumId . '/';
        ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/blueimp-gallery@3.4.0/css/blueimp-gallery.min.css">
        <style>
            .gallery-hero { background: linear-gradient(135deg, var(--site-theme, #fd7e14) 0%, #c75000 100%); }
            .photo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px; }
            .photo-item { position: relative; border-radius: 10px; overflow: hidden; cursor: pointer; background: #eee; }
            .photo-item img { width: 100%; height: 200px; object-fit: cover; display: block; transition: transform .25s; }
            .photo-item:hover img { transform: scale(1.05); }
            .photo-caption { position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,.5); color: #fff; font-size: .78rem; padding: 6px 10px; opacity: 0; transition: opacity .2s; }
            .photo-item:hover .photo-caption { opacity: 1; }
            #blueimp-gallery .slides .slide-content { max-height: 90vh !important; }
        </style>

        <!-- Hero -->
        <div class="container-xxl py-5 gallery-hero hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn"><?= htmlspecialchars($album['album_name']) ?></h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>">Home</a></li>
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>gallery">Gallery</a></li>
                                <li class="breadcrumb-item text-white active"><?= htmlspecialchars($album['album_name']) ?></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <section class="py-5">
            <div class="container">
                <h2 class="fw-bold mb-2" style="font-size:1.9rem; color:#222; letter-spacing:.2px;"><?= htmlspecialchars($album['album_name']) ?></h2>
                <?php if (!empty($album['album_description'])): ?>
                    <p class="text-muted mb-4"><?= htmlspecialchars($album['album_description']) ?></p>
                <?php endif; ?>

                <?php if (empty($images)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-camera fa-3x mb-3"></i>
                        <p class="fs-5">No photos in this album yet.</p>
                    </div>
                <?php else: ?>
                    <!-- Hidden full-size links for BlueIMP -->
                    <div id="gallery-links" style="display:none">
                        <?php foreach ($images as $img): ?>
                            <a href="<?= $imgPath . htmlspecialchars($img['image_filename']) ?>"
                               title="<?= htmlspecialchars($img['image_caption'] ?? '') ?>"
                               data-gallery>
                                <img src="<?= $imgPath . htmlspecialchars($img['thumb_filename']) ?>"
                                     alt="<?= htmlspecialchars($img['image_caption'] ?? '') ?>">
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- Visible thumbnail grid -->
                    <div class="photo-grid" id="photo-grid">
                        <?php foreach ($images as $i => $img): ?>
                            <div class="photo-item" data-index="<?= $i ?>">
                                <img src="<?= $imgPath . htmlspecialchars($img['thumb_filename']) ?>"
                                     alt="<?= htmlspecialchars($img['image_caption'] ?? $album['album_name']) ?>">
                                <?php if (!empty($img['image_caption'])): ?>
                                    <div class="photo-caption"><?= htmlspecialchars($img['image_caption']) ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="<?= $base ?>gallery" class="btn btn-outline-secondary btn-sm">
                        &larr; Back to Gallery
                    </a>
                </div>
            </div>
        </section>

        <!-- BlueIMP Gallery lightbox container -->
        <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" aria-label="image viewer">
            <div class="slides" aria-live="polite"></div>
            <h3 class="title"></h3>
            <a class="prev" aria-label="previous slide" aria-controls="blueimp-gallery">&lsaquo;</a>
            <a class="next" aria-label="next slide" aria-controls="blueimp-gallery">&rsaquo;</a>
            <a class="close" aria-label="close" aria-controls="blueimp-gallery">&times;</a>
            <a class="play-pause" aria-label="play slideshow" aria-controls="blueimp-gallery"></a>
            <ol class="indicator"></ol>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/blueimp-gallery@3.4.0/js/blueimp-gallery.min.js"></script>
        <script>
        (function () {
            var links = document.getElementById('gallery-links');
            if (!links) return;

            document.getElementById('photo-grid').addEventListener('click', function (e) {
                var item = e.target.closest('.photo-item');
                if (!item) return;

                var index = parseInt(item.dataset.index, 10);
                var anchors = links.getElementsByTagName('a');

                blueimp.Gallery(anchors, {
                    index: index,
                    event: e,
                    onopen: function () { document.body.classList.add('blueimp-open'); },
                    onclose: function () { document.body.classList.remove('blueimp-open'); }
                });
            });
        })();
        </script>
        <?php
    }
}
