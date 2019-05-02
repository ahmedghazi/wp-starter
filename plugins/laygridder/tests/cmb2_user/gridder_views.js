var lg_metabox_view = function(model) {
	return String()
	+'<div class="lg-textformat-parent">'
		+'<p style="color:'+model.get('lg_colorpicker').val+';">'+model.get('lg_text').val+'</p>'
	+'</div>';
};