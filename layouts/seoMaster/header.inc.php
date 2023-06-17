
    <header class="navbar navbar-transparent navbar-fixed-top">
        <div class="container">
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Navbar & Hero Start -->
        <div class="container-xxl position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
                <a href="<?=$this->config->getHomePage()?>" class="navbar-brand p-0">
                    <h1 class="m-0">Dor<span class="fs-9">Guzen</span></h1><!--It was 'fs-5'-->
                    <!-- <img src="img/logo.png" alt="Logo"> -->
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="<?=$this->config->getHomePage()?>" class="nav-item nav-link active"><i class="fa fa-home"></i>Home</a>
                        <!--<a href="about.html" class="nav-item nav-link">About</a>
                        <a href="service.html" class="nav-item nav-link">Service</a>
                        <a href="project.html" class="nav-item nav-link">Project</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                            <div class="dropdown-menu m-0">
                                <a href="team.html" class="dropdown-item">Our Team</a>
                                <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                                <a href="404.html" class="dropdown-item">404 Page</a>
                            </div>
                        </div>-->
                        <a href="<?=$this->config->getFileRootPath()?>feedback" class="nav-item nav-link">Contact</a>
                        <?php
                        if (!isset($_SESSION['authenticated'])) { ?>
                            <a href="<?=$this->config->getFileRootPath()?>auth/login" class="nav-item nav-link">Login</a>
                            <?php
                             $config = new \configs\Config();
                             if ($config->getConfig()['allow_registration'] === true)
                             { ?>
                                <a href="<?=$this->config->getFileRootPath()?>auth/signup" type="button" class="nav-item nav-link">Register</a>
                             <?php
                             }
                        }
                        else
                        { ?>
                            <a href="<?=$this->config->getFileRootPath()?>auth/logout" class="nav-item nav-link">Logout</a>
                            <a href="<?=$this->config->getFileRootPath()?>admin/dashboard" class="nav-item nav-link">Dashboard</a>
                            <?php
                        } ?>
                    </div>

                    <butaton type="button" class="btn text-secondary ms-3" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fa fa-search"></i></butaton>
                </div>
            </nav>

            <?php
            list($controller, $method) = $this->config->getCurrentRoute();
            ?>
            <?php //<!--------------------------------------- FULL SITE SEARCH FORM -------------------------------------------------------> */?>
                    <div class="modal fade" id="searchModal" tabindex="-1">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content" style="background: rgba(29, 29, 39, 0.7);">
                                <form method="get" action="<?=$this->config->getFileRootPath()?>search">


                                    <div class="modal-header border-0">
                                        <button type="button" class="btn bg-white btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body d-flex align-items-center justify-content-center">
                                        <div class="input-group" style="max-width: 600px;">
                                            <input type="text" name="search_keyword" class="form-control bg-transparent border-light p-3" placeholder="Type search keyword">
                                            <button type="submit" class="btn btn-light px-4"><i class="bi bi-search"></i></button>
                                        </div>
                                    </div>

                                    <!--We send the current controller with this form, so the system knows which view the user searched from
                                            as this search form is available every where on the site-->
                                    <input type="hidden" name="searchOrigin" value="<?=$controller?>/<?=$method?>" />
                                </form>

                            </div>
                        </div>
                    </div>
            <?php //<!--------------------------------------------------------------------------------------------------------------------> */?>
        </div>

        </div>
      </div>
    </header>
