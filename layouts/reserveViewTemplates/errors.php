<?php
ob_start();
include($_SERVER['DOCUMENT_ROOT'].'/sweeteaze/controllers/title.inc.php');
require_once('controllers/config.inc.php');
$currentPage = basename($_SERVER['SCRIPT_FILENAME']);

$econfirm = false;
$mailresult = false;
?>
<!DOCTYPE html>
<html>
<head>
    <!-- ==========================
    	Meta Tags 
    =========================== -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    
    <!-- ==========================
        NEWSLETTER - START 
    =========================== -->
        <?php include('controllers/html_dependencies_top.inc.php'); ?>
    <!-- ==========================
        NEWSLETTER - END 
    =========================== -->

    
</head>
<body>
	
    <!-- ==========================
    	SCROLL TOP - START 
    =========================== -->
    <div id="scrolltop" class="hidden-xs"><i class="fa fa-angle-up"></i></div>
    <!-- ==========================
    	SCROLL TOP - END 
    =========================== -->
    

    
    <div id="page-wrapper"> <!-- PAGE - START -->
    
	<!-- ==========================
    	HEADER - START 
    =========================== -->
        <?php include('controllers/header.inc.php'); ?>
    <!-- ==========================
    	HEADER - END 
    =========================== -->  
    
    <!-- ==========================
    	BREADCRUMB - START 
    =========================== -->
    <section class="breadcrumb-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-xs-6">
                </div>
                <div class="col-xs-6">
                    <ol class="breadcrumb">
                        <li><a href="index.phtml">Home</a></li>
                        <li class="active">Sign Up</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
	<!-- ==========================
    	BREADCRUMB - END 
    =========================== -->
        
        
        
    
    <!-- ==========================
    	CONTENT - START 
    =========================== -->
   <div id="error_panel" class="panel panel-primary">
        
            <div class="panel-heading" style="background: red; color: white;text-align: center;">
        
                <h3 class="panel-title">THERE WAS AN ERROR</h3>
        
            </div>
            
        
            <div class="panel-body">
            
            	<?php 
				
				if ((isset($_GET['econfirm'])) && ($_GET['econfirm'] == "false"))
				{ 
					echo '<h4>Your activation code is wrong, try again, or contact us<br /><br />
							The problem may be because you waited too long to activate your account. <br /><br />
							If that is the case, try registering again, or just contact us for help. <br /><br />
							Thank you.<br /><br />
							The Sweeteaze team</h4>';
				} 
				
				
				######################## NEXT KIND OF ERROR-IF A USER TRIES TO VIEW ALL OR A PARTICULAR PROD FROM THE HOME/CATALOG PAGE BUT THE LINK SENDS THE WRONG PROD DETAIL ##################
				
				if ((isset($_GET['c'])) && ($_GET['c'] == 0))
				{ 
					echo "<h4>We're sorry, there was an error with the product type you are trying to view<br /><br />
							Go back and try the link again, or try searching using the search box. This issue has been logged and will be resolved as soon as possible.<br /><br />
							The Sweeteaze team</h4>"; 
				}
				
				
				?> 
   
            </div> <!--END OF PANEL BODY-->
            
            
            <div class="panel-footer clearfix">
        
        	</div><!--end of panel footer-->        
        
        
        </div><!--END OF PANEL-->
    <!-- ==========================
    	CONTENT - END 
    =========================== -->


        <!-- ==========================
            FOOTER - START 
        =========================== -->
            <?php include('controllers/footer.inc.php'); ?>
        <!-- ==========================
            FOOTER - END 
        =========================== -->



        </div> <!-- PAGE - END -->
        
        
    
        <!-- ==========================
            JS - START 
        =========================== -->
            <?php include('controllers/html_dependencies_bottom.inc.php'); ?>
        <!-- ==========================
            NEWSLETTER - END 
        =========================== -->  
    
    
</body>
</html>



























        



        
      

	




