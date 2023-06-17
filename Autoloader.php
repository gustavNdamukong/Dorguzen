<?php

//----------------------------------------------------------------
class Autoloader
{
	const MODULE_CONFIG_DIR = __DIR__ . '/configs/';


    public static function LoadFromnamespaces(string $className)
    {
        $namespaces = explode('\\', $className);
		$class = array_pop($namespaces);
		$fileName = __DIR__ . '/' . implode('/', $namespaces) . '/' . $class . '.php';

		if(file_exists($fileName)) { 
			include_once($fileName);
		}

		//Autoload module classes which can be problematic (namespace not accurately being found)
		$fileName2 = SELF::MODULE_CONFIG_DIR . $class . '.php';
		if (
			(! isset($namespaces[0])) && 
			(file_exists($fileName2))
		) 
		{
			include_once($fileName2);
		}
    }



	public static function LoadFromDirectories(string $className)
    {
		//function loadController($className) {
			$classFolders = array('configs', 'controllers', 'DGZ_library', 'models');
			foreach ($classFolders as $folder)
			{
				$fileName = $folder .'/'. basename($className) . '.php';
				//echo $fileName.'<br>';////////
				if (file_exists($fileName))
				{
					include_once($fileName);
					//echo $fileName.'<br> included';////////
				}
			}
		//}
	}
}

spl_autoload_register('Autoloader::LoadFromnamespaces');
# You can define multiple autoloaders:
spl_autoload_register('Autoloader::LoadFromDirectories');
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
