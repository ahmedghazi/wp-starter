<?php
// http://codex.wordpress.org/Settings_API#Examples
class LayGridderOptions{

	public static $phoneLayoutActive;
	public static $disableInputs;
	public static $phone_breakpoint;
	public static $tablet_breakpoint;

	public function __construct(){
		add_action( 'admin_menu', array($this, 'layoptions_setup_menu'), 9 );
		add_action( 'admin_init', array($this, 'gridder_options_settings_api_init') );
		add_action( 'admin_enqueue_scripts', array($this, 'lg_options_enqueue_scripts') );

		LayGridderOptions::$phone_breakpoint = (int)get_option( 'gridder_options_breakpoint', 700 );
		LayGridderOptions::$tablet_breakpoint = (int)get_option( 'gridder_options_textformats_tablet_breakpoint', 1024 );

		LayGridderOptions::$phoneLayoutActive = (get_option('gridder_options_extra_gridder_for_phone', '') == "on");
		LayGridderOptions::$disableInputs = (get_option('gridder_options_disable_gridder_inputs', '') == "on");

		add_action( 'wp_head', array($this, 'horizontal_lines_css') );
		add_action( 'admin_head', array($this, 'horizontal_lines_css'));
}

	public function lg_options_enqueue_scripts($hook){
		if($hook == "toplevel_page_laygridder-options-page"){
			wp_enqueue_style( 'wp-color-picker' ); 
			wp_enqueue_script( 'lg_settings_colorpicker_controller', LG_PLUGIN_URL.'/options/assets/js/lg_settings_colorpicker_controller.js', array( 'wp-color-picker' ), LG_VER, true );
			wp_enqueue_script( 'lg_settings_showhide', LG_PLUGIN_URL.'/options/assets/js/showhide-settings.js', array(), LG_VER );
		}
	}

	public function layoptions_setup_menu(){
        add_submenu_page( 'laygridder-options-page', 'Options', 'Options', 'can_manage_lg_options', 'laygridder-options-page', array($this, 'gridder_options_markup') );
	}
	 
	public function gridder_options_markup(){
    	require_once( LG_PLUGIN_PATH.'/options/options_markup.php' );
	}

	public static function horizontal_lines_css(){
		// horizontal lines
		$hr_height = get_option( 'gridder_options_hr_height', '1' );
		$hr_color = get_option( 'gridder_options_hr_color', '#000000' );
		echo
		'<!-- horizontal lines -->
		<style>
			.lay-hr{
				height:'.$hr_height.'px;
				background-color:'.$hr_color.';
			}
		</style>';
	}

