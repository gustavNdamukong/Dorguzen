<?php

namespace Dorguzen\Modules\Videos\Controllers;

use Dorguzen\Core\DGZ_View;
use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Core\DGZ_Validate;
use Dorguzen\Core\DGZ_ModuleControllerInterface;
use Dorguzen\Core\DGZ_ModuleControllerTrait;
use Dorguzen\Modules\Videos\Services\VideoService;
use Dorguzen\Modules\Videos\Services\VideoAdminService;

class VideosController extends DGZ_Controller implements DGZ_ModuleControllerInterface
{
    use DGZ_ModuleControllerTrait;

    public function __construct(
        private VideoService      $videoService,
        private VideoAdminService $adminService,
    ) {
        parent::__construct();
    }

    public function getDefaultAction(): string
    {
        return 'index';
    }

    // =========================================================================
    // Frontend routes
    // =========================================================================

    public function index(): void
    {
        $view = DGZ_View::getModuleView('videos', 'videosIndex', $this, 'html');
        $this->setLayoutDirectory($this->config->getConfig()['layoutDirectory']);
        $this->setLayoutView($this->config->getConfig()['defaultLayout']);
        $view->show($this->videoService->videosIndexPayload());
    }

    public function album(): void
    {
        $albumId = (int) ($_GET['albumId'] ?? 0);

        if ($albumId <= 0) {
            $this->redirect('videos', '');
            return;
        }

        $view = DGZ_View::getModuleView('videos', 'videosAlbum', $this, 'html');
        $this->setLayoutDirectory($this->config->getConfig()['layoutDirectory']);
        $this->setLayoutView($this->config->getConfig()['defaultLayout']);
        $view->show($this->videoService->videosAlbumPayload($albumId));
    }

    // =========================================================================
    // Admin routes
    // =========================================================================

