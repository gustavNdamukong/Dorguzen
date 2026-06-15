<?php

namespace Dorguzen\Modules\Gallery\Controllers;

use Dorguzen\Core\DGZ_View;
use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Core\DGZ_Validate;
use Dorguzen\Core\DGZ_ModuleControllerInterface;
use Dorguzen\Core\DGZ_ModuleControllerTrait;
use Dorguzen\Modules\Gallery\Services\GalleryService;
use Dorguzen\Modules\Gallery\Services\GalleryAdminService;

class GalleryController extends DGZ_Controller implements DGZ_ModuleControllerInterface
{
    use DGZ_ModuleControllerTrait;

    public function __construct(
        private GalleryService      $galleryService,
        private GalleryAdminService $adminService,
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
        $view = DGZ_View::getModuleView('gallery', 'galleryIndex', $this, 'html');
        $this->setLayoutDirectory($this->config->getConfig()['layoutDirectory']);
        $this->setLayoutView($this->config->getConfig()['defaultLayout']);
        $view->show($this->galleryService->galleryIndexPayload());
    }

    public function album(): void
    {
        $albumId = (int) ($_GET['albumId'] ?? 0);

        if ($albumId <= 0) {
            $this->redirect('gallery', '');
            return;
        }

        $view = DGZ_View::getModuleView('gallery', 'galleryAlbum', $this, 'html');
        $this->setLayoutDirectory($this->config->getConfig()['layoutDirectory']);
        $this->setLayoutView($this->config->getConfig()['defaultLayout']);
        $view->show($this->galleryService->galleryAlbumPayload($albumId));
    }

    // =========================================================================
    // Admin routes
    // =========================================================================

    public function manageAlbums(): void
    {
        $view = DGZ_View::getModuleView('gallery', 'adminManageAlbums', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($this->adminService->manageAlbumsPayload());
    }

    /**
     * GET  /admin/gallery/create            — blank form
     * GET  /admin/gallery/create?edit=1&albumId=X — pre-filled edit form
     * POST /admin/gallery/create            — save new album
     * POST /admin/gallery/create?edit=1     — update existing album
     */
    public function createAlbum(): void
    {
        $isEdit  = (($_GET['edit'] ?? '') === '1');
        $albumId = (int) ($_GET['albumId'] ?? $_POST['albumId'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $val  = new DGZ_Validate();
            $name = trim($val->fix_string($_POST['album_name'] ?? ''));
            $desc = trim($val->fix_string($_POST['album_description'] ?? ''));
            $status = in_array($_POST['album_status'] ?? '', ['active', 'inactive'])
                      ? $_POST['album_status'] : 'active';

            if ($name === '') {
                $this->addErrors('<p>Album name is required.</p>', 'Error');
                $this->redirect('admin/gallery', $isEdit ? "create?edit=1&albumId={$albumId}" : 'create');
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
                    'album_status'      => $status,
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
                    'album_status'      => $status,
                ]);
                if ($newId) {
                    $this->addSuccess('Album created successfully.', 'Done!');
                } else {
                    $this->addErrors('Could not create the album.', 'Error');
                }
            }

            $this->redirect('admin/gallery', '');
            return;
        }

        $albumData = null;
        if ($isEdit && $albumId > 0) {
            $albumData = container(\Dorguzen\Modules\Gallery\Models\GalleryAlbum::class)->getAlbumById($albumId);
        }

        $view = DGZ_View::getModuleView('gallery', 'adminCreateAlbum', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($this->adminService->createAlbumPayload($albumData ? [$albumData] : null));
    }

    public function deleteAlbum(): void
    {
        $albumId = (int) ($_GET['albumId'] ?? 0);

        if ($albumId <= 0) {
            $this->redirect('admin/gallery', '');
            return;
        }

        // Delete physical files via service
        $this->adminService->deleteAlbumFiles($albumId);

        if ($this->adminService->deleteAlbum($albumId)) {
            $this->addSuccess('Album and all its images have been deleted.', 'Done!');
        } else {
            $this->addErrors('Could not delete the album.', 'Error');
        }

        $this->redirect('admin/gallery', '');
    }

    public function manageImages(): void
    {
        $albumId = (int) ($_GET['albumId'] ?? 0);

        if ($albumId <= 0) {
            $this->redirect('admin/gallery', '');
            return;
        }

        $view = DGZ_View::getModuleView('gallery', 'adminManageImages', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($this->adminService->manageImagesPayload($albumId));
    }

    public function uploadImages(): void
    {
        $albumId = (int) ($_POST['albumId'] ?? 0);

        if ($albumId <= 0 || !isset($_FILES['gallery_images'])) {
            $this->redirect('admin/gallery', '');
            return;
        }

        $fileField  = $_FILES['gallery_images'];
        $isMultiple = is_array($fileField['name']);
        $total      = $isMultiple ? count($fileField['name']) : 1;

        for ($i = 0; $i < $total; $i++) {
            // Replace $_FILES so DGZ_Uploader picks up only this one file
            $_FILES = [
                'gallery_image' => $isMultiple ? [
                    'name'     => $fileField['name'][$i],
                    'type'     => $fileField['type'][$i],
                    'tmp_name' => $fileField['tmp_name'][$i],
                    'error'    => $fileField['error'][$i],
                    'size'     => $fileField['size'][$i],
                ] : $fileField,
            ];

            if ($_FILES['gallery_image']['error'] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            // File upload delegated to service
            $filename = $this->adminService->uploadImage($albumId);

            if ($filename !== '') {
                $caption = trim($_POST['captions'][$i] ?? '');
                $this->adminService->saveImage($albumId, $filename, $caption);

                // Auto-set as cover if the album has none yet
                $album = container(\Dorguzen\Modules\Gallery\Models\GalleryAlbum::class)->getAlbumById($albumId);
                if ($album && empty($album['album_cover'])) {
                    $this->adminService->setCover($albumId, $filename);
                }

                $this->addSuccess("Image '{$filename}' uploaded successfully.", 'Uploaded');
            } else {
                $this->addErrors('An image could not be uploaded.', 'Upload Error');
            }
        }

        unset($_FILES['gallery_image']);

        $this->redirect('admin/gallery', "images?albumId={$albumId}");
    }

    public function deleteImage(): void
    {
        $imageId = (int) ($_GET['imageId'] ?? 0);
        $albumId = (int) ($_GET['albumId'] ?? 0);

        if ($imageId <= 0) {
            $this->redirect('admin/gallery', $albumId > 0 ? "images?albumId={$albumId}" : '');
            return;
        }

        // Delete physical files via service, get image row for redirect
        $image = $this->adminService->deleteImageFiles($imageId);

        if ($image) {
            $this->adminService->deleteImage($imageId);
            $aid = (int) $image['album_id'];
            $this->addSuccess('Image deleted.', 'Done!');
            $this->redirect('admin/gallery', "images?albumId={$aid}");
        } else {
            $this->addErrors('Image not found.', 'Error');
            $this->redirect('admin/gallery', $albumId > 0 ? "images?albumId={$albumId}" : '');
        }
    }

    public function setCover(): void
    {
        $albumId  = (int) ($_POST['albumId'] ?? 0);
        $filename = trim($_POST['filename'] ?? '');

        if ($albumId > 0 && $filename !== '') {
            $this->adminService->setCover($albumId, $filename);
            $this->addSuccess('Cover image updated.', 'Done!');
        } else {
            $this->addErrors('Could not set cover image.', 'Error');
        }

        $this->redirect('admin/gallery', "images?albumId={$albumId}");
    }
}
