<?php

namespace Dorguzen\Core;

use Dorguzen\Config\Config;



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
     * @var Config contains the configs of the site. Other parts of your application like view files, 
	 * and their controllers etc need access to it
	 * This class is the only place where your application settings will be loaded. It will then be distributed
	 * to other places like controllers, views & models from here. This is because we are loading here the config data of all modules, 
	 * making sure you have access to all config data at run time. 
     */
	protected $config;


	public function __construct($useFullLayout = true, $appName = null, $layout = null) 
	{
		$this->config = container(Config::class);
		if($appName == null) {
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
