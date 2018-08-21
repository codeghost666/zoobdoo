var MainController = function () {
    this.pageWrappers = $('html, body');
    this.slider = $('.slider');
};

MainController.prototype.whiteHeader = function () {
    var headerNavbar = $('.header-navbar');

    $(document).on('scroll', function () {
        var scroll = $(window).scrollTop();

        if (scroll >= 1) {
            headerNavbar.addClass('header-navbar-white');
        } else {
            headerNavbar.removeClass('header-navbar-white');
        }
    }.bind(this));

    if (headerNavbar.offset().top > 0) {
        headerNavbar.toggleClass('header-navbar-white');
    }
};

MainController.prototype.pageScroll = function () {

    $('a[ href^="#features" ]').on('click', function (e) {
        var scrollHref = $('#features');

        if (scrollHref != null) {
            this.pageWrappers.animate({scrollTop: scrollHref.offset().top - 50}, 900);
        }
        return false;
    }.bind(this));
};

MainController.prototype.carousel = function () {
    var carouselSlider = $('.carousel-property');

    this.slider.carousel({interval: 5000});

    this.slider.swiperight(function () {
        $(this).carousel('prev');
    });
    this.slider.swipeleft(function () {
        $(this).carousel('next');
    });

    carouselSlider.slick({
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 4,
        slidesToScroll: 3,
        respondTo: 'window',
        prevArrow: $('.prop-title .fa-chevron-circle-left'),
        nextArrow: $('.prop-title .fa-chevron-circle-right'),
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 2,
                    infinite: false,
                    dots: false
                }
            },
            {
                breakpoint: 990,
                arrows: false,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 589,
                arrows: false,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
};

MainController.prototype.run = function () {
    this.whiteHeader();
    this.pageScroll();
    this.carousel();
    
    $('a.disabled').on('click', function (event) {
        event.preventDefault();
    });
};

$(function () {
    var controller = new MainController();
    controller.run();
});
