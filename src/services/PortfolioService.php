<?php

namespace Dorguzen\Services;

use Dorguzen\Models\Portfolio;
use Dorguzen\Core\DGZ_Uploader\DGZ_Uploader;
use Dorguzen\Core\DGZ_Uploader\DGZ_Upload;

/**
 * PortfolioService
 *
 * Owns all business logic for the Portfolio feature.
 */
class PortfolioService
{
    public function __construct(private Portfolio $portfolio) {}

    // -------------------------------------------------------------------------
    // Payload builders
    // -------------------------------------------------------------------------

    /**
     * Payload for the public portfolio page.
     */
    public function portfolioPayload(): array
    {
        return [
            'portfolioItems' => $this->portfolio->getAllPortfolio(),
        ];
    }

    /**
     * Payload for the admin manage page.
     */
    public function managePortfolioPayload(): array
    {
        return [
            'portfolioItems' => $this->portfolio->getAllPortfolio(),
        ];
    }

    /**
     * Payload for the create/edit form.
     *
     * @param array|null $item  Existing row (edit mode) or null (create mode).
     */
    public function createPortfolioPayload(?array $item = null): array
    {
        return [
            'portfolioItem' => $item,
        ];
    }

    // -------------------------------------------------------------------------
    // File handling
    // -------------------------------------------------------------------------

    /**
     * Handle image upload for a portfolio item.
     *
     * On edit, deletes the existing image and thumbnail before uploading the new one.
     * Returns the stored relative path ('assets/images/portfolio/filename.jpg') or '' if no file
     * was uploaded.
     */
    public function handleImageUpload(int $portfolioId, bool $isEdit): string
    {
        if (!isset($_FILES['portfolio_image']) || $_FILES['portfolio_image']['error'] !== UPLOAD_ERR_OK) {
            return '';
        }

        $uploadDir = base_path('assets/images/portfolio/');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Delete old file when replacing on edit
        if ($isEdit && $portfolioId > 0) {
            $existing = $this->portfolio->getSinglePortfolioItem($portfolioId);
            if (!empty($existing['portfolio_image'])) {
                $oldFile  = base_path($existing['portfolio_image']);
                $oldThumb = dirname($oldFile) . '/' . DGZ_Upload::thumbName(basename($oldFile));
                if (file_exists($oldFile))  @unlink($oldFile);
                if (file_exists($oldThumb)) @unlink($oldThumb);
            }
        }

        $uploader = new DGZ_Uploader($uploadDir);
        $uploader->move('resize');
        $filenames = $uploader->getFilenames();

        if (!empty($filenames[0])) {
            return 'assets/images/portfolio/' . $filenames[0];
        }

        return '';
    }

    // -------------------------------------------------------------------------
    // Write operations
    // -------------------------------------------------------------------------

    /**
     * Save a new portfolio item. Returns new portfolio_id or false on failure.
     */
    public function savePortfolioItem(array $data): int|false
    {
        $record = container(Portfolio::class);

        $record->portfolio_title        = $data['portfolio_title']        ?? '';
        $record->portfolio_company_name = $data['portfolio_company_name'] ?? null;
        $record->portfolio_website      = $data['portfolio_website']      ?? null;
        $record->portfolio_description  = $data['portfolio_description']  ?? null;

        if (!empty($data['portfolio_image'])) {
            $record->portfolio_image = $data['portfolio_image'];
        }

        $insertId = $record->save();
        return $insertId ? (int) $insertId : false;
    }

    /**
     * Update an existing portfolio item. Returns true on success.
     */
    public function updatePortfolioItem(int $id, array $data): bool
    {
        $fields = [
            'portfolio_title'        => $data['portfolio_title']        ?? '',
            'portfolio_company_name' => $data['portfolio_company_name'] ?? null,
            'portfolio_website'      => $data['portfolio_website']      ?? null,
            'portfolio_description'  => $data['portfolio_description']  ?? null,
        ];

        if (!empty($data['portfolio_image'])) {
            $fields['portfolio_image'] = $data['portfolio_image'];
        }

        return (bool) $this->portfolio->updateObject($fields, ['portfolio_id' => $id]);
    }

    /**
     * Delete a portfolio item by ID.
     * Fetches and removes associated image files before deleting the DB record.
     */
    public function deletePortfolioItem(int $id): bool
    {
        $existing = $this->portfolio->getSinglePortfolioItem($id);

        if ($existing && !empty($existing['portfolio_image'])) {
            $oldFile  = base_path($existing['portfolio_image']);
            $oldThumb = dirname($oldFile) . '/' . DGZ_Upload::thumbName(basename($oldFile));
            if (file_exists($oldFile))  @unlink($oldFile);
            if (file_exists($oldThumb)) @unlink($oldThumb);
        }

        return (bool) $this->portfolio->deleteWhere(['portfolio_id' => $id]);
    }
}
