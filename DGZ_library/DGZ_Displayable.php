<?php

/**
 * Description of Displayable
 *
 * @author Gustav
 */
namespace DGZ_library;




interface DGZ_Displayable {
	
	/**
	 * Return a string saying what action to call if not specified in the URL
	 */
	public function getDefaultAction();

	
	/**
	 * Return a string saying what layout to use if it is not defined in the page method
	 */
	public function getDefaultLayout();


	
	public function display($action, array $inputParameters);
	
}