	public function gridder_options_settings_api_init() {
	 	// Add the section to gridderdefaults settings so we can add our
	 	// fields to it
	 	add_settings_section(
			'laygridder-options_section',
			'',
			'',
			'laygridder-options-page'
		);

	 	add_settings_section(
			'laygridder-textformats_options_section',
			'Textformats Options',
			'',
			'laygridder-options-page'
		);

	 	add_settings_section(
 			'laygridder-hr_section',
 			'Horizontal Lines',
 			'',
 			'laygridder-options-page'
 		);

	 	add_settings_field(
			'gridder_options_breakpoint',
			'Phone Breakpoint',
			array($this, 'breakpoint_setting'),
			'laygridder-options-page',
			'laygridder-options_section'
		);
	 	register_setting( 'laygridder-options-page', 'gridder_options_breakpoint' );

 	 	add_settings_field(
 			'gridder_options_max_width',
 			'Max width of content',
 			array($this, 'max_width_setting'),
 			'laygridder-options-page',
 			'laygridder-options_section'
 		);
 	 	register_setting( 'laygridder-options-page', 'gridder_options_max_width' );

 	 	add_settings_field(
 			'gridder_options_templates',
 			'Activate Templates',
 			array($this, 'templates_setting'),
 			'laygridder-options-page',
 			'laygridder-options_section'
 		);
 	 	register_setting( 'laygridder-options-page', 'gridder_options_templates' );

 	 	add_settings_field(
 			'gridder_options_extra_gridder_for_phone',
 			'Activate Custom Phone Layouts',
 			array($this, 'extra_gridder_for_phone_setting'),
 			'laygridder-options-page',
 			'laygridder-options_section'
 		);
 	 	register_setting( 'laygridder-options-page', 'gridder_options_extra_gridder_for_phone' );

 	 	add_settings_field(
 			'gridder_options_disable_gridder_inputs',
 			'Hide Gridder inputs "Column Count", "Column Gutter", "Row Gutter", "Set Frame"',
 			array($this, 'disable_gridder_inputs_setting'),
 			'laygridder-options-page',
 			'laygridder-options_section'
 		);
 	 	register_setting( 'laygridder-options-page', 'gridder_options_disable_gridder_inputs' );

 	 	add_settings_field(
 			'gridder_options_textformats_everywhere',
 			'Add "Text Formats" button to all WYSIWYG editors',
 			array($this, 'textformats_everywhere_setting'),
 			'laygridder-options-page',
 			'laygridder-textformats_options_section'
 		);
 	 	register_setting( 'laygridder-options-page', 'gridder_options_textformats_everywhere' );

 	 	add_settings_field(
 			'gridder_options_minimal_wysiwyg_buttons',
 			'For WYSIWYG editors with "Text Formats" button, only show minimal amount of buttons',
 			array($this, 'minimal_wysiwyg_buttons_setting'),
 			'laygridder-options-page',
 			'laygridder-textformats_options_section'
 		);
 	 	register_setting( 'laygridder-options-page', 'gridder_options_minimal_wysiwyg_buttons' );

 	 	add_settings_field(
 			'gridder_options_textformats_for_tablet',
 			'Add "Tablet" settings to Textformats',
 			array($this, 'textformats_for_tablet_setting'),
 			'laygridder-options-page',
 			'laygridder-textformats_options_section'
 		);
 	 	register_setting( 'laygridder-options-page', 'gridder_options_textformats_for_tablet' );

 	 	add_settings_field(
 			'gridder_options_textformats_tablet_breakpoint',
 			'Tablet Breakpoint for Textformats',
 			array($this, 'textformats_tablet_breakpoint_setting'),
 			'laygridder-options-page',
 			'laygridder-textformats_options_section'
 		);
 	 	register_setting( 'laygridder-options-page', 'gridder_options_textformats_tablet_breakpoint' );

 	 	add_settings_field(
 			'gridder_options_textformats_advanced_lineheight',
 			'More line-height options',
 			array($this, 'textformats_advanced_lineheight_setting'),
 			'laygridder-options-page',
 			'laygridder-textformats_options_section'
 		);
 	 	register_setting( 'laygridder-options-page', 'gridder_options_textformats_advanced_lineheight' );

 	 	// hr section
 	 	add_settings_field(
 			'gridder_options_hr_color',
 			'Horizontal Line Color',
 			array($this, 'hr_color_setting'),
 			'laygridder-options-page',
 			'laygridder-hr_section'
 		);
 	 	register_setting( 'laygridder-options-page', 'gridder_options_hr_color' );

 	 	add_settings_field(
 			'gridder_options_hr_height',
 			'Horizontal Line Height',
 			array($this, 'hr_height_setting'),
 			'laygridder-options-page',
 			'laygridder-hr_section'
 		);
 	 	register_setting( 'laygridder-options-page', 'gridder_options_hr_height' );

	}

	function hr_height_setting(){
		$val = get_option( 'gridder_options_hr_height', '1' );
		echo '<input type="number" min="1" step="1" name="gridder_options_hr_height" id="gridder_options_hr_height" value="'.$val.'"/> px';		
	}

	function hr_color_setting(){
		$val = get_option( 'gridder_options_hr_color', '#000000' );
		echo '<input type="text" name="gridder_options_hr_color" id="gridder_options_hr_color" value="'.$val.'" class="lay-hr-color-picker">';		
	}

	function templates_setting(){
		$val = get_option( 'gridder_options_templates', "" );
		$checked = "";
		if( $val == "on" ){
			$checked = "checked";
		}
		echo '<input type="checkbox" name="gridder_options_templates" id="gridder_options_templates" '.$checked.'>';		
	}

	function textformats_for_tablet_setting(){
		$val = get_option( 'gridder_options_textformats_for_tablet', "" );
		$checked = "";
		if( $val == "on" ){
			$checked = "checked";
		}
		echo '<input type="checkbox" name="gridder_options_textformats_for_tablet" id="gridder_options_textformats_for_tablet" '.$checked.'>';		
	}

