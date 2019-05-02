var crossPageController = (function () {
 
    init()

    function init() {
        bindEvents()
    }

    function bindEvents() {
        $(document).keyup(function (event) {
            if(event.keyCode == 27){
                $(".modal-close").click()
            }
        });
    }


    
})();