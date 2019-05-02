tinymce.PluginManager.add('height', function( editor ) {

	// heights of top and bottom panels
	var diff = 215;

	var height = (window.innerHeight - diff) / 100 * 70;

	jQuery(window).on('resize', function(){
		height = (window.innerHeight - diff) / 100 * 70;
		editor.theme.resizeTo('100%', height);
	});

	editor.on('init', function(e){
        editor.theme.resizeTo('100%', height);
    });

});