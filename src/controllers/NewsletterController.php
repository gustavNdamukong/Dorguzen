<?php

namespace Dorguzen\Controllers;

use Dorguzen\Core\DGZ_View;
use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Core\DGZ_Validate;
use Dorguzen\Services\NewsletterService;

class NewsletterController extends DGZ_Controller
{
    public function __construct(private NewsletterService $newsletterService)
    {
        parent::__construct();
    }

    public function getDefaultAction(): string
    {
        return 'manageNewsletters';
    }

    // =========================================================================
    // PUBLIC ROUTES
    // =========================================================================

    /**
     * POST /subscribe
     *
     * Saves a new subscriber from the public subscribe modal.
     */
    public function unsubscribe(): void
    {
        $email = trim($_GET['email'] ?? '');

        if ($email !== '') {
            $this->newsletterService->unsubscribeByEmail($email);
        }

        $view = DGZ_View::getView('unsubscribe', $this, 'html');
        $view->show(['email' => $email]);
    }

    public function subscribe(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('home', 'home');
            return;
        }

        $val   = new DGZ_Validate();
        $email = trim($_POST['subscriber_email'] ?? '');
        $name  = $val->fix_string($_POST['subscriber_firstname'] ?? '');

        if ($email === '') {
            $this->addErrors('<p>Please enter a valid email address.</p>', 'Oops!');
            $this->redirectBack();
            return;
        }

        $emailError = $val->validate_email($email);
        if ($emailError !== '') {
            $this->addErrors('<p>Please enter a valid email address.</p>', 'Oops!');
            $this->redirectBack();
            return;
        }

        $saved = $this->newsletterService->saveSubscriber([
            'subscriber_email'     => $email,
            'subscriber_firstname' => $name,
        ]);

        if ($saved) {
            $this->addSuccess('You have successfully subscribed to our newsletter!', 'Thank you!');
        } else {
            $this->addErrors('<p>That email address is already subscribed. Thank you!</p>', 'Already subscribed');
        }

