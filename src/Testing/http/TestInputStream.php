<?php

namespace Dorguzen\Testing\Http;


/**
 * TestInputStream creates fake request bodies in tests.
 * In fact, it's our custom stream wrapper (or simulator)
 * for php://input stream during tests.
 * It works with file_get_contents().
 * It's the technique for working with PHP's streams in test environments.
 * You basically have to create or register your own stream wrapper/simulator.
 * There is the important rule to keep. If you register a stream wrapper, the
 * class (in our case TestInputStream) must implement the stream wrapper protocol.
 * That means the class must implement the following methods to fullfil the contract:
 *
 *      -stream_open()
 *      -stream_read()
 *      -stream_eof()
 *
 * On the requesting side (eg tests), you would use setContent() to write the data being
 * sent via the stream. On the receiving (request-handling) side, those methods above like
 * stream_open() will automatically be called by PHP when you do this:
 *
 *  $raw = file_get_contents('php://input');
 *
 * This is because for JSON requests, the only source of accessing POST data from JSON is
 * via php://input, and using JSON is the only means available for tests to make requests
 * to the backend of your application.
 */
class TestInputStream
{
    protected static string $content = '';

    /** Required by PHP stream wrapper protocol */
    public $context;

    protected int $position = 0;

    /**
     * setContent() is for injecting content into the stream
     * @param string $content
     * @return void
     */
    public static function setContent(string $content): void
    {
        self::$content = $content;
    }

    public static function reset(): void
    {
        self::$content = '';
    }


    // --- PHP stream wrapper methods ---

    public function stream_open($path, $mode, $options, &$opened_path): bool
    {
        $this->position = 0;
        return true;
    }

    public function stream_read(int $count): string
    {
        $chunk = substr(self::$content, $this->position, $count);
        $this->position += strlen($chunk);
        return $chunk;
    }

    public function stream_eof(): bool
    {
        return $this->position >= strlen(self::$content);
    }

    public function stream_stat(): array
    {
        return [];
    }
}
