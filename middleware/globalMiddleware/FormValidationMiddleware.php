<?php
namespace Dorguzen\Middleware\GlobalMiddleware;

use Dorguzen\Core\DGZ_MiddlewareInterface;
use Dorguzen\Core\JetForms\JetFormsRegistry;
use Dorguzen\Core\DGZ_Request;
use Dorguzen\Core\Exceptions\ValidationException;

/**
 * FormValidationMiddleware
 *  -This middleware detects submitted forms (hidden _form_name),    
 *  -It resolves the form by name via FormRegistry,
 *  -fills the form class with the request data,
 *  -it validates the form based on its own defined rules,
 *  -throws ValidationException when validation fails,
 *  -the DGZ router catches the ValidationException & handles it
 *      then sets SESSION['old_input'] & SESSION['validation_errors']
 *      (which is useful in re-populating form in the next steps)
 *  -if validation passes, it sets on SESSION['old_input'], and  
 *      then and optionally calls the target handler (controller) passing it the 
 *      validated data, or lets controller handle further processing.
 * 
 * I put priority = 5 so it runs after CSRF (which you set to priority 1). Adjust ordering as desired.
 *
 * Notice we set the priority in the middleware stack = 5 so it runs after CSRF (which has priority 1). 
 * You can adjust ordering as you see fit.
 * On fail we throw new ValidationException(...).  
 * After catching this ValidationException, the router sets a session flash message & redirects to referer. 
 * If the ValidationException constructor signature differs-again, feel free to adapt accordingly.
 */
class FormValidationMiddleware implements DGZ_MiddlewareInterface
{
    public int $priority = 5;
    public string $name = 'FormValidationMiddleware';

    public function boot(): array
    {
        return [];
    }

    public function handle(string $controller, string $controllerShortName, string $method): bool
    {
        // Resolve request from container
        /** @var DGZ_Request $request */
        $request = container(DGZ_Request::class);

        // Support GET form submissions + POST + method override
        $method = strtoupper($request->method());
        $payload = ($method === 'GET') ? $request->get() : $request->post();

        // Allow method override: _method = PUT/PATCH/DELETE
        if (isset($payload['_method'])) {
            $method = strtoupper($payload['_method']);
        }

        // We only validate when a form was actually submitted
        if (!in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']))
        { 
            return true;
        }
        
    
        if (empty($payload['_form_name'])) {
            // not a reusable form submission; proceed
            return true;
        }

        $formName = $payload['_form_name'];

        // resolve registry from container or create one
        $registry = container(JetFormsRegistry::class);

        $form = $registry->resolve($formName);
        if (!$form) {
            // unknown form — not our responsibility, continue.
            return true;
        }

        // Store posted data in data property of form
        $form->fill($payload);

        // Run validation
        $validator = $form->validate();

        if ($validator->fails()) {
            // Put raw input + errors in ValidationException — router's existing catch will flash them
            $errors = $validator->errors();
            $input = $payload; // entire post payload; you can strip CSRF/_form_name if desired

            $redirectBack = $input['_redirectBack'] ?? $_SERVER['HTTP_REFERER'] ?? '/';

            // Optional: remove CSRF token and internal fields
            unset($input['_csrf_token'], $input['_form_name'], $input['_method']);

            // Optionally store old input into session now so DGZ_Form can re-populate
            $_SESSION['old_input'] = $input;
            $_SESSION['validation_errors'] = $errors;

            // Throw ValidationException — your router already handles ValidationException specially 
            // if you were to return a failed validation status code, use 422)
            throw new ValidationException(
                $errors, 
                $input, 
                $errors, 
                'Form validation failed',
                422,
                $redirectBack
                );
        }

        // validation passed — put validated data in session/request for controller to use
        $_SESSION['old_input'] = $form->getValidated();

        // Proceed to next middleware / controller
        return true;
    }
}