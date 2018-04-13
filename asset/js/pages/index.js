(function ($) {
    "use strict";

    var resizeView = function(){
        var h = window.innerHeight;
                var navbar = $('#menu'), overlay = $('.home-sec .overlay'), tagline = $('.tag-line');                
                var sumNT = navbar.innerHeight() + tagline.innerHeight();            
                var oH = (h - sumNT) + navbar.innerHeight();            
                overlay.css({"min-height": oH});
    }

    var mainApp = {
        scrollAnimation_fun: function () {

            /*====================================
             ON SCROLL ANIMATION SCRIPTS 
            ======================================*/
                       
            window.scrollReveal = new scrollReveal();

        },
        scroll_fun: function () {

        /*====================================
                EASING PLUGIN SCRIPTS 
            ======================================*/
        $(function () {
            $('.move-me a').bind('click', function (event) { //just pass move-me in design and start scrolling
                var $anchor = $(this);
                $('html, body').stop().animate({
                    scrollTop: $($anchor.attr('href')).offset().top
                }, 1000, 'easeInOutQuad');
                event.preventDefault();
            });
        });

    },
        top_flex_slider_fun:function()
        {
            /*====================================
            FLEX SLIDER SCRIPTS 
            ======================================*/
            $('#main-section').flexslider({
                animation: "fade", //String: Select your animation type, "fade" or "slide"
                slideshowSpeed: 3000,           //Integer: Set the speed of the slideshow cycling, in milliseconds
                animationSpeed: 1000,           //Integer: Set the speed of animations, in milliseconds
                startAt: 0,    //Integer: The slide that the slider should start on. Array notation (0 = first slide)

            });
        },      
        custom_fun:function()
        {
            /*====================================
             WRITE YOUR   SCRIPTS  BELOW
            ======================================*/
            $('.action-btn').on('click', function () {
                var setting = {};
                setting.ACTION_TYPE = this.innerHTML.trim();
                setting.PAGE_TYPE = $(this).data('fetch');
                mainApp.renderPage(setting); 
            });

            $('.btn-activate').on('click', function () {
                var setting = {};
                setting.PAGE_TYPE = $(this).data('page');
                mainApp.loginSwitch(setting);
            });

            $(window).resize(function () {
                resizeView();             
            });

        },
        renderPage: function (setting) {
            switch(setting.PAGE_TYPE){
                case 'ticket-login':
                    window.location.href = 'login.php';
                    break;
            }  
        }

    }  
   
   
    $(document).ready(function () {
        mainApp.scrollAnimation_fun();
        mainApp.scroll_fun();
        mainApp.top_flex_slider_fun();
        mainApp.custom_fun();
        resizeView();
    });
}(jQuery));


