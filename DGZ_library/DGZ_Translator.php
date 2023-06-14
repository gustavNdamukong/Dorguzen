<?php

namespace DGZ_library;

use configs\config;


class DGZ_Translator extends \DGZ_library\DGZ_Lang
{


    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Set the default language of your application
     * @param $default_lang
     */
    public static function setDefaultLang($default_lang)
    {
        self::$default_lang = $default_lang;
    }



    public static function getCurrentLang()
    {
        if (isset($_SESSION['lang']))
        {
            $lang = $_SESSION['lang'];
        }
        else
        {
            $lang = self::$default_lang;
        }
        return $lang;
    }




    /**
     * Assuming your app has a translation feature, it  takes the language the user has chosen,
     * the translation file to read from, and the specific text in the file to get.
     *
     * @param $lang the language the user has chosen
     * @param $file the translation file for the view the user is on (located at 'lang/fileName')
     * @param $phrase the text phrase you want to display to the user in the chosen language
     * @return mixed
     */
    public static function translate($lang, $file, $phrase)
    {
        if (file_exists("lang/$lang/$file")) {
            $langSourceFile = include("lang/$lang/$file");
        }
        else
        {
            $config = new config();
            $defaultLang = $config->getconfig()['fallback_locale'];
            $langSourceFile = include("lang/$defaultLang/$file");
        }

        return $langSourceFile[$phrase];
    }


}
	

