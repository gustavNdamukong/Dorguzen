
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
                <a href="<?=$this->config->getHomePage()?>" class="navbar-brand p-0 d-flex flex-column align-items-center" style="line-height:1;">
                    <img src="<?=$this->config->getFileRootPath()?>assets/images/dorguzen-logo.png" alt="Dorguzen" style="height:60px; width:auto;">
                    <span style="font-size:0.7rem; font-weight:700; letter-spacing:2px; color:#fff; background:#0d6efd; padding:2px 8px; border-radius:3px; margin-top:3px; text-transform:uppercase;">ADMIN</span>
                </a>
                
                <button onclick="toggleSlideMenu(event)" type="button" class="navbar-toggler" data-bs-toggle="collapse">
                    <span class="fa fa-bars"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">

                        <a href="<?=$this->config->getHomePage()?>" class="nav-item nav-link"><i class="fa fa-home"></i> Home</a>

                        <!-- Content dropdown -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-pencil-square-o me-1"></i> Content</a>
                            <div class="dropdown-menu shadow-sm border-0 rounded-3 py-2">
                                <a href="<?=$this->config->getFileRootPath()?>admin/news" class="dropdown-item">📰 News</a>
                                <a href="<?=$this->config->getFileRootPath()?>admin/portfolio" class="dropdown-item">🖼️ Portfolio</a>
                                <?php if (config('app.modules.gallery') === 'on'): ?>
                                <a href="<?=$this->config->getFileRootPath()?>admin/gallery" class="dropdown-item">📷 Gallery</a>
                                <?php endif; ?>
                                <?php if (config('app.modules.videos') === 'on'): ?>
                                <a href="<?=$this->config->getFileRootPath()?>admin/videos" class="dropdown-item">🎬 Videos</a>
                                <?php endif; ?>
                                <?php if (config('app.modules.blog') === 'on'): ?>
                                <a href="<?=$this->config->getFileRootPath()?>admin/blog" class="dropdown-item">✍️ Blog</a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Communications dropdown -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-comments-o me-1"></i> Communications</a>
                            <div class="dropdown-menu shadow-sm border-0 rounded-3 py-2">
                                <a href="<?=$this->config->getFileRootPath()?>admin/newsletters" class="dropdown-item">📧 Newsletters</a>
                                <a href="<?=$this->config->getFileRootPath()?>admin/subscribers" class="dropdown-item">👥 Subscribers</a>
                                <a href="<?=$this->config->getFileRootPath()?>admin/contactMessages" class="dropdown-item">✉️ Messages</a>
                            </div>
                        </div>

                        <!-- Admin dropdown -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-cog me-1"></i> Admin</a>
                            <div class="dropdown-menu shadow-sm border-0 rounded-3 py-2">
                                <a href="<?=$this->config->getFileRootPath()?>admin/dashboard" class="dropdown-item">🏠 Dashboard</a>
                                <a href="<?=$this->config->getFileRootPath()?>admin/manageUsers" class="dropdown-item">👤 Manage Users</a>
                                <a href="<?=$this->config->getFileRootPath()?>admin/baseSettings?change=0" class="dropdown-item">⚙️ Settings</a>
                                <a href="<?=$this->config->getFileRootPath()?>admin/log" class="dropdown-item">📋 Logs</a>
                            </div>
                        </div>

                        <?php if (!isset($_SESSION['authenticated'])): ?>
                            <a href="<?=$this->config->getFileRootPath()?>auth/login" class="nav-item nav-link">Login</a>
                            <?php if (config('app.allow_registration') === true): ?>
                                <a href="<?=$this->config->getFileRootPath()?>auth/signup" class="nav-item nav-link">Register</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="<?=$this->config->getFileRootPath()?>auth/logout" class="nav-item nav-link">Logout</a>
                        <?php endif; ?>

                    </div>

                </div>
            </nav>
        </div>

        </div>
      </div>
    </header>

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
