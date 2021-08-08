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
        echo $str;
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
        echo $str;
    }




    /**
     * This covers any type of field you can type data into e.g. text/password/number/email/textarea
     *
     * @param $input_name
     * @param $input_type
     * @param array $attributes
     * @param string $input_type String to hold the text to pre-populate the field if there is any
     * @return string
     */
    public static function input($input_name, $input_type, $attributes = array(), $input_value = false)
    {
        // Check for a value in POST:
        if (isset($_SESSION['postBack'][$input_name])) {
            $input_value = $_SESSION['postBack'][$input_name];
        }

        // determine the element type to create
        //In case of a textarea
        if ($input_type == 'textarea')
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
            echo $str;

        }
        else
        {
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
            echo $str;

        }


    }





    public static function hidden($input_name, $value)
    {
        $str = '<input type="hidden" name="' . $input_name . '" id="' . $input_name . '" value="'.$value .'" />';
        echo $str;
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

            echo $str;
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
            echo $str;
        }

    }





    public static function radio($input_name, $value, $attributes = array(), $preselected = '')
    {
        $str = '<input type="radio" name="' . $input_name . '" id="' . $value . '"';

        //is the field pre-selected
        if ((isset($_SESSION['postBack'][$input_name])) && ($_SESSION['postBack'][$input_name] == $value)) {
            $str .= " checked='checked' ";
        }
        elseif ($value == $preselected)
        {
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
        echo $str;
    }




    /**
     * @param $selectName the name of the select field. This will also be used as its ID
     * @param $data an associative array of data to display in the select field in 'key => value' pairs where the keys will be the option values, & values the option text shown to the user.
     * @param array $preSelected this will contain a numerically-indexed, single-level array of strings matching the value(s) that you want preselected
     * @param bool $multipleSelect whether you want the field to be a multi-select field or not
     * @param array $attributes any attributes you want applied to the select tag
     * @return string the created select field
     */
    public static function select($selectName, $data, $preSelected = [], $multipleSelect = false, $attributes = array())
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
                if (is_array($displayValue))
                {
                    foreach ($displayValue as $displayVals => $displayVal)
                    {
                        $str .= '<option value="' . $displayVals . '" ';
                        //check if preselected
                        if ((isset($_SESSION['postBack'][$selectName])) && (is_array($_SESSION['postBack'][$selectName]) && (in_array($displayVals, $_SESSION['postBack'][$selectName]))))
                        {
                            $str .= " selected='selected' ";
                            $selection = true;
                        }
                        elseif (isset($_SESSION['postBack'][$selectName]))
                        {
                            if ((is_array($_SESSION['postBack'][$selectName])) && (empty($_SESSION['postBack'][$selectName])))
                            {
                                //This is where this foreach conditional ends coz it is abt the postBack being an array & if the array is empty then we have no further checks to do
                                if ((in_array($displayVals, $preSelected)) && ($selection === false))
                                {
                                    $str .= " selected='selected' ";
                                }
                            }
                        }
                        else
                        {
                            //if there is no postBack, then lets check if the developer has passed in values to be preselected by default
                            // Note that we only check for matches between the keys (option values) of the select field and the preselected array
                            if ((in_array($displayVals, $preSelected)) && ($selection === false))
                            {
                                $str .= " selected='selected' ";
                            }
                        }
                        $str .= '>' . $displayVal . '</option>';
                    }
                }
                else {
                    //The data provided is not a multidimensional array
                    $str .= '<option value="' . $displayValue . '" ';
                    //check if preselected
                    if ((isset($_SESSION['postBack'][$selectName])) && (is_array($_SESSION['postBack'][$selectName]) && (in_array($value, $_SESSION['postBack'][$selectName])))) {
                        $str .= " selected='selected' ";
                        $selection = true;
                    }
                    elseif (isset($_SESSION['postBack'][$selectName]))
                    {
                        //if there is postBack but it is empty
                        if ((is_array($_SESSION['postBack'][$selectName])) && (empty($_SESSION['postBack'][$selectName]))) {
                            if ((in_array($value, $preSelected)) && ($selection === false)) {
                                $str .= " selected='selected' ";
                            }
                        }
                    }
                    else {
                        //if there is no postBack
                        if ((in_array($value, $preSelected)) && ($selection === false)) {
                            $str .= " selected='selected' ";
                        }
                    }
                    $str .= '>' . $displayValue . '</option>';
                }
            }

            //close the select field
            $str .= '</select>';
        }
        else {
            //it is a single select field-note that we still have an array to contain any existing single preselected value
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
                if (is_array($displayValue)) {
                    foreach ($displayValue as $displayValueArrayKey => $displayValueArrayVal) {
                        $str .= '<option value="' . $displayValueArrayKey . '" ';
                        //check if preselected
                        if ((isset($_SESSION['postBack'][$selectName])) && (is_array($_SESSION['postBack'][$selectName]) && (in_array($displayValueArrayKey, $_SESSION['postBack'][$selectName])))) {
                            $str .= " selected='selected' ";
                            $selection = true;
                        }
                        elseif (isset($_SESSION['postBack'][$selectName])) {
                            if ((is_array($_SESSION['postBack'][$selectName])) && (empty($_SESSION['postBack'][$selectName]))) {
                                if ((in_array($displayValueArrayKey, $preSelected)) && ($selection === false)) {
                                    $str .= " selected='selected' ";
                                }
                            }
                        }
                        else {
                            if ((in_array($displayValueArrayKey, $preSelected)) && ($selection === false)) {
                                $str .= " selected='selected' ";
                            }
                            elseif (in_array($displayValueArrayKey, $preSelected))
                            {
                                $str .= " selected='selected' ";
                            }
                        }
                        $str .= '>' . $displayValueArrayVal . '</option>';
                    }
                }
                else {

                    $str .= '<option value="' . $value . '" ';
                    //check if preselected
                    if ((isset($_SESSION['postBack'][$selectName])) && ($_SESSION['postBack'][$selectName] == $value)) {
                        $str .= " selected='selected' ";
                        $selection = true;
                    }

                    if ((in_array($value, $preSelected)) && ($selection === false)) {
                        $str .= " selected='selected' ";
                    }

                    $str .= '>' . $displayValue . '</option>';
                }
            }

            //close the select field
            $str .= '</select>';
        }

        echo $str;

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
            echo $str;
        }
        elseif ($inputType == 'button')
        {
            $hrefValue = '';
            $str = "<button ";
            //add any extra attributes
            foreach ($attributes as $attribute => $attributeValue)
            {
                if ($attribute != 'href') {
                    $str .= $attribute . '="' . $attributeValue . '"' . ' ';
                }
                else
                {
                    $hrefValue = $attributeValue;
                }
            }

            $str .= '>';
            if(array_key_exists('href', $attributes)) {
                $str .= "<a href='$hrefValue'>$value</button></a>";
            }
            else
            {
                $str .= $value . ' </button>';
            }

            echo $str;
        }
    }




    public static function close()
    {
        $str = "</form>";
        echo $str;
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

        echo $str;
    }





    public static function validateToken($token)
    {
        if (hash_equals($_SESSION['postBack']['csrf'], $token))
        {
            unset($_SESSION['postBack']);
            return true;
        }
        else
        {
            return false;
        }
    }

}