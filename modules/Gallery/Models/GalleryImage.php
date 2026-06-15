<?php

namespace Dorguzen\Modules\Gallery\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;

class GalleryImage extends DGZ_Model
{
    protected $_columns  = [];
    protected $data      = [];
    protected $_hasParent = [];
    protected $_hasChild  = [];

    protected $table      = 'gallery_images';
    protected $primaryKey = 'image_id';

    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    public function getImagesByAlbum(int $albumId): array
    {
        return $this->query(
            "SELECT * FROM gallery_images WHERE album_id = ? ORDER BY image_sort_order ASC, image_id ASC",
            [$albumId]
        ) ?: [];
    }

    public function getImageById(int $imageId): ?array
    {
        $result = $this->query(
            "SELECT * FROM gallery_images WHERE image_id = ?", [$imageId]
        );
        return ($result && isset($result[0])) ? $result[0] : null;
    }

    public function countByAlbum(int $albumId): int
    {
        $result = $this->query(
            "SELECT COUNT(*) as cnt FROM gallery_images WHERE album_id = ?", [$albumId]
        );
        return (int) ($result[0]['cnt'] ?? 0);
    }

    public function deleteByAlbum(int $albumId): bool
    {
        return (bool) $this->deleteWhere(['album_id' => $albumId]);
    }
}
