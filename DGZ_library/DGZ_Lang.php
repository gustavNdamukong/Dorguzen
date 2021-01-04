<?php

namespace DGZ_library;

	use settings\Settings;

	abstract class DGZ_Lang
	{
 		protected static $default_lang = false;
	

		public function __construct()
		{
			$settings = new Settings();
			self::$default_lang = $settings->getSettings()['locale'];
		}


		/**
		 * Set the default language of your application
		 * @param $default_lang
		 */
    	public static function setDefaultLang($default_lang)
		{
			self::$default_lang = $default_lang;
		}
	

}