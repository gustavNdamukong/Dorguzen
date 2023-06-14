<?php

namespace DGZ_library;

use configs\Config;


/**
 * Class representing your application's configuration. It just gets all its data from the config file
 * (/configs/Config.php) so that it can relay it to different parts of your application that need to know about
 * those configuration settings.
 *
 * @author Gustav
 */
class DGZ_Application {

	protected $useFullLayout = true;
	protected $appName;
	protected $businessName;
	protected $defaultLayoutDirectory;
	protected $defaultLayout;
	/**
     * @var object contains the configs of the site. Other parts of your application like view files, 
	 * and their controllers etc need access to it
	 * This class is the only place where your application settings will be loaded. It will then be distributed
	 * to other places from here. This is because we also pull in the config data of modules, making sure your 
	 * have access to all your app's configuration data in one place. 
     */
	protected $config;


	public function __construct($useFullLayout = true, $appName = null, $layout = null) {
		$this->config = new Config();
		if(is_null($appName)) {
			$this->useFullLayout = $useFullLayout;
			$this->appName = $this->config->getConfig()['appName'];
			$this->businessName = $this->config->getConfig()['appBusinessName'];
			$this->defaultLayoutDirectory = $this->config->getConfig()['layoutDirectory'];
			$this->defaultLayout = $this->config->getConfig()['defaultLayout'];
		}

		$_SESSION['_application'] = ['appName' => $this->appName,
			'defaultLayoutDirectory' => $this->defaultLayoutDirectory,
			'defaultLayout' => $this->defaultLayout,
			'useFullLayout' => $this->useFullLayout,
		];

		//Load all site config data. This ncan be gotten from all the config files (classes) in the 'configs/'
		//directory. The class names must match their file names, and they must all have a getConfig() method that returns an array.
		$configDir = 'configs/';
		$configFiles = scandir($configDir);
		foreach ($configFiles as $file) {
			if ($file !== '.' && $file !== '..') {
			  /////$filePath = $configDir . '/' . $file;
			  $filePath = $configDir . $file;
		  
			  // Check if the file is a PHP file
			  if (is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
				require_once $filePath;
				//die('class & path has been required '.$filePath);/////////// WORKS: 'configs/Config.php'

				// Get the class name from the file assuming the class name matches the filename
				$className = pathinfo($filePath, PATHINFO_FILENAME);
				/////die('CLASS NAME IS: '.$className);/////////// WORKS: 'Config'
				if (class_exists($className)) {
					$instance = new $className();
					die('TESTING INSTANCE OF CLASS for appName: '.$instance->getConfig()['appName']);////////////
					if (method_exists($instance, 'getConfig')) {
						die('IT EXISTS');///////////
						//append the config data to the global config array as a sub-array
						$this->config->setModuleConfigs(strtolower($className), $instance->getConfig());
					}
				} else { die('THE CLASS DOES NOT EXIST: '.$className); }
			  }
			}
		} 
	}


	public function getUseFullLayoutSetting() {
		return $this->useFullLayout;
	}


	public function getAppName() {
		return $this->appName;
	}


	public function getBusinessName() {
		return $this->businessName;
	}


	public function getDefaultLayoutDirectory() {
		return $this->defaultLayoutDirectory;
	}


	public function getDefaultLayout() {
		return $this->defaultLayout;
	}

	public function getAppConfig() {
		return $this->config;
	}


}
