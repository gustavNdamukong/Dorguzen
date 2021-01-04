<?php

namespace DGZ_library\DGZ_functions;

/**
 * Utility functions for working with numbers which represent currency values.
 *
 * @author Gustav Ndamukong
 */
class CurrencyConversion {
	
	/**
	 * Formats a currency value.
	 * 
	 * Does mathematical rounding (not truncation) and then formats to always
	 * show 2 decimal places. Non numerics are turned to null.
	 * 
	 * @param mixed $value The value to format
	 * @return string A formatted number shown to 2dp.
	 */
	public static function to2dp($value) {
		
		if(is_numeric($value)) {
			return number_format(round($value, 2), 2, '.', '');
		} else {
			return null;
		}
							
	}
	
	/**
	 * Formats a currency value, and coalesces non-numeric values to 0.00
	 * (so for example the return of this can be used to feed a numeric field in Merlin)
	 * 
	 * @param mixed $value The value to convert
	 * @return float The corresponding value, or 0.00 if the input is not numeric.
	 */
	public static function to2dpNotNull($value) {
	
		$value = self::to2dp($value);
		
		if(!$value) {
			return 0.00;
		} else {
			return $value;
		}
	}
	
	
}
