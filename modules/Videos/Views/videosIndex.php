<?php

namespace Dorguzen\Modules\Videos\Views;

use Dorguzen\Modules\Videos\Services\VideoService;

class videosIndex extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel); // $albums
        $base = $this->controller->config->getFileRootPath();
        ?>
        <style>
            .videos-hero { background: linear-gradient(135deg, var(--site-theme, #fd7e14) 0%, #c75000 100%); }
            .album-card { border: none; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 18px rgba(0,0,0,.12); transition: transform .2s, box-shadow .2s; background: #fff; }
            .album-card:hover { transform: translateY(-5px); box-shadow: 0 8px 30px rgba(0,0,0,.2); }
            .album-cover { position: relative; height: 200px; overflow: hidden; background: #1a1a2e; }
            .album-cover img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; }
            .album-card:hover .album-cover img { transform: scale(1.06); }
            .play-overlay { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,.35); opacity: 0; transition: opacity .25s; }
            .album-card:hover .play-overlay { opacity: 1; }
            .play-icon { width: 52px; height: 52px; border-radius: 50%; background: rgba(255,255,255,.9); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: #1a1a2e; }
            .cover-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #444; font-size: 3rem; }
            .video-count-badge { position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,.6); color: #fff; border-radius: 20px; padding: 3px 12px; font-size: .78rem; }
            .album-name { font-weight: 700; font-size: 1rem; color: #222; margin-bottom: 4px; }
            .album-desc { font-size: .83rem; color: #777; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        </style>

        <!-- Hero -->
        <div class="container-xxl py-5 videos-hero hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn">Videos</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>">Home</a></li>
                                <li class="breadcrumb-item text-white active" aria-label="page">Videos</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Albums grid -->
        <section class="py-5">
            <div class="container">
                <h2 class="text-center fw-bold mb-4" style="font-size:1.8rem; letter-spacing:.3px;">Video Albums</h2>
                <?php if (empty($albums)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-film fa-3x mb-3"></i>
                        <p class="fs-5">No videos yet. Check back soon!</p>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($albums as $album):
                            $cover = $album['cover_video'] ?? null;
                            $thumb = $cover ? VideoService::thumbnailUrl($cover) : '';
                        ?>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <a href="<?= $base ?>videos/album?albumId=<?= (int) $album['album_id'] ?>"
                                   class="text-decoration-none">
                                    <div class="album-card card h-100">
                                        <div class="album-cover">
                                            <?php if ($thumb !== ''): ?>
                                                <img src="<?= htmlspecialchars($thumb) ?>"
                                                     alt="<?= htmlspecialchars($album['album_name']) ?>">
                                            <?php else: ?>
                                                <div class="cover-placeholder"><i class="fa fa-film"></i></div>
                                            <?php endif; ?>
                                            <div class="play-overlay">
                                                <div class="play-icon"><i class="fa fa-play ms-1"></i></div>
                                            </div>
                                            <span class="video-count-badge">
                                                <?= (int) $album['video_count'] ?> video<?= $album['video_count'] != 1 ? 's' : '' ?>
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
