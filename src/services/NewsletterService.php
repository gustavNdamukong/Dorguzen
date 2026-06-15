<?php

namespace Dorguzen\Services;

use Dorguzen\Models\Subscribers;
use Dorguzen\Models\Newsletters;
use Dorguzen\Models\PendingEmails;

/**
 * NewsletterService
 *
 * Owns all business logic for the Newsletter / Subscriber feature.
 *
 * Controllers served:
 *   - NewsletterController
 */
class NewsletterService
{
    public function __construct(
        private Subscribers  $subscribers,
        private Newsletters  $newsletters,
        private PendingEmails $pendingEmails
    ) {}

    // -------------------------------------------------------------------------
    // Payload builders
    // -------------------------------------------------------------------------

    /**
     * Payload for the manage-subscribers admin page.
     *
     * Used by: NewsletterController::manageSubscribers()
     */
    public function manageSubscribersPayload(): array
    {
        return [
            'subscribers'       => $this->subscribers->getAllSubscribers(),
            'activeSubscribers' => $this->subscribers->getActiveSubscribers(),
            'newSubscribers'    => $this->subscribers->getNewSubscribers(),
            'newsletters'       => $this->newsletters->getAllNewsletters(),
        ];
    }

    /**
     * Payload for the manage-newsletters admin page.
     *
     * Used by: NewsletterController::manageNewsletters()
     */
    public function manageNewslettersPayload(): array
    {
        return [
            'newsletters' => $this->newsletters->getAllNewsletters(),
            'templates'   => $this->scanEmailTemplates(),
        ];
    }

    /**
     * Payload for the create/edit newsletter form.
     *
     * Used by: NewsletterController::createNewsletter()
     *
     * @param array|null $newsletterData  Existing row (edit mode) or null (create mode)
     */
    public function createNewsletterPayload(?array $newsletterData = null): array
    {
        return [
            'newsletterData' => $newsletterData,
            'templates'      => $this->scanEmailTemplates(),
        ];
    }

    // -------------------------------------------------------------------------
    // Write operations — subscribers
    // -------------------------------------------------------------------------

    /**
     * Save a new subscriber.
     * Returns true on success, false if the email is already subscribed.
     */
    public function saveSubscriber(array $data): bool
    {
        $email = trim($data['subscriber_email'] ?? '');

        if ($email === '') {
            return false;
        }

        // Duplicate check
        if ($this->subscribers->findByEmail($email) !== null) {
            return false;
        }

        $record = container(Subscribers::class);
        $record->subscriber_email     = $email;
        $record->subscriber_firstname = trim($data['subscriber_firstname'] ?? '');
        $record->subscriber_welcomed  = 0;
        $record->subscriber_active    = 1;

        return (bool) $record->save();
    }

    /**
     * Delete a subscriber by ID.
     */
    public function deleteSubscriber(int $id): bool
    {
        return (bool) $this->subscribers->deleteWhere(['subscriber_id' => $id]);
    }

    /**
     * Unsubscribe — mark a subscriber inactive by email.
     * Always returns true (we don't reveal whether the email was found).
     */
    public function unsubscribeByEmail(string $email): bool
    {
        $subscriber = $this->subscribers->findByEmail($email);

        if ($subscriber) {
            $this->subscribers->deactivateByEmail($email);
        }

        return true;
    }

    // -------------------------------------------------------------------------
    // File handling
    // -------------------------------------------------------------------------

    /**
     * Handle image upload for a newsletter.
     *
     * Uses move_uploaded_file() directly (no resize — newsletter images are not thumbnailed).
     * Returns the stored relative path ('assets/images/newsletters/filename.ext') or '' if no
     * file was uploaded.
     */
    public function handleImageUpload(): string
    {
        if (!isset($_FILES['newsletter_image']) || $_FILES['newsletter_image']['error'] !== UPLOAD_ERR_OK) {
            return '';
        }

        $uploadDir = base_path('assets/images/newsletters/');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext      = strtolower(pathinfo($_FILES['newsletter_image']['name'], PATHINFO_EXTENSION));
        $filename = uniqid('nl_', true) . '.' . $ext;

        if (move_uploaded_file($_FILES['newsletter_image']['tmp_name'], $uploadDir . $filename)) {
            return 'assets/images/newsletters/' . $filename;
        }

        return '';
    }

    // -------------------------------------------------------------------------
    // Write operations — newsletters
    // -------------------------------------------------------------------------

