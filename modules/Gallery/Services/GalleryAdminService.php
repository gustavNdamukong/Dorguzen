<?php

namespace Dorguzen\Modules\Gallery\Services;

use Dorguzen\Modules\Gallery\Models\GalleryAlbum;
use Dorguzen\Modules\Gallery\Models\GalleryImage;
use Dorguzen\Core\DGZ_Uploader\DGZ_Upload;
use Dorguzen\Core\DGZ_Uploader\DGZ_Uploader;

class GalleryAdminService
{
    public function __construct(
        private GalleryAlbum  $albumModel,
        private GalleryImage  $imageModel,
    ) {}

    // -------------------------------------------------------------------------
    // Payload builders
    // -------------------------------------------------------------------------

    public function manageAlbumsPayload(): array
    {
        $albums = $this->albumModel->getAllAlbums();

        foreach ($albums as &$album) {
            $album['image_count'] = $this->imageModel->countByAlbum((int) $album['album_id']);
        }
        unset($album);

        return ['albums' => $albums];
    }

    public function createAlbumPayload(?array $albumData = null): array
    {
        return ['albumData' => $albumData];
    }

    public function manageImagesPayload(int $albumId): array
    {
        $album  = $this->albumModel->getAlbumById($albumId);
        $images = $album ? $this->imageModel->getImagesByAlbum($albumId) : [];

        foreach ($images as &$img) {
            $img['thumb_filename'] = DGZ_Upload::thumbName($img['image_filename']);
        }
        unset($img);

        return [
            'album'  => $album,
            'images' => $images,
        ];
    }

    // -------------------------------------------------------------------------
    // File handling
    // -------------------------------------------------------------------------

    /**
     * Upload a single gallery image from $_FILES['gallery_image'] into the album directory.
     *
     * Returns the filename on success, or '' on failure / no file.
     * The caller is responsible for swapping $_FILES to the correct single-file slice
     * before calling this method (when handling multi-file uploads).
     */
    public function uploadImage(int $albumId): string
    {
        if (!isset($_FILES['gallery_image']) || $_FILES['gallery_image']['error'] === UPLOAD_ERR_NO_FILE) {
            return '';
        }

        $uploadDir = base_path('assets/images/gallery/' . $albumId . '/');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uploader = new DGZ_Uploader($uploadDir);
        $uploader->move('resize');

        $filenames = $uploader->getFilenames();
        return $filenames[0] ?? '';
    }

    /**
     * Get uploader messages from the last uploadImage() call.
     * Note: because DGZ_Uploader is instantiated inside uploadImage(), this method
     * returns an empty array — callers must track messages via a local uploader instance
     * if they need per-file feedback. For the controller, success/error feedback is
     * inferred from whether a filename was returned.
     */

    /**
     * Delete all files for an album from disk.
     */
    public function deleteAlbumFiles(int $albumId): void
    {
        $images  = $this->imageModel->getImagesByAlbum($albumId);
        $imgDir  = base_path('assets/images/gallery/' . $albumId . '/');

        foreach ($images as $img) {
            $orig  = $imgDir . $img['image_filename'];
            $thumb = $imgDir . DGZ_Upload::thumbName($img['image_filename']);
            if (is_file($orig))  @unlink($orig);
            if (is_file($thumb)) @unlink($thumb);
        }

        if (is_dir($imgDir)) {
            @rmdir($imgDir);
        }
    }

    /**
     * Delete a single image's files from disk.
     * Returns the image row so the caller can use album_id for redirect, or null if not found.
     */
    public function deleteImageFiles(int $imageId): ?array
    {
        $image = $this->imageModel->getImageById($imageId);
        if (!$image) {
            return null;
        }

        $aid     = (int) $image['album_id'];
        $imgDir  = base_path('assets/images/gallery/' . $aid . '/');
        $orig    = $imgDir . $image['image_filename'];
        $thumb   = $imgDir . DGZ_Upload::thumbName($image['image_filename']);

        if (is_file($orig))  @unlink($orig);
        if (is_file($thumb)) @unlink($thumb);

        return $image;
    }

    // -------------------------------------------------------------------------
    // Write operations
    // -------------------------------------------------------------------------

    public function saveAlbum(array $data): int|false
    {
        $record = container(GalleryAlbum::class);

        $record->album_name        = $data['album_name'];
        $record->album_slug        = $data['album_slug'];
        $record->album_description = $data['album_description'] ?? '';
        $record->album_status      = $data['album_status'] ?? 'active';

        $insertId = $record->save();
        return $insertId ? (int) $insertId : false;
    }

    public function updateAlbum(int $albumId, array $data): bool
    {
        $fields = [
            'album_name'        => $data['album_name'],
            'album_slug'        => $data['album_slug'],
            'album_description' => $data['album_description'] ?? '',
            'album_status'      => $data['album_status'] ?? 'active',
        ];

        if (!empty($data['album_cover'])) {
            $fields['album_cover'] = $data['album_cover'];
        }

        return (bool) $this->albumModel->updateObject($fields, ['album_id' => $albumId]);
    }

    public function setCover(int $albumId, string $filename): bool
    {
        return (bool) $this->albumModel->updateObject(
            ['album_cover' => $filename],
            ['album_id' => $albumId]
        );
    }

    /**
     * Mark one album as the featured (home slider) album.
     * Clears the flag on any currently-featured album first, so only one wins.
     */
    public function setFeaturedAlbum(int $albumId): bool
    {
        // Clear the flag on whichever album currently holds it.
        $this->albumModel->updateObject(
            ['album_is_featured' => 0],
            ['album_is_featured' => 1]
        );

        return (bool) $this->albumModel->updateObject(
            ['album_is_featured' => 1],
            ['album_id' => $albumId]
        );
    }

    public function deleteAlbum(int $albumId): bool
    {
        $this->imageModel->deleteByAlbum($albumId);
        return (bool) $this->albumModel->deleteWhere(['album_id' => $albumId]);
    }

    public function saveImage(int $albumId, string $filename, string $caption = ''): int|false
    {
        $record = container(GalleryImage::class);

        $record->album_id        = $albumId;
        $record->image_filename  = $filename;
        $record->image_caption   = $caption;
        $record->image_sort_order = 0;

        $insertId = $record->save();
        return $insertId ? (int) $insertId : false;
    }

    public function deleteImage(int $imageId): ?array
    {
        $image = $this->imageModel->getImageById($imageId);
        if ($image) {
            $this->imageModel->deleteWhere(['image_id' => $imageId]);
        }
        return $image;
    }

    public function generateSlug(string $name): string
    {
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
        $base = $slug;
        $i    = 1;
        while ($this->albumModel->slugExists($slug)) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    public function generateUniqueSlug(string $name, int $excludeId = 0): string
    {
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
        $base = $slug;
        $i    = 1;
        while ($this->albumModel->slugExists($slug, $excludeId)) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
