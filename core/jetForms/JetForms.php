<?php

namespace Dorguzen\Core\JetForms;

use Dorguzen\Core\DGZ_Form;
use Dorguzen\Core\DGZ_Validator;

/**
 * JetForms — extend this for each custom/reusable form.
 *
 * Fields:
 *  - $name (string) unique form identifier (required)
 *  - $handler (callable|string|null) optional handler to be called on success
 *  - $method (string) type of HTTP request to submit the form (required)
 *  - $rules (array) validation rules in DGZ_Validator format
 *  - $messages (array) custom messages keyed like 'field.rule' or 'rule'
 * 
 *  -The following 4 are abstract properties (since PHP 8.1) that children forms must declare:
 *      -$name 
 *      -$handler 
 *      -$method 
 *      -$redirectBack
 *
 * Methods:
 *  - render(): returns HTML (you can echo too)
 *  - fill(array $data): set internal values (for re-populating)
 *  - validate(): runs validator and returns DGZ_Validator instance
 *  - getValidated(): validated data (after validate() passes)
 */
abstract class JetForms extends DGZ_Form
{
    /** 
     * $name is very important. It must be the name used as the key to register your 
     * form class in bootstrap/app.php If those two do not match, your form will not work 
     * This unique string name value must be used in a hidden input field by the name: 
     * '_form_name' (see the render() method below).
     *  
     */
    protected string $name;


    protected $id = '';

    /** handler - it would be used in the 'action' attribute of the form
     * May used by middleware to forward on successful validation. 
     * This must be a valid route to the form handler 
     * usually a controller to process the form. 
    */
    protected string $handler;


    /** the HTTP method to be used in submitting the form */
    protected string $method;


    protected array $extraHiddenFields = [];


    /** 
     * redirectTo string. Path to send the user back to if validation fails.   
     * This must be the valid route to the view that displays the form. 
     * This is how the application knows which view the form was submitted from.
     */
    protected string $redirectBack; 


    /** rules array for DGZ_Validator */
    protected array $rules = [];


    /** custom messages */
    protected array $messages = [];


    /** filled input data (raw request payload) */
    protected array $data = []; 


    /** validated data returned after successful validation */
    protected array $validated = [];



    /**
     * Optionally call this parent constructor from any child Jet form to pass in an array of field-value 
     * data. It's ideal for same data processing or testing. You can achieve the same thing when creating 
     * a form by calling fill($data_array), just before you render the form.
     * @param array $initialData
     */
    public function __construct(array $initialData = [])
    {
        if ($initialData) {
            $this->fill($initialData);
        }
        else if (isset($_SESSION['old_input']))
        {
            $this->fill($_SESSION['old_input']); 
        }
    }


    /**
     * fill form with input (e.g. from request()->post() or test sample data). 
     */
    public function fill(array $data): void 
    {
        $this->data = $data;
    }

     
    public function setName($name)
    {
        $this->name = $name;
    }


    /**
     * get the form name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    public function setMethod($method)
    {
        $this->method = $method;
    }


    public function getMethod()
    {
        return $this->method;
    }


    /**
     * Return handler (callable or controller@method)
     */
    public function getHandler()
    {
        return $this->handler;
    }


    public function setHandler($handler)
    {
        $this->handler = $handler;
    }


    public function getRedirectBack()
    {
        return $this->redirectBack;
    }


    public function setRedirectBack($redirectBack)
    {
        $this->redirectBack = $redirectBack;
    }


    /**
     * Return filled input (raw)
     */
    public function getData(): array
    {
        return $this->data;
    }


    /**
     * Return validated data after successful validate()
     */
    public function getValidated(): array
    {
        return $this->validated;
    }


    /**
     * Return rules array (allow override)
     */
    public function getRules(): array
    {
        return $this->rules;
    }


    /**
     * Return custom messages
     */
    public function getMessages(): array
    {
        return $this->messages;
    }



    public function addHiddenField(string $field, string $value)
    {
        $this->extraHiddenFields[$field] = $value;
    }




