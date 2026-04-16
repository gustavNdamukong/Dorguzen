<?php

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Config\Config;

class ConfigCommand extends AbstractCommand
{
    protected static $defaultName = 'config';
    protected static $defaultDescription = 'Display all configuration values';


    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('config')
            ->setDescription('Display all configuration values');
    }


    protected function handle(): int
    {
        $output = $this->output;

        /** @var Config $config */
        $config = $this->container->get(Config::class);

        $items = $config->all();

        if (empty($items)) {
            $output->writeln('<comment>No configuration loaded.</comment>');
            return self::SUCCESS;
        }

        foreach ($items as $group => $values) {
            $output->writeln("<info>{$group}</info>");
            $output->writeln(str_repeat('-', 50));
            $output->writeln(sprintf('%-20s %s', 'KEY', 'VALUE'));
            $output->writeln(str_repeat('-', 50));

            foreach ($values as $key => $value) {
                $value = $this->stringify($value);
                $output->writeln(sprintf('  %-20s %s', $key, $value));
            }

            $output->writeln('');
        }

        return self::SUCCESS;
    }

    protected function stringify($value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        if ($value === null) {
            return 'null';
        }

        return (string) $value;
    }
}