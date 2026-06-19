
    <header class="navbar navbar-transparent navbar-fixed-top">
        <div class="container">
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Navbar & Hero Start -->
        <div class="container-xxl position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
                <a href="<?=$this->config->getFileRootPath()?>" class="navbar-brand p-0">
                    <img src="<?=$this->config->getFileRootPath()?>assets/images/dorguzen-logo.png" alt="Dorguzen" style="height:70px; width:auto;">
                </a>

                <button onclick="toggleSlideMenu(event)" type="button" class="navbar-toggler" data-bs-toggle="collapse">
                    <span class="fa fa-bars"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">

                        <a href="<?=$this->config->getFileRootPath()?>" class="nav-item nav-link active"><i class="fa fa-home me-1"></i> Home</a>

                        <!-- Content dropdown -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-pen-square me-1"></i> Content</a>
                            <div class="dropdown-menu shadow-sm border-0 rounded-3 py-2">
                                <a href="<?=$this->config->getFileRootPath()?>news" class="dropdown-item"><i class="fa fa-newspaper me-2"></i> News</a>
                                <a href="<?=$this->config->getFileRootPath()?>portfolio" class="dropdown-item"><i class="fa fa-briefcase me-2"></i> Portfolio</a>
                                <?php if (config('app.modules.gallery') === 'on'): ?>
                                <a href="<?=$this->config->getFileRootPath()?>gallery" class="dropdown-item"><i class="fa fa-image me-2"></i> Gallery</a>
                                <?php endif; ?>
                                <?php if (config('app.modules.videos') === 'on'): ?>
                                <a href="<?=$this->config->getFileRootPath()?>videos" class="dropdown-item"><i class="fa fa-film me-2"></i> Videos</a>
                                <?php endif; ?>
                                <?php if (config('app.modules.blog') === 'on'): ?>
                                <a href="<?=$this->config->getFileRootPath()?>blog" class="dropdown-item"><i class="fa fa-pencil-square me-2"></i> Blog</a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (config('app.modules.testimonials') === 'on'): ?>
                        <a href="<?=$this->config->getFileRootPath()?>testimonials" class="nav-item nav-link"><i class="fa fa-star me-1"></i> Review</a>
                        <?php endif; ?>
                        <a href="<?=$this->config->getFileRootPath()?>feedback" class="nav-item nav-link"><i class="fa fa-envelope me-1"></i> Contact</a>

                        <?php if (!isset($_SESSION['authenticated'])): ?>
                            <a href="<?=$this->config->getFileRootPath()?>auth/login" class="nav-item nav-link"><i class="fa fa-sign-in-alt me-1"></i> Login</a>
                            <?php if (config('app.allow_registration') === true): ?>
                            <a href="<?=$this->config->getFileRootPath()?>auth/signup" class="nav-item nav-link"><i class="fa fa-user-plus me-1"></i> Register</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Account dropdown -->
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-user-circle me-1"></i> Account</a>
                                <div class="dropdown-menu shadow-sm border-0 rounded-3 py-2">
                                    <a href="<?=$this->config->getFileRootPath()?><?= Auth()->isAdmin() ? 'admin/dashboard' : 'user/dashboard' ?>" class="dropdown-item"><i class="fa fa-tachometer-alt me-2"></i> Dashboard</a>
                                    <div class="dropdown-divider"></div>
                                    <a href="<?=$this->config->getFileRootPath()?>auth/logout" class="dropdown-item"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                    <button type="button" class="btn text-secondary ms-3" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fa fa-search"></i></button>
                </div>
            </nav>

            <?php
            list($controller, $method) = getCurrentRoute();
            ?>
                    <div class="modal fade" id="searchModal" tabindex="-1" data-bs-backdrop="false">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content" style="background: rgba(29, 29, 39, 0.85);">
                                <form method="get" action="<?=$this->config->getFileRootPath()?>search">

                                    <div class="modal-header border-0 justify-content-end">
                                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal" aria-label="Close">
                                            <i class="fa fa-times me-1"></i> Close
                                        </button>
                                    </div>

                                    <div class="modal-body d-flex flex-column align-items-center justify-content-center">
                                        <p class="text-white mb-3">Search news, portfolio, blog posts and more</p>
                                        <div class="input-group" style="max-width: 600px;">
                                            <input type="text" name="search_keyword" class="form-control border-0 p-3"
                                                   placeholder="Type search keyword..." autofocus>
                                            <button type="submit" class="btn btn-primary px-4"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>

                                    <input type="hidden" name="searchOrigin" value="<?=$controller?>/<?=$method?>" />
                                </form>
                            </div>
                        </div>
                    </div>
        </div>

        </div>
      </div>
    </header>

    <!-- Side Slide-In Menu (auto-included for all frontend views) -->
    <div id="side-menu" class="container side-nav" style="display:none;">
        <div class="d-flex align-items-center justify-content-between px-3 py-2 text-white fw-bold"
             style="background:var(--site-theme, #fd7e14); font-size:1rem;">
            <span>Menu</span>
            <a href="#" class="btn btn-close btn-close-white btn-sm" onclick="closeSlideMenu(event)"></a>
        </div>
        <ul class="nav navbar-nav navbar-right list-group">
            <a href="<?=$this->config->getFileRootPath()?>home" class="list-group-item list-group-item-action"><i class="fa fa-home fa-fw me-2"></i> Home</a>
            <a href="<?=$this->config->getFileRootPath()?>news" class="list-group-item list-group-item-action"><i class="fa fa-newspaper fa-fw me-2"></i> News</a>
            <a href="<?=$this->config->getFileRootPath()?>portfolio" class="list-group-item list-group-item-action"><i class="fa fa-briefcase fa-fw me-2"></i> Portfolio</a>
            <?php if (config('app.modules.gallery') === 'on'): ?>
            <a href="<?=$this->config->getFileRootPath()?>gallery" class="list-group-item list-group-item-action"><i class="fa fa-image fa-fw me-2"></i> Gallery</a>
            <?php endif; ?>
            <?php if (config('app.modules.videos') === 'on'): ?>
            <a href="<?=$this->config->getFileRootPath()?>videos" class="list-group-item list-group-item-action"><i class="fa fa-film fa-fw me-2"></i> Videos</a>
            <?php endif; ?>
            <?php if (config('app.modules.blog') === 'on'): ?>
            <a href="<?=$this->config->getFileRootPath()?>blog" class="list-group-item list-group-item-action"><i class="fa fa-pencil-square fa-fw me-2"></i> Blog</a>
            <?php endif; ?>
            <?php if (config('app.modules.testimonials') === 'on'): ?>
            <a href="<?=$this->config->getFileRootPath()?>testimonials" class="list-group-item list-group-item-action"><i class="fa fa-star fa-fw me-2"></i> Review</a>
            <?php endif; ?>
            <a href="<?=$this->config->getFileRootPath()?>feedback" class="list-group-item list-group-item-action"><i class="fa fa-envelope fa-fw me-2"></i> Contact</a>
            <?php if (!isset($_SESSION['authenticated'])): ?>
                <a href="<?=$this->config->getFileRootPath()?>auth/login" class="list-group-item list-group-item-action"><i class="fa fa-sign-in-alt fa-fw me-2"></i> Login</a>
                <?php if (config('app.allow_registration') === true): ?>
                <a href="<?=$this->config->getFileRootPath()?>auth/signup" class="list-group-item list-group-item-action"><i class="fa fa-user-plus fa-fw me-2"></i> Register</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="<?=$this->config->getFileRootPath()?>auth/logout" class="list-group-item list-group-item-action"><i class="fa fa-sign-out-alt fa-fw me-2"></i> Logout</a>
                <a href="<?=$this->config->getFileRootPath()?>admin/dashboard" class="list-group-item list-group-item-action"><i class="fa fa-tachometer-alt fa-fw me-2"></i> Dashboard</a>
                <a href="<?=$this->config->getFileRootPath()?>" class="list-group-item list-group-item-action"><i class="fa fa-external-link-alt fa-fw me-2"></i> Exit Dashboard</a>
            <?php endif; ?>
        </ul>
    </div>

    <script type="application/javascript">
        function toggleSlideMenu(e) {
            e.preventDefault();
            const sideMenu = document.querySelector('#side-menu');
            const sideMenuStyle = window.getComputedStyle(sideMenu);

            document.getElementById('side-menu').style.width = sideMenuStyle.width === '250px' ? '0' : '250px';
            document.getElementById('side-menu').style.display = sideMenuStyle.display === 'none' ? 'block' : 'none';
        }

        function closeSlideMenu(e) {
            e.preventDefault();
            document.getElementById('side-menu').style.width = '0';
            document.getElementById('side-menu').style.display = 'none';
        }
    </script>   
