<?php

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Core\DGZ_Logger;


class LogsCommand extends AbstractCommand
{
    protected static $defaultName = 'log';
    protected static $defaultDescription = 'Display recent application logs';


    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('log')
            ->setDescription('Display recent application logs');
    }


    protected function handle(): int
    {
        $output = $this->output;

        // Fetch logs via logger (DB-based)
        $logs = DGZ_Logger::getAll('logs_created DESC');

        if (!$logs || empty($logs)) {
            $output->writeln('<comment>No logs found.</comment>');
            return self::SUCCESS;
        }

        foreach ($logs as $log) {
            $level   = strtoupper($log['level'] ?? 'INFO');
            $message = $log['message'] ?? '';
            $context = $log['context_json'] ?? null;
            $time    = $log['logs_created'] ?? '';

            $line = sprintf(
                '[%s] %-8s %s',
                $time,
                $level,
                $message
            );

            if (!empty($context)) {
                $line .= ' ' . $context;
            }

            $output->writeln($line);
        }

        // Warn about file-only channels whose entries never reach the DB
        $fileOnlyChannels = [];
        $channels = config('logging.channels') ?? [];

        foreach ($channels as $name => $config) {
            if (($config['driver'] ?? 'db') === 'file') {
                $fileOnlyChannels[] = $name;
            }
        }

        if (!empty($fileOnlyChannels)) {
            $list = implode(', ', $fileOnlyChannels);
            $output->writeln('');
            $output->writeln("<comment>Note: channel(s) [{$list}] use driver 'file' — their entries are not stored in the DB and do not appear above.</comment>");

            foreach ($fileOnlyChannels as $channel) {
                $output->writeln("<comment>      To inspect their logs from file; run this command: php dgz log:tail --channel={$channel}</comment>");
            }
        }

        return self::SUCCESS;
    }
}