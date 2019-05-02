<?php
class LayGridderFormatsManager{

	public static $customFormats;
	public static $fontWeights = array('Thin (100)' => '100', 'Extra Light (200)' => '200', 'Light (300)' => '300', 'Normal (400)' => '400', 'Medium (500)' => '500', 'Semi Bold (600)' => '600', 'Bold (700)' => '700', 'Extra Bold (800)' => '800', 'Black (900)' => '900' );
  	public static $defaultFormat = array(
  		'formatname' => 'Default',
  		'type' => 'Paragraph',
  		'fontfamily' => 'helvetica,sans-serif',
  		'fontsize' => '16',
  		'color' => '#000000',
  		'letterspacing' => '0',
  		'fontweight' => '400',
  		'spacebottom' => '20',
  		'spacetopmu' => 'px',
  		'spacetop' => '0',
  		'spacebottommu' => 'px',
  		'textalign' => 'left',
  		'lineheight' => '1.2',
  		'lineheightmu' => '',
  		'textindent' => '0',
  		'caps' => false,
  		'italic' => false,
  		'underline' => false,
  		'borderbottom' => false,
  		'tablet-spacetop' => "0",
  		'tablet-spacetopmu' => 'px',
  		'tablet-spacebottom' => '20',
  		'tablet-spacebottommu' => 'px',
  		'tablet-fontsizemu' => 'px',
  		'tablet-fontsize' => '16',
  		'tablet-lineheight' => '1.2',
  		'tablet-lineheightmu' => '',
  		'phone-spacetop' => "0",
  		'phone-spacetopmu' => 'px',
  		'phone-spacebottom' => '20',
  		'phone-spacebottommu' => 'px',
  		'phone-fontsizemu' => 'px',
  		'phone-fontsize' => '16',
  		'phone-lineheight' => '1.2',
  		'phone-lineheightmu' => ''
	);
  	public static $hasTabletSettings;
  	public static $advancedLineheightSettings;

