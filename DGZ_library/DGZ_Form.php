
<?php
##########THIS SECTION BELOW DEALS WITH FUNCTIONS PROCESSING VARIOUS FORMS EG LOGIN, LOGOUT, REGISTER ETC ETC###########
// This script defines any functions required by the various forms.
// This script is created in Chapter 4.

// This function generates a form INPUT or TEXTAREA tag.
// It takes three arguments:
// - The name to be given to the element.
// - The type of element (e.g. text, password, textarea).
// - An array of errors.

class DGZ_Form
{

    public static function create_input($form_name, $form_element_type, $errors) 
    {

            // Assume no value already exists:
            $value = false;

            // Check for a value in POST:
            if (isset($_POST[$form_name])) $form_value = $_POST[$form_name];

            // Strip slashes if Magic Quotes is enabled:
            if ($form_value && get_magic_quotes_gpc()) $form_value = stripslashes($form_value);

            // Conditional to determine what kind of element to create:
            if ( ($form_element_type == 'text') || ($form_element_type == 'password') ) { // Create text or password inputs.

                    // Start creating the input:
                    echo '<input type="' . $form_element_type . '" name="' . $form_name . '" id="' . $form_name . '"';

                    // Add the value to the input:
                    if ($form_value) echo ' value="' . htmlspecialchars($form_value) . '"';

                    // Check for an error:
                    if (array_key_exists($form_name, $errors)) {
                            echo 'class="error" /> <span class="error">' . $errors[$form_name] . '</span>';
                    } else {
                            echo ' />';		
                    }

            } elseif ($form_element_type == 'textarea') { // Create a TEXTAREA.

                    // Display the error first: 
                    if (array_key_exists($form_name, $errors)) echo ' <span class="error">' . $errors[$form_name] . '</span>';

                    // Start creating the textarea:
                    echo '<textarea name="' . $form_name . '" id="' . $form_name . '" rows="5" cols="75"';

                    // Add the error class, if applicable:
                    if (array_key_exists($form_name, $errors)) {
                            echo ' class="error">';
                    } else {
                            echo '>';		
                    }

                    // Add the value to the textarea:
                    if ($form_value) echo $form_value;

                    // Complete the textarea:
                    echo '</textarea>';

            } // End of primary IF-ELSE.

    } // End of the create_form_input() function.
}