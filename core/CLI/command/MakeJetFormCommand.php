<?php 

namespace Dorguzen\Core\CLI\Command;

use Symfony\Component\Console\Input\InputArgument;
use RuntimeException;

class MakeJetFormCommand extends AbstractCommand
{
    protected static $defaultName = 'make:jetform';
    protected static $defaultDescription = 'Create a reusable JetForm class';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('make:jetform')
            ->setDescription('Generate a reusable JetForm')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the JetForm class (e.g. ContactForm)'
            );
    }

    protected function handle(): int
    {
        $name = trim($this->input->getArgument('name'));

        if (!preg_match('/^[A-Z][A-Za-z0-9_]*$/', $name)) {
            throw new RuntimeException(
                'JetForm class name must be StudlyCase (e.g. ContactForm)'
            );
        }

        $className = $name;
        $filePath  = DGZ_BASE_PATH . '/src/forms/' . $className . '.php';

        if (file_exists($filePath)) {
            throw new RuntimeException("JetForm already exists: {$filePath}");
        }

        // Convert ContactForm → contact_form
        $formName = strtolower(
            preg_replace('/(?<!^)[A-Z]/', '_$0', str_replace('Form', '', $className))
        );

        $stub = $this->buildStub($className, $formName);

        // Ensure directory exists
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0775, true);
        }

        // Write file using plain PHP
        file_put_contents($filePath, $stub);

        $this->output->writeln("<info>JetForm created:</info> {$filePath}");
        $this->output->writeln("<comment>Don't forget to register it in bootstrap/app.php</comment>");

        return self::SUCCESS;
    }

    /**
     * Build the JetForm class stub.
     */
    protected function buildStub(string $className, string $formName): string
    {
        return <<<PHP
<?php

namespace Dorguzen\\Forms;

use Dorguzen\\Core\\JetForms\\JetForms;

/**
 * {$className}
 *
 * JetForms are reusable, self-contained form objects.
 * They define:
 *  - Validation rules
 *  - Error messages
 *  - Submission handler
 *  - Where to redirect back on validation failure
 *
 * IMPORTANT:
 * After creating this form, you MUST register it with the JetForms registry 
 * (core/jetForms/JetFormsRegistry) in bootstrap/app.php
 *
 * Example:
 *
 *   \$container->get(JetFormsRegistry::class)
 *       ->register('{$formName}', {$className}::class);
 * 
 * The first argument to register() must exactly match your form name '{$formName}'. 
 * The second argument must be the fully qualified class name.
 */
class {$className} extends JetForms
{
    /**
     * Unique form name.
     * This MUST match the name used when registering the form.
     * It is automatically injected into the form as a hidden field (_form_name).
     */
    public string \$name = '{$formName}';

    /**
     * Route that handles the form submission.
     * Must be a valid Dorguzen route.
     */
    public string \$handler = 'data/handle-{$formName}';

    /**
     * HTTP method used to submit the form.
     */
    public string \$method = 'POST';

    /**
     * Route to redirect back to if validation fails.
     * This should be the route that renders the form.
     */
    public string \$redirectBack = 'data/{$formName}';

    /**
     * Validation rules (DGZ_Validator syntax).
     */
    protected array \$rules = [
        // 'field' => 'required|min:3',
    ];

    /**
     * Custom validation error messages.
     */
    protected array \$messages = [
        // 'field.required' => 'This field is required.',
    ];

    /**
     * Define the form fields using DGZ_Form helpers.
     *
     * Example helpers available:
     *  - self::label()
     *  - self::input()
     *  - self::select()
     *  - self::submit()
     *
     * \$this->data contains previously submitted values
     * and is automatically populated after validation failure.
     */
    protected function renderFields(): void
    {
        self::label('example', 'Example field');
        self::input('example', 'text', ['class' => 'form-control'], \$this->data['example'] ?? null);

        echo '<br>';

        self::submit('submit', 'Submit', ['class' => 'btn btn-primary']);
    }
}

PHP;
    }
}