<?php

namespace Dorguzen\Services;

use Dorguzen\Models\ContactFormMessage;
use Dorguzen\Core\DGZ_Validate;

/**
 * FeedbackService
 *
 * Owns the database operation for saving contact form messages.
 *
 * Controllers served:
 *   - FeedbackController (processContact)
 *
 * The controller keeps: input validation, email notification to admin,
 * flash messages, and redirects.
 */
class FeedbackService
{
    private ContactFormMessage $contactFormMessage;

    public function __construct(ContactFormMessage $contactFormMessage)
    {
        $this->contactFormMessage = $contactFormMessage;
    }

    /**
     * Validate the contact form submission.
     *
     * Name and message are required; email must be well-formed.
     * Returns an HTML error string on failure, empty string when valid.
     */
    public function validateContactInput(string $name, string $email, string $message): string
    {
        $val  = new DGZ_Validate();
        $fail = '';
        if ($name === '')    $fail .= '<p>Please enter your name</p>';
        if ($message === '') $fail .= '<p>Please enter a message</p>';
        $emailError = $val->validate_email($email);
        if ($emailError !== '') $fail .= $emailError;
        return $fail;
    }

    /**
     * Persist a contact form submission.
     * The date column defaults to CURRENT_TIMESTAMP, so it is not set here.
     *
     * @param array $data {name, email, phone, message}
     * @return bool  True if the row was saved.
     */
    public function saveContactMessage(array $data): bool
    {
        $record = container(ContactFormMessage::class);

        $record->contactformmessage_name    = $data['name'];
        $record->contactformmessage_email   = $data['email'];
        $record->contactformmessage_phone   = $data['phone'];
        $record->contactformmessage_message = $data['message'];

        return (bool) $record->save();
    }
}
