<?php
ob_start();
require_once('../includes/gen_access_timeout.inc.php');
 //require_once('../includes/session_timeout.inc.php'); this file here will be reserved only for highly secure (member-only) pages.
include('../includes/title.inc.php');
require_once('../includes/config.inc.php'); 
require('../includes/login.inc.php'); //this file in turn includes utilityfuncs.inc.php (the file containing the JS funcs that the JS script below uses to validate logging in)
$currentPage = basename($_SERVER['SCRIPT_FILENAME']);


?>

<!DOCTYPE HTML>
<html class="no-js" lang="en-gb">  
<head>    
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<!--<meta name="viewport" content="width=device-width">-->    
<title><?php echo "Camerooncom-".$title; ?></title>
        
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
    <!--<script src="js/videogallery_slider.js"></script>-->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
   <!--<script src="jquery-1.7.js" type="text/javascript"></script>
    <script src="jquery.tmpl.js" type="text/javascript"></script>-->
    <script src="jquery-ui-1.8.16.custom.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="styles.css"/>
    <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.8.16.custom.css"/>
    
    <!--This style line below here fetches stylish font from google, thanks cHRIS mills-->
   <link href='http://fonts.googleapis.com/css?family=Lobster|Terminal+Dosis' rel='stylesheet' type='text/css'>
    
 <script type="text/javascript">				

function validate(form)
{
		if ($('#forgotstatus').val() == 'yes')
		{
			fail = validateEmail(form.forgot_pass_input.value);
			if (fail == "")
			{
				return true;
			}
			else
			{
				alert(fail);
				return false;
			}
		}
		else
		{
			fail = validateUsername(form.username.value)
			fail += validatePassword(form.pwd.value)
		
			if (fail == "") 
			{
				return true;
			}
			else 
			{ 
				alert(fail); 
				return false; 
			}
		}
}   
			    
    </script>
        

<link rel="icon" href="../../favicon.ico">

<link rel="stylesheet" type="text/css" href="../css/random.css">

<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">

<link rel="stylesheet" href="../css/normalize.min.css">
        
<link href="../css/superfish.css" rel="stylesheet" type="text/css" />        
              
        <style type= "text/css">
		
		article, aside, audio, canvas, datalist, details, figcaption, figure, footer, mark, header, hgroup, menu,
		nav, section, video { 
		
		display: block;
		}
		
		</style>
        
        <script src="../js/css3-mediaqueries.js"></script>
        
        <script src="../js/selectivizr.js"></script>

        <script src="../js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
        
        <!--[if Lt IE 9]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js">
        </script>
        <![endif]-->
        

        
        
 <script src="../js/hoverIntent.js" type="text/javascript" charset="utf-8"></script>
 <script src="../js/superfish.js" type="text/javascript" charset="utf-8"></script>
        
 <script type="text/javascript">
$(function() {
	$('ul.sf-menu').superfish({
		autoArrows: true,
		speed: 'fast'
	});
});

</script>
     

<!--[if lt IE 9]>      
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js">       
</script>     

<![endif]-->   

</head>  

<body>
<section id="mainwrapper" class="container"> <!--THIS CONTAINER CLASS SHOULD INVOKE BOOTSTRAP-->

<section id="headerforviews">


<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/camerooncom/includes/header.inc.php"); ?>

</section><!--END OF HEADER SECTION (CONTAINER)-->


<section id="main_content">

<article id="sidebar">
<?php
//Let's include the sidebar here
 include("../includes/camcomsidebar.inc.php");
?>
</article>

<article id="floatbg" style="background-color: #FFF;">

<div class="jumbotron">
<div class="well">
<!--<table class="signup" class="table" cellpadding="5"
	cellspacing="5">-->
<h2><?php if (($econfirm) && ($econfirm = "true")) { echo RegisLoginTransGarrison::accountActivationMsg(); } else { ?><p style="color: #069; font-weight: bold;"><?php echo RegisLoginTransGarrison::loginIntroWell(); ?></p> <?php } ?></h2>
<br />
  
<?php 
if (isset($_SESSION['authenticated'])) 
{ 
	echo '<pre>';/////////////////////////////////////////////////////////////////////////////////
	print_r($_SESSION); 
}?>


<?php if ($fail) { echo "<p style='color: red; font-size: 14; font-weight: bold;'>".RegisLoginTransGarrison::authphpErrorAnnounce()."</p><br /><p style='color: red; background-color: white;'><i>$fail</i></p>"; } else if (isset($success)) { echo "<p style='background-color: white;'><font color=green size=3><br />$success</font></p>"; } 

if (isset($login_errors)) //these are errors coming from grabbing the login details from the db.
{
	foreach ($login_errors as $logerrs)
	{
		echo "<p style='color: red; background-color: white;'>$logerrs</p>";
	}
}

if (isset($_GET['expired'])) {
?>
<p  style='background-color: white; class: warning;'><font color=red size=3>Your session has expired. Please log in again.</font></p>
<?php } ?>




