<?php
ob_start();
require_once('controllers/config.inc.php');
session_start();
// run this script only if the logout button has been clicked
//if (isset($_POST['logout'])) {
  // empty the $_SESSION array contents
  $_SESSION = array();
  // invalidate the session cookie

  //if (isset($_COOKIE[session_name()])) {
	//setcookie(session_name(), '', time()-86400, '/');///////This is the cookie auto set by the session n it's named PHPSESSID n its 
			//value is the session id (e long set of numbers)
  //}
  
  
  	//This is the cookie auto set by the session n it's named PHPSESSID n its 
		//value is the session id (e long set of numbers)
    if (isset($_COOKIE[session_name()])) 
	{
		setcookie(session_name(), '', time() - 86400, '/');
  	}
  
  
  	//This is the cookie i set with rem_me, we delete it cos if the user clicked on the logout button, then they most likely
		//nolonger wish to be remembered on their computer, else they would have simply closed their browser.  
    if (isset($_COOKIE['rem_me'])) 
	{
		setcookie('rem_me', '', time()-86400);
	}
  
  	//end session and redirect
  	session_destroy();

	//Let's throw them back to the home page, logged out of course
  	header("Location: $homepagePath");
  exit;
//}
?>

<?php
//This section below is the form to logout, note that you can put this whole file including this logout form on every page that needs the logout facility, but in order to avoid typing this code above, or having it occuppy space in so many files (not that that's a big issue i admin), it's more tidy to leave the above code inside the includes folder and just place the below form in every page and have the action attrib refer to the logout file in the includes folder. But in this case we will do it a bit differently; because this logout file facility is used inside files in the admin folder, it will be a complex include to use the action attrib to refer to the logout.inc.php file wh is inside the includes folder, so we will make a copy of logout.inc.php and place it inside the admin folder to serve only the loging out of the admin user-better still make it belong just to the admin by calling it admin_logout.php instead of logout.inc.php. Therefore the logout form in all admin files will simply have an action attrib like this: action="admin_logout.php".

//Or, alternatively, have a logout button in your main menu header, which you include in all your files, ideally in the header. 
?>
 
<!--<form id="logoutForm" method="post" action="">
  <input name="logout" type="submit" id="logout" value="Log out">
</form>-->
