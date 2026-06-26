<?php

namespace Dorguzen\Modules\Gallery\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;

class GalleryAlbum extends DGZ_Model
{
    protected $_columns  = [];
    protected $data      = [];
    protected $_hasParent = [];
    protected $_hasChild  = [];

    protected $table      = 'gallery_albums';
    protected $primaryKey = 'album_id';

    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    public function getAllAlbums(): array
    {
        return $this->query(
            "SELECT * FROM gallery_albums ORDER BY created_at DESC"
        ) ?: [];
    }

    public function getActiveAlbums(): array
    {
        return $this->query(
            "SELECT * FROM gallery_albums WHERE album_status = 'active' ORDER BY created_at DESC"
        ) ?: [];
    }

    public function getAlbumById(int $albumId): ?array
    {
        $result = $this->query(
            "SELECT * FROM gallery_albums WHERE album_id = ?", [$albumId]
        );
        return ($result && isset($result[0])) ? $result[0] : null;
    }

    public function getAlbumBySlug(string $slug): ?array
    {
        $result = $this->query(
            "SELECT * FROM gallery_albums WHERE album_slug = ?", [$slug]
        );
        return ($result && isset($result[0])) ? $result[0] : null;
    }

    /**
     * The active album flagged as featured (powers the homepage slider).
     * Only one album should be featured at a time; the most recently updated wins.
     */
    public function getFeaturedAlbum(): ?array
    {
        $result = $this->query(
            "SELECT * FROM gallery_albums
             WHERE album_is_featured = 1 AND album_status = 'active'
             ORDER BY updated_at DESC LIMIT 1"
        );
        return ($result && isset($result[0])) ? $result[0] : null;
    }

    public function slugExists(string $slug, int $excludeId = 0): bool
    {
        $query  = "SELECT album_id FROM gallery_albums WHERE album_slug = ?";
        $params = [$slug];
        if ($excludeId > 0) {
            $query  .= ' AND album_id != ?';
            $params[] = $excludeId;
        }
        $result = $this->query($query, $params);
        return !empty($result);
    }
}
