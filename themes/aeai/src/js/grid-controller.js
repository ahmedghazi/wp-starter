var gridController = (function () {

    pubsub.on("documentReady", init)
    
    function init() {
        console.log( "gridController" );
        if($("body").hasClass("home"))
            grid()
    }

    function grid() {

        var $grid = $('.grid');

        $grid.imagesLoaded(function() {
            
            setTimeout(function() {
                $grid.isotope({
                    itemSelector: '.col-md-4',
                    masonry: {
                        columnWidth: '.col-md-4'
                    }
                })
            }, 400)

        });


        $("html").off("click", "a[rel=filter]");
        $("html").on("click", "a[rel=filter]", function(e){
            e.preventDefault();

            $("nav a").not(this).removeClass("active")

            var filterValue = "*";
            if(!$(this).hasClass("active")){
                $(this).addClass("active")
                filterValue = ".category-"+$(this).attr("href").split("#")[1]
            }else{
                $(this).removeClass("active")
            }
            console.log(filterValue)
            
            $grid.isotope({ filter: filterValue });
        })
    }
    
})();