	function disable_gridder_inputs_setting(){
		$val = get_option( 'gridder_options_disable_gridder_inputs', "" );
		$checked = "";
		if( $val == "on" ){
			$checked = "checked";
		}
		echo '<input type="checkbox" name="gridder_options_disable_gridder_inputs" id="gridder_options_disable_gridder_inputs" '.$checked.'>';
	}

	function minimal_wysiwyg_buttons_setting(){
		$val = get_option( 'gridder_options_minimal_wysiwyg_buttons', "" );
		$checked = "";
		if( $val == "on" ){
			$checked = "checked";
		}
		echo '<input type="checkbox" name="gridder_options_minimal_wysiwyg_buttons" id="gridder_options_minimal_wysiwyg_buttons" '.$checked.'><label for="gridder_options_minimal_wysiwyg_buttons">This is useful if you want to make sure that only Text Formats are used for styling text.</label>';
	}

	function textformats_advanced_lineheight_setting(){
		$val = get_option('gridder_options_textformats_advanced_lineheight', '');
		$checked = "";
		if( $val == "on" ){
			$checked = "checked";
		}
		echo '<input type="checkbox" name="gridder_options_textformats_advanced_lineheight" id="gridder_options_textformats_advanced_lineheight" '.$checked.'>
		<label for="gridder_options_textformats_advanced_lineheight">Line-height will be setable for desktop, tablet and phone in unitless and px.</label>';		
	}

	function textformats_tablet_breakpoint_setting(){
		$breakpoint = get_option( 'gridder_options_textformats_tablet_breakpoint', 1024 );
		echo 
		'<input type="number" min="0" step="1" value="'.$breakpoint.'" name="gridder_options_textformats_tablet_breakpoint" id="gridder_options_textformats_tablet_breakpoint"> px
		<br><label for="gridder_options_textformats_tablet_breakpoint">Breakpoint between desktop and tablet version. Needs to be bigger than "Phone Breakpoint".</label>
		
		<br><code>@media (min-width: <i>Breakpoint+1</i> ){ desktop textformats }</code>
		<br><code>@media (max-width: <i>Breakpoint</i> ){ tablet textformats }</code>';		
	}

	function breakpoint_setting(){
		$breakpoint = get_option( 'gridder_options_breakpoint', 700 );
		echo 
		'<input type="number" min="0" step="1" value="'.$breakpoint.'" name="gridder_options_breakpoint" id="gridder_options_breakpoint"> px
		<br><label for="gridder_options_breakpoint">Breakpoint between desktop and phone version</label>
		
		<br><code>@media (min-width: <i>Breakpoint+1</i> ){ desktop version }</code>
		<br><code>@media (max-width: <i>Breakpoint</i> ){ phone version }</code>';
	}

	function max_width_setting(){
		$maxwidth = get_option( 'gridder_options_max_width', '0' );
		echo '<input type="number" min="0" step="1" name="gridder_options_max_width" id="gridder_options_max_width" value="'.$maxwidth.'"> <label for="gridder_options_max_width"> px (0 = off)</label>';
	}

	function textformats_everywhere_setting(){
		$val = get_option('gridder_options_textformats_everywhere', '');
		$checked = "";
		if( $val == "on" ){
			$checked = "checked";
		}
		echo '<input type="checkbox" name="gridder_options_textformats_everywhere" id="gridder_options_textformats_everywhere" '.$checked.'>
		<br><label for="gridder_options_textformats_everywhere">To make the Text Format "Default" work on the frontend you need to use a wrapping div with a class of <code>lg-textformat-parent</code>.<br>And your text elements need to be direct children of <code>.lg-textformat-parent</code> for example: <code>&lt;div class="lg-textformat-parent"&gt;&lt;p&gt;hello&lt;/p&gt;&lt;/div&gt;</code>.</label>';
	}

	function extra_gridder_for_phone_setting(){
		$val = get_option('gridder_options_extra_gridder_for_phone', '');
		$checked = "";
		if( $val == "on" ){
			$checked = "checked";
		}
		echo '<input type="checkbox" name="gridder_options_extra_gridder_for_phone" id="gridder_options_extra_gridder_for_phone" '.$checked.'>
		<br><label for="gridder_options_extra_gridder_for_phone">In the Gridder you will have new buttons to switch between the desktop layout and custom phone layout.</label>';		
	}


}
new LayGridderOptions();