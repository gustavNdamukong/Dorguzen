<?php

namespace Dorguzen\Core\CLI\Command;

use Symfony\Component\Console\Input\InputOption;

class ServeCommand extends AbstractCommand
{
    protected static $defaultName = 'serve';
    protected static $defaultDescription = 'Start the Dorguzen built-in development server';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('serve')
            ->setDescription('Start the Dorguzen built-in development server')
            ->addOption(
                'host',
                null,
                InputOption::VALUE_OPTIONAL,
                'The hostname to listen on',
                'localhost'
            )
            ->addOption(
                'port',
                null,
                InputOption::VALUE_OPTIONAL,
                'The port to listen on',
                8000
            );
    }

    protected function handle(): int
    {
        $host = $this->input->getOption('host');
        $port = (int) $this->input->getOption('port');

        $serverScript = DGZ_BASE_PATH . '/server.php';

        if (!file_exists($serverScript)) {
            $this->output->writeln('<error>server.php not found at: ' . $serverScript . '</error>');
            return self::FAILURE;
        }

        $this->output->writeln('');
        $this->output->writeln('<info>Dorguzen development server started.</info>');
        $this->output->writeln(sprintf('  <comment>Listening on:</comment>  http://%s:%d', $host, $port));
        $this->output->writeln('  <comment>Document root:</comment> ' . DGZ_BASE_PATH);
        $this->output->writeln('  <comment>Press Ctrl+C to stop.</comment>');
        $this->output->writeln('');

        // Use the same PHP binary that launched this CLI tool, to guarantee
        // version consistency. Pass the .htaccess upload limits as -d flags
        // since .htaccess directives are not read by the built-in server.
        $command = implode(' ', [
            escapeshellcmd(PHP_BINARY),
            '-S', escapeshellarg($host . ':' . $port),
            '-d', 'upload_max_filesize=2G',
            '-d', 'post_max_size=2G',
            '-d', 'memory_limit=900M',
            escapeshellarg($serverScript),
        ]);

        // Inherit stdin/stdout/stderr so server logs stream to the terminal
        // and Ctrl+C propagates naturally to the child process.
        $descriptors = [
            0 => STDIN,
            1 => STDOUT,
            2 => STDERR,
        ];

        $process = proc_open($command, $descriptors, $pipes);

        if (!is_resource($process)) {
            $this->output->writeln('<error>Failed to start the development server.</error>');
            return self::FAILURE;
        }

        // Block here until the server process exits (e.g. user presses Ctrl+C).
        $exitCode = proc_close($process);

        $this->output->writeln('');
        $this->output->writeln('<info>Development server stopped.</info>');

        return $exitCode === 0 ? self::SUCCESS : self::FAILURE;
    }
}
