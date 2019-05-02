<?php
function lg_register_demo_metabox() {
	$prefix = 'lg_';
	/**
	 * Sample metabox to demonstrate each field type included
	 */
	$cmb_demo = new_cmb2_box( array(
		'id'            => 'lg_metabox',
		'title'         => 'Test Element',
	) );

	$cmb_demo->add_field( array(
		'name'       => 'Test Text',
		'id'         => 'lg_text',
		'type'       => 'text',
		'default' 	 => 'John',
		'repeatable'      => true,
	) );

	$cmb_demo->add_field( array(
		'name'    => __( 'Test Color Picker', 'cmb2' ),
		'desc'    => __( 'field description (optional)', 'cmb2' ),
		'id'      => 'lg_colorpicker',
		'type'    => 'colorpicker',
		'default' => '#00ffff',
		'attributes' => array(
			'data-colorpicker' => json_encode( array(
				'palettes' => array( '#3dd0cc', '#ff834c', '#4fa2c0', '#0bc991', ),
			) ),
		),
	) );
}
add_action( 'cmb2_admin_init', 'lg_register_demo_metabox' );

function show_lg_metabox_on_frontend( $element ){
	return '<div class="lg-textformat-parent"><p style="color:'.$element->lg_colorpicker->val.'">'.implode(", ", $element->lg_text->val).'</p></div>';
}
add_filter('lg_frontend_lg_metabox', 'show_lg_metabox_on_frontend', 10, 1);

function add_cmb2_to_laygridder( $metabox_form_ids ){
    $metabox_form_ids []= 'lg_metabox';
    return $metabox_form_ids;
}
add_filter( 'lg_cmb2_modals', 'add_cmb2_to_laygridder' );

// add js element view to show element in gridder
add_action( 'admin_enqueue_scripts', function(){
	wp_enqueue_script( 'gridder_custom_views', LG_PLUGIN_URL.'/tests/cmb2_user/gridder_views.js' );
});