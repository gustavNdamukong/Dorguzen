<?php

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Models\Logs;
use Symfony\Component\Console\Input\InputOption;


/**
 * LogPruneCommand — php dgz log:prune
 *
 * Deletes log files older than N days, optionally scoped to a single channel.
 * Always writes a DB audit entry so there is a permanent record of every prune run.
 *
 * Usage:
 *   php dgz log:prune                                    # prune all channels, files older than 30 days
 *   php dgz log:prune --days=7                           # prune all channels, files older than 7 days
 *   php dgz log:prune --channel=payments                 # prune one channel only
 *   php dgz log:prune --dry-run                          # show what would be deleted, touch nothing
 *   php dgz log:prune --channel=security --days=90 --dry-run
 */
class LogPruneCommand extends AbstractCommand
{
    protected static $defaultName        = 'log:prune';
    protected static $defaultDescription = 'Delete log files older than N days (with optional dry-run and DB audit)';


    public function __construct($container)
    {
        parent::__construct($container);
    }


    protected function configure(): void
    {
        $this
            ->setName('log:prune')
            ->setDescription('Delete log files older than N days (with optional dry-run and DB audit)')
            ->addOption(
                'days',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Delete files with mtime older than this many days',
                30
            )
            ->addOption(
                'channel',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Log channel to prune (default: all channels)',
                'all'
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'List files that would be deleted without actually deleting them'
            );
    }


    protected function handle(): int
    {
        $output     = $this->output;
        $days       = max(0, (int) $this->input->getOption('days'));
        $channelArg = $this->input->getOption('channel');
        $isDryRun   = (bool) $this->input->getOption('dry-run');
        $cutoff     = time() - ($days * 86400);

        if ($isDryRun) {
            $output->writeln('<comment>[dry-run] No files will be deleted.</comment>');
            $output->writeln('');
        }

        // ── Collect channels to process ───────────────────────────────────────
        if ($channelArg !== 'all') {
            $channelConfig = config("logging.channels.{$channelArg}");

            if (!is_array($channelConfig)) {
                $output->writeln("<error>Unknown channel '{$channelArg}'. Check configs/logging.php for valid channel names.</error>");
                return self::FAILURE;
            }

            $channels = [$channelArg => $channelConfig];
        } else {
            $allChannels = config('logging.channels');

            if (!is_array($allChannels)) {
                $output->writeln('<error>No channels found in configs/logging.php.</error>');
                return self::FAILURE;
            }

            $channels = $allChannels;
        }

        $deleted = 0;
        $skipped = 0;

        // ── Process each channel ──────────────────────────────────────────────
        foreach ($channels as $channelName => $channelConfig) {
            $driver = $channelConfig['driver'] ?? 'db';

            // db-only channels have no files
            if ($driver === 'db') {
                $output->writeln("<comment>Skipping channel '{$channelName}' (driver: db — no log files).</comment>");
                continue;
            }

            $relativePath = ltrim($channelConfig['path'] ?? 'storage/logs', '/');
            $logDirectory = DGZ_BASE_PATH . '/' . $relativePath;
            $prefix       = $channelConfig['filename_prefix'] ?? $channelName;

            if (!is_dir($logDirectory)) {
                $output->writeln("<comment>Skipping channel '{$channelName}': directory not found ({$logDirectory}).</comment>");
                continue;
            }

            $files = glob("{$logDirectory}/{$prefix}-*.log");

            if (empty($files)) {
                $output->writeln("<comment>No log files found for channel '{$channelName}' in {$logDirectory}.</comment>");
                continue;
            }

            $output->writeln("<info>Channel '{$channelName}' — scanning {$logDirectory}</info>");

            foreach ($files as $file) {
                $mtime = filemtime($file);

                if ($mtime === false || $mtime >= $cutoff) {
                    // File is recent enough — keep it
                    $skipped++;
                    continue;
                }

                $age      = (int) round((time() - $mtime) / 86400);
                $basename = basename($file);

                if ($isDryRun) {
                    $output->writeln("  <comment>[would delete]</comment> {$basename} (age: {$age}d)");
                    $deleted++;
                } else {
                    if (@unlink($file)) {
                        $output->writeln("  <info>[deleted]</info> {$basename} (age: {$age}d)");
                        $deleted++;
                    } else {
                        $output->writeln("  <error>[failed]</error> Could not delete {$basename} — check file permissions.");
                        $skipped++;
                    }
                }
            }
        }

        $output->writeln('');

        if ($isDryRun) {
            $output->writeln("<info>Dry run complete. {$deleted} file(s) would be deleted, {$skipped} kept.</info>");
        } else {
            $output->writeln("<info>Prune complete. {$deleted} file(s) deleted, {$skipped} kept.</info>");
        }

        // ── DB audit (always written, even on --dry-run) ──────────────────────
        try {
            $this->container->get(Logs::class)->log(
                'INFO',
                $isDryRun
                    ? "log:prune dry-run — {$deleted} file(s) would be deleted"
                    : "log:prune — {$deleted} file(s) deleted",
                [
                    'days'     => $days,
                    'channel'  => $channelArg,
                    'deleted'  => $deleted,
                    'skipped'  => $skipped,
                    'dry_run'  => $isDryRun,
                ]
            );
        } catch (\Throwable $e) {
            $output->writeln("<comment>Warning: could not write DB audit entry — {$e->getMessage()}</comment>");
        }

        return self::SUCCESS;
    }
}
