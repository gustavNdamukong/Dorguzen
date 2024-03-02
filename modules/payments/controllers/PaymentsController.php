<?php

namespace modules\payments\controllers;

use DGZ_library\DGZ_View;
use DGZ_library\DGZ_Translator;
use \Stripe\Stripe;
use \Stripe\Customer;
use \Stripe\Charge;
use DGZ_library\DGZ_Validate;

class PaymentsController extends \DGZ_library\DGZ_Controller
{

    private $stripe;


    public function __construct()
    {
        parent::__construct();

        //use a secret API key (API call cannot be made with a publishable API key)
        /////$this->stripe = Stripe::setApiKey('sk_test_51OoQKPFRQteXl4ynTkN5HQOpPZNtpsx5eGI82KavqzRbuNZsWJESBAGpkXu1HAQQV2Al23ZRwAhRqUa6PP4VZP5B00cJu0fyma');
        //$this->stripe = new StripeClient("sk_test_51OoQKPFRQteXl4ynTkN5HQOpPZNtpsx5eGI82KavqzRbuNZsWJESBAGpkXu1HAQQV2Al23ZRwAhRqUa6PP4VZP5B00cJu0fyma");
        
        // OR for the Checkout Session method, do this (switch to your live private key when in production):
        \Stripe\Stripe::setApiKey('sk_test_51OoQKPFRQteXl4ynTkN5HQOpPZNtpsx5eGI82KavqzRbuNZsWJESBAGpkXu1HAQQV2Al23ZRwAhRqUa6PP4VZP5B00cJu0fyma');
    }


    public function getDefaultAction()
    {
        return 'defaultAction';
    }


    public function defaultAction()
    { 
        $view = DGZ_View::getModuleView('payments', 'index', $this, 'html');
        $this->setPageTitle('Payments');
        $view->show();
    }

    /**
     * The endpoint of this method is 
     *  "http://yourAppName/payments/pay"
     * 
     * This one is good for the payment of a single product or multiple products
     * 
     * You can prepare your products in a shopping card page with a checkout button that takes them here
     * or create them here dynamically before making the API call to send the user to pay.
     */
    public function pay()
    {
        //Here we use the Checkout Session method
        // (https://docs.stripe.com/payments/checkout/how-checkout-works)
        header('Content-Type: application/json');

        $successLink = 'https://localhost/Dorguzen/payments/success';
        $landingPage = 'https://localhost/Dorguzen/payments';

        $checkout_session = \Stripe\Checkout\Session::create([

        //To checkout multiple products, pass similar multiple arrays as the value of 'line_items' as seen 
        //below-one for each product
        //To checkout just one product, pass just one array 
        'line_items' => [
            [
                # Provide info of the product you want to sell, or the Price ID (e.g. pr_1234)
                # to provide a product, provide its info in an array here using the key 'product_data' 
                # to provide the price ID, go to the already created product on Stripe, go into its details
                #   & copy its price id (dashboard > Product catalog > copy the API ID value) & pass it here 
                #   using the key 'price' 

                /*'price_data' => [
                    'currency' => 'GBP',
                    'product_data' => [
                        'name' => 'Test product',
                    ],
                    'unit_amount' => 2000,//price per item eg 2000 for GBP 20
                ],*/

                'price' => 'price_1OpXKWFRQteXl4yngb9PyxJj',
                'quantity' => 1,
            ],
            [
                'price_data' => [
                    'currency' => 'GBP',
                    'product_data' => [
                        'name' => 'Japanese microwave',
                    ],
                    'unit_amount' => 3000,//price per item eg 2000 for GBP 20
                ],
                'quantity' => 5
            ]
        ],    

        'mode' => 'payment',
        'success_url' => $successLink,
        'cancel_url' => $landingPage . '?cancelled=yes',
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);
        //confirm paymets by looging into stripe, then dashboard > Payments > switch to relevant mode to see payments
    }


    /**
     * The endpoint for this method is:
     *  "http://yourAppName/payments/pay2"
     * 
     * This one is good for a single payment for a product, or a total (fixed) amount
     * 
     * Design a shopping card on a checkout page like the basic form in index.php and send the data here via POST.
     * This will only work on a server with a valid SSL connection such as in production. This is because a valid 
     *  stripe-recognised token is needed, and that will not have been generated locally unless you have a valid 
     * SSL certificate working on your localhost.
     */
    public function pay2()
    {
        $val = new DGZ_Validate();

        $first_name = isset($_POST['first_name']) ? $val->fix_string($_POST['first_name']) : '';
        $last_name = isset($_POST['last_name']) ? $val->fix_string($_POST['last_name']) : '';
        $email = isset($_POST['email']) ? $val->fix_string($_POST['email']) : '';
        $token = isset($_POST['stripe_token']) ? $_POST['stripe_token'] : '';

        //create the customer in stripe;
        $customer = \Stripe\Customer::create([
            "email" => $email,
            "source" => $token
        ]);

        //charge the customer
        $charge = \Stripe\Charge::create([
            "amount" => 5000, //$50
            "currency" => "usd",
            "description" => "Nice product", //otional
            "customer" => $customer->id
        ]);

        /////echo '<pre> You have been paid!'; die(print_r($charge));
        $this->redirect('payments', 'success');
    }
    
    public function success()
    {
        $view = DGZ_View::getModuleView('payments', 'success', $this, 'html');
        $this->setPageTitle('Success');
        $view->show();
    }
}


  
	
	
	