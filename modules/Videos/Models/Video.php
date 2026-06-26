<?php

namespace Dorguzen\Modules\Videos\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;

class Video extends DGZ_Model
{
    protected $_columns   = [];
    protected $data       = [];
    protected $_hasParent = [];
    protected $_hasChild  = [];

    protected $table      = 'videos';
    protected $primaryKey = 'video_id';

    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    public function getVideosByAlbum(int $albumId): array
    {
        return $this->query(
            "SELECT * FROM videos WHERE album_id = ? ORDER BY video_sort_order ASC, created_at DESC",
            [$albumId]
        ) ?: [];
    }

    public function getVideoById(int $videoId): ?array
    {
        $result = $this->query(
            "SELECT * FROM videos WHERE video_id = ?", [$videoId]
        );
        return ($result && isset($result[0])) ? $result[0] : null;
    }

    public function countByAlbum(int $albumId): int
    {
        $result = $this->query(
            "SELECT COUNT(*) AS total FROM videos WHERE album_id = ?", [$albumId]
        );
        return (int) ($result[0]['total'] ?? 0);
    }

    public function getFirstByAlbum(int $albumId): ?array
    {
        $result = $this->query(
            "SELECT * FROM videos WHERE album_id = ? ORDER BY video_sort_order ASC, created_at ASC LIMIT 1",
            [$albumId]
        );
        return ($result && isset($result[0])) ? $result[0] : null;
    }

    public function deleteByAlbum(int $albumId): void
    {
        $this->deleteWhere(['album_id' => $albumId]);
    }
}