        $this->redirectBack();
    }

    // =========================================================================
    // ADMIN ROUTES — Subscribers
    // =========================================================================

    /**
     * GET /admin/subscribers
     */
    public function manageSubscribers(): void
    {
        $this->requireAdmin();

        $payload = $this->newsletterService->manageSubscribersPayload();

        $view = DGZ_View::getAdminView('manageSubscribers', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($payload);
    }

    /**
     * GET /admin/subscribers/delete
     */
    public function deleteSubscriber(): void
    {
        $this->requireAdmin();

        $id = (int) ($_GET['subscriber_id'] ?? 0);

        if ($id > 0 && $this->newsletterService->deleteSubscriber($id)) {
            $this->addSuccess('Subscriber deleted successfully.', 'Done');
        } else {
            $this->addErrors('Could not delete subscriber.', 'Error');
        }

        $this->redirect('admin/subscribers', '');
    }

    /**
     * POST /admin/subscribers/sendWelcome
     *
     * Dispatches welcome-email jobs for the given new-subscriber IDs.
     */
    public function sendWelcomeEmails(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/subscribers', '');
            return;
        }

        $subscriberIds = $_POST['subscriber_ids'] ?? [];
        $newsletterId  = (int) ($_POST['newsletter_id'] ?? 0);

        if (empty($subscriberIds) || $newsletterId < 1) {
            $this->addErrors('Please select a newsletter and at least one subscriber.', 'Error');
            $this->redirect('admin/subscribers', '');
            return;
        }

        $count = $this->newsletterService->queueWelcomeEmails($subscriberIds, $newsletterId);
        $this->addSuccess("{$count} welcome email(s) queued. They will be sent automatically on the next scheduler run.", 'Done');
        $this->redirect('admin/subscribers', '');
    }

    /**
     * POST /admin/subscribers/sendBulk
     *
     * Dispatches bulk newsletter email jobs.
     */
    public function sendBulkEmail(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/subscribers', '');
            return;
        }

        $subscriberIds = $_POST['subscriber_ids'] ?? [];
        $newsletterId  = (int) ($_POST['newsletter_id'] ?? 0);

        if (empty($subscriberIds) || $newsletterId < 1) {
            $this->addErrors('Please select a newsletter and at least one subscriber.', 'Error');
            $this->redirect('admin/subscribers', '');
            return;
        }

        $count = $this->newsletterService->queueBulkEmail($subscriberIds, $newsletterId);
        $this->addSuccess("{$count} newsletter email(s) queued. They will be sent automatically on the next scheduler run.", 'Done');
        $this->redirect('admin/subscribers', '');
    }

    // =========================================================================
    // ADMIN ROUTES — Newsletters
    // =========================================================================

    /**
     * GET /admin/newsletters
     */
    public function manageNewsletters(): void
    {
        $this->requireAdmin();

        $payload = $this->newsletterService->manageNewslettersPayload();

        $view = DGZ_View::getAdminView('manageNewsletters', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($payload);
    }

    /**
     * GET /admin/newsletters/create  — show form
     * POST /admin/newsletters/create — save or update
     */
    public function createNewsletter(): void
    {
        $this->requireAdmin();

        $isEdit      = isset($_GET['edit']) || isset($_POST['edit']);
        $newsletterId = (int) ($_GET['newsletterId'] ?? $_POST['newsletterId'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $val     = new DGZ_Validate();
            $subject = $val->fix_string($_POST['newsletter_subject'] ?? '');
            $body    = $_POST['newsletter_body'] ?? '';
            $template = $val->fix_string($_POST['newsletter_template'] ?? 'welcome_mail');

            if ($subject === '' || $body === '') {
                $this->addErrors('<p>Subject and body are required.</p>', 'Error');
                $redir = $isEdit && $newsletterId > 0
                    ? "create?edit=1&newsletterId={$newsletterId}"
                    : 'create';
                $this->redirect('admin/newsletters', $redir);
                return;
            }

            // File handling delegated to service
            $imagePath = $this->newsletterService->handleImageUpload();

            $data = [
                'newsletter_subject'  => $subject,
                'newsletter_body'     => $body,
                'newsletter_template' => $template,
            ];

            if ($imagePath !== '') {
                $data['newsletter_image'] = $imagePath;
            }

            if ($isEdit && $newsletterId > 0) {
                $ok = $this->newsletterService->updateNewsletter($newsletterId, $data);
                if ($ok) {
                    $this->addSuccess('Newsletter updated successfully.', 'Great!');
                } else {
                    $this->addErrors('Could not update the newsletter.', 'Error');
                }
            } else {
                $newId = $this->newsletterService->saveNewsletter($data);
                if ($newId) {
                    $this->addSuccess('Newsletter created successfully.', 'Great!');
                } else {
                    $this->addErrors('Could not save the newsletter.', 'Error');
                }
            }

            $this->redirect('admin/newsletters', '');
            return;
        }

        // GET — show form
        $newsletterData = null;
        if ($isEdit && $newsletterId > 0) {
            $row = container(\Dorguzen\Models\Newsletters::class)->getSingleNewsletter($newsletterId);
            $newsletterData = $row ? [$row] : null;
        }

        $payload = $this->newsletterService->createNewsletterPayload($newsletterData);

        $view = DGZ_View::getAdminView('createNewsletter', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($payload);
    }

    /**
     * GET /admin/newsletters/delete
     */
    public function deleteNewsletter(): void
    {
        $this->requireAdmin();

        $id = (int) ($_GET['newsletter_id'] ?? 0);

        if ($id > 0 && $this->newsletterService->deleteNewsletter($id)) {
            $this->addSuccess('Newsletter deleted successfully.', 'Done');
        } else {
            $this->addErrors('Could not delete the newsletter.', 'Error');
        }

        $this->redirect('admin/newsletters', '');
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

    /**
     * Redirect back to the previous page (HTTP_REFERER) or to home.
     */
    private function redirectBack(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';

        if ($referer !== '') {
            header('Location: ' . $referer);
            exit;
        }

        $this->redirect('home', 'home');
    }
}