    public function manageAlbums(): void
    {
        $view = DGZ_View::getModuleView('videos', 'adminManageAlbums', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($this->adminService->manageAlbumsPayload());
    }

    /**
     * GET  /admin/videos/create             — blank form
     * GET  /admin/videos/create?edit=1&albumId=X — pre-filled edit form
     * POST /admin/videos/create             — save new album
     * POST /admin/videos/create?edit=1      — update existing album
     */
    public function createAlbum(): void
    {
        $isEdit  = (($_GET['edit'] ?? '') === '1');
        $albumId = (int) ($_GET['albumId'] ?? $_POST['albumId'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $val  = new DGZ_Validate();
            $name = trim($val->fix_string($_POST['album_name'] ?? ''));
            $desc = trim($val->fix_string($_POST['album_description'] ?? ''));

            if ($name === '') {
                $this->addErrors('<p>Album name is required.</p>', 'Error');
                $this->redirect('admin/videos', $isEdit ? "create?edit=1&albumId={$albumId}" : 'create');
                return;
            }

            $slug = $isEdit
                ? $this->adminService->generateUniqueSlug($name, $albumId)
                : $this->adminService->generateSlug($name);

            if ($isEdit && $albumId > 0) {
                $ok = $this->adminService->updateAlbum($albumId, [
                    'album_name'        => $name,
                    'album_slug'        => $slug,
                    'album_description' => $desc,
                ]);
                if ($ok) {
                    $this->addSuccess('Album updated successfully.', 'Done!');
                } else {
                    $this->addErrors('Could not update the album.', 'Error');
                }
            } else {
                $newId = $this->adminService->saveAlbum([
                    'album_name'        => $name,
                    'album_slug'        => $slug,
                    'album_description' => $desc,
                ]);
                if ($newId) {
                    $this->addSuccess('Album created successfully.', 'Done!');
                } else {
                    $this->addErrors('Could not create the album.', 'Error');
                }
            }

            $this->redirect('admin/videos', '');
            return;
        }

        $albumData = null;
        if ($isEdit && $albumId > 0) {
            $albumData = container(\Dorguzen\Modules\Videos\Models\VideoAlbum::class)->getAlbumById($albumId);
        }

        $view = DGZ_View::getModuleView('videos', 'adminCreateAlbum', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($this->adminService->createAlbumPayload($albumData ? [$albumData] : null));
    }

    public function deleteAlbum(): void
    {
        $albumId = (int) ($_GET['albumId'] ?? 0);

        if ($albumId <= 0) {
            $this->redirect('admin/videos', '');
            return;
        }

        if ($this->adminService->deleteAlbum($albumId)) {
            $this->addSuccess('Album and all its videos have been deleted.', 'Done!');
        } else {
            $this->addErrors('Could not delete the album.', 'Error');
        }

        $this->redirect('admin/videos', '');
    }

    public function manageVideos(): void
    {
        $albumId = (int) ($_GET['albumId'] ?? 0);

        if ($albumId <= 0) {
            $this->redirect('admin/videos', '');
            return;
        }

        $view = DGZ_View::getModuleView('videos', 'adminManageVideos', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($this->adminService->manageVideosPayload($albumId));
    }

    /**
     * POST /admin/videos/addVideo — add a video to an album
     */
    public function addVideo(): void
    {
        $albumId = (int) ($_POST['albumId'] ?? 0);

        if ($albumId <= 0) {
            $this->redirect('admin/videos', '');
            return;
        }

        $val    = new DGZ_Validate();
        $title  = trim($val->fix_string($_POST['video_title'] ?? ''));
        $desc   = trim($val->fix_string($_POST['video_description'] ?? ''));
        $source = $_POST['video_source'] ?? '';
        $ref    = trim($_POST['video_ref'] ?? '');
        $sort   = (int) ($_POST['video_sort_order'] ?? 0);

        if ($title === '') {
            $this->addErrors('Video title is required.', 'Error');
            $this->redirect('admin/videos', "videos?albumId={$albumId}");
            return;
        }

        if (!in_array($source, ['youtube', 'vimeo'], true)) {
            $this->addErrors('Please select a valid video source (YouTube or Vimeo).', 'Error');
            $this->redirect('admin/videos', "videos?albumId={$albumId}");
            return;
        }

        if ($ref === '') {
            $this->addErrors('Video ID / reference is required.', 'Error');
            $this->redirect('admin/videos', "videos?albumId={$albumId}");
            return;
        }

        // Strip full URLs down to just the video ID
        $ref = $this->extractVideoRef($source, $ref);

        $newId = $this->adminService->saveVideo([
            'album_id'          => $albumId,
            'video_title'       => $title,
            'video_description' => $desc,
            'video_source'      => $source,
            'video_ref'         => $ref,
            'video_sort_order'  => $sort,
        ]);

        if ($newId) {
            $this->addSuccess('Video added successfully.', 'Done!');
        } else {
            $this->addErrors('Could not add the video.', 'Error');
        }

        $this->redirect('admin/videos', "videos?albumId={$albumId}");
    }

    public function deleteVideo(): void
    {
        $videoId = (int) ($_GET['videoId'] ?? 0);
        $albumId = (int) ($_GET['albumId'] ?? 0);

        if ($videoId <= 0) {
            $this->redirect('admin/videos', $albumId > 0 ? "videos?albumId={$albumId}" : '');
            return;
        }

        $video = $this->adminService->deleteVideo($videoId);

        if ($video) {
            $this->addSuccess('Video deleted.', 'Done!');
            $this->redirect('admin/videos', "videos?albumId=" . (int) $video['album_id']);
        } else {
            $this->addErrors('Video not found.', 'Error');
            $this->redirect('admin/videos', $albumId > 0 ? "videos?albumId={$albumId}" : '');
        }
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function extractVideoRef(string $source, string $input): string
    {
        if ($source === 'youtube') {
            // Handle youtu.be/ID, youtube.com/watch?v=ID, youtube.com/embed/ID
            if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/))([A-Za-z0-9_\-]{11})/', $input, $m)) {
                return $m[1];
            }
        }

        if ($source === 'vimeo') {
            // Handle vimeo.com/12345678 or player.vimeo.com/video/12345678
            if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $input, $m)) {
                return $m[1];
            }
        }

        // Bare ID — strip any accidentally included query params (e.g. "ID&list=...&start_radio=1")
        return preg_replace('/[?&].*$/', '', $input);
    }
}
