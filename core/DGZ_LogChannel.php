<?php

namespace Dorguzen\Core;

use Dorguzen\Models\Logs;


/**
 * DGZ_LogChannel
 *
 * Represents a single, independently-configured logging destination.
 * Instances are created and cached by DGZ_Logger::channel().
 *
 * Each channel has its own:
 *   - driver     (file | db | both)
 *   - format     (text | json)
 *   - log directory
 *   - minimum severity level (messages below it are silently ignored)
 *
 * Usage:
 *   DGZ_Logger::channel('payments')->warning("Charge failed", ['amount' => 5000]);
 *   DGZ_Logger::channel('security')->critical("Brute force attempt", ['ip' => $ip]);
 */
class DGZ_LogChannel
{
    private const LEVELS = [
        'debug'    => 0,
        'info'     => 1,
        'notice'   => 2,
        'warning'  => 3,
        'error'    => 4,
        'critical' => 5,
    ];

    private string $name;
    private string $driver;
    private string $format;
    private string $logDirectory;
    private string $filenamePrefix;
    private int    $minLevelValue;
    private Logs   $logsModel;


    public function __construct(string $name, array $channelConfig, Logs $logsModel)
    {
        $this->name           = $name;
        $this->driver         = $channelConfig['driver']          ?? 'db';
        $this->format         = $channelConfig['format']          ?? 'text';
        $this->filenamePrefix = $channelConfig['filename_prefix'] ?? $name;
        $this->logsModel      = $logsModel;
        $this->minLevelValue  = self::LEVELS[$channelConfig['min_level'] ?? 'debug'] ?? 0;

        // Resolve the log directory relative to the project root
        $relativePath       = ltrim($channelConfig['path'] ?? 'storage/logs', '/');
        $this->logDirectory = DGZ_BASE_PATH . '/' . $relativePath;

        if (!is_dir($this->logDirectory)) {
            mkdir($this->logDirectory, 0775, true);
        }
    }


    /**
     * Write a log entry through this channel.
     * Messages below the channel's min_level are silently discarded.
     */
    public function log(string $level, string $message, array $context = []): void
    {
        if (empty($level) || empty($message)) {
            return;
        }

        $levelValue = self::LEVELS[strtolower($level)] ?? 0;
        if ($levelValue < $this->minLevelValue) {
            return;
        }

        $line = $this->formatLine($level, $message, $context);

        if ($this->driver === 'file' || $this->driver === 'both') {
            $this->writeToFile($line);
        }

        if ($this->driver === 'db' || $this->driver === 'both') {
            // Store the channel name inside context_json so DB logs are filterable by channel
            $dbContext = array_merge(['_channel' => $this->name], $context);
            $this->logsModel->log(strtoupper($level), $message, $dbContext);
        }
    }


    // Convenience wrappers matching the DGZ_Logger public API
    public function debug(string $msg, array $ctx = []): void    { $this->log('debug',    $msg, $ctx); }
    public function info(string $msg, array $ctx = []): void     { $this->log('info',     $msg, $ctx); }
    public function notice(string $msg, array $ctx = []): void   { $this->log('notice',   $msg, $ctx); }
    public function warning(string $msg, array $ctx = []): void  { $this->log('warning',  $msg, $ctx); }
    public function error(string $msg, array $ctx = []): void    { $this->log('error',    $msg, $ctx); }
    public function critical(string $msg, array $ctx = []): void { $this->log('critical', $msg, $ctx); }


    /**
     * Format a log line as text or JSON.
     * The channel name is included so mixed log directories remain scannable.
     */
    private function formatLine(string $level, string $message, array $context): string
    {
        $timestamp = date('Y-m-d H:i:s');

        if ($this->format === 'json') {
            return json_encode([
                'time'    => $timestamp,
                'channel' => $this->name,
                'level'   => strtoupper($level),
                'message' => $message,
                'context' => $context,
            ]) . PHP_EOL;
        }

        // text format
        $ctx = !empty($context) ? ' ' . json_encode($context) : '';
        return "[{$timestamp}] [{$this->name}] " . strtoupper($level) . ": {$message}{$ctx}" . PHP_EOL;
    }


    /**
     * Append a line to a daily-rotating file using flock() for concurrency safety.
     * File naming: {channel}-YYYY-MM-DD.log  e.g. payments-2025-11-10.log
     */
    private function writeToFile(string $line): void
    {
        $filename = $this->logDirectory . '/' . $this->filenamePrefix . '-' . date('Y-m-d') . '.log';

        $fp = fopen($filename, 'a');
        if (!$fp) {
            error_log("DGZ_LogChannel [{$this->name}]: Cannot open log file: {$filename}");
            return;
        }

        flock($fp, LOCK_EX);
        fwrite($fp, $line);
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}
