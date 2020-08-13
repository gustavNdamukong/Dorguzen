<?php
namespace settings;




	######################### THE IDEA IS TO MAKE THIS CLASS TAKE OVER THE RESPONSIBILITY OF MANAGING SITE-WIDE SETTINGS ##########################
	/*
	 * The Dorguzen Application, adminController, DB Adapter (which is extended by all models) classes, as well as any classes that will all need
	 * to be aware of some app-wide settings will instantiate this class and inject it into themselves at run time, giving you access to all your
	 * application Settings at all times.
	 *
	 * The advantage of course is that all these app-wide settings can be configured from one location - this settings class file.
	 *
	 * Take note that Dorguzen ships with a settings DB table 'baseSettings' which you may prefer to use for these app-wide settings.
	 * whichever you use is entirely up to your preference.
	 *
	 */
	class Settings
	{

		public function getSettings()
		{
			return [



				/**
				|--------------------------------------------------------------------------
				| Application name and Layout settings
				|--------------------------------------------------------------------------
				|
				| State the name of your application. This is the name the system will use everywhere to refer to your application.
				| The businessName is an optional name you may like to use in certain documents from your app as opposed to the website name, e.g.
				|		Facebook Ltd instead of the appName facebook
				| The appSlogan is the slogan of your business/organisation which you may want to incorporate in documents coming from your app.
				| Also, specify here which layout folder and layout file are to be used by default for your views.
				|
				*/


				'appName' => 'dorguzApp',

				'appBusinessName' => 'Dorguzen',

				'appSlogan' => 'The Rapid Web Development Toolkit',

				'appURL' => 'http://www.nolimitmedia.co.uk',

				'layoutDirectory' => 'dorguzApp',

				'defaultLayout' => 'dorguzAppLayout',




				/**
				|--------------------------------------------------------------------------
				| Application URL (local/live)
				|--------------------------------------------------------------------------
				|
				| This URL is used by the console to properly generate URLs when using
				| the Artisan command line tool. You should set this to the root of
				| your application so that it is used when building the URL of any links or pages in your site.
				|
				| Update this with your local server URL and port number, and put in your live URL as well when
				| when you eventually go live.
				|
				 */

				'localUrl' => 'http://localhost:8888/dorguzApp/',
				'liveUrl' => 'http://www.nolimitmedia.co.uk/',
				'liveUrlSecure' => 'https://www.nolimitmedia.co.uk/',
				'fileRootPathLocal' => '/dorguzApp/',
				'fileRootPathLive' => '/',






				/**
				|--------------------------------------------------------------------------
				| SET the local/live DB connection credentials
				|--------------------------------------------------------------------------
				|
				| It's recommended to create another (2nd) user for your DB with less privileges,
				| so that you can switch between the 2 depending on the purpose.
				|
				| For the 'host', enter your live hostname e. g. ''
				|
				| Change this to match your application DB settings
				|
				 */


				'localDBcredentials' => [
					'username' => 'dorguz',
					'pwd' => 'dorguz123',
					'db' => 'dorguzApp',
					'host' => 'localhost',
					'connectionType' => 'mysqli',
					'key' => 'takeThisWith@PinchOfSalt'
				],

				'liveDBcredentials' => [
					'username' => 'dorguz',
					'pwd' => 'dorguz123',
					'db' => 'dorguzApp',
					'host' => 'localhost',
					'connectionType' => 'mysqli',
					'key' => 'takeThisWith@PinchOfSalt'
				],






				/**
				|--------------------------------------------------------------------------
				| Are we running on the live site?
				|--------------------------------------------------------------------------
				|
				| Set to true if so, and false if not
				|
				 */

				'live' => false,


				


				/**----------------------------FILE UPLOADING------------------------------------*/
				/**
				|--------------------------------------------------------------------------
				| Maximum file upload size
				|--------------------------------------------------------------------------
				|
				| The maximum file size accepted by your application
				|
				*/

				'maxFileUploadSize' => 10240000000,


				/**
                |--------------------------------------------------------------------------
                | File upload destination
                |--------------------------------------------------------------------------
                |
                | Specify where uploaded files should go.
                | Let it be in this format:
                |
                |   'fileUploadPath' => 'images/store_imgs/',
                |
                | Take note of the trailing slash.
                | You can create as many of these for different file upload destination paths.
                | For example; one for gallery images (you just have to append the album ID to it in the calling script):
                |   'gallery' => 'images/gallery/',
                |
                | Another for user profiles (you just have to append the user's ID to it in the calling script):
                |   'userProfiles' => 'images/userProfiles/',
                |
                | Another for product ads etc
                |   'products' => 'images/productImgs/',
                |
                |
                | To upload an image, just pass to DGZ_Uploader() the path to upload to like so:
                |   DGZ_Uploader('galleryImageDir');
                |   or
                |   DGZ_Uploader('userProfiles');
                |   or
                |   DGZ_Uploader('products');
                |
                | For now, we give you 'default' path for general purpose uploading which uploads to a sub folder 'store_imgs' in the
                | Laravel public/images/store_imgs. Keep this 'default' key as the uploader needs one.
                | But feel free to change the value to your needs. You could perhaps just use 'images' so it goes to public/images.
                | Again, feel free to add other key-value pairs.
                |
                */

				'defaultImageDir' => 'images/',

				'emailImageDir' => 'assets/images/email_images/',

				'audioUploadDir' => 'docs/audio/',

				'videoUploadDir' => 'docs/videos/',





				/**
				|--------------------------------------------------------------------------
				| Home page slider settings
				|--------------------------------------------------------------------------
				|
				| Upon generating a view file in your controller; after determining whether
				| to show an image slider in the view file, determine here what type of
				| slider to use using 'sliderType'; there are two types:
				|
				| -slider (regular awesome slider from right to left direction)
				| -sliderEngine (powerful slider with alternative directions and effects)
				|
				*/

				'sliderType' => 'slider',






				/**
				|--------------------------------------------------------------------------
				| Application Locale Configuration
				|--------------------------------------------------------------------------
				|
				| The application locale determines the default locale that will be used
				| by the translation service provider. You are free to set this value
				| to any of the locales which will be supported by the application.
				|
				*/

				'locale' => 'en',




				/**
				|--------------------------------------------------------------------------
				| Application Fallback Locale
				|--------------------------------------------------------------------------
				|
				| The fallback locale determines the default language to use when the current one
				| is not available. You may change the value to correspond to any of
				| the language folders that are provided through your application.
				|
				*/

				'fallback_locale' => 'en',






				/**
				|--------------------------------------------------------------------------
				| SITE CONTACT DETAILS AND EMAIL SETTINGS
				|--------------------------------------------------------------------------
				|
				| The contact details of this site, like emails addresses, phone numbers, fax, postal addresses etc
				| The 'Reply-To' header field allows an email receiver to be able to hit reply on their email app and
				| automatically reply to who the message came from. Note that the address below is the same as this site's
				| email address (appEmail). Have your scripts override it depending on who you want the email to come from
				|	(e.g. the email address of someone who sent an enquiry on your site contact form)
				|
				*/

				'site_contact_tel' => '00000000000',
				'site_postal_address' => '',

				'appEmail' => 'your@applicationEmailAddressHere', //We recommend you use one created on your web host's domain

				'appEmailOther' => 'your@alternativeEmailAddress',//this can be used for CCing contacts,

				'localHeaderFrom' => 'your@applicationEmailAddressHere',//These should match the appEmail value
				'liveHeaderFrom' => 'your@applicationEmailAddressHere',

				'headerReply-To' => 'your@applicationEmailAddressHere', //Again, make this match the appEmail value, but you can use another email address



			];
		}


		

		public function getFileRootPath()
		{
			if ($this->getSettings()['live'])
			{
				return $this->getSettings()['fileRootPathLive'];
			}
			else
			{
				return $this->getSettings()['fileRootPathLocal'];
			}
		}



		/**
		 * Get the URL to the home page of the app to link to
		 */
		public function getHomePage()
		{
			if ($this->getSettings()['live'])
			{
				return $this->getSettings()['liveUrl'];
			}
			else
			{
				return $this->getSettings()['localUrl'];
			}
		}



		/**
		 * Get the URL to the live home page of the app using SSL
		 */
		public function getHomePageSecure()
		{
			return $this->getSettings()['liveUrlSecure'];
		}



		/**
		 * Used to determine what view a user is coming into another view from. It returns the string of the controler name without the 'Controller' bit
		 * It is then used to redirect the user back to that controller if necessary.
		 *
		 * @return mixed
		 */
		public function getCurrentController()
		{
			$currentUrlString0 = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);

			$currentUrlString = explode('/', $currentUrlString0);
			if ($this->getSettings()['live'] == true)
			{
				//we are live
				//there may not be a method specified (3rd slash level) eg when a user visits the home page, so check if there's one
				if (!empty($currentUrlString[1])) {
					//For their convenience; if they only enter 'index', or 'index.phtml', we should show them the home page too
					if ($currentUrlString[1] == 'index' || $currentUrlString[1] == 'index.php')
					{
						//send them to the HomeController
						return 'Home';
					}
					else
					{
						return $currentUrlString[1];
					}
				}
				else
				{
					//If they just visited the root of the app, then it was the home controller
					return 'Home';
				}
			}
			else
			{
				//we are local
				//there may not be a method specified (3rd slash level) eg when a user visits the home page, so check if there's one
				if (!empty($currentUrlString[2])) {
					if ($currentUrlString[2] == 'index' || $currentUrlString[2] == 'index.php')
					{
						return 'Home';
					}
					else
					{
						return $currentUrlString[2];
					}

				}
				else
				{
					//If they just visited the root of the app, then it was the home controller
					return 'Home';
				}

			}

		}
	}


  
	
	
	