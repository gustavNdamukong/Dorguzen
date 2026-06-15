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
}
