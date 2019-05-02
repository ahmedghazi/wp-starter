<?php

class LayGridderCMB2Integration {

	public static function init(){
		add_action( 'admin_enqueue_scripts', 'LayGridderCMB2Integration::lg_localize_gridder_js_with_cmb2', 11 );
		add_action( 'lay_after_gridder_buttons', 'LayGridderCMB2Integration::lg_echo_add_buttons' );
		add_action( 'lg_echo_cmb2_modals', 'LayGridderCMB2Integration::lg_echo_metaboxes' );
		add_filter( 'cmb2_script_dependencies', 'LayGridderCMB2Integration::add_cmb2_script_dependencies', 10, 1 );
	}

	public static function lg_localize_gridder_js_with_cmb2() {
		$metabox_form_ids = apply_filters( 'lg_cmb2_modals', array());
		$array = array();

		foreach ($metabox_form_ids as $metabox_form_id) {
			$metabox_instance = cmb2_get_metabox($metabox_form_id);
			$array []= array(
				'cmb_id' => $metabox_instance->cmb_id,
				'meta_box' => $metabox_instance->meta_box
			);
		}

		wp_localize_script( 'gridder-app', 'cmb2LgPassedData', $array ); 
	}
	

	// TODO: delete and test
	public static function lg_echo_add_buttons() {
		$metabox_form_ids = apply_filters( 'lg_cmb2_modals', array());

		foreach ($metabox_form_ids as $metabox_form_id) {
			$metabox_instance = cmb2_get_metabox($metabox_form_id);

			echo 
			'<button type="button" class="btn btn-default btn-cmb2-metabox" data-cmb2id="'.$metabox_form_id.'">
				<span class="glyphicon glyphicon-plus"></span> '.$metabox_instance->meta_box['title'].'
			</button>';
		}
	}


	public static function lg_echo_metaboxes() {
		// https://developer.wordpress.org/reference/functions/apply_filters/
		$metabox_form_ids = apply_filters( 'lg_cmb2_modals', array());

		$screen = get_current_screen();

		if (LayGridderLocation::$gridder_located_here) {
			foreach ($metabox_form_ids as $metabox_form_id) {

				$metabox_instance = cmb2_get_metabox($metabox_form_id);

				// add modal
				echo
				'<div id="'.$metabox_form_id.'" class="laygridder-cmb2-metabox lay-input-modal">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">'.$metabox_instance->meta_box['title'].'</h3>
								<button type="button" class="close close-modal-btn"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							</div>
						<div class="panel-body">
							'.cmb2_metabox_form($metabox_form_id, '', array('echo'=>false)).'
						</div>
						<div class="panel-footer clearfix"><button type="button" class="btn btn-default btn-lg btn-lg-cmb2-ok">Ok</button></div>
					</div>
					<div class="background"></div>
				</div>';
			}
		}
	}

	// add datepicker and timepicker as dependencies
	public static function add_cmb2_script_dependencies($dependencies) {
		$dependencies['jquery-ui-core'] = 'jquery-ui-core';
		$dependencies['jquery-ui-datepicker'] = 'jquery-ui-datepicker';
		$dependencies['jquery-ui-datetimepicker'] = 'jquery-ui-datetimepicker';
		return $dependencies;
	}
}

LayGridderCMB2Integration::init();