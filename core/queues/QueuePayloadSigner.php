<?php

namespace Dorguzen\Core\Queues;

use RuntimeException;

/**
 * QueuePayloadSigner signs queued jobs when they are stored and checks the
 * signature before they are rebuilt. unserialize() can run code from whatever
 * text it is given, so a payload changed in the DB or the message broker must
 * never reach it. Requires QUEUE_SIGNING_KEY in .env.
 */
class QueuePayloadSigner
{
    public function __construct(
        private string $key
    ) {
        if ($key === '') {
            throw new RuntimeException(
                "The db and rabbitmq queue drivers require QUEUE_SIGNING_KEY in .env.\n" .
                "Generate one with:  php -r \"echo bin2hex(random_bytes(32));\""
            );
        }
    }

    public function sign(object $job): string
    {
        $serialized = serialize($job);

        return hash_hmac('sha256', $serialized, $this->key) . ':' . $serialized;
    }

    /**
     * @throws RuntimeException if the payload is unsigned or has been changed
     */
    public function verifyAndRestore(string $payload): object
    {
        [$signature, $serialized] = array_pad(explode(':', $payload, 2), 2, '');

        if (! hash_equals(hash_hmac('sha256', $serialized, $this->key), $signature)) {
            throw new RuntimeException('Queue payload has an invalid signature');
        }

        return unserialize($serialized);
    }
}
