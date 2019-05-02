<div id="gridder-modals">
	<div id="text-input-modal" class="lay-input-modal">
		<div class="text-modal-notices-wrap">
		<?php				
			if (get_option( 'lay_show_texteditor_notice_for_linebreak', '' ) == '') {
				echo 
				'<div data-optionname="lay_show_texteditor_notice_for_linebreak" class="alert alert-info text-modal-notice notice_for_linebreak" role="alert">
					Press "Shift" + "Enter" for a linebreak. Press only "Enter" for a new paragraph. 
					<button type="button" class="btn btn-default btn-xs dont-show-again">Don\'t show again</button> 
					<button type="button" class="btn btn-default btn-xs next-tip">Next tip</button>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>';
			}
			if (get_option( 'lay_show_texteditor_notice_for_textformats', '' ) == '') {
				echo 
				'<div data-optionname="lay_show_texteditor_notice_for_textformats" class="alert alert-info text-modal-notice" role="alert">
					Want to change the default Text Style? Or create a Text Style for Paragraphs or Headlines and use it anywhere? <a target="_blank" href="'.get_admin_url( null, "admin.php?page=laygridder-textformats").'">Use Textformats!</a> 
					<button type="button" class="btn btn-default btn-xs dont-show-again">Don\'t show again</button> 
					<button type="button" class="btn btn-default btn-xs next-tip">Next tip</button>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>';
			}
			if (get_option( 'lay_show_texteditor_notice_for_clear_formatting', '' ) == '') {
				echo 
				'<div data-optionname="lay_show_texteditor_notice_for_clear_formatting" class="alert alert-info text-modal-notice" role="alert">
					Did you apply a Text Format but it doesn\'t look right? Try these steps:
					<div style="margin: 10px 0;">
						- Select your text<br>
						- Click <img src="'.LG_PLUGIN_URL.'/gridder/assets/img/textmodal_notices/clear_formatting.png" alt=""> "Clear formatting"<br>
						- Apply your Text Format
					</div>
					<button type="button" class="btn btn-default btn-xs dont-show-again">Don\'t show again</button> <button type="button" class="btn btn-default btn-xs next-tip">Next tip</button>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>';
			}
			if (get_option( 'lay_show_texteditor_notice_for_nonbreakingspace', '' ) == '') {
				echo 
				'<div data-optionname="lay_show_texteditor_notice_for_nonbreakingspace" class="alert alert-info text-modal-notice" role="alert">
					Need a space but want to prevent a linebreak? Use a <img src="'.LG_PLUGIN_URL.'/gridder/assets/img/textmodal_notices/nonbreaking_space.png" alt=""> "Nonbreaking space".
					<button type="button" class="btn btn-default btn-xs dont-show-again">Don\'t show again</button> <button type="button" class="btn btn-default btn-xs next-tip">Next tip</button>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>';
			}
			if (get_option( 'lay_show_texteditor_notice_for_softhyphen', '' ) == '') {
				echo 
				'<div data-optionname="lay_show_texteditor_notice_for_softhyphen" class="alert alert-info text-modal-notice" role="alert">
					Have a long word that overflows its text column? Use a <img src="'.LG_PLUGIN_URL.'/gridder/assets/img/textmodal_notices/soft_hyphen.png" alt=""> "Soft hyphen" to make the word break at a certain position. <a href="http://en.wikipedia.org/wiki/Soft_hyphen" target="_blank">more info</a>
					<button type="button" class="btn btn-default btn-xs dont-show-again">Don\'t show again</button> 
					<button type="button" class="btn btn-default btn-xs next-tip">Close</button>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>';
			}
			// attention! only last tip should have 'close' as next-button text instead of 'next tip'
		?>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
					<h3 class="panel-title js-text-modal-title">Add Text</h3>
					<button type="button" class="close close-modal-btn"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				</div>
				<div class="panel-body">
				<?php			
				wp_editor( "", "gridder_text_editor", LayGridder::$tinymceSettings );
				?>
			</div>
			<div class="panel-footer clearfix"><button type="button" class="btn btn-default btn-lg add-text-btn">Ok</button></div>
		</div>
		<div class="background"></div>
	</div>

	<div id="gridder-header-cmb2-buttons">
		<?php do_action( 'lay_after_gridder_buttons' ); ?>
	</div>

	<?php do_action( 'lg_echo_cmb2_modals' ); ?>
</div>

<script language="javascript">
	<?php 
	if (get_current_screen()->base == 'term') { ?>
		// category
		jQuery('.form-table').first().after(jQuery('#gridder-modals'));
		jQuery('.form-table').first().after('<div id="gridder"></div>');
		// submit button fix
		jQuery(document).ready(() => jQuery('.edit-tag-actions .button').on('click', function() { jQuery('#edittag').submit() }));
	<?php } else { ?>
		// post/page
		jQuery('#postbox-container-2').append('<div id="gridder"></div>');
		jQuery('#postbox-container-2').append(jQuery('#gridder-modals'));
	<?php } ?>
	jQuery('#gridder-metabox').remove();
	jQuery('#gridder-modals > *').hide();
</script>