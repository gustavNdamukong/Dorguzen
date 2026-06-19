<?php

namespace Dorguzen\Modules\Videos\Views;

use Dorguzen\Modules\Videos\Services\VideoService;

class videosAlbum extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel); // $album, $videos
        $base = $this->controller->config->getFileRootPath();

        if (empty($album)) {
            ?>
            <div class="container py-5 text-center">
                <h3 class="text-muted">Album not found.</h3>
                <a href="<?= $base ?>videos" class="btn btn-outline-dark mt-3">Back to Videos</a>
            </div>
            <?php
            return;
        }
        ?>
        <style>
            .videos-hero { background: linear-gradient(135deg, var(--site-theme, #fd7e14) 0%, #c75000 100%); }
            .video-card { border: none; border-radius: 14px; overflow: hidden; box-shadow: 0 3px 14px rgba(0,0,0,.1); background: #fff; transition: transform .2s, box-shadow .2s; cursor: pointer; }
            .video-card:hover { transform: translateY(-4px); box-shadow: 0 7px 24px rgba(0,0,0,.18); }
            .video-thumb { position: relative; height: 180px; overflow: hidden; background: #111; }
            .video-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; }
            .video-card:hover .video-thumb img { transform: scale(1.06); }
            .play-btn { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,.4); }
            .play-circle { width: 54px; height: 54px; border-radius: 50%; background: rgba(255,255,255,.92); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #1a1a2e; }
            .source-badge { position: absolute; top: 8px; left: 10px; font-size: .72rem; padding: 2px 9px; border-radius: 20px; font-weight: 700; }
            .badge-youtube { background: #ff0000; color: #fff; }
            .badge-vimeo   { background: #1ab7ea; color: #fff; }
            .thumb-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #555; font-size: 2.5rem; background: #1a1a2e; }

            /* Lightbox overlay */
            #videoModal .modal-dialog { max-width: 860px; }
            #videoModal .ratio { --bs-aspect-ratio: 56.25%; }
            #videoModal .modal-content { background: #000; border: none; }
            #videoModal .btn-close { filter: invert(1); }
        </style>

        <!-- Hero -->
        <div class="container-xxl py-5 videos-hero hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn"><?= htmlspecialchars($album['album_name']) ?></h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>">Home</a></li>
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>videos">Videos</a></li>
                                <li class="breadcrumb-item text-white active"><?= htmlspecialchars($album['album_name']) ?></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Videos grid -->
        <section class="py-5">
            <div class="container">
                <h2 class="fw-bold mb-2" style="font-size:1.9rem; color:#222; letter-spacing:.2px;">
                    <?= htmlspecialchars($album['album_name']) ?>
                </h2>
                <?php if (!empty($album['album_description'])): ?>
                    <p class="text-muted mb-4"><?= htmlspecialchars($album['album_description']) ?></p>
                <?php else: ?>
                    <div class="mb-4"></div>
                <?php endif; ?>

                <?php if (empty($videos)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-film fa-3x mb-3"></i>
                        <p class="fs-5">No videos in this album yet.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($videos as $video):
                            $embedUrl = VideoService::embedUrl($video);
                            $thumb    = VideoService::thumbnailUrl($video);
                        ?>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="video-card"
                                     data-embed="<?= htmlspecialchars($embedUrl) ?>"
                                     data-title="<?= htmlspecialchars($video['video_title']) ?>"
                                     onclick="openVideo(this)">
                                    <div class="video-thumb">
                                        <?php if ($thumb !== ''): ?>
                                            <img src="<?= htmlspecialchars($thumb) ?>"
                                                 alt="<?= htmlspecialchars($video['video_title']) ?>">
                                        <?php else: ?>
                                            <div class="thumb-placeholder"><i class="fa fa-play-circle"></i></div>
                                        <?php endif; ?>
                                        <div class="play-btn">
                                            <div class="play-circle"><i class="fa fa-play ms-1"></i></div>
                                        </div>
                                        <span class="source-badge badge-<?= $video['video_source'] ?>">
                                            <?= $video['video_source'] === 'youtube' ? 'YouTube' : 'Vimeo' ?>
                                        </span>
                                    </div>
                                    <div class="p-3">
                                        <p class="fw-semibold mb-1 text-dark" style="font-size:.92rem; line-height:1.3;">
                                            <?= htmlspecialchars($video['video_title']) ?>
                                        </p>
                                        <?php if (!empty($video['video_description'])): ?>
                                            <p class="text-muted mb-0" style="font-size:.78rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                                <?= htmlspecialchars($video['video_description']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="mt-5">
                    <a href="<?= $base ?>videos" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Back to Videos
                    </a>
                </div>
            </div>
        </section>

        <!-- Video lightbox modal -->
        <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h6 class="modal-title text-white" id="videoModalLabel"></h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-2">
                        <div class="ratio ratio-16x9">
                            <iframe id="videoFrame" src="" title="" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function openVideo(card) {
            var embed = card.getAttribute('data-embed');
            var title = card.getAttribute('data-title');
            var sep = embed.indexOf('?') === -1 ? '?' : '&';
            document.getElementById('videoFrame').src = embed + sep + 'autoplay=1';
            document.getElementById('videoModalLabel').textContent = title;
            var modal = new bootstrap.Modal(document.getElementById('videoModal'));
            modal.show();
        }

        document.getElementById('videoModal').addEventListener('hide.bs.modal', function () {
            document.getElementById('videoFrame').src = '';
        });
        </script>
        <?php
    }
}
