<?php

namespace DGZ_library;

use \settings\Settings;
/**
 * Class representing your application's configuration. It just gets all its data from the settings file
 * (/settings/Settings.php) so that it can relay it to different parts of your application that need to know about
 * those settings.
 *
 * @author Gustav
 */
class DGZ_Application {

	protected $useFullLayout = true;
	protected $appName;
	protected $businessName;
	protected $defaultLayoutDirectory;
	protected $defaultLayout;


	public function __construct($useFullLayout = true, $appName = null, $layout = null) {
		$config = new Settings();

		//set default app settings for this specific app
		if(is_null($appName)) {
			$this->useFullLayout = $useFullLayout;
			$this->appName = $config->getSettings()['appName'];
			$this->businessName = $config->getSettings()['appBusinessName'];
			$this->defaultLayoutDirectory = $this->appName;
			$this->defaultLayout = $this->appName.'Layout';
		}


		//put the settings vars in session vars in case we need em elsewhere in the app
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


}
