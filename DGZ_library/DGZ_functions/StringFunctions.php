<?php

namespace DGZ_library\DGZ_functions;

/**
* Description of StringFunctions
*
* @author Gustav Ndamukong
*/
class StringFunctions {

/**
* @description standard startsWidth function that doesn't seem to exist in php
* @usage  if (/StringFunctions::startsWith($this->getSellerType(), 'Bus')==true)<br>
return "is a business";
* @param string $haystack
* @param string $needle
* @return boolean
*/
public static function startsWith($haystack, $needle) {
return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}


}
