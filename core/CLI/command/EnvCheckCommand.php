<?php 

namespace Dorguzen\Core\CLI\Command;


class EnvCheckCommand extends AbstractCommand
{
    protected static $defaultName = 'env:check';
    protected static $defaultDescription = 'Check required environment variables';


    public function __construct($container)
    {
        parent::__construct($container);

        // Ensure Symfony knows the default name explicitly
        if (empty($this->getName())) {
            $this->setName('env:check');
        }
    }

    protected function handle(): int
    { 
        $input  = $this->input;
        $output = $this->output;

        $basePath = defined('DGZ_BASE_PATH') ? DGZ_BASE_PATH : getcwd();

        $exampleFiles = [
            $basePath . '/.env.example',
            $basePath . '/.env.local.example',
        ];

        $expectedKeys = [];

        foreach ($exampleFiles as $file) {
            if (!file_exists($file)) {
                continue;
            }

            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                $line = trim($line);

                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }

                if (!str_contains($line, '=')) {
                    continue;
                }

                [$key] = explode('=', $line, 2);
                $expectedKeys[] = trim($key);
            }
        }

        $expectedKeys = array_unique($expectedKeys);

        if (empty($expectedKeys)) {
            $output->writeln('<comment>No .env.example files found. Nothing to check.</comment>');
            return 0;
        }

        $missing = [];

        foreach ($expectedKeys as $key) {
           if (!array_key_exists($key, $_SERVER)) {
                $missing[] = $key;
            }
        }

        if (empty($missing)) {
            $output->writeln('<info>✔ Environment is valid. All required variables are set.</info>');
            return 0;
        }

        $output->writeln('<error>✖ Missing environment variables:</error>');

        foreach ($missing as $key) {
            $output->writeln("  - {$key}");
        }

        return 1;
    }
}
