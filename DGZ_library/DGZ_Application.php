<?php

namespace DGZ_library;

use \settings\Settings;
/**
 * Base class representing your application's configuration. It just does the same thing that the settings file (/settings/config.inc.php)
 * does-using one or the other is completely up to your discretion. Ideally settings could come from the DB, but you could make your settings
 * right here in this class
 *
 * come back n find a better way to set this $appName var. As it stands, the programmer will pass the name of the app (wh is the same as the layout
 * folder name), and the layout file to use or they can just pass null (to keep the same app and layout folder) n the new layout to use in that
 * same app layout folder, or a different app (n layout folder) n a new layout file to use
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
		$config = new settings();

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
