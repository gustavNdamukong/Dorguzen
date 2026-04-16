<?php

namespace Dorguzen\Core;



class DGZ_Form
{

    protected array $old = []; 


    protected array $errors = [];


    public static function getInstance(): self
    {
        return self::$instance ?? new self();
    }


    public static function open($formName = '', $action = '', $method = '', $attributes = [])
    {
        $str = "<form ";

        if ($formName != "")
        {
            $str .= "name='".$formName."' ";
            if (!isset($attributes['id']))
            {
                $str .= "id='{$formName}' ";
            }
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
        $str .= "<input type='hidden' name='_csrf_token' value='".getCsrfToken()."'>";
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
     * This covers any type of field you can type data into e.g. text/password/number/email/date/textarea
     *
     * @param $input_name
     * @param $input_type
     * @param array $attributes
     * @param string $input_type String to hold the text to pre-populate the field if there is any
     * @return string
     */
    public static function input($input_name, $input_type, $attributes = [], $input_value = false)
    {
        // Check for a value in POST:
        if (SELF::getOldValue($input_name != null))
        {
            $input_value = SELF::getOldValue($input_name);
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
        $str = '<input type="hidden" name="' . $input_name . '" id="' . $input_name . '" value="'.htmlentities($value, ENT_COMPAT, 'UTF-8') .'" />';
        echo $str; 
    }



    /**
     * Summary of checkbox
     * @param mixed $input_name
     * @param mixed $value
     * @param mixed $multiple
     * @param mixed $attributes
     * @return void
     */
    public static function checkbox($input_name, $value, $multiple = false, $attributes = array())
    {
        //are we displaying multiple checkboxes for a field?
        if ($multiple) 
        {
            $start = 0;
            for ($start = 0;$start < $multiple;$start++)
            {
                $str = '<input type="checkbox" name="' . $input_name . '[]" id="' . $value . '"';

                // if the field is pre-selected
                if (SELF::getOldValue($input_name) != null && in_array($value, SELF::getOldValue($input_name)))
                {
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
            if (SELF::getOldValue($input_name) != null && SELF::getOldValue($input_name) == $value)
            {
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



    /**
     * Summary of radio
     * @param mixed $input_name
     * @param mixed $value
     * @param mixed $attributes
     * @param mixed $preselected
     * @return void
     */
    public static function radio($input_name, $value, $attributes = array(), $preselected = '')
    {
        $str = '<input type="radio" name="' . $input_name . '" id="' . $value . '"';

        // is the field pre-selected
        if (SELF::getOldValue($input_name) != null && SELF::getOldValue($input_name) == $value)
        {
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
     * About this select() method:
     *      -allows nested optgroups
     *      -supports preSelected values
     *      -escapes values (safe HTML)
     *      -supports both single & multi-select 
     * 
     *  Example use:
     * 
     *      DGZ_Form::select(
     *           'category',
     *           [
     *               'Phones' => [
     *                   'iphone' => 'Apple iPhone',
     *                   'samsung' => 'Samsung Galaxy',
     *               ],
     *               'Laptops' => [
     *                   'macbook' => 'MacBook Pro',
     *                   'lenovo' => 'Lenovo Thinkpad',
     *               ],
     *               'other' => 'Miscellaneous'
     *           ],
     *           ['iphone'], // pre-selected
     *       );
     * 
     * @param mixed $selectName the name of the select field. This will also be used as its ID
     * @param mixed $data an associative array of data to display in the select field in 'key => value' pairs where the keys will be the option values, & values the option text shown to the user
     * @param mixed $preSelected this will contain a numerically-indexed, single-level array of strings matching the value(s) that you want preselected
     * @param mixed $multipleSelect whether you want the field to be a multi-select field or not
     * @param mixed $attributes any attributes you want applied to the select tag
     * @return string containing the created select field
     */
    public static function select($selectName, $data, $preSelected = [], $multipleSelect = false, $attributes = [])
    {
        $selection = false;
        $name = $multipleSelect ? $selectName.'[]' : $selectName;

        $str = '<select name="'.$name.'" id="'.$selectName.'" ';
        foreach ($attributes as $attr => $attrVal) {
            $str .= $attr.'="'.$attrVal.'" ';
        }
        if ($multipleSelect) $str .= 'multiple ';
        $str .= '>';

        $str .= '<option value="">Select one</option>';

        foreach ($data as $key => $value) {

            // Case 1: OPTGROUP (value is array)
            if (is_array($value)) {

                $str .= '<optgroup label="'.htmlspecialchars($key).'">';

                foreach ($value as $optValue => $label) {
                    $str .= self::renderOption($selectName, $optValue, $label, $preSelected, $selection, $multipleSelect);
                }
                $str .= '</optgroup>';
                continue;
            }

            // Case 2: standard option
            $str .= self::renderOption($selectName, $key, $value, $preSelected, $selection, $multipleSelect);
        }

        $str .= '</select>';
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
        SELF::clearOldValues();
        echo $str;
    }



    private static function renderOption($fieldName, $value, $label, $preSelected, &$selection, $multipleSelect)
    {
        $selected = '';

        //PRE-SELECT — multiple
        if (
            $multipleSelect &&
            SELF::getOldValue($fieldName) != null && 
            is_array(SELF::getOldValue($fieldName)) && 
            in_array($value, SELF::getOldValue($fieldName))
        )
        {
            $selected = " selected='selected' ";
            $selection = true;
        }

        //PRE-SELECT — single
        else if (
            !$multipleSelect &&
            SELF::getOldValue($fieldName) != null && 
            SELF::getOldValue($fieldName) == $value
        )
        {
            $selected = " selected='selected' ";
            $selection = true;
        }

        //DEFAULT PRE-SELECT
        elseif (!$selection && in_array($value, $preSelected)) {
            $selected = " selected='selected' ";
        }

        return "<option value='".htmlspecialchars($value)."' $selected>".htmlspecialchars($label)."</option>";
    }



    /**
     * Fill the static old array (used when rendering fields)
     * Typical use: in controller before rendering view call:
     *   $form = new MyForm(); $form->fill($request->post()); OR
     *   DGZ_Form::setOld($_SESSION['old_input'] ?? []);
     */
    public static function setOld(array $old): void
    {
        // you already had instance-level $old; here static for backward compat rendering helpers
        $_SESSION['old_input_for_forms'] = $old;
    }



    /**
     * Retrieve an "old" value for a field: prefers session flash 'old_input' then provided fallback.
     */
    public static function getOldValue(string $key, $default = null)
    {
        // 1) Prefer explicit session used by middleware
        if (isset($_SESSION['old_input']) && array_key_exists($key, $_SESSION['old_input'])) {
            return $_SESSION['old_input'][$key];
        }
        // 2) Backwards compat postBack
        if (isset($_SESSION['postBack'][$key])) {
            return $_SESSION['postBack'][$key];
        }
        // 3) special static storage used by controller or form instances
        if (isset($_SESSION['old_input_for_forms']) && array_key_exists($key, $_SESSION['old_input_for_forms'])) {
            return $_SESSION['old_input_for_forms'][$key];
        }

        $thisClass = self::getInstance();
        return $thisClass->old[$key] ?? $default;
    }



    public static function clearOldValues()
    {
        // clear old input after success
        // 1) Prefer explicit session used by middleware
        if (isset($_SESSION['old_input'])) {
            unset($_SESSION['old_input']);
        }

        // 2) Clear previous validation errors
        if (isset($_SESSION['validation_errors'])) {
            unset($_SESSION['validation_errors']);
        }
        // 3) Backwards compat postBack 
        if (isset($_SESSION['postBack'])) {
            unset($_SESSION['postBack']);
        }
        // 4) special static storage used by controller or form instances
        if (isset($_SESSION['old_input_for_forms'])) {
            unset($_SESSION['old_input_for_forms']);
        }
    }



    /**
     * Fill form with request input (e.g. from request()->post() or test sample data).  
     */
    public function fill(array $data): void
    {
        $this->old = $data; 
    }



    /**
     * Return stored old input
     */
    public function getOld(): array
    {
        return $this->old; 
    }



    /**
     * Convenience: return value for single field
     */
    public function old(string $key, $default = null)
    {
        return $this->old[$key] ?? $default; 
    }
}