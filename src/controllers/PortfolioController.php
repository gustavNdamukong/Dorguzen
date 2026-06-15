<?php

namespace Dorguzen\Controllers;

use Dorguzen\Core\DGZ_View;
use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Core\DGZ_Validate;
use Dorguzen\Services\PortfolioService;

class PortfolioController extends DGZ_Controller
{
    public function __construct(private PortfolioService $portfolioService)
    {
        parent::__construct();
    }

    public function getDefaultAction(): string
    {
        return 'portfolio';
    }

    // =========================================================================
    // PUBLIC ROUTES
    // =========================================================================

    /**
     * GET /portfolio
     */
    public function portfolio(): void
    {
        $payload = $this->portfolioService->portfolioPayload();

        $view = DGZ_View::getView('portfolio', $this, 'html');
        $this->setLayoutDirectory('seoMaster');
        $this->setLayoutView('seoMasterLayout');
        $this->setPageTitle('Portfolio');
        $view->show($payload);
    }

    // =========================================================================
    // ADMIN ROUTES
    // =========================================================================

    /**
     * GET /admin/portfolio
     */
    public function managePortfolio(): void
    {
        $this->requireAdmin();

        $payload = $this->portfolioService->managePortfolioPayload();

        $view = DGZ_View::getAdminView('managePortfolio', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($payload);
    }

    /**
     * GET  /admin/portfolio/create  — show form
     * POST /admin/portfolio/create  — save or update
     */
    public function createPortfolio(): void
    {
        $this->requireAdmin();

        $isEdit      = isset($_GET['edit']) || isset($_POST['edit']);
        $portfolioId = (int) ($_GET['portfolioId'] ?? $_POST['portfolioId'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $val   = new DGZ_Validate();
            $title = trim($val->fix_string($_POST['portfolio_title'] ?? ''));
            $desc  = trim($_POST['portfolio_description'] ?? '');

            if ($title === '') {
                $this->addErrors('<p>A title is required.</p>', 'Error');
                $redir = $isEdit && $portfolioId > 0
                    ? "create?edit=1&portfolioId={$portfolioId}"
                    : 'create';
                $this->redirect('admin/portfolio', $redir);
                return;
            }

            // File handling delegated to service
            $imagePath = $this->portfolioService->handleImageUpload($portfolioId, $isEdit);

            $data = [
                'portfolio_title'        => $title,
                'portfolio_company_name' => trim($val->fix_string($_POST['portfolio_company_name'] ?? '')),
                'portfolio_website'      => trim($_POST['portfolio_website'] ?? ''),
                'portfolio_description'  => $desc,
            ];

            if ($imagePath !== '') {
                $data['portfolio_image'] = $imagePath;
            }

            if ($isEdit && $portfolioId > 0) {
                $ok = $this->portfolioService->updatePortfolioItem($portfolioId, $data);
                $ok
                    ? $this->addSuccess('Portfolio item updated successfully.', 'Great!')
                    : $this->addErrors('Could not update the portfolio item.', 'Error');
            } else {
                $newId = $this->portfolioService->savePortfolioItem($data);
                $newId
                    ? $this->addSuccess('Portfolio item created successfully.', 'Great!')
                    : $this->addErrors('Could not save the portfolio item.', 'Error');
            }

            $this->redirect('admin/portfolio', '');
            return;
        }

        // GET — show form
        $item = null;
        if ($isEdit && $portfolioId > 0) {
            $item = container(\Dorguzen\Models\Portfolio::class)->getSinglePortfolioItem($portfolioId);
        }

        $payload = $this->portfolioService->createPortfolioPayload($item);

        $view = DGZ_View::getAdminView('createPortfolio', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($payload);
    }

    /**
     * GET /admin/portfolio/delete
     */
    public function deletePortfolio(): void
    {
        $this->requireAdmin();

        $id = (int) ($_GET['portfolio_id'] ?? 0);

        if ($id > 0) {
            // File cleanup is handled inside deletePortfolioItem()
            if ($this->portfolioService->deletePortfolioItem($id)) {
                $this->addSuccess('Portfolio item deleted successfully.', 'Done');
            } else {
                $this->addErrors('Could not delete the portfolio item.', 'Error');
            }
        } else {
            $this->addErrors('Could not delete the portfolio item.', 'Error');
        }

        $this->redirect('admin/portfolio', '');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function requireAdmin(): void
    {
        if (!isset($_SESSION['authenticated']) ||
            !in_array($_SESSION['user_type'] ?? '', ['admin', 'admin_gen', 'super_admin'])) {
            $this->redirect('auth', 'login');
            exit;
        }
    }
}