	public function __construct(){

		$customFormatsJSON = get_option('formatsmanager_json');
		if($customFormatsJSON){
			LayGridderFormatsManager::$customFormats = json_decode($customFormatsJSON, true);
		}else{
			LayGridderFormatsManager::$customFormats = false;
		}
		$gridder_options_textformats_for_tablet = get_option( 'gridder_options_textformats_for_tablet', "" );
		if($gridder_options_textformats_for_tablet == "on"){
			LayGridderFormatsManager::$hasTabletSettings = true;
		}else{
			LayGridderFormatsManager::$hasTabletSettings = false;
		}

		$gridder_options_textformats_advanced_lineheight = get_option('gridder_options_textformats_advanced_lineheight', '');
		if($gridder_options_textformats_advanced_lineheight == "on"){
			LayGridderFormatsManager::$advancedLineheightSettings = true;
		}else{
			LayGridderFormatsManager::$advancedLineheightSettings = false;
		}


		add_action( 'admin_menu', array($this, 'textformats_setup_menu'), 10 );
		
		add_action( 'admin_init', array($this, 'register_settings') );

		add_action( 'admin_enqueue_scripts', array( $this, 'formatsmanager_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'formatsmanager_scripts' ), 9 );

		add_action( 'admin_head', array( $this, 'gridder_textformats_css' ), 10 );
		add_action( 'admin_head', array( $this, 'add_tinymce_js_vars' ), 10 );

		add_action( 'admin_footer', array( $this, 'print_JSON' ) );
		add_action( 'admin_head', array( $this, 'print_option_textformats_everywhere' ) );

		add_action( 'mce_external_plugins', array( $this, 'tinymce_add_textformatsloader') );
		add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_formats') );
	
		add_action( 'wp_head', array( $this, 'frontend_textformats_css' ) );

		add_action( 'admin_init', array($this, 'register_scripts') );


		add_action( 'init', array($this, 'remove_editor_styles') );

		// teeny_mce_buttons needed for basic wysiwyg, for example acf pro uses this
		add_filter( 'teeny_mce_buttons', array($this, 'add_textformats_button_if_textformats_everywhere'), 10, 2 );
		add_filter( 'mce_buttons' , array($this, 'add_textformats_button_if_textformats_everywhere'), 10, 2 );
		add_filter( 'mce_buttons_2' , array($this, 'second_tinymce_button_row'), 10, 2 );
	}

	public static function remove_editor_styles(){
		// when using a theme like twenty-sixteen for example, that theme includes a stylesheet for tinymce that overrides the <p> margins provided by laygridder's textformats
		// thats why i remove their editor stylesheet here
		remove_editor_styles();
	}

	public static function add_tinymce_js_vars(){
		// need to add info about whether advanced lineheight is true as a js variable in admin head
		// because I cannot use wp-localize-script for "textformatsloader" tinymce external plugin
		$bool = "false";
		$gridder_options_textformats_advanced_lineheight = get_option('gridder_options_textformats_advanced_lineheight', '');
		
		if($gridder_options_textformats_advanced_lineheight == "on"){
			$bool = "true";
		}

		// are formats supposed to be used everywhere?
		$everywhere_bool = get_option('gridder_options_textformats_everywhere', '') == "on" ? "true" : "false";

		echo 
		'<script>
			var lg_advanced_lineheights = '.$bool.';
			var lg_textformats_everywhere = '.$everywhere_bool.';
		</script>';
	}

	public static function get_lineheight_row_markup($type = ""){
		// no lineheight settings needed for phone and tablet if advancedLineheightSettings is off
		if( ($type == "tablet" || $type == "phone") && !LayGridderFormatsManager::$advancedLineheightSettings ){
			return '';
		}

		// if type is "" the type is desktop
		$name = "";
		if($type != ""){
			$name = ucfirst($type);
			$name .= " ";
			$type = $type.'-';
		}
		$markup = '<input type="number" class="'.$type.'line-height" value="1.2" step="0.1">';
        if(LayGridderFormatsManager::$advancedLineheightSettings){
        	$markup .=  
        	' <select class="'.$type.'line-height-mu">
        		<option value=""> </option>
        	    <option value="px">px</option>
        	</select>';
        }

        return 
        '<tr class="'.$type.'line-height-row">
            <td>'.$name.'Line Height</td>
            <td>'.$markup.'</td>
        </tr>';
	}

	function second_tinymce_button_row($buttons, $editor_id){
		$everywhere = get_option('gridder_options_textformats_everywhere', '');
		$minimal_buttons = get_option( 'gridder_options_minimal_wysiwyg_buttons', "" );
		if($everywhere == "on" && $minimal_buttons == "on"){
			return array();
		}

		return $buttons;
	}

	function add_textformats_button_if_textformats_everywhere($buttons, $editor_id) {	
		$everywhere = get_option('gridder_options_textformats_everywhere', '');
		if($everywhere == "on"){
			array_unshift( $buttons, 'styleselect' );
		}
		$minimal_buttons = get_option( 'gridder_options_minimal_wysiwyg_buttons', "" );
		if($minimal_buttons == "on" && $everywhere == "on"){
			$buttons = LayGridderSetup::$minimal_tinymce_buttons;
		}

		return $buttons;
	}

	function print_option_textformats_everywhere(){
		$val = get_option('gridder_options_textformats_everywhere', '');
		if($val == "on"){
			echo "<script>var lg_textformats_everywhere = true;</script>";
		}else{
			echo "<script>var lg_textformats_everywhere = false;</script>";
		}
	}

	// http://wordpress.stackexchange.com/questions/144705/unable-to-add-code-button-to-tinymce-toolbar
	// formats dropdown for tinymce
	public function tinymce_formats($in) {

		// using 'attributes' => array('class' => '_'.$customFormats[$i]['formatname']),
		// instead of 'classes' => '_'.$customFormats[$i]['formatname']
		// this way the user can only have one format applied to a selected text

	    $paragraph_formats = array();
	    $headline_formats = array();
	    $character_formats = array();

	    $customFormats = LayGridderFormatsManager::$customFormats;
	    $style_formats = array();

	    if($customFormats){
	    	for($i=0; $i<count($customFormats); $i++){
	    		switch($customFormats[$i]['type']){
	    			case 'Paragraph':
	    				$paragraph_formats []= array( 
    						'title' => $customFormats[$i]['formatname'],
    						'attributes' => array('class' => '_'.$customFormats[$i]['formatname']),
    						'block' => 'p',
   							'exact' => true
	    				);
	    			break;
	    			case 'Headline':
	    				$headline_formats []= array( 
    						'title' => $customFormats[$i]['formatname'],
    						'attributes' => array('class' => '_'.$customFormats[$i]['formatname']),
    						'block' => 'h1'
	    				);
	    			break;
	    			case 'Character':
	    				$character_formats []= array( 
    						'title' => $customFormats[$i]['formatname'],
    						'attributes' => array('class' => '_'.$customFormats[$i]['formatname']),
    						'inline' => 'span'
	    				);
	    			break;
	    		}
	    	}

    	    if($paragraph_formats){
    	    	$style_formats []= array(
	            	'title' => 'Paragraph', 
	            	'items' => $paragraph_formats
            	);
    	    }
    	    if($headline_formats){
    	    	$style_formats []= array(
	            	'title' => 'Headline', 
	            	'items' => $headline_formats
            	);
    	    }
    	    if($character_formats){
    	    	$style_formats []= array(
	            	'title' => 'Character', 
	            	'items' => $character_formats
            	);
    	    }

	    	$in['style_formats'] = json_encode( $style_formats );
	    }
	    else{
	    	$style_formats []= array(
            	'title' => 'Paragraph', 
            	'items' => array()
        	);

	    	$style_formats []= array(
            	'title' => 'Headline', 
            	'items' => array()
        	);

	    	$style_formats []= array(
            	'title' => 'Character',
            	'items' => array()
        	);

    	    $in['style_formats'] = json_encode( $style_formats );
	    }

	    return $in; 
	}

	public function tinymce_add_textformatsloader( $plugins ) {
		$plugins['textformatsloader'] = LG_PLUGIN_URL.'/formatsmanager/assets/js/tinymce_plugin/tinymce_textformatsloader.js';
		return $plugins;
	}

	public function print_JSON(){
		echo '<textarea style="display: none;" id="formatsmanager_json" name="formatsmanager_json">'.get_option( 'formatsmanager_json', json_encode(array(LayGridderFormatsManager::$defaultFormat)) ).'</textarea>';
	}

	private static function getTabletSpecificCSS($array){
		$tablet_fontsize = array_key_exists('tablet-fontsize', $array) ? $array['tablet-fontsize'] : "16";
		$tablet_fontsizemu = array_key_exists('tablet-fontsizemu', $array) ? $array['tablet-fontsizemu'] : "px";

		$tablet_spacetop = array_key_exists('tablet-spacetop', $array) ? $array['tablet-spacetop'] : "0";
		$tablet_spacetopmu = array_key_exists('tablet-spacetopmu', $array) ? $array['tablet-spacetopmu'] : "px";

		$tablet_spacebottom = array_key_exists('tablet-spacebottom', $array) ? $array['tablet-spacebottom'] : "20";
		$tablet_spacebottommu = array_key_exists('tablet-spacebottommu', $array) ? $array['tablet-spacebottommu'] : "px";

		$return = 'font-size:'.$tablet_fontsize.$tablet_fontsizemu.';';

		if($array['type'] != "Character"){
			$return .= 'margin:'.$tablet_spacetop.$tablet_spacetopmu.' 0 '.$tablet_spacebottom.$tablet_spacebottommu.' 0;';
		
			if(LayGridderFormatsManager::$advancedLineheightSettings){
				$tablet_lh_mu = array_key_exists('tablet-lineheightmu', $array) ? $array['tablet-lineheightmu'] : "";
				$tablet_lh = array_key_exists('tablet-lineheight', $array) ? $array['tablet-lineheight'] : "1.2";
				$return .= 'line-height:'.$tablet_lh.$tablet_lh_mu.';';
			}
		}
		
		return $return;		
	}

	private static function getPhoneSpecificCSS($array){
		$phone_fontsize = array_key_exists('phone-fontsize', $array) ? $array['phone-fontsize'] : "16";
		$fontsizemu = array_key_exists('fontsizemu', $array) ? $array['fontsizemu'] : 'px';
		$phone_fontsizemu = array_key_exists('phone-fontsizemu', $array) ? $array['phone-fontsizemu'] : $fontsizemu;

		$phone_spacetop = array_key_exists('phone-spacetop', $array) ? $array['phone-spacetop'] : "0";
		$phone_spacetopmu = array_key_exists('phone-spacetopmu', $array) ? $array['phone-spacetopmu'] : "px";

		$phone_spacebottom = array_key_exists('phone-spacebottom', $array) ? $array['phone-spacebottom'] : "20";
		$phone_spacebottommu = array_key_exists('phone-spacebottommu', $array) ? $array['phone-spacebottommu'] : "px";

		$return = 'font-size:'.$phone_fontsize.$phone_fontsizemu.';';

		if($array['type'] != "Character"){
			$return .= 'margin:'.$phone_spacetop.$phone_spacetopmu.' 0 '.$phone_spacebottom.$phone_spacebottommu.' 0;';
			
			if(LayGridderFormatsManager::$advancedLineheightSettings){
				$phone_lh_mu = array_key_exists('phone-lineheightmu', $array) ? $array['phone-lineheightmu'] : "";
				$phone_lh = array_key_exists('phone-lineheight', $array) ? $array['phone-lineheight'] : "1.2";
				$return .= 'line-height:'.$phone_lh.$phone_lh_mu.';';
			}
		}

		return $return;
	}

	private static function getDesktopSpecificCSS($array){
		$fontsizemu = array_key_exists('fontsizemu', $array) ? $array['fontsizemu'] : 'px';

		$spacetop = array_key_exists('spacetop', $array) ? $array['spacetop'] : '0';
		$spacebottom = array_key_exists('spacebottom', $array) ? $array['spacebottom'] : '20';

		$spacetopmu = array_key_exists('spacetopmu', $array) ? $array['spacetopmu'] : "px";
		$spacebottommu = array_key_exists('spacebottommu', $array) ? $array['spacebottommu'] : "px";

		$return = 'font-size:'.$array['fontsize'].$fontsizemu.';';

		if($array['type'] != "Character"){
			$return .= 'margin:'.$spacetop.$spacetopmu.' 0 '.$spacebottom.$spacebottommu.' 0;';

			if(LayGridderFormatsManager::$advancedLineheightSettings){
				$desktop_lh_mu = array_key_exists('lineheightmu', $array) ? $array['lineheightmu'] : "";
				$desktop_lh = $array['lineheight'];
				$return .= 'line-height:'.$desktop_lh.$desktop_lh_mu.';';
			}
		}

		return $return;
	}

	private static function getFontCSS($array){
		$textindent = array_key_exists('textindent', $array) ? $array['textindent'] : '0';
		$fontweight = array_key_exists('fontweight', $array) ? $array['fontweight'] : '400';
		$textalign = array_key_exists('textalign', $array) ? $array['textalign'] : 'left';

		$caps = "";
		if( array_key_exists('caps', $array) && $array['caps'] == true ){
			$caps = "text-transform:uppercase;";
		}else{
			$caps = "text-transform:none;";
		}

		$italic = "";
		if( array_key_exists('italic', $array) && $array['italic'] == true ){
			$italic = "font-style:italic;";
		}else{
			$italic = "font-style:normal;";
		}
		$underline = "";
		if( array_key_exists('underline', $array) && $array['underline'] == true ){
			$underline = "text-decoration: underline;";
		}else{
			$underline = "text-decoration: none;";
		}

		$borderbottom = "";
		if( array_key_exists('borderbottom', $array) && $array['borderbottom'] == true ){
			$borderbottom = "border-bottom: 1px solid;";
		}else{
			$borderbottom = "";
		}

		// font-variation-settings: "opsz" 100, "wght" 152, "ital" 12;
		$variablesettings = "";
		if( array_key_exists('variablesettings', $array) ){
			$variablesettings = 'font-variation-settings: ';
			$values = array();
			foreach($array['variablesettings'] as $obj){
				$values []= '"'.$obj['tag'].'" '.$obj['value'];
			}
			$variablesettings .= join(', ', $values);
			$variablesettings .= ';';
		}

		$return =
		'font-family:'.$array['fontfamily'].';'
		.'color:'.$array['color'].';'
		.'letter-spacing:'.$array['letterspacing'].'em;'
		.'font-weight:'.$fontweight.';'
		.'text-align:'.$textalign.';'
		.'text-indent:'.$textindent.'em;'
		.'padding: 0;'
		.$caps
		.$italic
		.$underline
		.$borderbottom
		.$variablesettings;

		if(!LayGridderFormatsManager::$advancedLineheightSettings){
			$return .= 'line-height:'.$array['lineheight'].';';
		}

		return $return;
	}

	public static function frontend_textformats_css(){

		$formatsJsonString = get_option( 'formatsmanager_json', json_encode(array(LayGridderFormatsManager::$defaultFormat)) );
		$formatsJsonArr = json_decode($formatsJsonString, true);
		
		$formatsCSS = '';

		for($i = 0; $i<count($formatsJsonArr); $i++) {
		 	if($formatsJsonArr[$i]["formatname"] == "Default"){
		 		// "Default" textformat
 		 		echo 
 		 		'<!-- default text format "Default" -->
 		 		<style>
 			 		.lg-textformat-parent > *, ._Default{
 			 			'.LayGridderFormatsManager::getFontCSS($formatsJsonArr[$i]).'
 			 		}';

		 		if(LayGridderFormatsManager::$hasTabletSettings){
	 				echo
		 			'@media (min-width: '.((int)LayGridderOptions::$tablet_breakpoint+1).'px){
		 				.lg-textformat-parent > *, ._Default{
		 					'.LayGridderFormatsManager::getDesktopSpecificCSS($formatsJsonArr[$i]).'
		 				}
		 				.lg-textformat-parent > *:last-child, ._Default:last-child{
		 					margin-bottom: 0;
		 				}
			 		}
			 		@media (min-width: '.((int)LayGridderOptions::$phone_breakpoint+1).'px) and (max-width: '.((int)LayGridderOptions::$tablet_breakpoint).'px){
		 				.lg-textformat-parent > *, ._Default{
		 					'.LayGridderFormatsManager::getTabletSpecificCSS($formatsJsonArr[$i]).'
		 				}
		 				.lg-textformat-parent > *:last-child, ._Default:last-child{
		 					margin-bottom: 0;
		 				}
			 		}
			 		@media (max-width: '.LayGridderOptions::$phone_breakpoint.'px){
			 			.lg-textformat-parent > *, ._Default{
			 				'.LayGridderFormatsManager::getPhoneSpecificCSS($formatsJsonArr[$i]).'
			 			}
			 			.lg-textformat-parent > *:last-child, ._Default:last-child{
			 				margin-bottom: 0;
			 			}
			 		}';
		 		}else{
 			 		echo 
 			 		'.lg-textformat-parent > *, ._Default{
			 			'.LayGridderFormatsManager::getFontCSS($formatsJsonArr[$i]).'
			 		}
			 		.lg-textformat-parent > *:last-child, ._Default:last-child{
			 			margin-bottom: 0;
			 		}
		 			@media (min-width: '.((int)LayGridderOptions::$phone_breakpoint+1).'px){
		 				.lg-textformat-parent > *, ._Default{
		 					'.LayGridderFormatsManager::getDesktopSpecificCSS($formatsJsonArr[$i]).'
		 				}
		 				.lg-textformat-parent > *:last-child, ._Default:last-child{
		 					margin-bottom: 0;
		 				}
			 		}
			 		@media (max-width: '.LayGridderOptions::$phone_breakpoint.'px){
			 			.lg-textformat-parent > *, ._Default{
			 				'.LayGridderFormatsManager::getPhoneSpecificCSS($formatsJsonArr[$i]).'
			 			}
			 			.lg-textformat-parent > *:last-child, ._Default:last-child{
			 				margin-bottom: 0;
			 			}
			 		}';		
		 		}
		 		echo '</style>';
		 	}
		 	else{
		 		// textformats that are not default textformat don't get "lg-textformat-parent" as prefix
	 		    $formatsCSS .= 
	 		    '._'.$formatsJsonArr[$i]['formatname'].'{'
	 			    .LayGridderFormatsManager::getFontCSS($formatsJsonArr[$i])
	 		    .'}';
		 		// custom textformats
		 		if(LayGridderFormatsManager::$hasTabletSettings){
		 			$formatsCSS .= 
		 			'@media (min-width: '.((int)LayGridderOptions::$tablet_breakpoint+1).'px){
		 				._'.$formatsJsonArr[$i]['formatname'].'{'
		 					.LayGridderFormatsManager::getDesktopSpecificCSS($formatsJsonArr[$i]).
		 				'}
		 				._'.$formatsJsonArr[$i]['formatname'].':last-child{
		 					margin-bottom: 0;
		 				}
		 			}
	 			    @media (min-width: '.((int)LayGridderOptions::$phone_breakpoint+1).'px) and (max-width: '.(LayGridderOptions::$tablet_breakpoint).'px){
	 			    	._'.$formatsJsonArr[$i]['formatname'].'{'
	 			    		.LayGridderFormatsManager::getTabletSpecificCSS($formatsJsonArr[$i]).
	 			    	'}
	 			    	._'.$formatsJsonArr[$i]['formatname'].':last-child{
	 			    		margin-bottom: 0;
	 			    	}	 			    	
	 			    }
	 			    @media (max-width: '.LayGridderOptions::$phone_breakpoint.'px){
	 			    	._'.$formatsJsonArr[$i]['formatname'].'{'
	 			    		.LayGridderFormatsManager::getPhoneSpecificCSS($formatsJsonArr[$i]).
	 			    	'}
	 			    	._'.$formatsJsonArr[$i]['formatname'].':last-child{
	 			    		margin-bottom: 0;
	 			    	}
	 			    }';
		 		}else{
	 			    $formatsCSS .= 
	 			    '@media (min-width: '.((int)LayGridderOptions::$phone_breakpoint+1).'px){'
	 			    	.'._'.$formatsJsonArr[$i]['formatname'].'{'
	 			    		.LayGridderFormatsManager::getDesktopSpecificCSS($formatsJsonArr[$i])
	 			    	.'}
	 			    	._'.$formatsJsonArr[$i]['formatname'].':last-child{
	 			    		margin-bottom: 0;
	 			    	}
	 			    }'
	 			    .'@media (max-width: '.LayGridderOptions::$phone_breakpoint.'px){'
	 			    	.'._'.$formatsJsonArr[$i]['formatname'].'{'
	 			    		.LayGridderFormatsManager::getPhoneSpecificCSS($formatsJsonArr[$i])
	 			    	.'}
	 			    	._'.$formatsJsonArr[$i]['formatname'].':last-child{
	 			    		margin-bottom: 0;
	 			    	}
	 			    }';		 			
		 		}

		 	}
		} 

		if($formatsCSS != ""){
			echo 
			'<!-- custom text formats -->
			<style>
				'.$formatsCSS.'
			</style>';
		}

	}

