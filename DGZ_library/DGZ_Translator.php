<?php

namespace DGZ_library;

use settings\Settings;


class DGZ_Translator extends \DGZ_library\DGZ_Lang
{


    public function __construct()
    {
        parent::__construct();
    }


    /**
     * This is the setter meth that sets the default language of your application, and it is called from the config file, the one place where that variable will be
     * set and this setter meth on this main (parent) abstract translation class will be called. Because the next method following this one which is a getter method
     * that that ddetermines the language used by the site will first of all check for the existence of a session lang	variable; if the user has clicked on a language
     * flag to choose a language, that choice will override the value of this $default_lang property. The fact that this public static function here can be accessed
     * from outside the class-in this case from the config.inc.php file to set the default language for the site, is a pointer to the fact that code may not be able
     * to accessed a property secured under encapsulation, but it can still do so only by passing through a setter method. You should
     * therefore implement your behaviour (rules e.g. validation rules) for accessing your protected property.
     *
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
            $settings = new Settings();
            $defaultLang = $settings->getSettings()['fallback_locale'];
            $langSourceFile = include("lang/$defaultLang/$file");
        }

        return $langSourceFile[$phrase];
    }


}
	

