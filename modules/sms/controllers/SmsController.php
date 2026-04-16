<?php

namespace Dorguzen\Modules\Sms\Controllers;

use Twilio\Rest\Client;
use Dorguzen\Core\DGZ_ModuleControllerInterface;
use Dorguzen\Core\DGZ_ModuleControllerTrait;

class SmsController extends \Dorguzen\Core\DGZ_Controller implements DGZ_ModuleControllerInterface
{
    use DGZ_ModuleControllerTrait;

    protected array $controllers = [];

    private $stripe;


    public function __construct()
    {
        parent::__construct();

    }


    public function getDefaultAction()
    {
        return 'defaultAction';
    }


    /**
     * The endpoint of this method is 
     *  "http://yourAppName/sms" 
     */
    public function defaultAction()
    { 
        $this->notify();
    }

    /**
     * The endpoint of this method is 
     *  "http://yourAppName/sms/notify" 
     */
    public function notify()
    {
        /**
         * Visit: https://www.twilio.com/ > login (or sign up & login) > 
         * Install the Twilio SDK like so:
         *      
         *      composer require twilio/sdk
         * 
         * In https://console.twilio.com/
         * Get your Account SID, Auth token from https://console.twilio.com/us1/account/keys-credentials/api-keys
         *  Remember to choose the right one for the environment you're in (test or live)
         * 
         */
        $sid    = env('TWILIO_SID');
        $token  = env('TWILIO_AUTH_TOKEN');
        $twilioClient = new Client($sid, $token);

        $message = $twilioClient->messages
                    ->create(
                        //to: the number you will like to send a text message to

                        //For this to work, you need to have gone into 'sms Geographic Permissions' & selected the
                        //geo region within which this number falls, for messages to be allowed to be sent from there

                        //Note: If you are in a trial account, you will only be able to send messages & make calls to
                        //phone numbers that you have verified in your account (go to 'verified caller IDs'). A verified
                        //number is a number that you have indicated in your Twilio account that you wish to use as a
                        //caller ID or as the 'To' number for outbound calls/messages from the Sandbox (Twilio-purchased)
                        //number. Once you go live, your app will then be able to fire off sms messages to a less limited
                        //amount of numbers.
                        env('TWILIO_TO_NUMBER'),
                        array(
                            //the Twilio phone number you purchased at twilio.com/console
                            'from' => env('TWILIO_FROM_NUMBER'),
                            //the body of the text message you you'd like to send
                                "body" => "My test message here!"
                        )
                    );

        if ($message)
        {
            //redirect the user to another view or do whatever you want to do
            echo 'Message sent';
        }
        else 
        {
            echo 'Error: Something went wrong';
        }
    }
}


  
	
	
	