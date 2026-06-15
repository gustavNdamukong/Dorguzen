<?php

namespace Dorguzen\Modules\Videos\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;

class VideoAlbum extends DGZ_Model
{
    protected $_columns   = [];
    protected $data       = [];
    protected $_hasParent = [];
    protected $_hasChild  = [];

    protected $table      = 'video_albums';
    protected $primaryKey = 'album_id';

    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    public function getAllAlbums(): array
    {
        return $this->query(
            "SELECT * FROM video_albums ORDER BY created_at DESC"
        ) ?: [];
    }

    public function getAlbumById(int $albumId): ?array
    {
        $result = $this->query(
            "SELECT * FROM video_albums WHERE album_id = ?", [$albumId]
        );
        return ($result && isset($result[0])) ? $result[0] : null;
    }

    public function getAlbumBySlug(string $slug): ?array
    {
        $result = $this->query(
            "SELECT * FROM video_albums WHERE album_slug = ?", [$slug]
        );
        return ($result && isset($result[0])) ? $result[0] : null;
    }

    public function slugExists(string $slug, int $excludeId = 0): bool
    {
        $query  = "SELECT album_id FROM video_albums WHERE album_slug = ?";
        $params = [$slug];
        if ($excludeId > 0) {
            $query  .= ' AND album_id != ?';
            $params[] = $excludeId;
        }
        return !empty($this->query($query, $params));
    }
}
