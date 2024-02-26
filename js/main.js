(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();
    
    
    // Initiate the wowjs
    new WOW().init();


    // Sticky Navbar
    $(window).scroll(function () {
        if ($(this).scrollTop() > 45) {
            $('.navbar').addClass('sticky-top shadow-sm');
        } else {
            $('.navbar').removeClass('sticky-top shadow-sm');
        }
    });
    
    
    // Dropdown on mouse hover
    const $dropdown = $(".dropdown");
    const $dropdownToggle = $(".dropdown-toggle");
    const $dropdownMenu = $(".dropdown-menu");
    const showClass = "show";
    
    $(window).on("load resize", function() {
        if (this.matchMedia("(min-width: 992px)").matches) {
            $dropdown.hover(
            function() {
                const $this = $(this);
                $this.addClass(showClass);
                $this.find($dropdownToggle).attr("aria-expanded", "true");
                $this.find($dropdownMenu).addClass(showClass);
            },
            function() {
                const $this = $(this);
                $this.removeClass(showClass);
                $this.find($dropdownToggle).attr("aria-expanded", "false");
                $this.find($dropdownMenu).removeClass(showClass);
            }
            );
        } else {
            $dropdown.off("mouseenter mouseleave");
        }
    });
    
    
    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        margin: 25,
        dots: false,
        loop: true,
        center: true,
        responsive: {
            0:{
                items:1
            },
            576:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:3
            }
        }
    });


    // Portfolio isotope and filter
    var portfolioIsotope = $('.portfolio-container').isotope({
        itemSelector: '.portfolio-item',
        layoutMode: 'fitRows'
    });
    $('#portfolio-flters li').on('click', function () {
        $("#portfolio-flters li").removeClass('active');
        $(this).addClass('active');

        portfolioIsotope.isotope({filter: $(this).data('filter')});
    });

    //cookie consent popup
    //----------------------------------------------------------------------------------
    //Using Local storage
    //const storageType = localStorage; (UNCOMMENT THIS LINE TO USE LOCAL STORAGE)

    //OR
    //Use Session storage
    //works exactly the same way as local storage with same methods & all. Only diff is;
    //session variables get wiped out if the browser gets closed
    //const storageType = sessionStorage; (UNCOMMENT THIS LINE TO USE SESSION STORAGE)

    //OR
    //Using Cookies
    //cookies are written differently, so we can write custom get/setItem functions to 
    //make it work just like Session & Local storage  
    const cookieStorage = {
        getItem: (key) => {
            //get list of all cookies & capture the values into an object (key)
            const cookies = document.cookie
                    .split(';')
                    .map(cookie => cookie.split('='))
                    .reduce(
                        //2nd arg here ({}) initialises the return value as an object
                        (acc, [key, value]) => ({ ...acc, [key.trim()]:value }), {} 
                    );
            return cookies[key];
        },
        setItem: (key, value) => {
            document.cookie = `${key} = ${value}`;
        }
    } // (UNCOMMENT THIS BLOCK TO USE COOKIES FOR STORAGE)

    //we can then say 
    const storageType = cookieStorage; 
    //----------------------------------------------------------------------------------

    const consentPropertyName = 'jdc_consent';

    const shouldShowPopup = () => !storageType.getItem(consentPropertyName);
    const saveToStorage = () => storageType.setItem(consentPropertyName, true);

    const consentPopup = document.getElementById('consent-popup');
    const consentAcceptBtn = document.getElementById('accept-cookie-use');

    //when accept btn is clicked
    const acceptFunc = event => {
        saveToStorage(storageType);
        consentPopup.classList.add('consent-hidden');
    }

    consentAcceptBtn.addEventListener('click', acceptFunc);

    if (shouldShowPopup(storageType))
    {
        //Delay the consent popup by 2 secs
        setTimeout(() => {
            consentPopup.classList.remove('consent-hidden');
        }, 2000);
    }
    
})(jQuery);

