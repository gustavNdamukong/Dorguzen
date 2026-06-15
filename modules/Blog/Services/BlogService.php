<?php

namespace Dorguzen\Modules\Blog\Services;

use Dorguzen\Modules\Blog\Models\BlogCategory;
use Dorguzen\Modules\Blog\Models\BlogComment;
use Dorguzen\Modules\Blog\Models\BlogPost;

class BlogService
{
    public function __construct(
        private BlogCategory $categoryModel,
        private BlogPost     $postModel,
        private BlogComment  $commentModel,
    ) {}

    public function blogIndexPayload(int $page = 1, ?int $categoryId = null, ?string $search = null): array
    {
        if ($search !== null && $search !== '') {
            $allPosts = $this->postModel->searchPublished($search);
        } elseif ($categoryId) {
            $allPosts = $this->postModel->getPublishedByCategory($categoryId);
        } else {
            $allPosts = $this->postModel->getPublishedPosts();
        }

        $categories  = $this->categoryModel->getAllCategories();
        $recentPosts = $this->postModel->getRecentPublished(5);

        $perPage    = 6;
        $total      = count($allPosts);
        $totalPages = (int) ceil($total / $perPage) ?: 1;
        $page       = max(1, min($page, $totalPages));
        $posts      = array_slice($allPosts, ($page - 1) * $perPage, $perPage);

        return [
            'posts'       => $posts,
            'categories'  => $categories,
            'recentPosts' => $recentPosts,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'total'       => $total,
            'categoryId'  => $categoryId,
            'search'      => $search,
        ];
    }

    public function blogPostPayload(string $slug): ?array
    {
        $post = $this->postModel->getPostBySlug($slug);
        if (!$post) {
            return null;
        }

        // Generate captcha numbers and store expected answer in session
        $n1 = random_int(1, 9);
        $n2 = random_int(1, 9);
        $_SESSION['_blog_captcha'] = $n1 + $n2;

        return [
            'post'        => $post,
            'comments'    => $this->commentModel->getApprovedByPost((int) $post['post_id']),
            'recentPosts' => $this->postModel->getRecentPublished(5),
            'categories'  => $this->categoryModel->getAllCategories(),
            'captchaN1'   => $n1,
            'captchaN2'   => $n2,
        ];
    }

    public function saveComment(int $postId, array $data): array
    {
        $errors = [];

        $name  = trim($data['author_name'] ?? '');
        $email = trim($data['author_email'] ?? '');
        $body  = trim($data['body'] ?? '');
        $guess = (int) ($data['captcha_answer'] ?? -1);

        if ($name === '')  { $errors[] = 'Your name is required.'; }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }
        if ($body === '')  { $errors[] = 'Comment text is required.'; }
        if ($guess !== (int) ($_SESSION['_blog_captcha'] ?? -99)) {
            $errors[] = 'Incorrect answer to the security question. Please try again.';
        }

        if (!empty($errors)) {
            return ['ok' => false, 'errors' => $errors];
        }

        $this->commentModel->post_id      = $postId;
        $this->commentModel->author_name  = htmlspecialchars($name);
        $this->commentModel->author_email = htmlspecialchars($email);
        $this->commentModel->body         = htmlspecialchars($body);
        $this->commentModel->status       = 'pending';
        $this->commentModel->save();

        unset($_SESSION['_blog_captcha']);

        return ['ok' => true];
    }
}
