<?php

namespace Dorguzen\Modules\Blog\Services;

use Dorguzen\Modules\Blog\Models\BlogCategory;
use Dorguzen\Modules\Blog\Models\BlogComment;
use Dorguzen\Modules\Blog\Models\BlogPost;
use Dorguzen\Core\DGZ_Uploader\DGZ_Uploader;
use Dorguzen\Core\DGZ_Uploader\DGZ_Upload;

class BlogAdminService
{
    public function __construct(
        private BlogCategory $categoryModel,
        private BlogPost     $postModel,
        private BlogComment  $commentModel,
    ) {}

    // -----------------------------------------------------------------------
    // Payloads
    // -----------------------------------------------------------------------

    public function managePostsPayload(): array
    {
        return [
            'posts'      => $this->postModel->getAllPosts(),
            'categories' => $this->categoryModel->getAllCategories(),
        ];
    }

    public function createPostPayload(?array $postData = null): array
    {
        return [
            'postData'   => $postData,
            'categories' => $this->categoryModel->getAllCategories(),
        ];
    }

    public function editPostPayload(int $postId): ?array
    {
        $post = $this->postModel->getPostById($postId);
        if (!$post) {
            return null;
        }
        return [
            'postData'   => $post,
            'categories' => $this->categoryModel->getAllCategories(),
        ];
    }

    public function manageCommentsPayload(): array
    {
        return [
            'comments'     => $this->commentModel->getAllComments(),
            'pendingCount' => $this->commentModel->getPendingCount(),
        ];
    }

    // -----------------------------------------------------------------------
    // File handling
    // -----------------------------------------------------------------------

    /**
     * Handle cover image upload for a blog post.
     *
     * Returns the filename (not the full path) on success, or null if no file was uploaded.
     * Images are stored flat in assets/images/blog/.
     */
    public function handleCoverImageUpload(): ?string
    {
        if (!isset($_FILES['cover_image']) || $_FILES['cover_image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = base_path('assets/images/blog/');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uploader = new DGZ_Uploader($uploadDir);
        $uploader->move('resize');
        $filenames = $uploader->getFilenames();

        return $filenames[0] ?? null;
    }

    /**
     * Delete the cover image files (original + thumbnail) for a post by filename.
     */
    public function deleteCoverImageFiles(string $filename): void
    {
        if ($filename === '') {
            return;
        }

        $dir  = base_path('assets/images/blog/');
        $orig = $dir . $filename;

        if (file_exists($orig)) @unlink($orig);

        // Use glob so extension differences between original and thumbnail never cause a miss
        // (DGZ_Thumbnail always writes .jpg for jpeg regardless of original extension)
        foreach (glob($dir . pathinfo($filename, PATHINFO_FILENAME) . '_thb.*') ?: [] as $thumbFile) {
            @unlink($thumbFile);
        }
    }

    // -----------------------------------------------------------------------
    // Categories
    // -----------------------------------------------------------------------

    public function saveCategory(string $name): int|false
    {
        $this->categoryModel->name = trim($name);
        $this->categoryModel->slug = $this->generateCategorySlug(trim($name));
        return $this->categoryModel->save();
    }

    public function deleteCategory(int $id): bool
    {
        return (bool) $this->categoryModel->deleteWhere(['category_id' => $id]);
    }

    // -----------------------------------------------------------------------
    // Posts
    // -----------------------------------------------------------------------

    public function savePost(array $data): int|false
    {
        $status = $data['status'] ?? 'draft';

        $this->postModel->title       = trim($data['title']);
        $this->postModel->slug        = $this->generatePostSlug(trim($data['title']));
        $this->postModel->excerpt     = trim($data['excerpt'] ?? '');
        $this->postModel->body        = $data['body'];
        $this->postModel->category_id = !empty($data['category_id']) ? (int) $data['category_id'] : null;
        $this->postModel->cover_image = $data['cover_image'] ?? null;
        $this->postModel->author      = trim($data['author'] ?: 'Admin');
        $this->postModel->status      = $status;
        $this->postModel->published_at = $status === 'published' ? date('Y-m-d H:i:s') : null;

        return $this->postModel->save();
    }

    public function updatePost(int $postId, array $data): bool
    {
        $existing = $this->postModel->getPostById($postId);
        if (!$existing) {
            return false;
        }

        $status = $data['status'] ?? 'draft';

        $fields = [
            'title'       => trim($data['title']),
            'slug'        => $this->generateUniquePostSlug(trim($data['title']), $postId),
            'excerpt'     => trim($data['excerpt'] ?? ''),
            'body'        => $data['body'],
            'category_id' => !empty($data['category_id']) ? (int) $data['category_id'] : null,
            'author'      => trim($data['author'] ?: 'Admin'),
            'status'      => $status,
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        if (!empty($data['cover_image'])) {
            $fields['cover_image'] = $data['cover_image'];
        }

        // Set published_at only on first publish
        if ($status === 'published' && $existing['status'] !== 'published') {
            $fields['published_at'] = date('Y-m-d H:i:s');
        }

        return (bool) $this->postModel->updateObject($fields, ['post_id' => $postId]);
    }

    /**
     * Delete a post record (and its comments). Also removes cover image files from disk.
     * Returns the post row on success, null if not found.
     */
    public function deletePost(int $postId): ?array
    {
        $post = $this->postModel->getPostById($postId);
        if (!$post) {
            return null;
        }

        $this->commentModel->deleteByPost($postId);
        $this->postModel->deleteWhere(['post_id' => $postId]);

        // Clean up cover image files
        if (!empty($post['cover_image'])) {
            $this->deleteCoverImageFiles($post['cover_image']);
        }

        return $post;
    }

    // -----------------------------------------------------------------------
    // Comments
    // -----------------------------------------------------------------------

    public function approveComment(int $commentId): bool
    {
        return (bool) $this->commentModel->updateObject(
            ['status' => 'approved'],
            ['comment_id' => $commentId]
        );
    }

    public function deleteComment(int $commentId): bool
    {
        return (bool) $this->commentModel->deleteWhere(['comment_id' => $commentId]);
    }

    // -----------------------------------------------------------------------
    // Slug helpers
    // -----------------------------------------------------------------------

    private function generatePostSlug(string $title): string
    {
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title), '-'));
        $base = $slug;
        $i    = 1;
        while ($this->postModel->slugExists($slug)) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    private function generateUniquePostSlug(string $title, int $excludeId): string
    {
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title), '-'));
        $base = $slug;
        $i    = 1;
        while ($this->postModel->slugExists($slug, $excludeId)) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    private function generateCategorySlug(string $name): string
    {
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
        $base = $slug;
        $i    = 1;
        while ($this->categoryModel->slugExists($slug)) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
