<?php

	/** 
	 * Module configs do not need namespaces
	 * You can have multiple config files like this for your modules. This just helps you 
	 * manage your applications configs by separating module ones from your main app ones. 
	 * So you can put your external module configs within the main Config.php if you wanted. 
	 * The values in all the module config files in here will be retrieved and merged with 
	 * the values of the main config file at run time.  
	 */
	class ModuleConfigExample
	{
		public function getConfig()
		{
			return [

				'bikko' => 'Bwaaaam',

				'appName' => 'Doggy DOG',

				'chaaai' => 'Buyaaaa',

				'caroool' => 'ehhheeen'
			];
		}
	}


  
	
	
	