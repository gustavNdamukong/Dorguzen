<?php

namespace DGZ_library\DGZ_functions;

/**
 * Description of StringFunctions
 *
 * @author vasicj
 */
class StringFunctions {
	
	/**
	 * @description standard startsWidth function that doesn't seem to exist in php for some reason
	 * @usage  if (/Utils/StringFunctions::startsWith($this->model->salesOrder, 'IBT')==true)<br>
				   return "is IBT";
	 * @param string $haystack
	 * @param string $needle
	 * @return boolean
	 */
	public static function startsWith($haystack, $needle) {
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
	}
	
	
}
