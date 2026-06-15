<?php

namespace Dorguzen\Modules\Videos\Views;

use Dorguzen\Modules\Videos\Services\VideoService;

class adminManageVideos extends \Dorguzen\Core\DGZ_AdminHtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel); // $album, $videos
        $base = $this->controller->config->getFileRootPath();

        if (empty($album)) {
            ?>
            <div class="container py-5 text-center">
                <h3 class="text-muted">Album not found.</h3>
                <a href="<?= $base ?>admin/videos" class="btn btn-outline-dark mt-3">Back to Albums</a>
            </div>
            <?php
            return;
        }

        $albumId = (int) $album['album_id'];
        ?>
        <style>
            .videos-table-wrap { border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.08); overflow: hidden; }
            .videos-table-wrap thead th { background: #1a1a2e !important; color: #fff !important; font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; border: none !important; white-space: nowrap; }
            .videos-table-wrap tbody tr:hover { background: #f0f4ff; }
            .source-badge { font-size: .72rem; padding: 2px 9px; border-radius: 20px; font-weight: 700; }
            .badge-youtube { background: #ff0000; color: #fff; }
            .badge-vimeo   { background: #1ab7ea; color: #fff; }
            .thumb-preview { width: 60px; height: 40px; object-fit: cover; border-radius: 5px; }
            .thumb-ph { width: 60px; height: 40px; background: #1a1a2e; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #888; font-size: 1.1rem; }
        </style>

        <!-- Hero -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn"><?= htmlspecialchars($album['album_name']) ?></h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>admin/videos">Videos</a></li>
                                <li class="breadcrumb-item text-white active">Manage Videos</li>
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

                <!-- Add video form -->
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Add a Video</h5>
                        <form method="POST" action="<?= $base ?>admin/videos/addVideo">
                            <input type="hidden" name="_csrf_token" value="<?= getCsrfToken() ?>">
                            <input type="hidden" name="albumId" value="<?= $albumId ?>">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="video_title" class="form-control"
                                           placeholder="e.g. Opening Ceremony Highlights" maxlength="255" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Source <span class="text-danger">*</span></label>
                                    <select name="video_source" class="form-select" required>
                                        <option value="">— Select —</option>
                                        <option value="youtube">YouTube</option>
                                        <option value="vimeo">Vimeo</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">
                                        Video ID or URL <span class="text-danger">*</span>
                                        <small class="text-muted fw-normal d-block" style="font-size:.75rem;">
                                            Paste the full URL <em>or</em> just the ID —
                                            for YouTube use the value after <code>v=</code>
                                            (e.g. <code>dQw4w9WgXcQ</code>);
                                            for Vimeo use the number after <code>vimeo.com/</code>.
                                        </small>
                                    </label>
                                    <input type="text" name="video_ref" class="form-control"
                                           placeholder="Full URL or bare ID — e.g. dQw4w9WgXcQ" required>
                                </div>
                                <div class="col-md-9">
                                    <label class="form-label fw-semibold">Description</label>
                                    <input type="text" name="video_description" class="form-control"
                                           placeholder="Optional short description" maxlength="500">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Sort Order</label>
                                    <input type="number" name="video_sort_order" class="form-control"
                                           value="0" min="0">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn text-white px-4" style="background: #1a1a2e;">
                                        <i class="fa fa-plus me-1"></i> Add Video
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Existing videos list -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Videos in this Album <span class="badge bg-secondary"><?= count($videos) ?></span></h4>
                </div>

                <?php if (empty($videos)): ?>
                    <div class="alert alert-info">No videos yet. Use the form above to add one.</div>
                <?php else: ?>
                    <div class="videos-table-wrap">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Thumb</th>
                                        <th>Title</th>
                                        <th>Source</th>
                                        <th>Video ID</th>
                                        <th class="text-center">Order</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($videos as $video):
                                        $thumb = VideoService::thumbnailUrl($video);
                                    ?>
                                        <tr>
                                            <td>
                                                <?php if ($thumb !== ''): ?>
                                                    <img class="thumb-preview" src="<?= htmlspecialchars($thumb) ?>" alt="">
                                                <?php else: ?>
                                                    <div class="thumb-ph"><i class="fa fa-film"></i></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($video['video_title']) ?></strong>
                                                <?php if (!empty($video['video_description'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars(mb_substr($video['video_description'], 0, 60)) ?>…</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="source-badge badge-<?= $video['video_source'] ?>">
                                                    <?= $video['video_source'] === 'youtube' ? 'YouTube' : 'Vimeo' ?>
                                                </span>
                                            </td>
                                            <td><code style="font-size:.78rem;"><?= htmlspecialchars($video['video_ref']) ?></code></td>
                                            <td class="text-center"><?= (int) $video['video_sort_order'] ?></td>
                                            <td>
                                                <a href="<?= $base ?>admin/videos/deleteVideo?videoId=<?= (int) $video['video_id'] ?>&albumId=<?= $albumId ?>"
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Delete this video?');">
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

                <div class="mt-4">
                    <a href="<?= $base ?>admin/videos" class="btn btn-outline-dark">
                        <i class="fa fa-arrow-left me-1"></i> Back to Albums
                    </a>
                </div>
            </div>
        </section>
        <?php
    }
}
