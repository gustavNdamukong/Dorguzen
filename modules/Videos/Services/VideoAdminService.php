<?php

namespace Dorguzen\Modules\Videos\Services;

use Dorguzen\Modules\Videos\Models\VideoAlbum;
use Dorguzen\Modules\Videos\Models\Video;

class VideoAdminService
{
    public function __construct(
        private VideoAlbum $albumModel,
        private Video      $videoModel,
    ) {}

    // -------------------------------------------------------------------------
    // Payloads
    // -------------------------------------------------------------------------

    public function manageAlbumsPayload(): array
    {
        $albums = $this->albumModel->getAllAlbums();
        foreach ($albums as &$album) {
            $album['video_count'] = $this->videoModel->countByAlbum((int) $album['album_id']);
        }
        unset($album);
        return ['albums' => $albums];
    }

    public function createAlbumPayload(?array $albumData = null): array
    {
        return ['albumData' => $albumData];
    }

    public function manageVideosPayload(int $albumId): array
    {
        $album  = $this->albumModel->getAlbumById($albumId);
        $videos = $album ? $this->videoModel->getVideosByAlbum($albumId) : [];
        return compact('album', 'videos');
    }

    // -------------------------------------------------------------------------
    // Write operations — albums
    // -------------------------------------------------------------------------

    public function saveAlbum(array $data): int|false
    {
        return $this->albumModel->insert($data);
    }

    public function updateAlbum(int $albumId, array $data): bool
    {
        return (bool) $this->albumModel->updateObject($data, ['album_id' => $albumId]);
    }

    public function deleteAlbum(int $albumId): bool
    {
        $this->videoModel->deleteByAlbum($albumId);
        return (bool) $this->albumModel->deleteWhere(['album_id' => $albumId]);
    }

    // -------------------------------------------------------------------------
    // Write operations — videos
    // -------------------------------------------------------------------------

    public function saveVideo(array $data): int|false
    {
        return $this->videoModel->insert($data);
    }

    public function deleteVideo(int $videoId): ?array
    {
        $video = $this->videoModel->getVideoById($videoId);
        if (!$video) {
            return null;
        }
        $this->videoModel->deleteWhere(['video_id' => $videoId]);
        return $video;
    }

    // -------------------------------------------------------------------------
    // Slug helpers
    // -------------------------------------------------------------------------

    public function generateSlug(string $name): string
    {
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
        return $this->makeUniqueSlug($slug, 0);
    }

    public function generateUniqueSlug(string $name, int $excludeId): string
    {
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
        return $this->makeUniqueSlug($slug, $excludeId);
    }

    private function makeUniqueSlug(string $base, int $excludeId): string
    {
        $slug = $base;
        $i    = 1;
        while ($this->albumModel->slugExists($slug, $excludeId)) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
