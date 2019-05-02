var carouselController = (function () {
    var $carousel = null, 
        len,part,ready;

    pubsub.on("navChanged", init)
    pubsub.on("documentReady", init)
    

    function init() {
        //console.log("carouselController init")
        if($("[data-carousel] figure").length){
            initCarousel()

            //if($("#pager").length)initMouseMovePager()
        }

        // pubsub.on("GALERIE_READY", initCarousel)
        pubsub.on("CAROUSEL_PLAY", function (e) { 
            if($carousel)$carousel.slick('play')
        })

        pubsub.on("CAROUSEL_PAUSE", function (e) { 
            if($carousel)$carousel.slick('pause')
        })
    }

    function initCarousel() {
        if($carousel)
            $carousel.slick('unslick')

        $carousel = $("[data-carousel]");
        
        $carousel.imagesLoaded( function() {  
            $carousel.on("init.slick", function(slick){
                len = $(".slide").length
                part = ww / len

                ready = true
            });
            $carousel.on('beforeChange', function(event, slick, currentSlide, nextSlide){
                ready = true;
            });

            var isHome = $("body").hasClass("home");
            var appendDots = isHome ? "#pager" : "";
            
            $carousel.slick({
                fade: isHome,
                infinite: true,
                speed: 600,
                slidesToShow: 1,
                dots: isHome,
                appendDots: appendDots,
                autoplay: true,
                //lazyLoad: 'ondemand',
                prevArrow: $('.control.prev'),
                nextArrow: $('.control.next')
            });
            
        });
    }

    function initMouseMovePager(){
        $(".carousel-wrap").on("mousemove", function (e) {
            mouseMovePager(e.pageX)
        })
    }

    function mouseMovePager(x){
        //console.log("mouseMovePager",ready)
        if(!ready)return
        ready = !ready;
        
        $(".carousel-wrap").on("mousemove", function (e) {
            var idx = Math.ceil(e.pageX / part)
            $("#pager li").eq(idx-1).click()
        })
    }

    function setReady(val) {  
        ready = val;
    }
    function getReady() {  
        return ready;
    }

    return {
        setReady: setReady,
        getReady: getReady
    };
})();
 
//myRevealingModule.setName( "Paul Kinlan" );

