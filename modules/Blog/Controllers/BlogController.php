<?php

namespace Dorguzen\Modules\Blog\Controllers;

use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Core\DGZ_ModuleControllerInterface;
use Dorguzen\Core\DGZ_ModuleControllerTrait;
use Dorguzen\Core\DGZ_View;
use Dorguzen\Modules\Blog\Services\BlogAdminService;
use Dorguzen\Modules\Blog\Services\BlogService;

class BlogController extends DGZ_Controller implements DGZ_ModuleControllerInterface
{
    use DGZ_ModuleControllerTrait;

    public function __construct(
        private BlogService      $blogService,
        private BlogAdminService $adminService,
    ) {
        parent::__construct();
    }

    public function getDefaultAction(): string
    {
        return 'index';
    }

    // -----------------------------------------------------------------------
    // Frontend
    // -----------------------------------------------------------------------

    public function index(): void
    {
        if (config('app.modules.blog') !== 'on') {
            $this->redirect('', '');
            return;
        }

        $page       = max(1, (int) ($_GET['page'] ?? 1));
        $categoryId = !empty($_GET['category']) ? (int) $_GET['category'] : null;
        $search     = trim($_GET['search'] ?? '');

        $payload = $this->blogService->blogIndexPayload($page, $categoryId, $search ?: null);

        $view = DGZ_View::getModuleView('blog', 'blogIndex', $this, 'html');
        $view->show($payload);
    }

    public function post(): void
    {
        if (config('app.modules.blog') !== 'on') {
            $this->redirect('', '');
            return;
        }

        $slug = trim($_GET['slug'] ?? '');
        if ($slug === '') {
            $this->redirect('blog', '');
            return;
        }

        $payload = $this->blogService->blogPostPayload($slug);
        if (!$payload) {
            $this->redirect('blog', '');
            return;
        }

        $view = DGZ_View::getModuleView('blog', 'blogPost', $this, 'html');
        $view->show($payload);
    }

    public function comment(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('blog', '');
            return;
        }

        $postId = (int) ($_POST['post_id'] ?? 0);
        $slug   = trim($_POST['post_slug'] ?? '');

        $result = $this->blogService->saveComment($postId, $_POST);

        if ($result['ok']) {
            $this->addSuccess('Your comment has been submitted and is awaiting moderation. Thank you!', 'Thank you!');
        } else {
            $this->addErrors(implode(' ', $result['errors']), 'Error');
        }

        $this->redirect('blog', "post?slug={$slug}");
    }

    // -----------------------------------------------------------------------
    // Admin — Posts
    // -----------------------------------------------------------------------

    public function managePosts(): void
    {
        $this->requireAdmin();
        $view = DGZ_View::getModuleView('blog', 'adminManagePosts', $this, 'html');
        $view->show($this->adminService->managePostsPayload());
    }

    public function createPost(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // File upload delegated to service
            $coverImage = $this->adminService->handleCoverImageUpload();

            $data = array_merge($_POST, ['cover_image' => $coverImage]);
            $postId = $this->adminService->savePost($data);

            if ($postId) {
                $this->addSuccess('Post created successfully.', 'Done!');
                $this->redirect('admin/blog', '');
            } else {
                $this->addErrors('Failed to save post. Please try again.', 'Error');
                $this->redirect('admin/blog', 'create');
            }
            return;
        }

        $view = DGZ_View::getModuleView('blog', 'adminCreatePost', $this, 'html');
        $view->show($this->adminService->createPostPayload());
    }

    public function editPost(): void
    {
        $this->requireAdmin();

        $postId = (int) ($_GET['postId'] ?? $_POST['post_id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // File upload delegated to service
            $coverImage = $this->adminService->handleCoverImageUpload();

            $data = array_merge($_POST, ['cover_image' => $coverImage]);
            $ok   = $this->adminService->updatePost($postId, $data);

            if ($ok) {
                $this->addSuccess('Post updated successfully.', 'Done!');
                $this->redirect('admin/blog', '');
            } else {
                $this->addErrors('Failed to update post.', 'Error');
                $this->redirect('admin/blog', "edit?postId={$postId}");
            }
            return;
        }

        $payload = $this->adminService->editPostPayload($postId);
        if (!$payload) {
            $this->redirect('admin/blog', '');
            return;
        }

        $view = DGZ_View::getModuleView('blog', 'adminCreatePost', $this, 'html');
        $view->show($payload);
    }

    public function deletePost(): void
    {
        $this->requireAdmin();

        $postId = (int) ($_GET['postId'] ?? 0);
        if (!$postId) {
            $this->redirect('admin/blog', '');
            return;
        }

        // File cleanup + DB delete all handled inside service
        $post = $this->adminService->deletePost($postId);

        if ($post) {
            $this->addSuccess('Post deleted successfully.', 'Done!');
        } else {
            $this->addErrors('Post not found.', 'Error');
        }

        $this->redirect('admin/blog', '');
    }

    // -----------------------------------------------------------------------
    // Admin — Categories
    // -----------------------------------------------------------------------

    public function saveCategory(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/blog', '');
            return;
        }

        $name = trim($_POST['category_name'] ?? '');
        if ($name === '') {
            $this->addErrors('Category name is required.', 'Error');
            $this->redirect('admin/blog', '');
            return;
        }

        $this->adminService->saveCategory($name);
        $this->addSuccess('Category added.', 'Done!');
        $this->redirect('admin/blog', '');
    }

    public function deleteCategory(): void
    {
        $this->requireAdmin();

        $id = (int) ($_GET['categoryId'] ?? 0);
        if ($id) {
            $this->adminService->deleteCategory($id);
            $this->addSuccess('Category deleted.', 'Done!');
        }
        $this->redirect('admin/blog', '');
    }

    // -----------------------------------------------------------------------
    // Admin — Comments
    // -----------------------------------------------------------------------

    public function manageComments(): void
    {
        $this->requireAdmin();
        $view = DGZ_View::getModuleView('blog', 'adminManageComments', $this, 'html');
        $view->show($this->adminService->manageCommentsPayload());
    }

    public function approveComment(): void
    {
        $this->requireAdmin();

        $commentId = (int) ($_GET['commentId'] ?? 0);
        if ($commentId) {
            $this->adminService->approveComment($commentId);
            $this->addSuccess('Comment approved.', 'Done!');
        }
        $this->redirect('admin/blog', 'comments');
    }

    public function deleteComment(): void
    {
        $this->requireAdmin();

        $commentId = (int) ($_GET['commentId'] ?? 0);
        if ($commentId) {
            $this->adminService->deleteComment($commentId);
            $this->addSuccess('Comment deleted.', 'Done!');
        }
        $this->redirect('admin/blog', 'comments');
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    private function requireAdmin(): void
    {
        if (!Auth()->isAdmin()) {
            $this->redirect('auth', 'login');
        }
    }
}
