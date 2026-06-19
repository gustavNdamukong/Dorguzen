<?php

namespace Dorguzen\Modules\Testimonials\Controllers;

use Dorguzen\Core\DGZ_View;
use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Core\DGZ_ModuleControllerInterface;
use Dorguzen\Core\DGZ_ModuleControllerTrait;
use Dorguzen\Modules\Testimonials\Services\TestimonialsService;

class TestimonialsController extends DGZ_Controller implements DGZ_ModuleControllerInterface
{
    use DGZ_ModuleControllerTrait;

    public function __construct(
        private TestimonialsService $service,
    ) {
        parent::__construct();
    }

    public function getDefaultAction(): string
    {
        return 'index';
    }

    // =========================================================================
    // Frontend routes
    // =========================================================================

    public function index(): void
    {
        $view = DGZ_View::getModuleView('testimonials', 'testimonialsIndex', $this, 'html');
        $this->setLayoutDirectory($this->config->getConfig()['layoutDirectory']);
        $this->setLayoutView($this->config->getConfig()['defaultLayout']);
        $this->setPageTitle('Testimonials');
        $view->show($this->service->publicPagePayload());
    }

    public function submit(): void
    {
        $result = $this->service->submit($_POST);

        if ($result['ok']) {
            $this->addSuccess(
                'Thank you for your review! It will appear on the site once approved.',
                'Review Submitted'
            );
        } else {
            $this->addErrors('<p>' . htmlspecialchars($result['error']) . '</p>', 'Submission Error');
        }

        $this->redirect('testimonials', '');
    }

    // =========================================================================
    // Admin routes
    // =========================================================================

    public function manage(): void
    {
        $view = DGZ_View::getModuleView('testimonials', 'adminManageTestimonials', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($this->service->adminPayload());
    }

    public function approve(): void
    {
        $id     = (int) ($_POST['testimonial_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');

        if ($id <= 0 || !in_array($status, ['approved', 'rejected', 'pending'], true)) {
            $this->addErrors('Invalid request.', 'Error');
            $this->redirect('admin/testimonials', '');
            return;
        }

        if ($this->service->updateStatus($id, $status)) {
            $this->addSuccess('Testimonial ' . ucfirst($status) . '.', 'Done!');
        } else {
            $this->addErrors('Could not update testimonial status.', 'Error');
        }

        $this->redirect('admin/testimonials', '');
    }

    public function delete(): void
    {
        $id = (int) ($_GET['testimonial_id'] ?? 0);

        if ($id <= 0) {
            $this->redirect('admin/testimonials', '');
            return;
        }

        if ($this->service->delete($id)) {
            $this->addSuccess('Testimonial deleted.', 'Done!');
        } else {
            $this->addErrors('Could not delete the testimonial.', 'Error');
        }

        $this->redirect('admin/testimonials', '');
    }
}
