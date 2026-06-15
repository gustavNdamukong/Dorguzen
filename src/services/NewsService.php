<?php

namespace Dorguzen\Services;

use Dorguzen\Models\News;
use Dorguzen\Core\DGZ_Uploader\DGZ_Uploader;
use Dorguzen\Core\DGZ_Uploader\DGZ_Upload;

/**
 * NewsService
 *
 * Owns all database operations and payload building for the News feature.
 *
 * Controllers served:
 *   - NewsController
 */
class NewsService
{
    public function __construct(private News $news)
    {
    }

    // -------------------------------------------------------------------------
    // Payload builders
    // -------------------------------------------------------------------------

    /**
     * Payload for the public news listing page.
     *
     * Used by: NewsController::news()
     */
    public function newsListingPayload(): array
    {
        $newsItems  = $this->news->getPublishedNews();
        $latestNews = $this->news->getLatestNews(4);

        return [
            'newsItems'  => $newsItems,
            'latestNews' => $latestNews,
            'totalCount' => count($newsItems),
        ];
    }

    /**
     * Payload for a single article view.
     *
     * Used by: NewsController::article()
     */
    public function singleNewsItemPayload(int $newsId): array
    {
        $result  = $this->news->getSingleNewsItem($newsId);
        $newsItem = $result[0] ?? null;

        return [
            'newsItem'   => $newsItem,
            'latestNews' => $this->news->getLatestNews(4),
        ];
    }

    /**
     * Payload for the admin manage-news listing.
     *
     * Used by: NewsController::manageNews()
     */
    public function manageNewsPayload(): array
    {
        return [
            'newsItems' => $this->news->getAllNews(),
        ];
    }

    /**
     * Payload for the create/edit news form.
     *
     * Used by: NewsController::createNews()
     *
     * @param array|null $newsItemData  Existing row (edit mode) or null (create mode)
     */
    public function createNewsPayload(?array $newsItemData = null): array
    {
        return [
            'newsItemData' => $newsItemData,
        ];
    }

    // -------------------------------------------------------------------------
    // File handling
    // -------------------------------------------------------------------------

    /**
     * Handle image upload for a news item.
     *
     * On edit, deletes the existing image and thumbnail before uploading the new one.
     * Returns the stored relative path ('assets/images/news/filename.jpg') or '' if no file
     * was uploaded.
     */
    public function handleImageUpload(int $newsId, bool $isEdit): string
    {
        if (!isset($_FILES['news_image']) || $_FILES['news_image']['error'] !== UPLOAD_ERR_OK) {
            return '';
        }

        $uploadDir = base_path('assets/images/news/');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Delete old file when replacing on edit
        if ($isEdit && $newsId > 0) {
            $existing = $this->news->getSingleNewsItem($newsId);
            $existingItem = $existing[0] ?? null;
            if (!empty($existingItem['news_image'])) {
                $oldFile  = base_path($existingItem['news_image']);
                $oldThumb = dirname($oldFile) . '/' . DGZ_Upload::thumbName(basename($oldFile));
                if (file_exists($oldFile))  @unlink($oldFile);
                if (file_exists($oldThumb)) @unlink($oldThumb);
            }
        }

        $uploader = new DGZ_Uploader($uploadDir);
        $uploader->move('resize');
        $filenames = $uploader->getFilenames();

        if (!empty($filenames[0])) {
            return 'assets/images/news/' . $filenames[0];
        }

        return '';
    }

    // -------------------------------------------------------------------------
    // Write operations
    // -------------------------------------------------------------------------

    /**
     * Save a new news item. Returns the new news_id or false on failure.
     */
    public function saveNews(array $data): int|false
    {
        $record = container(News::class);

        $record->news_title       = $data['news_title']       ?? '';
        $record->news_description = $data['news_description'] ?? '';
        $record->news_status      = $data['news_status']      ?? 'draft';

        if (!empty($data['news_image'])) {
            $record->news_image = $data['news_image'];
        }

        if (!empty($data['news_video_url'])) {
            $record->news_video_url = $data['news_video_url'];
        }

        if (!empty($data['news_audio_url'])) {
            $record->news_audio_url = $data['news_audio_url'];
        }

        $insertId = $record->save();

        return $insertId ? (int) $insertId : false;
    }

    /**
     * Update an existing news item.
     */
    public function updateNews(int $newsId, array $data): bool
    {
        $fields = [
            'news_title'       => $data['news_title']       ?? '',
            'news_description' => $data['news_description'] ?? '',
            'news_status'      => $data['news_status']      ?? 'draft',
        ];

        if (isset($data['news_image']) && $data['news_image'] !== '') {
            $fields['news_image'] = $data['news_image'];
        }

        if (isset($data['news_video_url'])) {
            $fields['news_video_url'] = $data['news_video_url'];
        }

        if (isset($data['news_audio_url'])) {
            $fields['news_audio_url'] = $data['news_audio_url'];
        }

        return (bool) $this->news->updateObject($fields, ['news_id' => $newsId]);
    }

    /**
     * Delete a news item.
     * Fetches and removes associated image files before deleting the DB record.
     */
    public function deleteNews(int $newsId): bool
    {
        $existing = $this->news->getSingleNewsItem($newsId);
        $existingItem = $existing[0] ?? null;

        if ($existingItem && !empty($existingItem['news_image'])) {
            $oldFile  = base_path($existingItem['news_image']);
            $oldThumb = dirname($oldFile) . '/' . DGZ_Upload::thumbName(basename($oldFile));
            if (file_exists($oldFile))  @unlink($oldFile);
            if (file_exists($oldThumb)) @unlink($oldThumb);
        }

        return (bool) $this->news->deleteWhere(['news_id' => $newsId]);
    }
}
