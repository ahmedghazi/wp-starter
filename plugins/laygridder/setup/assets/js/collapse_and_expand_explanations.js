var lay_explanations = {};

lay_explanations = (function(){

	var bindHandleClick = function(){
		jQuery('button.lay-explanation-handle').on('click', function(e){
			e.preventDefault();
		});

		jQuery('button.lay-explanation-handle').on('click', _.throttle(function(e){
			e.preventDefault();
			var $parent = jQuery(this.parentNode.parentNode);
			var optionname = $parent.attr('data-expand-status-option-name');
			var value = 'expanded';

			if($parent.hasClass('collapsed')){
				$parent.removeClass('collapsed');
			}
			else{
				$parent.addClass('collapsed');
				value = 'collapsed';
			}

			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'set_explanation_expand_status_via_ajax',
					value: value,
					optionname: optionname,
				},
				success: function(result) {
					// console.log(result);
				}
			});

		}, 1000));
	};

	var initModule = function(){
		bindHandleClick();
	};

	return {
		initModule : initModule
	}
}());

jQuery(document).ready(function(){
	lay_explanations.initModule();
});