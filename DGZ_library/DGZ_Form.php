<?php

namespace DGZ_library;


class DGZ_Form
{

    public static function open($formName = '', $action = '', $method = '', $attributes = array())
    {
        $str = "<form ";

        if ($formName != "")
        {
                $str .= "name='".$formName."' ";
                $str .= "id='".$formName."' ";
        }

        if ($action != "")
        {
                $str .= "action='".$action."' ";
        }

        if ($method != "")
        {
            $str .= "method='".$method."' ";
        }

        if ($attributes)
        {
                foreach ($attributes as $attribute => $attributeValue)
                {
                        $str .= $attribute.'="'.$attributeValue.'"'.' ';
                }
        }

        $str .= ">";

        return $str;
    }




    public static function label($targetField, $value, $attributes = array())
    {
        $str = '<label for="' . $targetField . '" ';

        // Add any additional attributes
        if ($attributes)
        {
            foreach ($attributes as $attribute => $attributeValue)
            {
                $str .= $attribute.'="'.$attributeValue.'"'.' ';
            }
        }

        $str .= '>';
        $str .= $value.' </label>';

        return $str;
    }




    /**
     * This covers any type of field you can type data into e.g. text/password/number/email/textarea
     *
     * @param $input_name
     * @param $input_type
     * @param array $attributes
     * @return string
     */
    public static function input($input_name, $input_type, $attributes = array())
    {

        // Assume no value already exists:
        $input_value = false;

        // Check for a value in POST:
        if (isset($_SESSION['postBack'][$input_name])) {
            $input_value = $_SESSION['postBack'][$input_name];
        }

        // determine the element type to create (these days thanks to HTML5 many more input types exist beside the old text field)
        if (($input_type == 'text') || ($input_type == 'password') || ($input_type == 'number') || ($input_type == 'email')) {

            // Start creating the input:
            $str = '<input type="' . $input_type . '" name="' . $input_name . '" id="' . $input_name . '"';

            // Add the value to the input:
            if ($input_value)
                $str .= ' value="' . htmlentities($input_value, ENT_COMPAT, 'UTF-8') . '"';

            // Add any additional attributes
            if ($attributes)
            {
                foreach ($attributes as $attribute => $attributeValue)
                {
                    $str .= $attribute.'="'.$attributeValue.'"'.' ';
                }
            }

            $str .= '>';

            return $str;

        }
        // Create a TEXTAREA.
        elseif ($input_type == 'textarea')
        {
            // Start creating the textarea:
            $str = '<textarea name="' . $input_name . '" id="' . $input_name . '" ';


            // Add any additional attributes
            if ($attributes)
            {
                foreach ($attributes as $attribute => $attributeValue)
                {
                    $str .= $attribute.'="'.$attributeValue.'"'.' ';
                }
            }

            $str .= '>';

            // Add the value to the textarea:
            if ($input_value)
            {
                $str .= htmlentities($input_value, ENT_COMPAT, 'UTF-8');
            }

            // Complete the textarea:
            $str .= '</textarea>';
            return $str;

        }

    }






    public static function checkbox($input_name, $value, $multiple = false, $attributes = array())
    {
        //are we displaying multiple checkboxes for a field?
        if ($multiple) {
            $start = 0;
            for ($start = 0;$start < $multiple;$start++)
            {
                $str = '<input type="checkbox" name="' . $input_name . '[]" id="' . $value . '"';

                //is the field pre-selected
                if ((isset($_SESSION['postBack'][$input_name])) && (in_array($value, $_SESSION['postBack'][$input_name]))) {
                    $str .= " checked='checked' ";
                }

                if ($attributes)
                {
                    foreach ($attributes as $attribute => $attributeValue)
                    {
                        $str .= $attribute.'="'.$attributeValue.'"'.' ';
                    }
                }
                $str .= '>';
            }

            return $str;
        }
        else
        {
            //its a single checkbox
            $str = '<input type="checkbox" name="' . $input_name . '" id="' . $input_name . '" value="'.$value .'"';

            //is the field pre-selected
            if ((isset($_SESSION['postBack'][$input_name])) && ($_SESSION['postBack'][$input_name] == $value)) {
                $str .= " checked='checked' ";
            }

            if ($attributes)
            {
                foreach ($attributes as $attribute => $attributeValue)
                {
                    $str .= $attribute.'="'.$attributeValue.'"'.' ';
                }
            }
            $str .= '>';
            return $str;
        }

    }





    public static function radio($input_name, $value, $attributes = array())
    {
        $str = '<input type="radio" name="' . $input_name . '" id="' . $value . '"';

        //is the field pre-selected
        if ((isset($_SESSION['postBack'][$input_name])) && ($_SESSION['postBack'][$input_name] == $value)) {
            $str .= " checked='checked' ";
        }

        $str .= 'value="'.$value.'"'.' ';

        if ($attributes)
        {
            foreach ($attributes as $attribute => $attributeValue)
            {
                $str .= $attribute.'="'.$attributeValue.'"'.' ';
            }
        }

        $str .= '>';
        return $str;
    }




