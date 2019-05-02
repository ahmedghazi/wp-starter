tinymce.PluginManager.add('fontloader', function( editor ) {
	
	var jsonString = jQuery.trim(jQuery("#fontmanager_json").val());
	if(typeof jsonString != "undefined" && jsonString != ""){
		var jsonObject = JSON.parse(jsonString);

		// create font-face CSS for tinymce iframe
		var i;
		var css = '';
		for(i=0; i<jsonObject.length; i++){
			if(typeof jsonObject[i].type == "undefined" || jsonObject[i].type == "attachment"){
				css += '@font-face{ font-family: "'+jsonObject[i].fontname+'"; src: url("'+jsonObject[i].url+'") format("woff"); } ';
			}
		}

		// add "attachment" type font-face CSS
		editor.on('PreInit', function(e){
            editor.contentStyles.push(css);
        });

		// add "link" type font link tags
        editor.on('PreInit', function(e){
        	// http://www.tinymce.com/wiki.php/api4:property.tinymce.Editor.$
            var $head = editor.$('head');

            for(i=0; i<jsonObject.length; i++){
            	if(typeof jsonObject[i].type != "undefined" && jsonObject[i].type == "link"){
            		$head.append(jsonObject[i].link);
            	}
            	else if(typeof jsonObject[i].type != "undefined" && jsonObject[i].type == "script"){
            		$head.append(jsonObject[i].script);
            	}
            }
        });

	}

} );