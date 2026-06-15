<?php

namespace Dorguzen\Modules\Videos\Services;

use Dorguzen\Modules\Videos\Models\VideoAlbum;
use Dorguzen\Modules\Videos\Models\Video;

class VideoService
{
    public function __construct(
        private VideoAlbum $albumModel,
        private Video      $videoModel,
    ) {}

    public function videosIndexPayload(): array
    {
        $albums = $this->albumModel->getAllAlbums();

        foreach ($albums as &$album) {
            $album['video_count'] = $this->videoModel->countByAlbum((int) $album['album_id']);
            $first = $this->videoModel->getFirstByAlbum((int) $album['album_id']);
            $album['cover_video'] = $first;
        }
        unset($album);

        return ['albums' => $albums];
    }

    public function videosAlbumPayload(int $albumId): array
    {
        $album  = $this->albumModel->getAlbumById($albumId);
        $videos = $album ? $this->videoModel->getVideosByAlbum($albumId) : [];

        return compact('album', 'videos');
    }

    public static function embedUrl(array $video): string
    {
        if ($video['video_source'] === 'youtube') {
            return 'https://www.youtube.com/embed/' . htmlspecialchars($video['video_ref'])
                 . '?rel=0&modestbranding=1';
        }
        return 'https://player.vimeo.com/video/' . htmlspecialchars($video['video_ref']);
    }

    public static function thumbnailUrl(array $video): string
    {
        if ($video['video_source'] === 'youtube') {
            return 'https://img.youtube.com/vi/' . htmlspecialchars($video['video_ref']) . '/hqdefault.jpg';
        }
        // Vimeo thumbnails require an API call; return empty so the view can show a placeholder
        return '';
    }
}
