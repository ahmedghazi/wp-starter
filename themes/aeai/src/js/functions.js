// remap jQuery to $
(function($){})(window.jQuery);

var ww,wh;

jQuery(document).ready(function ($) {
	format();
	
	pubsub.emit("documentReady");
	init();
	
});

$(window).resize(function(){ 
	format();
});

$(window).load(function(){ 
	pubsub.emit("windowLoad");
	//console.log($)
	console.log('%cCODE ahmedghazi.com', "color: blue; font-size:15px;");
});



function init(){
	//console.log("functions init")
}

function format(){
	ww = $(window).width();
	wh = $(window).height();
}