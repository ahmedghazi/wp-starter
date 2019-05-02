<?php
class LayGridderFontManager{

	public static $customFonts = NULL;
	public static $originalFonts;

	public function __construct(){

		$customFontsJSON = get_option('fontmanager_json', false);
		if($customFontsJSON){
			LayGridderFontManager::$customFonts = json_decode($customFontsJSON, true);
		}

		LayGridderFontManager::$originalFonts = array(
			'Andale Mono' => 'andale mono,times,serif',
			'Arial' => 'arial,helvetica,sans-serif',
			'Arial Black' => 'arial black,avant garde,sans-serif',
			'Book Antiqua' => 'book antiqua,palatino,serif',
			'Comic Sans MS' => 'comic sans ms,sans-serif',
			'Courier New' => 'courier new,courier,monospace',
			'Georgia' => 'georgia,palatino,serif',
			'Helvetica' => 'helvetica,sans-serif',
			'Helvetica Neue' => 'helvetica neue,sans-serif',
			'Impact' => 'impact,chicago,sans-serif',
			'Tahoma' => 'tahoma,arial,helvetica,sans-serif',
			'Terminal' => 'terminal,monaco,monospace',
			'Times New Roman' => 'times new roman,times,serif',
			'Trebuchet MS' => 'trebuchet ms,geneva,sans-serif',
			'Verdana' => 'verdana,geneva,sans-serif'
		);

		add_action( 'admin_menu', array($this, 'font_setup_menu'), 11 );
		add_action( 'admin_init', array($this, 'fontmanager_json_settings_api_init') );

		add_action( 'admin_enqueue_scripts', array( $this, 'fontmanager_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'fontmanager_scripts' ) );
		add_filter( 'upload_mimes', array( $this, 'allow_fonts'), 1, 1);

		add_action( 'admin_enqueue_scripts', array( $this, 'backend_add_attachment_webfonts' ), 9 );
		add_action( 'admin_head', array( $this, 'backend_add_external_webfonts' ), 9 );

		add_action( 'wp_head', array( $this, 'frontend_add_attachment_webfonts' ) );
		add_action( 'wp_head', array( $this, 'frontend_add_external_webfonts' ) );

		add_action( 'admin_footer', array( $this, 'print_fontmanager_JSON' ) );
		add_action( 'mce_external_plugins', array( $this, 'tinymce_add_fontloader') );
		add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_add_fontmanager_fonts') );

	}

	// add link tags to gridder
	public function backend_add_external_webfonts(){
		if (is_array(LayGridderFontManager::$customFonts)) {
			for($i=0; $i<count(LayGridderFontManager::$customFonts); $i++){
				if(array_key_exists('type', LayGridderFontManager::$customFonts[$i]) && LayGridderFontManager::$customFonts[$i]['type'] == "link" ){
					echo LayGridderFontManager::$customFonts[$i]['link'];
				}
				if(array_key_exists('type', LayGridderFontManager::$customFonts[$i]) && LayGridderFontManager::$customFonts[$i]['type'] == "script" ){
					echo LayGridderFontManager::$customFonts[$i]['script'];
				}
			}
		}
	}

	// add font face css to gridder
	public function backend_add_attachment_webfonts(){
		$newfontsCSS = '';
		if (is_array(LayGridderFontManager::$customFonts)) {
			for($i=0; $i<count(LayGridderFontManager::$customFonts); $i++){
				if(!array_key_exists('type', LayGridderFontManager::$customFonts[$i]) || LayGridderFontManager::$customFonts[$i]['type'] == "attachment" ){
					$newfontsCSS .= '@font-face{ font-family: "'.LayGridderFontManager::$customFonts[$i]['fontname'].'"; src: url("'.LayGridderFontManager::$customFonts[$i]['url'].'") format("woff"); } ';
				}
			}
		}
		wp_add_inline_style( 'wp-admin', $newfontsCSS );
	}

	public function frontend_add_external_webfonts(){
		if (is_array(LayGridderFontManager::$customFonts)) {
			for($i=0; $i<count(LayGridderFontManager::$customFonts); $i++){
				if(array_key_exists('type', LayGridderFontManager::$customFonts[$i]) && LayGridderFontManager::$customFonts[$i]['type'] == "link" ){
					echo LayGridderFontManager::$customFonts[$i]['link'];
				}
				if(array_key_exists('type', LayGridderFontManager::$customFonts[$i]) && LayGridderFontManager::$customFonts[$i]['type'] == "script" ){
					echo LayGridderFontManager::$customFonts[$i]['script'];
				}
			}
		}
	}

	public function frontend_add_attachment_webfonts(){
		$newfontsCSS = 
		'<!-- webfonts -->
		<style type="text/css">';
		if (is_array(LayGridderFontManager::$customFonts)) {
			for($i=0; $i<count(LayGridderFontManager::$customFonts); $i++){
				if(!array_key_exists('type', LayGridderFontManager::$customFonts[$i]) || LayGridderFontManager::$customFonts[$i]['type'] == "attachment" ){
					$newfontsCSS .= '@font-face{ font-family: "'.LayGridderFontManager::$customFonts[$i]['fontname'].'"; src: url("'.LayGridderFontManager::$customFonts[$i]['url'].'") format("woff"); } ';
				}
			}
		}
		$newfontsCSS .= '</style>';
		echo $newfontsCSS;
	}

	public function tinymce_add_fontmanager_fonts( $initArray ) {
		// $original = 'Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats';
		$original = '';

		foreach (LayGridderFontManager::$originalFonts as $key => $value) {
			$original .= $key.'='.$value.';';
		}

		$newfonts = '';

		if (is_array(LayGridderFontManager::$customFonts)) {
			for($i=0; $i<count(LayGridderFontManager::$customFonts); $i++){
				if(!array_key_exists('type', LayGridderFontManager::$customFonts[$i]) || LayGridderFontManager::$customFonts[$i]['type'] == "attachment" ){
					$newfonts .= LayGridderFontManager::$customFonts[$i]['fontname'].'='.LayGridderFontManager::$customFonts[$i]['fontname'].';';
				}
				else if(LayGridderFontManager::$customFonts[$i]['type'] == "link" || LayGridderFontManager::$customFonts[$i]['type'] == "script"){
					$css = LayGridderFontManager::$customFonts[$i]['css'];

					$css = str_replace('font-family: ', '', $css);
					$css = str_replace('font-family:', '', $css);
					$css = str_replace(';', '', $css);

					$newfonts .= LayGridderFontManager::$customFonts[$i]['fontname'].'='.$css.';';
				}
			}
		}

		$initArray['font_formats'] = $newfonts.$original;

		return $initArray;
	}

	public function tinymce_add_fontloader( $plugins ) {
		$plugins['fontloader'] = LG_PLUGIN_URL.'/fontmanager/assets/js/tinymce_plugin/tinymce_fontloader.js';
		return $plugins;
	}

	// needed for "repositionAfterWebfontsLoaded"
	public function print_fontmanager_JSON(){
		echo '<textarea style="display: none;" id="fontmanager_json" name="fontmanager_json">'.get_option('fontmanager_json').'</textarea>';
	}

	public function allow_fonts($mime_types){
		$mime_types['woff'] = 'application/font-woff';
		$mime_types['ttf'] = 'application/x-font-ttf';
		// $mime_types['eot'] = 'application/vnd.ms-fontobject';
		// $mime_types['otf'] = 'application/font-sfnt';
		$mime_types['woff2'] = 'font/woff2';
		return $mime_types;
	}

	public function fontmanager_styles($hook) {
		if ( $hook == 'laygridder_page_laygridder-webfonts' ) {
			wp_enqueue_style( 'fontmanager-parsley', LG_PLUGIN_URL.'/assets/css/parsley.css' );
			wp_enqueue_style( 'fontmanager-bootstrap', LG_PLUGIN_URL.'/assets/css/bootstrap.css' );
			wp_enqueue_style( 'fontmanager-application', LG_PLUGIN_URL.'/fontmanager/assets/css/fontmanager.style.css', array(), LG_VER );
		}
	}

	public function fontmanager_scripts($hook){
		if ( $hook == 'laygridder_page_laygridder-webfonts' ) {
			wp_enqueue_script( 'plugin-bootstrap', LG_PLUGIN_URL."/assets/js/bootstrap.min.js", array( 'jquery' ), LG_VER, true );
			wp_enqueue_script( 'plugin-parsley', LG_PLUGIN_URL."/assets/js/parsley.min.js", array( 'jquery' ), LG_VER, true );
			wp_enqueue_script( 'fontmanager-app', LG_PLUGIN_URL."/fontmanager/assets/js/fontmanager.app.min.js", array( 'jquery' ), LG_VER, true );
			// if i do wp_enqueue_media before enqueueing 'fontmanager-app', then the media modal is empty :/
			wp_enqueue_media();
		}
	}

	public function fontmanager_json_settings_api_init() {
		// register_setting( 'admin-fonts-settings', 'fontmanager_json' );
		add_settings_section(
			'laygridder-webfonts-section',
			'',
			'',
			'laygridder-webfonts'
		);
	 	add_settings_field(
			'fontmanager_json',
			'Font Manager JSON',
			array($this, 'fontmanager_json_txtarea'),
			'laygridder-webfonts',
			'laygridder-webfonts-section'
		);
		register_setting( 'laygridder-webfonts', 'fontmanager_json' );
	}

	public function fontmanager_json_txtarea(){
		echo 
		'<textarea name="fontmanager_json" id="fontmanager_json">'.get_option('fontmanager_json', '').'</textarea>';
	}

	public function font_setup_menu(){
		add_submenu_page( 'laygridder-options-page', 'Webfonts', 'Webfonts', 'can_manage_lg_options', 'laygridder-webfonts', array($this, 'fonts_markup') );
	}
	 
	public function fonts_markup(){
		require_once( LG_PLUGIN_PATH.'/fontmanager/markup.php' );
	}

}
$fontmanager = new LayGridderFontManager();