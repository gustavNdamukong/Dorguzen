<?php

namespace Dorguzen\Controllers;

use Dorguzen\Core\DGZ_View;
use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Core\DGZ_Validate;
use Dorguzen\Services\NewsService;

class NewsController extends DGZ_Controller
{
    public function __construct(private NewsService $newsService)
    {
        parent::__construct();
    }

    public function getDefaultAction(): string
    {
        return 'news';
    }

    // -------------------------------------------------------------------------
    // Public routes
    // -------------------------------------------------------------------------

    /**
     * Public news listing page.
     */
    public function news(): void
    {
        $view = DGZ_View::getView('news', $this, 'html');
        $this->setLayoutDirectory($this->config->getConfig()['layoutDirectory']);
        $this->setLayoutView($this->config->getConfig()['defaultLayout']);
        $view->show($this->newsService->newsListingPayload());
    }

    /**
     * Single article page.
     */
    public function article(int $newsId = 0): void
    {
        $id = $newsId ?: (int) ($_GET['newsId'] ?? 0);

        $view = DGZ_View::getView('singleNewsItem', $this, 'html');
        $this->setLayoutDirectory($this->config->getConfig()['layoutDirectory']);
        $this->setLayoutView($this->config->getConfig()['defaultLayout']);
        $view->show($this->newsService->singleNewsItemPayload($id));
    }

    // -------------------------------------------------------------------------
    // Admin routes
    // -------------------------------------------------------------------------

    /**
     * Admin listing of all news items.
     */
    public function manageNews(): void
    {
        $view = DGZ_View::getAdminView('manageNews', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($this->newsService->manageNewsPayload());
    }

    /**
     * Create or edit a news item.
     *
     * GET  /admin/news/create           — blank create form
     * GET  /admin/news/create?edit=1&newsId=X — pre-filled edit form
     * POST /admin/news/create           — save new item
     * POST /admin/news/create?edit=1    — update existing item
     */
    public function createNews(string $edit = ''): void
    {
        $isEdit = ($edit === '1') || (($_GET['edit'] ?? '') === '1');
        $newsId = (int) ($_GET['newsId'] ?? $_POST['newsId'] ?? 0);

        // ---- POST: save or update ----
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $val   = new DGZ_Validate();
            $title = $val->fix_string($_POST['news_title']       ?? '');
            $desc  = $val->fix_string($_POST['news_description'] ?? '');
            $status = in_array($_POST['news_status'] ?? '', ['published', 'draft'])
                      ? $_POST['news_status']
                      : 'draft';

            if ($title === '' || $desc === '') {
                $this->addErrors('<p>Title and description are required.</p>', 'Error');
                $this->redirect('admin/news', $isEdit ? "create?edit=1&newsId={$newsId}" : 'create');
                return;
            }

            // File handling delegated to service
            $imagePath = $this->newsService->handleImageUpload($newsId, $isEdit);

            $data = [
                'news_title'       => $title,
                'news_description' => $desc,
                'news_status'      => $status,
                'news_video_url'   => $this->extractVideoEmbedUrl(trim($_POST['news_video_url'] ?? '')),
                'news_audio_url'   => trim($_POST['news_audio_url'] ?? ''),
            ];

            if ($imagePath !== '') {
                $data['news_image'] = $imagePath;
            }

            if ($isEdit && $newsId > 0) {
                $ok = $this->newsService->updateNews($newsId, $data);
                if ($ok) {
                    $this->addSuccess('The news item was successfully updated.', 'Great!');
                } else {
                    $this->addErrors('Could not update the news item.', 'Error');
                }
            } else {
                $newId = $this->newsService->saveNews($data);
                if ($newId) {
                    $this->addSuccess('The news item was successfully created.', 'Great!');
                } else {
                    $this->addErrors('Could not save the news item.', 'Error');
                }
            }

            $this->redirect('admin/news', '');
            return;
        }

        // ---- GET: show form ----
        $newsItemData = null;
        if ($isEdit && $newsId > 0) {
            $payload      = $this->newsService->singleNewsItemPayload($newsId);
            $newsItemData = $payload['newsItem'] ? [$payload['newsItem']] : null;
        }

        $view = DGZ_View::getAdminView('createNews', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($this->newsService->createNewsPayload($newsItemData));
    }

    /**
     * Delete a news item.
     * File cleanup is handled inside NewsService::deleteNews().
     */
    public function deleteNews(int $news_id = 0): void
    {
        $id = $news_id ?: (int) ($_GET['news_id'] ?? 0);

        if ($id > 0 && $this->newsService->deleteNews($id)) {
            $this->addSuccess('The news item was successfully deleted.', 'Done!');
            $this->redirect('admin/news', '');
            return;
        }

        $this->addErrors('Could not delete the news item.', 'Error');
        $this->redirect('news', 'manageNews');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function extractVideoEmbedUrl(string $input): string
    {
        if ($input === '') return '';

        // Full YouTube URL (watch, short, embed)
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/))([A-Za-z0-9_\-]{11})/', $input, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&modestbranding=1';
        }

        // Full Vimeo URL
        if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $input, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        // Bare YouTube ID — strip any trailing playlist/query params (e.g. "ID&list=...&start_radio=1")
        $bare = preg_replace('/[?&].*$/', '', $input);
        if (preg_match('/^[A-Za-z0-9_\-]{11}$/', $bare)) {
            return 'https://www.youtube.com/embed/' . $bare . '?rel=0&modestbranding=1';
        }

        return '';
    }
}