    /**
     * Save a new newsletter. Returns new newsletter_id or false on failure.
     */
    public function saveNewsletter(array $data): int|false
    {
        $record = container(Newsletters::class);

        $record->newsletter_subject  = $data['newsletter_subject']  ?? '';
        $record->newsletter_body     = $data['newsletter_body']     ?? '';
        $record->newsletter_template = $data['newsletter_template'] ?? 'newsletter-welcome';

        if (!empty($data['newsletter_image'])) {
            $record->newsletter_image = $data['newsletter_image'];
        }

        $insertId = $record->save();

        return $insertId ? (int) $insertId : false;
    }

    /**
     * Update an existing newsletter.
     */
    public function updateNewsletter(int $id, array $data): bool
    {
        $fields = [
            'newsletter_subject'  => $data['newsletter_subject']  ?? '',
            'newsletter_body'     => $data['newsletter_body']     ?? '',
            'newsletter_template' => $data['newsletter_template'] ?? 'newsletter-welcome',
        ];

        if (isset($data['newsletter_image']) && $data['newsletter_image'] !== '') {
            $fields['newsletter_image'] = $data['newsletter_image'];
        }

        return (bool) $this->newsletters->updateObject($fields, ['newsletter_id' => $id]);
    }

    /**
     * Delete a newsletter.
     */
    public function deleteNewsletter(int $id): bool
    {
        return (bool) $this->newsletters->deleteWhere(['newsletter_id' => $id]);
    }

    // -------------------------------------------------------------------------
    // Template scanning
    // -------------------------------------------------------------------------

    /**
     * Scan both core/email-views/ and views/emails/ for template names.
     * App overrides (views/emails/) take the same slot as core templates
     * with the same name — no duplicates in the result.
     */
    public function scanEmailTemplates(): array
    {
        $templates = [];

        // Core framework templates (baseline)
        $coreDir = dirname(__DIR__, 2) . '/core/email-views/';
        if (is_dir($coreDir)) {
            foreach (glob($coreDir . '*.php') as $file) {
                $name = pathinfo($file, PATHINFO_FILENAME);
                if ($name !== '') {
                    $templates[$name] = $name;
                }
            }
        }

        // App overrides — same key overwrites, new keys are added
        $appDir = dirname(__DIR__, 2) . '/views/emails/';
        if (is_dir($appDir)) {
            foreach (glob($appDir . '*.php') as $file) {
                $name = pathinfo($file, PATHINFO_FILENAME);
                if ($name !== '') {
                    $templates[$name] = $name;
                }
            }
        }

        $templates = array_values($templates);
        sort($templates);
        return $templates;
    }

    // -------------------------------------------------------------------------
    // Email queueing (pending_emails table)
    // -------------------------------------------------------------------------

    /**
     * Queue welcome emails for a list of subscriber IDs.
     *
     * Inserts one row per subscriber into `pending_emails`.
     * The scheduler's SendPendingEmailsTask will pick them up and send them.
     *
     * Returns the number of rows inserted.
     */
    public function queueWelcomeEmails(array $subscriberIds, int $newsletterId): int
    {
        return $this->insertPendingRows($subscriberIds, $newsletterId);
    }

    /**
     * Queue a bulk newsletter send for a list of subscriber IDs.
     *
     * Same mechanics as queueWelcomeEmails — both paths insert into pending_emails.
     * Returns the number of rows inserted.
     */
    public function queueBulkEmail(array $subscriberIds, int $newsletterId): int
    {
        return $this->insertPendingRows($subscriberIds, $newsletterId);
    }

    /**
     * Count of emails currently waiting in the pending queue.
     */
    public function pendingEmailsCount(): int
    {
        return $this->pendingEmails->pendingCount();
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    /**
     * Insert pending_email rows for each valid subscriber ID.
     */
    private function insertPendingRows(array $subscriberIds, int $newsletterId): int
    {
        $newsletter = $this->newsletters->getSingleNewsletter($newsletterId);

        if (!$newsletter) {
            return 0;
        }

        $count = 0;

        foreach ($subscriberIds as $rawId) {
            $subscriberId = (int) $rawId;

            if ($subscriberId <= 0) {
                continue;
            }

            $subscriber = $this->subscribers->findById($subscriberId);

            if (!$subscriber || empty($subscriber['subscriber_active'])) {
                continue;
            }

            $row = container(PendingEmails::class);
            $row->subscriber_id      = $subscriberId;
            $row->subscriber_email   = $subscriber['subscriber_email']     ?? '';
            $row->subscriber_name    = $subscriber['subscriber_firstname'] ?? '';
            $row->newsletter_id      = $newsletterId;
            $row->newsletter_subject = $newsletter['newsletter_subject']   ?? '';
            $row->status             = 'pending';

            if ($row->save()) {
                $count++;
            }
        }

        return $count;
    }
}
