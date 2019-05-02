var lg_options_showhide_settings = (function(){

	var initModule = function(){
		jQuery('#gridder_options_textformats_for_tablet').on('change', showhide_for_textformats_for_tablet);
		showhide_for_textformats_for_tablet();
	};

	var showhide_for_textformats_for_tablet = function(){
		var bool = jQuery('#gridder_options_textformats_for_tablet').is(':checked');
		if(bool){
			jQuery(document.getElementById("gridder_options_textformats_tablet_breakpoint").parentNode.parentNode).show();
		}
		else{
			jQuery(document.getElementById("gridder_options_textformats_tablet_breakpoint").parentNode.parentNode).hide();
		}
	};

	return {
		initModule : initModule
	}
}());

jQuery(document).ready(function(){
	lg_options_showhide_settings.initModule();
});