<?php
class LayGridderSetup{

	public static $minimal_tinymce_buttons = array('styleselect', 'bold', 'italic', 'undo', 'redo', 'link', 'unlink', 'alignleft', 'aligncenter', 'alignright', 'removeformat', 'charmap');

	public function __construct(){
		add_action( 'admin_menu', array($this, 'menu'), 8 );
		add_action( 'admin_enqueue_scripts', array($this, 'register_marionette'), 5 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_style'));
		add_filter( 'max_srcset_image_width', array( $this, 'set_max_srcset_image_width' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'lay_collapse_and_expand_explanations'));
		add_action( 'wp_ajax_set_explanation_expand_status_via_ajax', array( $this, 'set_explanation_expand_status_via_ajax') );

		add_action( 'admin_init', array( $this, 'register_lay_iris' ) );

		// https://wordpress.stackexchange.com/questions/28021/how-to-publish-a-post-with-empty-title-and-empty-content
		add_filter('pre_post_title', 'LayGridderSetup::wpse28021_mask_empty');
		add_filter('pre_post_content', 'LayGridderSetup::wpse28021_mask_empty');
		add_filter('wp_insert_post_data', 'LayGridderSetup::wpse28021_unmask_empty');
	}

	// https://codex.wordpress.org/Function_Reference/add_cap
	public static function lg_add_custom_capability(){
		/*
			I use capability 'can_manage_lg_options' for all 'add_submenu_page' option pages except for license key.
			I add the capability 'can_manage_lg_options' to admin user role
			In the demo website of laygridder, all demo accounts have the role "user".
			In the theme of the demo account, I add the capability 'can_manage_lg_options' to user role editor.
			This way, the demo user, can see all laygridder options, even though s/he is not an admin
		*/
		global $wp_roles; // global class wp-includes/capabilities.php
		$wp_roles->add_cap( 'administrator', 'can_manage_lg_options' ); 
	}

	public static function die_if_laytheme_active(){
    $theme = wp_get_theme();
    if ( 'Lay Theme' == $theme->name ) {
        // Stop activation redirect and show error
        wp_die('Sorry, but "LayGridder" is not compatible with Lay Theme!<br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
    }
	}

	public function register_lay_iris(){
		// using modified version of iris to prevent scrolling when user drags inside colorpicker
		wp_register_script( 'lay-iris', LG_PLUGIN_URL.'/assets/js/iris.js', array( 'jquery-ui-widget', 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch', 'jquery-color' ), LG_VER );
	}

	public static function set_max_srcset_image_width(){
		return 5000;
	}

	public static function register_marionette(){
		wp_register_script( 'marionette', LG_PLUGIN_URL.'/assets/js/backbone.marionette.min.js', array( 'jquery', 'underscore', 'backbone', 'json2' ), LG_VER );
	}

	public static function admin_style() {
	    wp_enqueue_style('laygridder-admin-style', LG_PLUGIN_URL.'/assets/css/admin.css', array(), LG_VER );
	}

	public function menu(){
		// http://wordpress.stackexchange.com/questions/66498/add-menu-page-with-different-name-for-first-submenu-item
			add_menu_page( 'LayGridder', 'LayGridder', 'can_manage_lg_options', 'laygridder-options-page', '' );
	}

	public function lay_collapse_and_expand_explanations(){
		wp_enqueue_script( 'lay-collapse-and-expand-explanations', LG_PLUGIN_URL."/setup/assets/js/collapse_and_expand_explanations.js", array( 'jquery', 'underscore' ), LG_VER);
	}

	public static function set_explanation_expand_status_via_ajax(){
		$optionname = $_POST['optionname'];
		$value = $_POST['value'];
		if($value == 'expanded' || $value == "collapsed"){
			update_option( $optionname, $value );
		}
		die();
	}

	public static function wpse28021_mask_empty($value) {
		if ( empty($value) ) {
				return ' ';
		}
		return $value;
	}

	public static function wpse28021_unmask_empty($data) {
		if ( ' ' == $data['post_title'] ) {
				$data['post_title'] = '';
		}
		if ( ' ' == $data['post_content'] ) {
				$data['post_content'] = '';
		}
		return $data;
	}
}
new LayGridderSetup();