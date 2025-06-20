
<header class="main-header clearfix">
    <div class="main-header__logo">
        <a href="index.html">
            <img src="assets/halpes/images/resources/logo-1.png" alt="">
        </a>
    </div>
    <div class="main-menu-wrapper">
        <div class="main-menu-wrapper__top">
            <div class="main-menu-wrapper__top-inner">
                <div class="main-menu-wrapper__left">
                    <div class="main-menu-wrapper__left-content">
                        <div class="main-menu-wrapper__left-text">
                            <p>Welcome to the Charity & Donation Theme WHY IS THIS NOT COMING THROUGH???</p>
                        </div>
                        <div class="main-menu-wrapper__left-email-box">
                            <div class="icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="email">
                                <a href="mailto:needhelp@company.com">needhelp@company.com</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="main-menu-wrapper__right">
                    <div class="main-menu-wrapper__right-social">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-facebook-square"></i></a>
                        <a href="#"><i class="fab fa-dribbble"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="main-menu-wrapper__bottom">
            <nav class="main-menu">
                <div class="main-menu__inner">
                    <a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a>
                    <ul class="main-menu__list">
                        <li class="dropdown current">
                            <a href="index.html">Home</a>
                            <ul>
                                <li>
                                    <a href="index.html">Home One</a>
                                </li>
                                <li><a href="index2.html">Home Two</a></li>
                                <li><a href="index3.html">Home Three</a></li>
                                <li class="dropdown">
                                    <a href="#">Header Styles</a>
                                    <ul>
                                        <li><a href="index.html">Header One</a></li>
                                        <li><a href="index.html">Header Two</a></li>
                                        <li><a href="index.html">Header Three</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#">Pages</a>
                            <ul>
                                <li><a href="about.html">About</a></li>
                                <li><a href="volunteers.html">Volunteers</a></li>
                                <li><a href="gallery.html">Gallery</a></li>
                                <li><a href="become-volunteer.html">Become a Volunteer</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#">Donations</a>
                            <ul>
                                <li><a href="causes.html">Causes</a></li>
                                <li><a href="causes-details.html">Causes Details</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#">Events</a>
                            <ul>
                                <li><a href="events.html">Events</a></li>
                                <li><a href="event-details.html">Event Details</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#">News</a>
                            <ul>
                                <li><a href="news.html">News</a></li>
                                <li><a href="news-details.html">News Details</a></li>
                            </ul>
                        </li>
                        <li><a href="contact.html">Contact</a></li>
                    </ul>
                    <div class="main-menu__right">
                        <a href="#" class="main-menu__search search-toggler icon-magnifying-glass"></a>
                        <a href="#" class="main-menu__cart icon-shopping-cart  "></a>
                        <div class="main-menu__phone-contact">
                            <div class="main-menu__phone-icon">
                                <span class="icon-chat"></span>
                            </div>
                            <div class="main-menu__phone-number">
                                <p>Call Anytime</p>
                                <a href="tel:92 666 888 0000">92 666 888 0000</a>
                            </div>
                        </div>
                        <a href="causes-details.html" class="main-menu__donate-btn"><i class="fa fa-heart"></i>Donate </a>
                    </div>
                </div>
            </nav>
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