</div>



                    
                    
                    <form id="loginform" method="post" action="" onSubmit="return validate(this)"><!--THIS IS WHERE THE JS FUNCTIONS ARE CALLED WHEN THE FORM IS SUBMITTED. You put it inside the opening <form> tag, its a listening function 'onSubmit', but then u use it as an attribute with = symbols, and obviously the action to be taken in quotes. validate(this) where validateform() is the function to be run, while this refers to the current object(wh in this case is a form).-->                                  
                    
                    	<label for="username" class="loginfieldinput" ><?php echo RegisLoginTransGarrison::loginUnLabel(); ?></label>
                    	<input type="text" maxlength="20" class="loginfieldinput" id="username" name="username" placeholder="username" <?php if (($fail) || ($errors)) { echo 'value="' . htmlentities($username, ENT_COMPAT, 'UTF-8') . '"'; } ?>></p><span id='info'></span>
                    	<label for="pwd" class="loginfieldinput" ><?php echo RegisLoginTransGarrison::loginPwLabel(); ?></label>
                    	<input type="password" maxlength="20" placeholder="password" class="loginfieldinput" name="pwd" id="pwd" /><!--For the password field we dont redisplay the value of e entered pw if there are errors. The "password" value of the 'type' attribute makes the form field hide the entered values-->
                                          
    <input type="button" id="forgot_pass" name="forgot_pass" value="<?php echo RegisLoginTransGarrison::loginPwForgotButtonText(); ?>" />
    <br />
    <input type="email" maxlength="40" class="loginfieldinput" id="forgot_pass_input" name="forgot_pass_input" placeholder="<?php echo RegisLoginTransGarrison::loginForgotPwEmailPlaceholder(); ?>" style="display: none; margin-top: 5px;" /><span style="display: none; margin-top: 5px;" class="loginfieldinput" id="forgot_pass_info">&nbsp;&larr;<font color=blue><?php echo RegisLoginTransGarrison::loginForgotPwResponseText(); ?></font></span>
    
     <input id="forgotstatus" type="hidden" name="forgotstatus" value="no" />  
    
 
    <p id="login_rem_me_checkbox"><input type="checkbox" id="rem_me" name="rem_me"/>&nbsp;<?php echo RegisLoginTransGarrison::loginRemmeCheckboxText(); ?></p>
    <br />
     
    <label></label>                   
    <button type="submit" id="login" name="login" class="btn btn-lg btn-primary" value="login"><?php echo RegisLoginTransGarrison::loginSubmitbuttonText(); ?></button>
                    
                    </form>


</div><!--END OF THE JUMBOTRON-->

<div class="clearer"></div><!--THIS IS THE LAST <DIV> (THING) INSIDE THIS FLOATBG CONTAINER COZ IT KEEPS EVERYTHING ABOVE IT-->

</article>

</section><!--END OF MAINCONTENT THAT CONTAINS THE SIDEBAR, N THE FLOATBG-->





<section class="first_footer">
	<div class="well">
      		<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/camerooncom/includes/first_footer.inc.php"); //include the 1st footer here ?>           
	        <div class="clearer" id="firstfooterdivclear"></div><!--NOTE THAT THIS IS THE LAST DIV (THING) INSIDE the well of THE first_footer-->
    </div><!--END OF THE WELL DIV INSIDE THE first_footer SECTION-->  
</section><!--End of first footer csection--> 
  

<article id="footer"><!--HERE'S THE START OF THE SECOND FOOTER; IT'S ENCLOSED IN THIS PAIR OF <article></article> tags ided 		
					'footer'-->
			<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/camerooncom/includes/footer.inc.php"); //include the 2nd footer here ?> 
			<div class="clearer"></div><!--to make this empty div work n keep everything before it above it; that's why it's the last 	
            			thing (div) inside the second-footer part of the website (enclosed in a pair of <article tags>. You also need 
                        to make sure the footer element has no height rule-->   
</article>

                            


        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>

        <script src="js/main.js"></script>
        
   
        
    <!--STYLES N SCRIPTS FOR THE FINAL 'OUR SERVICES' TAB, i had to mix it with that of the slider code, as well as leave the same <script> tags for the jq tabs at the top of this page for some reason bf it would work-->
    <script src="js/jquery-1.7.js" type="text/javascript"></script>
    
     <script src="js/sliderengine/jquery.js"></script>
     
    <script src="js/jquery.tmpl.js" type="text/javascript"></script>
    <script src="js/jquery-ui-1.8.16.custom.js" type="text/javascript"></script>
    
    <script src="js/sliderengine/amazingslider.js"></script>
    
    <link rel="stylesheet" type="text/css" href="css/styles.css"/>
    <link rel="stylesheet" type="text/css" href="js/jquery-ui-1.8.16.custom.css"/>

    <script src="js/sliderengine/initslider-1.js"></script> 


</section><!--END OF THE MAINWRAPPER SECTION THAT CONTAINS EVERYTHING-->



    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
   <!--<script src="../../dist/js/bootstrap.min.js"></script>
    <script src="../../assets/js/docs.min.js"></script>-->
    <script src="../js/bootstrap.js"></script>
    <script src="../js/docs.min.js"></script>
    
    
    
        <script src="js/jquery-1.7.js" type="text/javascript"></script>
    
     <script src="js/sliderengine/jquery.js"></script>
     
    <script src="js/jquery.tmpl.js" type="text/javascript"></script>
    <script src="js/jquery-ui-1.8.16.custom.js" type="text/javascript"></script>
    
    <script src="js/sliderengine/amazingslider.js"></script>
    
    <link rel="stylesheet" type="text/css" href="css/styles.css"/>
    <link rel="stylesheet" type="text/css" href="js/jquery-ui-1.8.16.custom.css"/>

    <script src="js/sliderengine/initslider-1.js"></script> 
    
   	<script type="text/javascript">
	$('#forgot_pass').click(function() {
		  $('.loginfieldinput').toggle();
		  
		  $("#forgotstatus").val('yes');
		  
		  if ($('#forgot_pass').val() == '<?php echo RegisLoginTransGarrison::loginResetbuttonText(); ?>') 
		  {
			location.reload(true);
		  }
		  else
		  {
			$('#forgot_pass').val('<?php echo RegisLoginTransGarrison::loginResetbuttonText(); ?>')
		  }
	});   
     
    
 </script>
    
      
</body>
</html>

