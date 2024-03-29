<?php ob_start();

//turn PHP errors on
error_reporting(E_ALL);
ini_set('display_errors', TRUE);

set_error_handler("var_dump");




//some site-wide constants
//on a local MAMP server this points to 'Applications/MAMP/htdocs/appname'
// but on a live server it may point to something like this: /home/i3v8zo1vaw30/public_html
define('CWD', getcwd());


	session_start();

	//check if they are a member, or register them n give them a visitor's pass
	if (!(isset($_SESSION['authenticated'])))
	{
		if (!((isset($_SESSION['_Guest'])) && ($_SESSION['_Guest'] == 'visitor')))
		{
			//All visitors, authenticated or not, will navigate thru this index.phtml whether they are aware of it or not.
			// But because this is where we also check whether users are authenticated or not and log then out if not
			//(see the "if (isset($_SESSION['authenticated']))" block statement below),
			//if your application is an EXCLUSIVELY membership-based one and you did that, this page will continuously refreshed
			//as visitors to your site not logged in are attempted to be redirected. This is annoying, hence the following solution:

			//set a session which we will use to manage n distinguish visitors from logged in users
			//for example we will not have to redirect them again now we already know they are a visitor
			$_SESSION['_Guest'] = 'visitor';
		}
	}

	//a registered visitor may get to this point, so let's make sure that only authenticated users have their sessions vars modified
	if (isset($_SESSION['authenticated'])) {
		//set a time limit in seconds, so 600 is for 10 minutes
		$timelimit = 7200; // 4 hours
		// get the current time
		$now = time();

		//only time session out for logged-in users who did not choose to be remembered
		if (!isset($_COOKIE['rem_me'])) {
			//if ($now > $_SESSION['start'] + $timelimit)
			if ((isset($_SESSION['start'])) && ($now > $_SESSION['start'] + $timelimit)) {
				//1) if timelimit has expired, empty the session variable
				$_SESSION = array();

				//2) invalidate the session cookie if it's set

				if (isset($_COOKIE[session_name()])) {
					ob_end_clean();
					setcookie(session_name(), '', time() - 86400, '/');
				}

				//3) destroy (end) the session and redirect (if you want) with a query string
				session_destroy();

				//this is where after signing them out, you could redirect users if nec, for example;
				//if your application is EXCLUSIVELY membership-based.
					//header("Location: {$redirect}?expired=yes");
					//exit;
			}
			else {
				//if it's got this far, it's OK, so update start time with the same length of e session time if they are still active on e
				//page (they'll refresh it thus activating this code
				$_SESSION['start'] = time();
			}
		}
	}





//Before we dive into the app, lets load all your objects
require_once('Autoloader.php');
//require the vendor/autoload.php file if it exists. We do this separately because a project does not have to come with a vendor dir.
//in this case, if you are not using Composer, that shouldn't stop from you using this framework
if (file_exists(__DIR__ . '/vendor/autoload.php'))
{
	include_once(__DIR__ . '/vendor/autoload.php');
} 

$currentPage = basename($_SERVER['SCRIPT_FILENAME']);

error_reporting(E_STRICT | E_ALL);


/**
 * I noticed that my application was not displaying any errors at all, which was making debugging a nightmare. So realising that this custom error handler function here may be the culprit, as
 * when i commented it out (this set_error_handler() function), errors started displaying on screen. I just had to catch those errors and display them nicely in an exception,
 * 		since we already have our custom exception handler (DGZ_Exception.php)
 * Therefore i added the try {} block around the initial contents of this function, because i see that i was throwing an exception which maybe was not being caught and reacted upon
 * 		anywhere else in the app
 * Then obviously, i added the catch{} block below it in an attempt to catch that exception and display the application exception view file (DGZExceptionView)-whose show() method
 * 		has to be given the exception object ($e) as its parameter
 *
 */
set_error_handler(
	function($errno, $errstr, $errfile = '', $errline = 0, $errcontext = []) {

		try {
			switch ($errno) {
				case E_COMPILE_ERROR:
					$errorType = \DGZ_library\DGZ_Exception::PHP_FATAL_ERROR;
					break;
				case E_WARNING:
					$errorType = \DGZ_library\DGZ_Exception::PHP_WARNING;
					break;
				case E_NOTICE:
					$errorType = \DGZ_library\DGZ_Exception::PHP_NOTICE;
					break;
				case E_ERROR:
					$errorType = \DGZ_library\DGZ_Exception::PHP_ERROR;
					break;
				default:
					$errorType = \DGZ_library\DGZ_Exception::PHP_OTHER_ERROR;
					break;
			}

			if (is_array($errcontext)) {
				$errcontextstring = print_r($errcontext, true);
			}
			elseif (is_object($errcontext)) {
				$errcontextstring = print_r($errcontext, true);
			}
			else {
				$errcontextstring = $errcontext;
			}

			$hintstr = <<<TEXT
			This is an internal error generated by the server and usually indicates a programming error.
							
			File: {$errfile}
			Line: {$errline}
			Context: {$errcontextstring}
TEXT;

			throw new \DGZ_library\DGZ_Exception($errstr, $errorType, $hintstr);
		}
		catch (\Exception $e)
		{  
			$hint = '';
            $traceHint = '';
            $message = '';
            $file = '';
            $line = '';
            $time = date("d-m-y h:i:s");

			// Is this a DGZ_Exception?
			if ($e instanceof \DGZ_library\DGZ_Exception) {

				/////$view = \DGZ_library\DGZ_View::getView('DGZExceptionView', null, 'html');
				/////$view->show($e);

				$hint = nl2br(htmlspecialchars($e->getHint()));
                if($e->getTraceAsString()) {
                    $traceHint = nl2br(htmlspecialchars($e->getTraceAsString()));
                }
                $message = nl2br(htmlspecialchars($e->getMessage()));
                $file = $e->getFile();
                $line = $e->getLine();
			}
			else {
				// If it's a normal exception then just use the default view
				/////$view = \DGZ_library\DGZ_View::getView('ExceptionView', null, 'html');
				/////$view->show($e);
				$message = nl2br(htmlspecialchars($e->getMessage()));
                if($e->getTraceAsString()) {
                    $hint = nl2br(htmlspecialchars($e->getTraceAsString()));
                }
			}

			//We have thrown an exception
            $logs = new Logs();
            $exceptionTitle = 'Runtime error';
            $exceptionData = 'Hint: '.$hint.
                '| Trace Hint: <b>'.$traceHint.'</b><br>'.
                '| Message: <b>'.$message.'</b><br>'.
                '| File: <b>'.$file.'</b><br>'.
                '| Line: <b>'.$line.'</b><br>'.
                '| Time: <b>'.$time.'</b>';
            $logs->log($exceptionTitle, $exceptionData);

			//Determine if we are live, then Send email to the site admin about this error
            $config = new \configs\Config();
            if ($config->getConfig()['live'])
            {
                $messenger = new \DGZ_library\DGZ_Messenger();
                $send = $messenger->sendErrorLogMsgToAdmin($message);
            }

			$controller = new \controllers\ExceptionController();
            $controller->addException($e);
            $controller->redirect('exception', 'error');
		}

	}
);








/*spl_autoload_register('loadController');

function loadController($className) {
	$classFolders = array('configs', 'controllers', 'DGZ_library', 'models');
	foreach ($classFolders as $folder)
	{
		$fileName = $folder .'/'. basename($className) . '.php';
		/////echo $fileName.'<br>';////////
		if (file_exists($fileName))
		{
			include_once($fileName);
			/////echo $fileName.'<br> included';////////
		}
	}
}*/




DGZ_library\DGZ_Router::route();

	