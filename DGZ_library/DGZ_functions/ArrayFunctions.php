<?php

namespace DGZ_library\DGZ_functions;



/**
 * Contains miscellaneous functions for working with arrays
 *
 * @author Gustav
 */
class ArrayFunctions {

	/**
	 * Gets the maximum depth of the input array. A 1-dimensional array is depth=1
	 * @param array $array Input array to measure the depth of
	 * @return int Max depth of the array
	 */
	public static function getMaxDepth(array $array) {
		$maxDepth = 1;

		foreach($array as $key => $value) {
			if(is_array($value)) {
				$depth = self::getMaxDepth($value) + 1;

				if($depth > $maxDepth) {
					$maxDepth = $depth;
				}
			}
		}

		return $maxDepth;
	}





	public static function getMaxLeaves(array $array, $ignoreNonArrayElements = true) {
		$leaves = 0;

		foreach($array as $key => $value) {
			if(is_array($value)) {
				$leaves += self::getMaxLeaves($value, $ignoreNonArrayElements);
			} elseif($ignoreNonArrayElements) {
				$leaves++;
			}
		}
		//var_dump($leaves);
		return $leaves;
	}





	/**
	 * function to check if item is in a multidimentional array
	 *
	 * Use it like so; isset($dropBasket) && (in_array_r($options, $dropBasket))) ? "selected='selected'" : ''
	 *
	 */
	function in_multidArray_r($needle, $haystack, $strict = false)
	{
		foreach ($haystack as $item)
		{
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict)))
			{
				return true;
			}
		}

		return false;
	}






	/**
	 * NOT IN USE YET!!!
	 *
	 * function to check if item is in a multidimentional array with matching indexes
	 *
	 * Use it like so; isset($dropBasket) && (in_array_r($options, $dropBasket))) ? "selected='selected'" : ''
	 *
	 * @returns boolean
	 */
	/*
	 * function in_multidArray_with_indexes($key, $needle, $haystack, $strict = false)
	{
		foreach ($haystack as $item)
		{
			if (!is_array($item)) {
				if ($strict) {
					if ((array_key_exists($key, $item)) && ($item[$key] === $needle)) {
						return true;
					}
				}
				elseif ((array_key_exists($key, $item)) && ($item[$key] == $needle)) {
					return true;
				}
			}
			elseif (is_array($item))
			{
				$this->in_multidArray_with_indexes($key, $needle, $item, $strict);
			}
		}

		return false;
	}
	*/


}
