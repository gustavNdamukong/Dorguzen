<?php

namespace Dorguzen\Core\DGZ_functions;

/**
* Description of StringFunctions
*
* @author Gustav Ndamukong
*/
class StringFunctions {

    /**
    * @description standard startsWidth function that doesn't seem to exist in php
    * @usage  if (/StringFunctions::startsWith($this->getSellerType(), 'Bus')==true)<br>
    * return "is a business";
    * @param string $haystack
    * @param string $needle
    * @return boolean
    */
    public static function startsWith($haystack, $needle) {
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }


    /**
     * This will split a text string into approximately two equal halves, but it will skip special (non-alphabetic) characters
     * @return array an array of the two separate strings
     */
    public static function splitStringIntoTwoEqualParts($data)
    {
        if (strlen($data) % 2 == 0) //if length is even number
            $length = strlen($data) / 2;
        else
            $length = (strlen($data) + 1) / 2; //adjust length

        for ($i = $length, $j = $length; $i > 0; $i--, $j++) //check towards forward and backward for non-alphabet
        {
            if (!ctype_alpha($data[$i - 1])) //forward
            {
                $point = $i; //break point
                break;
            }
            else if (!ctype_alpha($data[$j - 1])) //backward
            {
                $point = $j; //break point
                break;
            }
        }
        $string1 = substr($data, 0, $point);
        $string2 = substr($data, $point);

        return [$string1, $string2];
    }
}
