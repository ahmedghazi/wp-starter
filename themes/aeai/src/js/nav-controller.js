var navController = (function () {
 
    init()

    function init() {
        var options = {
            //anchors: ".menu > a",
            //blacklist: '.sub-menu a, .projet-more',
            prefetch: false,
            cacheLength: 2,
            onBefore: function($currentTarget, $container) {
                //console.log("onBefore")
                //$('.loader').css({width: 25+'%'});
            },
            onStart: {
                duration: 0, // Duration of our animation
                render: function ($container) {
                    // Add your CSS animation reversing class
                    $container.addClass('is-exiting');
                    //console.log("onStart")
                    $("#menu-wrapper").removeClass("nav-open");
                    // Restart your animation
                    //smoothState.restartCSSAnimations();
                    //$('.loader').css({width: 50+'%'});

                }
            },
            onProgress: {
                // How long this animation takes
                duration: 0,
                // A function that dictates the animations that take place
                render: function ($container) {
                    console.log("onProgress")
                    //$('.loader').css({width: 75+'%'});
                }
            },
            onReady: {
                duration: 0,
                render: function ($container, $newContent) {
                    console.log("onReady")
                    $container.removeClass('is-exiting-end');
                    // Inject the new content
                    $container.html($newContent);
                    //$('.loader').css({width: 100+'%'});
                    setTimeout(function(){
                        $("body").attr('class', $("[name=bc]").val())

                        pubsub.emit("navChanged");
                        
                        $container.removeClass('is-exiting');
                        
                    }, 100);
                    
                }
            },
            
            onAfter: function($container, $newContent){
                console.log("onAfter")
                $container.removeClass('is-exiting-end');
                setTimeout(function(){
                    //$('.loader').css({width: 0+'%'});
                }, 200)
            }
        };

        smoothState = $('#page').smoothState(options).data('smoothState');
        //console.log(smoothState)
        /*$.ajaxSetup({ 
            xhr: function () { 
                console.log('setup XHR...'); 
            } 
        })*/
    }

    
    // Reveal public pointers to
    // private functions and properties

    return {
        init: init
    };

})();