<?php

namespace DGZ_library;

	use configs\Config;

	abstract class DGZ_Lang
	{
 		protected static $default_lang = false;
	

		public function __construct()
		{
			$config = new Config();
			self::$default_lang = $config->getConfig()['locale'];
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