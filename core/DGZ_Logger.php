<?php 

namespace Dorguzen\Core;


use Dorguzen\Models\Logs;
use Dorguzen\Config\Config;

class DGZ_Logger
{
    private static string $logDirectory;
    private static Config $config;
    private static Logs $logs;
    private static bool $initialized = false;

    /** @var DGZ_LogChannel[] Cached channel instances, keyed by channel name */
    private static array $channelInstances = [];

    /**
     * Initialize logger
     */
    public static function init(string $directory = "")
    {
        if (self::$initialized) {
            return;
        }

        self::$config = container(Config::class);

        self::$logs   = new Logs(self::$config);

        self::$logDirectory = $directory ?: __DIR__ . '/../storage/logs';

        // Ensure directory exists
        if (!file_exists(self::$logDirectory)) {
            mkdir(self::$logDirectory, 0775, true);
        }

        self::$initialized = true;
    }

    /**
     * Retrieve (or create) a named log channel.
     *
     * Channel configuration is read from configs/logging.php.
     * Instances are cached so each channel is only configured once per request.
     *
     * Usage:
     *   DGZ_Logger::channel('payments')->warning("Charge failed", ['amount' => 100]);
     *   DGZ_Logger::channel('security')->critical("Brute force", ['ip' => $ip]);
     */
    public static function channel(string $name): DGZ_LogChannel
    {
        if (!self::$initialized) {
            self::init();
        }

        if (!isset(self::$channelInstances[$name])) {
            $channelConfig = config("logging.channels.{$name}");

            if (!is_array($channelConfig)) {
                // Unknown channel — fall back to the default channel config
                $channelConfig = config('logging.channels.default') ?? [
                    'driver'    => self::$config->getConfig()['log_driver'] ?? 'db',
                    'format'    => self::$config->getConfig()['log_format'] ?? 'text',
                    'path'      => 'storage/logs',
                    'min_level' => 'debug',
                ];
            }

            self::$channelInstances[$name] = new DGZ_LogChannel($name, $channelConfig, self::$logs);
        }

        return self::$channelInstances[$name];
    }


    /**
     * Generic logging method with PSR-3-style signature.
     */
    public static function log(string $level, string $message, array $context = [])
    {
        if (!self::$initialized) {
            self::init();
        }

        if ($level == "" && $message == "")
        {
            // silently abort the logging - we dont want to log blanks
            return;
        } 
        if ($message == NULL)
        {
            // silently abort the logging - we dont want to log blanks
            return;
        } 

        $driver = self::$config->getConfig()['log_driver'] ?? 'db';
        $format = self::$config->getConfig()['log_format'] ?? 'text';

        $line = self::formatLine($level, $message, $context, $format);

        if ($driver === 'file' || $driver === 'both') {
            try {
                self::writeToFile($line);
            } catch (\Throwable $e) {
                error_log('[DGZ_Logger file error] ' . $e->getMessage() . ' | Original: ' . $line);
            }
        }

        if ($driver === 'db' || $driver === 'both') {
            try {
                self::$logs->log(strtoupper($level), $message, $context);
            } catch (\Throwable $e) {
                error_log('[DGZ_Logger db error] ' . $e->getMessage() . ' | Original: ' . $line);
            }
        }
    }

    /**
     * --- Public convenience wrappers ---
     */
    public static function debug(string $msg, array $ctx = [])   { self::log('debug',   $msg, $ctx); }
    public static function info(string $msg, array $ctx = [])    { self::log('info',    $msg, $ctx); }
    public static function notice(string $msg, array $ctx = [])  { self::log('notice',  $msg, $ctx); }
    public static function warning(string $msg, array $ctx = []) { self::log('warning', $msg, $ctx); }
    public static function error(string $msg, array $ctx = [])   { self::log('error',   $msg, $ctx); }
    public static function critical(string $msg, array $ctx = []){ self::log('critical',$msg, $ctx); }

    /**
     * Build final log entry either in TEXT or JSON form.
     */
    private static function formatLine(string $level, string $message, array $context, string $format): string
    {
        $timestamp = date('Y-m-d H:i:s');

        if ($format === 'json') {
            return json_encode([
                'time'    => $timestamp,
                'level'   => $level,
                'message' => $message,
                'context' => $context
            ]) . PHP_EOL;
        }

        // Default: text format
        $ctx = (!empty($context)) ? ' ' . json_encode($context) : '';
        return "[" . $timestamp . "] " . strtoupper($level) . ": " . $message . $ctx . PHP_EOL;
    }


    public static function getAll(string $orderBy = '')
    {
        if (!self::$initialized) {
            self::init();
        }

        return self::$logs->getAll($orderBy);
    }



    /**
     * Write safely to rotating daily file using flock().
     */
    private static function writeToFile(string $line)
    {
        $filename = self::$logDirectory . '/dgz-' . date('Y-m-d') . '.log';

        $fp = fopen($filename, 'a');
        if (!$fp) {
            error_log("DGZ_Logger: Cannot open log file: {$filename}");
            return;
        }

        // Prevent race conditions when many writes happen at once
        flock($fp, LOCK_EX);
        fwrite($fp, $line);
        flock($fp, LOCK_UN);

        fclose($fp);
    }


    /**
     * Public retrieval helper for DB logs
     */
    public static function getRunTimeErrors($orderBy = '')
    {
        if (!self::$initialized) {
            self::init();
        }

        return self::$logs->getRunTimeErrors($orderBy);
    }


    /**
     * @param string $orderBy field you want results to be ordered by
     * @return array|bool
     */
    public function getAdminLoginData($orderBy = '')
    {
        return self::$logs->getAdminLoginData($orderBy);
    }
}