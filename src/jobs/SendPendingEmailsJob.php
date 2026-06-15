<?php

namespace Dorguzen\Jobs;

use Dorguzen\Models\PendingEmails;
use Dorguzen\Models\Newsletters;
use Dorguzen\Models\Subscribers;
use Dorguzen\Core\DGZ_Messenger;
use Dorguzen\Config\Config;

/**
 * SendPendingEmailsJob
 *
 * Scheduler job that processes the pending_emails queue.
 *
 * Registered in src/CLI/console/Schedule.php.
 * Run via:  php dgz schedule:run
 *
 * Processes up to 50 pending rows per run. Uses the framework's
 * renderEmail() pipeline via sendNewsletterWelcomeMsg() (first send)
 * or sendNewsletterMsg() (subsequent sends), so email templates in
 * views/emails/ are respected and the default layout is applied.
 */
class SendPendingEmailsJob
{
    public function handle(): void
    {
        /** @var PendingEmails $pendingEmails */
        $pendingEmails = container(PendingEmails::class);

        /** @var Newsletters $newsletters */
        $newsletters = container(Newsletters::class);

        /** @var Subscribers $subscribers */
        $subscribers = container(Subscribers::class);

        /** @var Config $config */
        $config  = container(Config::class);
        $baseUrl = $config->getHomePage();

        $rows = $pendingEmails->getPendingEmails(50);

        if (empty($rows)) {
            return;
        }

        foreach ($rows as $row) {
            $id           = (int) $row['id'];
            $subscriberId = (int) $row['subscriber_id'];
            $newsletterId = (int) $row['newsletter_id'];

            try {
                $newsletter = $newsletters->getSingleNewsletter($newsletterId);
                $subscriber = $subscribers->findById($subscriberId);

                if (!$newsletter || !$subscriber) {
                    error_log("SendPendingEmailsJob: missing newsletter ({$newsletterId}) or subscriber ({$subscriberId}) for pending_email id={$id}");
                    $pendingEmails->markFailed($id);
                    continue;
                }

                $name     = $subscriber['subscriber_firstname']  ?? '';
                $email    = $subscriber['subscriber_email']      ?? '';
                $subject  = $newsletter['newsletter_subject']    ?? '';
                $image    = $newsletter['newsletter_image']      ?? '';
                $template = $newsletter['newsletter_template'] ?? 'newsletter-welcome';
                // 'welcome_mail' was an old default that never had a corresponding file
                if ($template === 'welcome_mail') {
                    $template = 'newsletter-welcome';
                }

                // Build body with unsubscribe footer
                $unsubscribeUrl = rtrim($baseUrl, '/') . '/unsubscribe?email=' . urlencode($email);
                $body = ($newsletter['newsletter_body'] ?? '')
                    . '<p style="margin-top:24px;padding-top:16px;border-top:1px solid #eeeeee;'
                    . 'font-family:Helvetica,Arial,sans-serif;font-size:12px;color:#999999;">'
                    . 'You are receiving this because you subscribed to our newsletter. '
                    . '<a href="' . htmlspecialchars($unsubscribeUrl) . '" style="color:#999999;">Unsubscribe</a>'
                    . '</p>';

                $messenger = new DGZ_Messenger();
                $isFirstSend = empty($subscriber['subscriber_welcomed']);

                if ($isFirstSend) {
                    $messenger->sendNewsletterWelcomeMsg(
                        $name ?: 'Subscriber',
                        $email,
                        $subject,
                        $subject,
                        $body,
                        $image,
                        '',
                        $template ?: 'newsletter-welcome'
                    );
                } else {
                    $messenger->sendNewsletterMsg(
                        $name ?: 'Subscriber',
                        $email,
                        $subject,
                        $subject,
                        $body,
                        $image,
                        '',
                        $template ?: 'newsletter'
                    );
                }

                $pendingEmails->markSent($id);

                if ($isFirstSend) {
                    $subscribers->markAsWelcomed($subscriberId);
                }
            } catch (\Throwable $e) {
                error_log("SendPendingEmailsJob: failed to send pending_email id={$id}: " . $e->getMessage());
                $pendingEmails->markFailed($id);
            }
        }
    }
}
