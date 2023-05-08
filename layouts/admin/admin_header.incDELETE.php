
    <header class="navbar navbar-transparent navbar-fixed-top">
    	<div class="container">
            <div class="navbar-header">
               <a href="<?=$this->settings->getFileRootPath()?>Home/home" class="navbar-brand"><span>Dor</span>Guzen</a>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-bars"></i></button>
            </div>
            <div class="navbar-collapse collapse">
            	<p class="navbar-text hidden-xs hidden-sm">Your Rapid Web Development Toolkit</p>
            	<ul class="nav navbar-nav navbar-right">
                    <?php
                    if ((isset($_SESSION['authenticated'])) && ($_SESSION['authenticated'] == 'Let Go-'.$this->settings->getSettings()['appName'])) {
                        ?>
                        <li class="dropdown navbar-nav ml-auto text-white">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                               data-delay="300" data-close-others="true">Your account</a>
                            <ul class="dropdown-menu" role="menu">
                                <li class="nav-item mx-0 mx-lg-1"><a href="<?= $this->settings->getFileRootPath() ?>admin/dashboard">Dashboard</a></li>

                                <li class="nav-item mx-0 mx-lg-1"><a href="<?= $this->settings->getFileRootPath() ?>admin/manageUsers">Manage
                                        Users</a></li>

                                <li class="nav-item mx-0 mx-lg-1"><a href="<?= $this->settings->getFileRootPath() ?>admin/contactMessages">
                                        Messages</a></li>

                                <li class="nav-item mx-0 mx-lg-1">
                                    <form action="<?=$this->settings->getFileRootPath()?>admin/adminUserChangePw">
                                        <input type="hidden" name="userId" value="<?=$_SESSION['custo_id']?>">
                                        <input type="hidden" name="change" value="0">
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-user-secret"></i>Change Password</button>
                                    </form>
                                </li>
                                <li class="nav-item mx-0 mx-lg-1"><a href="<?= $this->settings->getFileRootPath() ?>admin/logout">Logout</a></li>
                            </ul>
                        </li>
                        <?php
                    } ?>
                </ul>
            </div>
        </div>
    </header>