<?php

namespace DGZ_library;

	use settings\Settings;

	abstract class DGZ_Lang
	{
		/**
		 * can only be accessed by methods of children or instances (objects) of this class (coz of the protected keyword)
		 *
		 * @var bool
		 */
 		protected static $default_lang = false;
	

		public function __construct()
		{
			$settings = new Settings();
			self::$default_lang = $settings->getSettings()['locale'];
		}


		/**
		 * This is the setter meth that sets the default lang of the app, and it is called from the config file, the one place where that variable will be
		 * set and this setter meth on this main (parent) abstract translation class will be called. Because the next method following this one which is a
		 * getter method that that ddetermines the language used by the site will first of all check for the existence of a session lang	variable; if the
		 * user has clicked on a language flag to choose a language, that choice will override the value of this $default_lang property.
		 * The fact that this public static function here can be accessed from outside the class-in this case from the config.inc.php file to set the default
		 * language for the site, is a pointer to the fact that code may not be able to accessed a property secured under encapsulation, but it can still do
		 * so only by passing through a setter method. You should therefore implement your behaviour (rules e.g. validation rules) for accessing your protected
		 * property.
		 *
		 *
		 * @param $default_lang
		 */
    	public static function setDefaultLang($default_lang)
		{
			self::$default_lang = $default_lang;
		}
		
		
		
		public static function getCurrentLang()
 		{

		}



		public static function translate($lang, $file, $phrase)
		{

		}

			
	

}