<?php


//----------------------------------------------------------------
class Autoloader
{
	const MODULES_DIR = __DIR__ . '/modules/';
	const MODULES_CONFIG_DIR = __DIR__ . '/configs/';
	const MODULES = [
		'seo'
	];


    public static function LoadFromnamespaces(string $className)
    {
		//$config = new Config();
        $namespaces = explode('\\', $className);
		$class = array_pop($namespaces);
		$fileName = __DIR__ . '/' . implode('/', $namespaces) . '/' . $class . '.php';

		if(file_exists($fileName)) { 
			include_once($fileName);
		}

		//Autoload module config classes which can be problematic (namespace not accurately being found)
		$fileName2 = SELF::MODULES_CONFIG_DIR . $class . '.php';
		if (
			(! isset($namespaces[0])) && 
			(file_exists($fileName2))
		) 
		{
			include_once($fileName2);
		}
    }


	/**
	 *  Note that module models & controller classes are stored separatelyfrom the core directories in 
	 * 		'modules/moduleName/models/' so autoload them too. For example:
	 * 
	 *		$modulePath = $_SERVER['DOCUMENT_ROOT'].'/'.$config->getFileRootPath().'/modules/'. 
	 *			strtolower($get_input).'/controllers/'.ucfirst($get_input) . '.php';
	 */
	public static function LoadFromDirectories(string $className)
    {
		/////$config = new Config();
		/////$classFolders = array('configs', 'controllers', 'DGZ_library', 'models');
		//WE ALREADY LOADED CONFIGS IN THE PREVIOUS METHOD ABOVE
		$classFolders = array('controllers', 'DGZ_library', 'models'); /////'modules/'.$className.'/models', 'modules/'.$className.'/controllers');
		foreach ($classFolders as $folder)
		{
			$fileName = $folder .'/'. basename($className) . '.php';
			//echo $fileName .' -- <br>';////////
			if (file_exists($fileName))
			{
				//echo $fileName .' -- AFTER CHECKING, BUT b4 INCLUSION <br>';////////
				include_once($fileName);
				
				//echo $fileName .' -- EXISTS <br>';////////
			}
		}
	}


	public static function LoadModules(string $className)
    {
		//$config = new Config(); //$config::MODULES_DIR 
		/////$classFolders = array('configs', 'controllers', 'DGZ_library', 'models', 'modules/'.$className.'/models', 'modules/'.$className.'/controllers');
		foreach (SELF::MODULES as $module) 
		{
			if ($className !== 'Config')
			{
				//Autoload both models & controllers
				$moduleModel = SELF::MODULES_DIR . strtolower($module).'/models/'.basename($className) . '.php';
				$moduleController = SELF::MODULES_DIR . strtolower($module).'/controllers/'.basename($className) . '.php';
				
				if (file_exists($moduleModel))
				{
					include_once($moduleModel);

					//echo $moduleModel .' -- EXISTS & HAS NOW BEEN INCLUDED <br>';////////
					
				}
				if (file_exists($moduleController))
				{
					include_once($moduleController);
					
					//echo $moduleController .' -- EXISTS & HAS NOW BEEN INCLUDED <br>';////////
					
				}
				//die($moduleModel .' -- BEFORE TRYING TO INCLUDE <br>');////////
				//die($moduleController .' -- BEFORE TRYING TO INCLUDE <br>');////////
			}
		}
	}
}

spl_autoload_register('Autoloader::LoadFromnamespaces');
# You can define multiple autoloaders:
spl_autoload_register('Autoloader::LoadFromDirectories');
# Autoload all external modules
spl_autoload_register('Autoloader::LoadModules');
# etc
//----------------------------------------------------------------

/*spl_autoload_register('autoload');
define('MODULE_CONFIG_DIR', __DIR__ . '/configs/');

function autoload($className) {
	// Try loading the file assuming the file path matches the namespace
	$namespaces = explode('\\', $className);
	$class = array_pop($namespaces);
	$fileName = __DIR__ . '/' . implode('/', $namespaces) . '/' . $class . '.php';
	//echo ($fileName != "/Applications/XAMPP/xamppfiles/htdocs/Dorguzen/configs/Config.php") ? $fileName.'<br>' : '';////////

	/*if(file_exists($fileName)) {
		include_once($fileName);
	}
	if (isset($namespaces[0])) {
		echo $fileName .' -- BEFORE CHECKING (Namespace is: '.$namespaces[0].') <br>';////////
	}
	echo 'Classname is: --- '.$className.' <br>';////////
	

	if(file_exists($fileName)) { // "/Applications/XAMPP/xamppfiles/htdocs/Dorguzen/configs/Gustav.php"
		/////echo $fileName .' -- AFTER CHECKING, BUT b4 INCLUSION <br>';////////
		include_once($fileName);
		/////echo $fileName .' -- EXISTS & HAS NOW BEEN INCLUDED <br>';////////
	}

	//-----------------------------
	//Autoload module classes which can be problematic (namespace not accurately being found)
	$fileName2 = MODULE_CONFIG_DIR . $class . '.php';
	if (
		(! isset($namespaces[0])) && 
		(file_exists($fileName2))
	) 
	{
		//echo $fileName2 .' -- AFTER CHECKING, BUT b4 INCLUSION <br>';////////
		include_once($fileName2);
		//echo $fileName2 .' -- EXISTS & HAS NOW BEEN INCLUDED <br>';////////
	}
	//------------------------------
}*/

require_once('DGZ_library/DGZ_Router.php');

/*HERE IS AN EXAMPLE OF HOW CLASSES ARE BEING AUTOLOADED:

echo ($fileName != "/Applications/XAMPP/xamppfiles/htdocs/Dorguzen/configs/Config.php") ? $fileName.'<br>' : '';////////
OR
echo $fileName.'<br>';////////

/Applications/XAMPP/xamppfiles/htdocs/Dorguzen/configs/Config.php
/Applications/XAMPP/xamppfiles/htdocs/Dorguzen/controllers/AdminController.php
/Applications/XAMPP/xamppfiles/htdocs/Dorguzen/DGZ_library/DGZ_Controller.php
/Applications/XAMPP/xamppfiles/htdocs/Dorguzen/DGZ_library/DGZ_Displayable.php
/Applications/XAMPP/xamppfiles/htdocs/Dorguzen/DGZ_library/DGZ_Application.php
/Applications/XAMPP/xamppfiles/htdocs/Dorguzen/DGZ_library/DGZ_Translator.php
/Applications/XAMPP/xamppfiles/htdocs/Dorguzen/DGZ_library/DGZ_Lang.php
/Applications/XAMPP/xamppfiles/htdocs/Dorguzen/middleware/Middleware.php
/Applications/XAMPP/xamppfiles/htdocs/Dorguzen/DGZ_library/DGZ_View.php
/Applications/XAMPP/xamppfiles/htdocs/Dorguzen/DGZ_library/DGZ_HtmlView.php
/Applications/XAMPP/xamppfiles/htdocs/Dorguzen//Gustav.php*/
