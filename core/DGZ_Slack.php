<?php

namespace Dorguzen\Core;

/**
 * DGZ_Slack — send Slack notifications via Incoming Webhooks.
 *
 * This is a thin, zero-dependency wrapper around Slack's Incoming Webhook API.
 * No Slack SDK is required — all communication is a single HTTPS POST request.
 *
 * Quick start:
 *   1. Create an Incoming Webhook at https://api.slack.com/apps → "Incoming Webhooks"
 *   2. Add the webhook URL to your .env:  SLACK_WEBHOOK_URL=https://hooks.slack.com/...
 *   3. Call: DGZ_Slack::send('Hello from Dorguzen!');
 *
 * .env keys:
 *   SLACK_WEBHOOK_URL       — required. Your Incoming Webhook URL.
 *   SLACK_DEFAULT_CHANNEL   — optional. Channel to post to (e.g. #alerts).
 *                             Overrides the channel configured on the webhook itself.
 *   SLACK_USERNAME          — optional. Bot display name (default: your APP_NAME).
 *   SLACK_ICON_EMOJI        — optional. Bot icon emoji (default: :bell:).
 */
class DGZ_Slack
{
    /**
     * Send a message to Slack.
     *
     * Usage examples:
     *
     *   // Plain message — posts to the webhook's default channel
     *   DGZ_Slack::send('New user registered: john_doe');
     *
     *   // Override the channel for this specific message
     *   DGZ_Slack::send('Payment failed for order #1234', '#payments');
     *
     *   // Pass extra Slack payload keys (attachments, blocks, etc.)
     *   DGZ_Slack::send('Deploy complete', '#deployments', [
     *       'attachments' => [[
     *           'color' => 'good',
     *           'text'  => 'Version 2.1.0 is live.',
     *       ]]
     *   ]);
     *
     * @param string      $message  The text to post.
     * @param string|null $channel  Channel override for this message only (e.g. '#alerts').
     *                              Falls back to SLACK_DEFAULT_CHANNEL, then the webhook default.
     * @param array       $extra    Additional Slack payload keys merged into the request body.
     *                              Use this for blocks, attachments, thread_ts, etc.
     * @return bool  true on success, false on failure (failure is also logged via DGZ_Logger).
     */
    public static function send(string $message, ?string $channel = null, array $extra = []): bool
    {
        $webhookUrl = env('SLACK_WEBHOOK_URL', '');

        if (empty($webhookUrl)) {
            DGZ_Logger::warning('DGZ_Slack::send() called but SLACK_WEBHOOK_URL is not configured in .env');
            return false;
        }

        // Build the base payload
        $payload = array_merge([
            'text'       => $message,
            'username'   => env('SLACK_USERNAME', env('APP_NAME', 'Dorguzen')),
            'icon_emoji' => env('SLACK_ICON_EMOJI', ':bell:'),
        ], $extra);

        // Channel priority: argument > SLACK_DEFAULT_CHANNEL env var > webhook's own default
        $resolved = $channel ?? env('SLACK_DEFAULT_CHANNEL');
        if (!empty($resolved)) {
            $payload['channel'] = $resolved;
        }

        // Send via cURL
        $ch = curl_init($webhookUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,   // fail fast — don't block a web request
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr || $httpCode !== 200) {
            DGZ_Logger::error('DGZ_Slack: message delivery failed', [
                'http_code'  => $httpCode,
                'curl_error' => $curlErr ?: null,
                'response'   => $response,
                'channel'    => $resolved ?? '(webhook default)',
            ]);
            return false;
        }

        return true;
    }
}