    /**
     * @param $selectName the name of the select field. This will also be used as its ID
     * @param $data an array of data to display in the select field, in the format of 'value => display value'
     * @param string $preSelected this will contain the string matching the value that you want preselected
     * @param bool $multipleSelect whether you want the field to be a multi-select field or not
     * @param array $attributes any attributes you wa t applied to the select tag
     * @return string the fully formed and filled with data select field
     */
    public static function select($selectName, $data, $preSelected = '', $multipleSelect = false, $attributes = array())
    {
        //track whether we have made a selection-we're gonna wanna do this only once for pre-selected & single select fields
        $selection = false;

        if ($multipleSelect)
        {
            $str = '<select name="'.$selectName.'[]" id="'.$selectName.'" multiple ';
            //add attributes
            if ($attributes)
            {
                foreach ($attributes as $attribute => $attributeValue)
                {
                    $str .= $attribute.'="'.$attributeValue.'"'.' ';
                }
            }
            $str .= '>';
            $str .= '<option value="">Select one</option>';

            //Create the content (options)
            foreach ($data as $value => $displayValue) {
                $str .= '<option value="' . $value . '" ';
                //check if preselected
                if ((isset($_SESSION['postBack'][$selectName])) && (is_array($_SESSION['postBack'][$selectName]) && (in_array($value, $_SESSION['postBack'][$selectName])))) {
                    $str .= " selected='selected' ";
                    $selection = true;
                }
                elseif (isset($_SESSION['postBack'][$selectName]))
                {
                    if ((is_array($_SESSION['postBack'][$selectName])) && (empty($_SESSION['postBack'][$selectName])))
                    {
                        if (($preSelected == $value) && ($selection === false))
                        {
                            $str .= " selected='selected' ";
                        }
                    }
                }
                else
                {
                    if (($preSelected == $value) && ($selection === false))
                    {
                        $str .= " selected='selected' ";
                    }
                }
                $str .= '>' . $displayValue . '</option>';
            }

            //close the select field
            $str .= '</select>';
        }
        else {
            $str = '<select name="' . $selectName . '" id="' . $selectName . '" ';
            //add attributes
            if ($attributes) {
                foreach ($attributes as $attribute => $attributeValue) {
                    $str .= $attribute . '="' . $attributeValue . '"' . ' ';
                }
            }
            $str .= '>';
            $str .= '<option value="">Select one</option> ';

            //Create the content (options)
            foreach ($data as $value => $displayValue) {
                $str .= '<option value="' . $value . '" ';
                //check if preselected
                if ((isset($_SESSION['postBack'][$selectName])) && ($_SESSION['postBack'][$selectName] == $value)) {
                    $str .= " selected='selected' ";
                    $selection = true;
                }
                elseif (($preSelected == $value) && ($selection == false)) {
                    $str .= " selected='selected' ";
                }

                $str .= '>' . $displayValue . '</option>';
            }

            //close the select field
            $str .= '</select>';
        }

        return $str;

    }






    /**
     * It takes an input type of either 'submit' or 'button'
     *
     * @param string $inputType
     * @param string $value
     * @param $attributes
     * @return string
     */
    public static function submit($inputType = 'submit', $value = 'Submit', $attributes = array())
    {
        if ($inputType == 'submit') {
            $str = "<input type='submit' value='".$value."' ";

            //add any extra attributes
            foreach ($attributes as $attribute => $attributeValue)
            {
                $str .= $attribute.'="'.$attributeValue.'"'.' ';
            }

            $str .= '>';
            return $str;
        }
        elseif ($inputType == 'button')
        {
            $str = "<button ";
            //add any extra attributes
            foreach ($attributes as $attribute => $attributeValue)
            {
                $str .= $attribute.'="'.$attributeValue.'"'.' ';
            }

            $str .= '>';
            $str .= $value.' </button>';
            return $str;
        }
    }




    public static function close()
    {
        $str = "</form>";

        return $str;
    }






    public static function getCsrfToken($customString = 'token')
    {
        if (empty($_SESSION['secret_key']))
        {
            //random string of 32 bytes
            $_SESSION['secret_key'] = bin2hex(random_bytes(32));
            $csrf = hash_hmac('sha256', $customString, $_SESSION['secret_key']);
        }
        else
        {
            $csrf = hash_hmac('sha256', $customString, $_SESSION['secret_key']);
        }

        $str = "<input type='hidden' name='csrf' value='".$csrf."' >";
        $_SESSION['postBack']['csrf'] = $csrf;

        return $str;
    }





    public static function validateToken($token)
    {
        if (hash_equals($_SESSION['postBack']['csrf'], $token))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}