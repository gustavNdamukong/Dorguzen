<?php

namespace Dorguzen\Modules\Gallery\Services;

use Dorguzen\Modules\Gallery\Models\GalleryAlbum;
use Dorguzen\Modules\Gallery\Models\GalleryImage;
use Dorguzen\Core\DGZ_Uploader\DGZ_Upload;

class GalleryService
{
    public function __construct(
        private GalleryAlbum  $albumModel,
        private GalleryImage  $imageModel,
    ) {}

    // -------------------------------------------------------------------------
    // Payload builders
    // -------------------------------------------------------------------------

    public function galleryIndexPayload(): array
    {
        $albums = $this->albumModel->getActiveAlbums();

        foreach ($albums as &$album) {
            $album['image_count'] = $this->imageModel->countByAlbum((int) $album['album_id']);
        }
        unset($album);

        return ['albums' => $albums];
    }

    public function galleryAlbumPayload(int $albumId): array
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

    /**
     * Images for the homepage slider, taken from the featured album.
     * Returns null when no active featured album exists or it has no images,
     * so the caller can fall back to its default behaviour.
     */
    public function homeSliderPayload(): ?array
    {
        $album = $this->albumModel->getFeaturedAlbum();
        if (!$album) {
            return null;
        }

        $images = $this->imageModel->getImagesByAlbum((int) $album['album_id']);
        if (empty($images)) {
            return null;
        }

        return [
            'album'  => $album,
            'images' => $images,
        ];
    }

    /**
     * Album for the footer "Project Gallery" thumbnails: the featured album if one
     * is set, otherwise the most recent active album. Returns null only when there
     * are no active albums that contain images.
     */
    public function footerGalleryPayload(): ?array
    {
        $album = $this->albumModel->getFeaturedAlbum();
        if (!$album) {
            $active = $this->albumModel->getActiveAlbums();   // ordered newest first
            $album  = $active[0] ?? null;
        }
        if (!$album) {
            return null;
        }

        $images = $this->imageModel->getImagesByAlbum((int) $album['album_id']);
        if (empty($images)) {
            return null;
        }

        return [
            'album'  => $album,
            'images' => $images,
        ];
    }
}
