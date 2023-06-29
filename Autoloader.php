<?php


class Autoloader
{
	const MODULES_DIR = __DIR__ . '/modules/';
	const MODULES_CONFIG_DIR = __DIR__ . '/configs/';
	const MODULES = [
		'seo'
	];


    public static function LoadFromnamespaces(string $className)
    {
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
	 *  Note that module models & controller classes are stored separately from the core directories in 
	 * 		'modules/moduleName/models/' so autoload them too. For example:
	 * 
	 *		$modulePath = $_SERVER['DOCUMENT_ROOT'].'/'.$config->getFileRootPath().'/modules/'. 
	 *			strtolower($get_input).'/controllers/'.ucfirst($get_input) . '.php';
	 */
	public static function LoadFromDirectories(string $className)
    {
		$classFolders = array('controllers', 'DGZ_library', 'models'); 
		foreach ($classFolders as $folder)
		{
			$fileName = $folder .'/'. basename($className) . '.php';
			if (file_exists($fileName))
			{
				include_once($fileName);
			}
		}
	}


	public static function LoadModules(string $className)
    {
		foreach (SELF::MODULES as $module) 
		{
			if ($className !== 'Config')
			{
				//Autoload both module models & controllers
				$moduleModel = SELF::MODULES_DIR . strtolower($module).'/models/'.basename($className) . '.php';
				$moduleController = SELF::MODULES_DIR . strtolower($module).'/controllers/'.basename($className) . '.php';
				
				if (file_exists($moduleModel))
				{
					include_once($moduleModel);
				}
				if (file_exists($moduleController))
				{
					include_once($moduleController);
				}
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

require_once('DGZ_library/DGZ_Router.php');
