var mobileController = (function () {
 
    init()

    function init() {
        console.log("mobileController");
        FastClick.attach(document.body);
        
        bindEvents();

        var userAgent = window.navigator.userAgent;
        if (userAgent.match(/iPad/i) || userAgent.match(/iPhone/i)) {
            $("html").addClass("is-ios")
        }
    }

    function bindEvents() {
        $("html").on('click', '.burger-wrap', function(event) {
            console.log($(this))
            if($(".burger").hasClass('active')){
                $("nav").hide();
                //$("footer").hide();
                $(".burger").removeClass('active');
            }else{
                $("nav").show();
                //$("footer").show();
                $(".burger").addClass('active');
            }
        });
    }


    
})();