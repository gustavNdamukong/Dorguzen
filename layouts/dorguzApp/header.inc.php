

    <header class="navbar navbar-transparent navbar-fixed-top">
    	<div class="container">
            <div class="navbar-header">
                <a href="<?=$this->settings->getHomePage()?>home" class="navbar-brand"><span>Dor</span>Guzen</a>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-bars"></i></button>



            </div>
            <div class="navbar-collapse collapse">
            	<p class="navbar-text hidden-xs hidden-sm">Your Rapid Web Development Toolkit</p>


                <!----NAV BAR STARTS HERE --->
            	<ul class="nav navbar-nav navbar-right">

                    <li class="dropdown megamenu">
                    	<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="300" data-close-others="true">Go To Menu</a>
                      	<ul class="dropdown-menu">
                            <li class="col-sm-4 col-md-3">
                            	<ul class="list-unstyled">
                                	<li class="title">Who Are We <span class="label label-danger pull-right">HOT</span></li>
                                    <li><a href="<?=$this->settings->getFileRootPath()?>home/home">Home page</a></li>
                                    <li><a>Something here</a></li>
                                    <li><a>Something here</a></li>
                                    <li><a>Something here</a></li>
                                    <li><a>Something here</a></li>
                                </ul>
                            </li>
                            <li class="col-sm-4 col-md-3">
                            	<ul class="list-unstyled">
                                    <li><a>Something here</a></li>
                                    <li><a>Something here</a></li>
                                    <li><a>Something here</a></li>
                                    <li><a>Something here</a></li>
                                    <li><a>Something here</a></li>
                                    <li><a>Something here</a></li>
                                    <li><a href="<?=$this->settings->getFileRootPath()?>feedback/contact">Your Contact Form</a></li>
                                </ul>
                            </li>
                            <li class="col-sm-4 col-md-3">
                            	<ul class="list-unstyled">
                                	<li class="title">Feedback</li>
                                    <?php
                                    if (!isset($_SESSION['authenticated'])) {
                                        //only show the log in link if the user is not currently logged in ?>
                                        <li><a href="<?=$this->settings->getFileRootPath()?>admin">Admin Login</a></li>
                                    <?php
                                    }
                                    else
                                    {
                                        //show a logout button if the user is logged in ?>
                                        <li><a href="<?=$this->settings->getFileRootPath()?>admin/logout">Logout</a></li>
                                        <li><a href="<?=$this->settings->getFileRootPath()?>admin/dashboard">Admin Dashboard</a></li>
                                    <?php
                                    } ?>
                                </ul>
                            </li>
                            <li class="hidden-xs hidden-sm col-md-3">
                            	<img  style="padding-top:70px;" max-width="948" max-height="632" src="<?=$this->settings->getFileRootPath()?>assets/images/categories/category-8.png" class="img-responsive center-block" alt="Put Your App Banner Here">
                            </li>
                      	</ul>
                    </li>

                    <?php
                        list($controller, $method) = $this->settings->getCurrentRoute();
                    ?>
                    <!--------------------------------------- THE SITE SEARCH FORM -------------------------------------------------------------->
                    <li class="dropdown navbar-search hidden-xs">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-search">Search</i></a>
                        <ul class="dropdown-menu">
                            <li>
                                <form method="get" action="<?=$this->settings->getFileRootPath()?>search/doSearch">
                                    <div class="input-group input-group-lg">
                                        <input type="text" class="form-control" name="search_keyword" placeholder="Search ..." />

                                        <!--below we need to sent the current controller so we know on which view the user searched from
                                            as this search form is available every where on the site-->
                                        <input type="hidden" name="searchOrigin" value="<?=$controller?>/<?=$method?>" /> 
                                        <span class="input-group-btn">
                                            <button type="submit" class="btn btn-primary">Search</button>
                                        </span>
                                    </div>
                                </form>
                            </li>
                        </ul>
                    </li>
                    <!--------------------------------------------------------------------------------------------------------------------------->
                </ul>
                <!----NAV BAR ENDS HERE --->


            </div>
        </div>
    </header>