    /**
     * Validate current $this->data using DGZ_Validator.
     * On success sets $this->validated and returns validator instance (passes() true).
     * On fail returns validator instance — middleware will then catch (then check for fails() == true on it) and act.
     */
    public function validate(): DGZ_Validator
    {
        $validator = new DGZ_Validator($this->data, $this->getRules(), $this->getMessages());
        $validator->validate();
        if ($validator->passes()) {
            // You can optionally filter/normalize data here (for now we simply keep the raw data)
            $this->validated = $this->data;
        }
        return $validator;
    }



    /**
     * render creates the actual form. Your individual class must implement renderFields() that builds the fields 
     * using Dorguzen\Core\DGZ_Form helpers).
     * You can override this render() method from within the specific form class if you want full control.
     * 
     * For the re-usable forms to work, the following three fields with these specific names must be created 
     * as hidden fields. We provide them here for you, but if you override render() within your specific form 
     * class, make sure you implement them:
     * 
     *      '_form_name'     -- its value is the $name property
     *      '_handler'       -- its value is the $handler property (actually the value of the 'action' attribute)
     *      '_redirectBack'  -- its value is the $redirectBack property
     * 
     * @param array $attributes optional attributes meant ONLY for the opening form tag.
     *      Attributes for individual fields should be passed in the field methods in the 
     *      renderFields() of the specific form. The attributes you pass in $attributes 
     *      will override the values set in $action, $id, $action, $method etc. 
     *  The values you need to pass into $attributes are:
     *      name
     *      action
     *      method
     *      id (omit to use the same value as the name property)
     * 
     * But we always include the _form_name hidden input.
     *   Use DGZ_Form helper to open + close; developer decides action/method in view.
     *   Example usage in view:
     *        $form = new ContactForm();
     *        $form->render(['action' => '/contact/submit', 'id' => 'my-form', 'method' => 'post']);
     *   OR
     *        $form = new \src\forms\ContactForm();
     *        $form->setHandler('seo/test-contact-form-from-module'); 
     *        $form->setRedirectBack('seo');
     *        $form->render(['class'=>'my-form']); 
     * 
     *  OR just keep it simple and do this: 
     * 
     *        $form = new \src\forms\ContactForm();
     *        $form->render(['class'=>'my-form']);
     * 
     *      to use the default values set in the form class for
     *      -name 
     *      -handler (action) 
     *      -method     
     */
    public function render(array $attributes = [])
    {
        $this->normalizeOpenTagAttributes($attributes);
        
        self::open($this->name, $this->handler, $this->method, $attributes);
         
        $formName = $this->name;
        $handler = $this->handler;
        $redirectBack = $this->redirectBack;

        // create the 3 essential fields
        self::hidden('_form_name', $formName);
        self::hidden('_handler', $handler);
        self::hidden('_redirectBack', $redirectBack);
        if (!empty($this->extraHiddenFields))
        {
            foreach ($this->extraHiddenFields as $key => $extraField)
            {
                self::hidden($key, $extraField);
            }
        }
        $this->renderFields();
        self::close();
    }


    /**
     * $attributes will contain something like this: 
     * 
     *  ['action' => '/contact/submit', 'method' => 'post']
     * The values you need to pass for the form tag are:
     *  name
     *  action
     *  method
     *  id (omit to use the same value as the name property/attribute)
     * 
     * @param mixed $attributes
     * @return void
     */
    private function normalizeOpenTagAttributes(&$attributes)
    {
        if (empty($attributes))
        {
            if ($this->id ==  "")
            {
                $this->id = $this->name;
            }
            return;
        }
        else 
        {
            if (isset($attributes['name']) && $attributes['name'] != '')
            {
                $this->name = $attributes['name'];
                unset($attributes['name']);
            }

            if (isset($attributes['action']) && $attributes['action'] != '')
            {
                $this->handler = $attributes['action'];
                unset($attributes['action']);
            }

            if (isset($attributes['method']) && $attributes['method'] != '')
            {
                $this->method = $attributes['method'];
                unset($attributes['method']);
            }
        }
    }



    /**
     * Implement in subclass to echo/render the fields using DGZ_Form::input/select/etc
     * Be4cause these children form classes extend this class, they are grandchildren 
     * of DGZ_Form so they can build form fields with the syntax: self::input(...) etc
     */
    abstract protected function renderFields(): void;
}