	public static function gridder_textformats_css(){
		$formatsJsonString = get_option( 'formatsmanager_json', json_encode(array(LayGridderFormatsManager::$defaultFormat)) );
		$formatsJsonArr = json_decode($formatsJsonString, true);
		
		$formatsCSS = '';

		for($i = 0; $i<count($formatsJsonArr); $i++) {
		 	if($formatsJsonArr[$i]["formatname"] == "Default"){
		 		// "Default" textformat

		 		echo 
		 		'<!-- default text format "Default" -->
		 		<style>
			 		#gridder .lg-textformat-parent > *,
			 		#gridder ._Default{
			 			'.LayGridderFormatsManager::getFontCSS($formatsJsonArr[$i]).'
			 		}
		 			#gridder .show-desktop-version .lg-textformat-parent > *,
		 			#gridder .show-desktop-version ._Default{
	 					'.LayGridderFormatsManager::getDesktopSpecificCSS($formatsJsonArr[$i]).'
	 				}
			 		#gridder .show-phone-version .lg-textformat-parent > *,
			 		#gridder .show-phone-version ._Default{
		 				'.LayGridderFormatsManager::getPhoneSpecificCSS($formatsJsonArr[$i]).'
		 			}
		 		</style>';
		 	}
		 	else{
		 		// custom textformats

	 		    $formatsCSS .= 
	 		    '#gridder .lg-textformat-parent ._'.$formatsJsonArr[$i]['formatname'].'{'
	 			    .LayGridderFormatsManager::getFontCSS($formatsJsonArr[$i])
	 		    .'}'
 		    	.'#gridder .show-desktop-version .lg-textformat-parent ._'.$formatsJsonArr[$i]['formatname'].'{'
 		    		.LayGridderFormatsManager::getDesktopSpecificCSS($formatsJsonArr[$i])
 		    	.'}'
 		    	.'#gridder .show-phone-version .lg-textformat-parent ._'.$formatsJsonArr[$i]['formatname'].'{'
 		    		.LayGridderFormatsManager::getPhoneSpecificCSS($formatsJsonArr[$i])
 		    	.'}';
		 	}
		} 

		if($formatsCSS != ""){
			echo 
			'<!-- custom text formats -->
			<style>
				'.$formatsCSS.'
			</style>';
		}
	}

	public function formatsmanager_styles($hook) {
		if ( $hook == 'laygridder_page_laygridder-textformats' ) {
			wp_enqueue_style( 'formatsmanager-parsley', LG_PLUGIN_URL.'/assets/css/parsley.css' );
			wp_enqueue_style( 'formatsmanager-iris', LG_PLUGIN_URL.'/formatsmanager/assets/css/iris.css' );
			wp_enqueue_style( 'formatsmanager-bootstrap', LG_PLUGIN_URL.'/assets/css/bootstrap.css' );
			wp_enqueue_style( 'formatsmanager-application', LG_PLUGIN_URL.'/formatsmanager/assets/css/formatsmanager.style.css', array(), LG_VER );
		}
	}

	public static function register_scripts(){
		// using modified version of iris to prevent scrolling when user drags inside colorpicker
		wp_register_script( 'lay-opentype', LG_PLUGIN_URL."/formatsmanager/assets/js/vendor/opentype.js", array(), LG_VER );
		wp_register_script( 'lay-variablefont', LG_PLUGIN_URL."/formatsmanager/assets/js/vendor/variablefont.js", array(), LG_VER );
	}

	public function formatsmanager_scripts($hook){
		if ( $hook == 'laygridder_page_laygridder-textformats' ) {
			wp_enqueue_script( 'plugin-bootstrap', LG_PLUGIN_URL."/assets/js/bootstrap.min.js", array( 'jquery' ), LG_VER);
			wp_enqueue_script( 'plugin-parsley', LG_PLUGIN_URL."/assets/js/parsley.min.js", array( 'jquery' ), LG_VER);
			wp_enqueue_script( 'formatsmanager-app', LG_PLUGIN_URL."/formatsmanager/assets/js/formatsmanager.app.min.js", array( 'jquery', 'lay-iris', 'lay-opentype', 'lay-variablefont' ), LG_VER, true);
			wp_localize_script( 'formatsmanager-app', 'formatslgPassedData', array(
					'advancedLineHeights' => LayGridderFormatsManager::$advancedLineheightSettings
				) 
			);
		}
	}

	public function register_settings() {
		register_setting( 'admin-textformats-settings', 'formatsmanager_json' );
	}

	public function textformats_setup_menu(){
        add_submenu_page( 'laygridder-options-page', 'Text Formats', 'Text Formats', 'can_manage_lg_options', 'laygridder-textformats', array($this, 'textformats_markup') );
	}
	 
	public function textformats_markup(){
    	require_once( LG_PLUGIN_PATH.'/formatsmanager/markup.php' );
	}

}
new LayGridderFormatsManager();