
        
    <!-- ==========================
    	Fonts 
    =========================== -->
    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,200,200italic,300,300italic,400italic,600,600italic,700,700italic,900,900italic&amp;subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Raleway:400,100,200,300,500,600,700,900,800' rel='stylesheet' type='text/css'>

    <!-- ==========================
    	CSS 
    =========================== -->
    <link href="<?=$this->settings->getFileRootPath()?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="https://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css">
    <link href="<?=$this->settings->getFileRootPath()?>assets/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="<?=$this->settings->getFileRootPath()?>assets/css/dragtable.css" rel="stylesheet" type="text/css">
    <link href="<?=$this->settings->getFileRootPath()?>assets/css/owl.carousel.css" rel="stylesheet" type="text/css">
    <link href="<?=$this->settings->getFileRootPath()?>assets/css/animate.css" rel="stylesheet" type="text/css">
    <link href="<?=$this->settings->getFileRootPath()?>assets/css/color-switcher.css" rel="stylesheet" type="text/css">
    <link href="<?=$this->settings->getFileRootPath()?>assets/css/custom.css" rel="stylesheet" type="text/css">
    <link href="<?=$this->settings->getFileRootPath()?>assets/css/carousel_slider.css" rel="stylesheet" type="text/css">

    
    <?php
    //Site the site theme dynamically
    if ($this->settings->getAppColorTheme())
    { ?>
        <link href="<?=$this->settings->getFileRootPath()?>assets/css/color/<?=$this->settings->getAppColorTheme()?>.css" rel="stylesheet" type="text/css">
    <?php
    } 
    else
    { 
        //Set the default site theme just in case ?>
        <link href="<?=$this->settings->getFileRootPath()?>assets/css/color/red.css" id="main-color" rel="stylesheet" type="text/css">
    <?php    
    } ?>



       <!-- ==========================
           JS 
       =========================== -->
       <!--[if lt IE 9]>
         <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
         <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
       <![endif]-->
