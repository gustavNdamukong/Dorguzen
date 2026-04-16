<?php

namespace Dorguzen\Core\CLI\Command;

use Symfony\Component\Console\Input\InputOption;


/**
 * LogTailCommand — php dgz log:tail
 *
 * Streams a Dorguzen log file in real time, exactly like `tail -f`.
 *
 * Usage:
 *   php dgz log:tail                        # tail the default channel
 *   php dgz log:tail --channel=payments     # tail a named channel
 *   php dgz log:tail --channel=security --lines=50
 */
class LogTailCommand extends AbstractCommand
{
    protected static $defaultName        = 'log:tail';
    protected static $defaultDescription = 'Stream a Dorguzen log file in real time (like tail -f)';


    public function __construct($container)
    {
        parent::__construct($container);
    }


    protected function configure(): void
    {
        $this
            ->setName('log:tail')
            ->setDescription('Stream a Dorguzen log file in real time (like tail -f)')
            ->addOption(
                'channel',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Log channel to tail (must use driver: file or both)',
                'default'
            )
            ->addOption(
                'lines',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Number of existing lines to show before following new output',
                20
            );
    }


    protected function handle(): int
    {
        $output      = $this->output;
        $channelName = $this->input->getOption('channel');
        $lines       = max(1, (int) $this->input->getOption('lines'));

        // ── Resolve channel config ────────────────────────────────────────────
        $channelConfig = config("logging.channels.{$channelName}");

        if (!is_array($channelConfig)) {
            $output->writeln("<comment>Unknown channel '{$channelName}', falling back to 'default'.</comment>");
            $channelConfig = config('logging.channels.default') ?? [];
            $channelName   = 'default';
        }

        $driver = $channelConfig['driver'] ?? 'db';

        // ── Guard: db-only channels have no file to tail ──────────────────────
        if ($driver === 'db') {
            $output->writeln("<error>Channel '{$channelName}' uses driver 'db' — there is no log file to tail.</error>");
            $output->writeln('<comment>Tip: set driver to "file" or "both" in configs/logging.php, then use: php dgz log</comment>');
            return self::FAILURE;
        }

        // ── Build the expected log file path ──────────────────────────────────
        $relativePath = ltrim($channelConfig['path'] ?? 'storage/logs', '/');
        $logDirectory = DGZ_BASE_PATH . '/' . $relativePath;
        $prefix       = $channelConfig['filename_prefix'] ?? $channelName;
        $filename     = $logDirectory . '/' . $prefix . '-' . date('Y-m-d') . '.log';

        if (!file_exists($filename)) {
            $output->writeln("<comment>Log file not found: {$filename}</comment>");
            $output->writeln('<comment>The file is created automatically on the first log write. Try logging something first:</comment>');
            $output->writeln('');
            $output->writeln("  <info>DGZ_Logger::channel('{$channelName}')->info('Hello from {$channelName}');</info>");
            $output->writeln('');
            return self::FAILURE;
        }

        // ── Start tailing ─────────────────────────────────────────────────────
        $output->writeln("<info>Tailing [{$channelName}]: {$filename}</info>");
        $output->writeln('<comment>Press Ctrl+C to stop.</comment>');
        $output->writeln('');

        $cmd = sprintf(
            'tail -f -n %s %s',
            escapeshellarg((string) $lines),
            escapeshellarg($filename)
        );

        $descriptors = [0 => STDIN, 1 => STDOUT, 2 => STDERR];
        $process = proc_open($cmd, $descriptors, $pipes);

        if (!is_resource($process)) {
            $output->writeln('<error>Failed to start tail process. Is `tail` available on this system?</error>');
            return self::FAILURE;
        }

        // Blocks until the user presses Ctrl+C (SIGINT terminates tail)
        proc_close($process);

        return self::SUCCESS;
    }
}
