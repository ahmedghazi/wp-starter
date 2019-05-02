<?php
class LayGridderFrontend{

	public static $topframe_mu;
	public static $bottomframe_mu;
	private static $siteUrl;
	private static $lg_noscript;

	public function __construct(){
		add_action( 'wp_head', 'LayGridderFrontend::plugin_identifier');
		add_shortcode( 'laygrid', array($this, 'laygrid_shortcode_handler' ) );
		add_action( 'wp_head', array( $this, 'shared_css' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_laygridder_css' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_laygridder_js' ), 99 );
		add_action( 'wp_head', array( $this, 'echo_max_width_option_css' ) );
		LayGridderFrontend::$lg_noscript = '<noscript>made with LayGridder</noscript>';
		LayGridderFrontend::$siteUrl = get_site_url();
	}

	public static function plugin_identifier() {
		echo '<!-- Thank you for using LayGridder '.LG_VER.' -->';
	}

	public static function enqueue_laygridder_js(){
		wp_enqueue_script( 'lg-flexbox-polyfill', LG_PLUGIN_URL."/frontend/assets/js/polyfill_flexbox.js", array( 'jquery' ), LG_VER);

		$phoneBreakpoint = get_option( 'gridder_options_breakpoint', 700 );

		wp_localize_script( 'lg-flexbox-polyfill', 'lg_lgPassedData', 
			array(
				'phoneSize' => $phoneBreakpoint,
			)
		);
	}

	public function enqueue_laygridder_css(){
		wp_enqueue_style( 'laygrid', LG_PLUGIN_URL."/frontend/assets/css/frontend.style.css", array(), LG_VER  );
	}

	public static function echo_max_width_option_css(){
		$maxwidth = get_option( 'gridder_options_max_width', '0' );

		if($maxwidth != '0'){
			echo 
			'<!-- laygridder max width option -->
			<style>
				@media (min-width: '.(LayGridderOptions::$phone_breakpoint+1).'px){
					.lg-row-inner{margin-left:auto;margin-right:auto;max-width:'.$maxwidth.'px;}
				}
			</style>';
		}
	}

	private static function get_static_url($url) {
		if (!(substr($url, 0, 4) === 'http')) {
			return LayGridderFrontend::$siteUrl . $url;
		}
		return $url;
	}

	// CSS

	// mainly alignment and apl css, css that is valid for all laygrids and automatically generated phone layouts (apl)
	public function shared_css(){	
		$shared = 
			'.lg-100vh, .lg-100vh .lg-row-inner, .lg-100vh .lg-column-wrap{
				min-height: 100vh;
			}';

		// desktop layout
		$desktop = '@media (min-width: '.(LayGridderOptions::$phone_breakpoint+1).'px){
			.lg-column-wrap {
				display: -webkit-flex;
				display: -ms-flexbox;
				display: flex;
			}

			.lg-align-middle {
				-webkit-align-self: center;
				-ms-flex-item-align: center;
				align-self: center;
				position: relative; }

			.lg-align-top {
				-webkit-align-self: flex-start;
				-ms-flex-item-align: start;
				align-self: flex-start; }

			.lg-align-bottom {
				-webkit-align-self: flex-end;
	      		-ms-flex-item-align: end;
				align-self: flex-end; }

			.lg-100vh .lg-col.lg-type-text {
			    position: absolute !important;
			    margin-left: 0 !important;
			    z-index: 1;
			}
        }';

        // automatically generated phone layout (uses .lg-desktop-grid layout and stacks all elements)
        $apl_defaults_topframe = get_option( 'apl_defaults_topframe', LG_Constants::apl_defaults_topframe );
        $apl_defaults_topframe_mu = get_option( 'apl_defaults_topframe_mu', LG_Constants::apl_defaults_topframe_mu );
        $apl_defaults_bottomframe = get_option( 'apl_defaults_bottomframe', LG_Constants::apl_defaults_bottomframe );
        $apl_defaults_bottomframe_mu = get_option( 'apl_defaults_bottomframe_mu', LG_Constants::apl_defaults_bottomframe_mu );

        $apl_defaults_rowgutter_mu = get_option( 'apl_defaults_rowgutter_mu', LG_Constants::apl_defaults_rowgutter_mu );
        $apl_defaults_row_gutter = get_option( 'apl_defaults_row_gutter', LG_Constants::apl_defaults_row_gutter );

        $apl_defaults_frame = get_option( 'apl_defaults_frame', LG_Constants::apl_defaults_frame );
        $apl_defaults_frame_mu = get_option( 'apl_defaults_frame_mu', LG_Constants::apl_defaults_frame_mu );

		$apl_defaults = 
		'.lg-desktop-grid.lg-grid{
			padding-top:'.$apl_defaults_topframe.$apl_defaults_topframe_mu.';
			padding-bottom:'.$apl_defaults_bottomframe.$apl_defaults_bottomframe_mu.';
		}
		.lg-desktop-grid .lg-row{
			padding-left:'.$apl_defaults_frame.$apl_defaults_frame_mu.';
			padding-right:'.$apl_defaults_frame.$apl_defaults_frame_mu.';
		}
		.lg-desktop-grid .lg-col{
			margin-bottom:'.$apl_defaults_row_gutter.$apl_defaults_rowgutter_mu.';
		}';

		// for apl layouts only a one-col-100vh-row can use alignment for its column 
        $phone = '@media (max-width: '.(LayGridderOptions::$phone_breakpoint).'px){
        	.lg-desktop-grid .lg-col {
        		width: 100%;
        		transform: none!important;
        		-webkit-transform: none!important;
        	}
        	.lg-desktop-grid .lg-row.lg-one-col-row.lg-100vh .lg-col.lg-align-middle {
        		-webkit-align-self: center;
    		    -ms-flex-item-align: center;
		        align-self: center;
        		position: relative;
        		margin-bottom: 0;
        	}
        	.lg-desktop-grid .lg-row.lg-one-col-row.lg-100vh .lg-col.lg-align-top {
				-webkit-align-self: flex-start;
				-ms-flex-item-align: start;
				align-self: flex-start; 
			}
        	.lg-desktop-grid .lg-row.lg-one-col-row.lg-100vh .lg-col.lg-align-bottom {
				-webkit-align-self: flex-end;
	      		-ms-flex-item-align: end;
				align-self: flex-end; 
			}
        	.lg-desktop-grid .lg-row.lg-one-col-row.lg-100vh .lg-column-wrap{
        		display: -webkit-flex;
        		display: -ms-flexbox;
        		display: flex;
        	}
        	'.$apl_defaults.'
        	.lg-desktop-grid .lg-row:last-child .lg-col:last-child{
        		margin-bottom: 0;
        	}
        }';

        echo '<style id="lg-shared-css">'.$shared.$desktop.$phone.'</style>';
	}

	public static function get_vw(&$obj, $span){
		$colcount = $obj->colCount;
		$colgutter = $obj->colGutter;
		$framemargin = $obj->frameMargin;

		$colandgutterspace = 100 - $framemargin * 2;
		$gutterspace = ($colcount-1) * $colgutter;
		$colspace = $colandgutterspace - $gutterspace;

		$onecolspace = $colspace / $colcount;
		$onegutterspace = $colgutter;

		if($span == 1){
			return $onecolspace;
		}else if($span > 0){
			return $onecolspace * $span + $onegutterspace * ($span-1);
		}
	}

	public static function getWidth(&$obj, $colspan){
		$oneColAndGutter = ((100.0 - 2 * $obj->frameMargin) + $obj->colGutter) / $obj->colCount;
		return $oneColAndGutter * $colspan - $obj->colGutter;
	}

	public static function getChildWidth(&$obj, $colspan, $width){
		$widthOfChildWithoutParent = LayGridderFrontend::getWidth($obj, $colspan);
		return $widthOfChildWithoutParent / $width;
	}

	public static function getChildMarginLeft(&$obj, $col, $width){
		$oneColAndGutter = ((100.0 - 2 * $obj->frameMargin) + $obj->colGutter) / $obj->colCount;
		return $oneColAndGutter * $col / $width;
  }

	public static function get_css_markup($obj, $grid_id){

    // if frame left right is px and not %, leave frame left right out of the equation, because I will just use padding-left and padding-right for that
    $frame_left_right_is_px = get_option( 'gridder_defaults_frame_mu', '%' ) == 'px' ? true : false;

		$css = "";
		$colcount = $obj->colCount;
		$colgutter = $obj->colGutter;
		$framemargin = $obj->frameMargin;
		$topframemargin = $obj->topFrameMargin;
		$bottomframemargin = $obj->bottomFrameMargin;

    $colandgutterspace = 100 - $framemargin * 2;
    if($frame_left_right_is_px){
      $colandgutterspace = 100;
    }
		$gutterspace = ($colcount-1) * $colgutter;
		$colspace = $colandgutterspace - $gutterspace;

		$onecolspace = $colspace / $colcount;
		$onegutterspace = $colgutter;

		for ($i=0; $i < $colcount+1; $i++) {
			// span
			$space = $onecolspace * $i + $onegutterspace * ($i-1);
			$css .= '#'.$grid_id.' .lg-span-'.$i.'{width:'.$space.'%}';
			$css .= '#'.$grid_id.' .lg-span-'.$i.'.frame-overflow-left{width: calc('.
				$space.'% + ' . $framemargin . ($frame_left_right_is_px ? 'px' : '%') . ')}';
			$css .= '#'.$grid_id.' .lg-span-'.$i.'.frame-overflow-right{width: calc('.
				$space.'% + ' . $framemargin . ($frame_left_right_is_px ? 'px' : '%') . ')}';
			$css .= '#'.$grid_id.' .lg-span-'.$i.'.frame-overflow-left.frame-overflow-right{width: calc('.
			$space.'% + ' . (2*$framemargin) . ($frame_left_right_is_px ? 'px' : '%') . ')}';
	
			// push for :first-child
      $spacefirst = $framemargin;
      if($frame_left_right_is_px){
        $spacefirst = 0;
      }
			$spacefirst += $onecolspace * $i + $onegutterspace * $i;

			$css .= '#'.$grid_id.' .lg-push-'.$i.':first-child{margin-left:'.$spacefirst.'%}';
			if($frame_left_right_is_px){
				$css .= '#'.$grid_id.' .lg-push-'.$i.'.frame-overflow-left:first-child{margin-left:-' . $framemargin . 'px}';
				$css .= '#'.$grid_id.' .lg-push-'.$i.'.frame-overflow-right:last-child{margin-right:-' . $framemargin . 'px}';
			} else {
				$css .= '#'.$grid_id.' .lg-push-'.$i.'.frame-overflow-left:first-child{margin-left:-' . ($spacefirst - $framemargin) . '%}';
			}
			// push for text-el in 100vh row -> needs left instead of margin-left, and always including left frame
			// this way we can have text elements overlapping anything
			$css .= '#'.$grid_id.' .lg-100vh .lg-col.lg-type-text.lg-push-'.$i.'{left:'.$spacefirst.'%}';
			$css .= '#'.$grid_id.' .lg-100vh .lg-col.lg-type-text.lg-push-'.$i.'.frame-overflow-left{left:0}';
			if($frame_left_right_is_px){
				$css .= '#'.$grid_id.' .lg-100vh .lg-col.lg-type-text.lg-push-'.$i.'.frame-overflow-left{margin-left:-' . $framemargin . 'px !important}';
			}

			// push
			$space = $onecolspace * $i + $onegutterspace * ($i+1);
      $css .= '#'.$grid_id.' .lg-push-'.$i.'{margin-left:'.$space.'%}';
    }

    if($frame_left_right_is_px){
      $css .= '#'.$grid_id.'{padding-left:'.$framemargin.'px; padding-right:'.$framemargin.'px;}';
    }
		return $css;
	}

	// this css is for individual grids
	public static function get_grid_css($gridder_json="", $phone_gridder_json="", $grid_id){	
		$css = '<style class="lg-grid-css">';

		// desktop
		if(trim($gridder_json) != ""){
			$desktopObj = json_decode($gridder_json);
			$desktopCSS = LayGridderFrontend::get_css_markup($desktopObj, $grid_id);
			// topframe and bottomframe space
			$desktopCSS .= '#'.$grid_id.'.lg-desktop-grid{padding-top:'.$desktopObj->topFrameMargin.LayGridder::$topframe_mu.';}';
			$desktopCSS .= '#'.$grid_id.'.lg-desktop-grid{padding-bottom:'.$desktopObj->bottomFrameMargin.LayGridder::$bottomframe_mu.';}';
			// row gutter css
			foreach ($desktopObj->rowGutters as $key => $gutter) {
				$desktopCSS .= '#'.$grid_id.'.lg-desktop-grid .lg-row-'.$key.'{margin-bottom:'.$gutter.LayGridder::$rowgutter_mu.';}';
			}

			$css .= '@media (min-width: '.(LayGridderOptions::$phone_breakpoint+1).'px){'.$desktopCSS.'}';
		}

		// custom phone layout
		if(LayGridderOptions::$phoneLayoutActive){
			if(trim($phone_gridder_json) != ""){
				$phoneObj = json_decode($phone_gridder_json);
				$customPhoneCSS = LayGridderFrontend::get_css_markup($phoneObj, $grid_id);
				// topframe and bottomframe space
				$customPhoneCSS .= '#'.$grid_id.'.lg-phone-grid{padding-top:'.$phoneObj->topFrameMargin.'%;}';
				$customPhoneCSS .= '#'.$grid_id.'.lg-phone-grid{padding-bottom:'.$phoneObj->bottomFrameMargin.'%;}';
				// row gutter css
				foreach ($phoneObj->rowGutters as $key => $gutter) {
					$customPhoneCSS .= '#'.$grid_id.'.lg-phone-grid .lg-row-'.$key.'{margin-bottom:'.$gutter.'%;}';
				}
				$css .= '@media (max-width: '.LayGridderOptions::$phone_breakpoint.'px){'.$customPhoneCSS.'}';
				// if custom phone layout exists, then hide desktop layout on phone screensize 
				// and hide phone layout on desktop screensize
				$css .= '@media (min-width: '.(LayGridderOptions::$phone_breakpoint+1).'px){ #'.$grid_id.'.lg-phone-grid{display:none;} }';
				$css .= '@media (max-width: '.LayGridderOptions::$phone_breakpoint.'px){ #'.$grid_id.'.lg-desktop-grid{display:none;} }';
			}
		}
		
		$css .= '</style>';
		return $css;
	}

	// HTML

	public static function laygrid_shortcode_handler( $atts="" ){
		$a = shortcode_atts( array(
		    'id' => '',
		    'type' => '',
		), $atts );

		$type = $a['type'];
		$id = $a['id'];

		return LayGridderFrontend::get_laygrid($id, $type);
	}

	// type can only be "", "post" or "term"
	private static function get_grid_id($id="", $type=""){
		global $post;

		// type and id not specified -> get current grid
		if($type == '' && $id == ''){
			if(is_tax() || is_category()){
				$queried_object = get_queried_object();
				$term_id = $queried_object->term_id;
				return 'laygrid_term_'.$term_id;
			}else{
				// post is null on 404
				if(!is_null($post)){
					return 'laygrid_post_'.$post->ID;
				}
			}
		}

		if($id == '' && $type != ''){
			if($type == 'term'){
				$queried_object = get_queried_object();
				$term_id = $queried_object->term_id;
				return 'laygrid_term_'.$term_id;
			}else{
				if(!is_null($post)){
					return 'laygrid_post_'.$post->ID;
				}
			}
		}

		if($type == ""){
			$type = "post";
		}

		return 'laygrid_'.$type.'_'.$id;

	}

	public static function lg_get_background_color($id="", $type=""){
		$gridder_json = "";

		if(is_tax() || is_category()){
			// if id isnt specified, get current id
			if($id==""){
				$queried_object = get_queried_object();
				$id = $queried_object->term_id;
			}
			$gridder_json = get_term_meta( $id, '_gridder_json', true );
		}else{
			// if id isnt specified, get current id
			if($id==""){
				global $post;
				// post is null on 404
				if(is_null($post)){
					return;
				}
				$id = $post->ID;
			}
			$gridder_json = get_post_meta( $id, '_gridder_json', true );
		}

		$obj = json_decode($gridder_json);
		return $obj->bgColor;
	}

	public static function get_laygrid($id="", $type=""){
		$grid_id = "";
		// get grid id
		if(is_tax() || is_category()){
			$grid_id = LayGridderFrontend::get_grid_id($id, 'term');
		}else{
			$grid_id = LayGridderFrontend::get_grid_id($id, 'post');
		}

		// if type and id aren't specified, get current grid
		if($type == '' && $id == ''){
			if(is_tax() || is_category()){
				return LayGridderFrontend::get_term_laygrid("", $grid_id);
			}else{
				return LayGridderFrontend::get_post_laygrid("", $grid_id);
			}
		}

		switch ($type) {
			case '':
			case 'post':
			case 'page':
				return LayGridderFrontend::get_post_laygrid($id, $grid_id);
			break;
			case 'category':
				return LayGridderFrontend::get_term_laygrid($id, $grid_id);
			break;
			default:
				// if type is not '', 'post', 'page' or 'category', then it could either be a custom taxonomy or a custom post type

				// get custom taxonomies' names
				$custom_taxonomies = get_taxonomies( array('public' => true, '_builtin' => false) );
				if(in_array($type, $custom_taxonomies)){
					$grid_id = LayGridderFrontend::get_grid_id($id, 'term');
					return LayGridderFrontend::get_term_laygrid($id, $grid_id);
				}

				// get custom post types names
				$custom_post_types = get_post_types( array('public' => true, '_builtin' => false) );
				if(in_array($type, $custom_post_types)){
					$grid_id = LayGridderFrontend::get_grid_id($id, 'post');
					return LayGridderFrontend::get_post_laygrid($id, $grid_id);
				}
			break;
		}
	}

	// returns current laygrid if $term_id is not specified
	public static function get_term_laygrid($term_id="", $grid_id){
		if($term_id==""){
			$queried_object = get_queried_object();
			$term_id = $queried_object->term_id;
		}

		$desktopGridHTML = "";
		$customPhoneGridHTML = "";
		$gridder_json = "";
		$phone_gridder_json = "";

		$gridder_json = get_term_meta( $term_id, '_gridder_json', true );
		if(is_plugin_active( 'qtranslate-x/qtranslate.php' )) {
			$gridder_json = qtranxf_gettext($gridder_json);
		}
		if(trim($gridder_json) != ""){
			$desktopGridHTML = LayGridderFrontend::get_laygrid_markup($gridder_json, 'lg-desktop-grid', $grid_id);
		}

		if(LayGridderOptions::$phoneLayoutActive){
			$phone_gridder_json = get_term_meta( $term_id, '_phone_gridder_json', true );
			if(is_plugin_active( 'qtranslate-x/qtranslate.php' )) {
				$phone_gridder_json = qtranxf_gettext($phone_gridder_json);
			}
			if(trim($phone_gridder_json) != ""){
				$customPhoneGridHTML = LayGridderFrontend::get_laygrid_markup($phone_gridder_json, 'lg-phone-grid', $grid_id);
			}
		}

		$css = LayGridderFrontend::get_grid_css($gridder_json, $phone_gridder_json, $grid_id);

		return $css.$desktopGridHTML.$customPhoneGridHTML.LayGridderFrontend::$lg_noscript;
	}

	// returns current laygrid if $id is not specified
	public static function get_post_laygrid($id="", $grid_id){
		if($id==""){
			global $post;
			// post is null on 404
			if(is_null($post)){
				return;
			}
			$id = $post->ID;
		}

		$desktopGridHTML = "";
		$customPhoneGridHTML = "";
		$gridder_json = "";
		$phone_gridder_json = "";

		$gridder_json = get_post_meta( $id, '_gridder_json', true );
		if(trim($gridder_json) != ""){
			$desktopGridHTML = LayGridderFrontend::get_laygrid_markup($gridder_json, 'lg-desktop-grid', $grid_id);
		}

		if(LayGridderOptions::$phoneLayoutActive){
			$phone_gridder_json = get_post_meta( $id, '_phone_gridder_json', true );
			if(trim($phone_gridder_json) != ""){
				$customPhoneGridHTML = LayGridderFrontend::get_laygrid_markup($phone_gridder_json, 'lg-phone-grid', $grid_id);
			}
		}

		$css = LayGridderFrontend::get_grid_css($gridder_json, $phone_gridder_json, $grid_id);

		return $css.$desktopGridHTML.$customPhoneGridHTML.LayGridderFrontend::$lg_noscript;
	}

	public static function get_laygrid_markup($json, $wrapperclass, $grid_id){
		$obj = json_decode($json);

		$rowAmt = count($obj->rowGutters) + 1;
		
		$rowsArr = array();

		for ($i=0; $i < $rowAmt; $i++) { 
			$rowsArr []= array();
		}
		$html_id = "";

		$content = $obj->cont;
		foreach ($content as $element) {
			LayGridderFrontend::get_element_markup($rowsArr, $element, $html_id, $obj);
		}

		$gridStyle = "";
		if($obj->bgColor != ""){
			$gridStyle = 'style="background-color:'.$obj->bgColor.';"';
		}

		$markup = '<div class="lg-grid '.$wrapperclass.'" '.$gridStyle.' id="'.$grid_id.'">';
		foreach ($rowsArr as $key => $row) {
			// add row attributes
			$class = "";
			$rowStyle = "";
			$rowBgVideo = false;
			$rowBgImage = false;
			
			if( array_key_exists($key, $obj->rowAttrs) ){
				$rowAttrs = $obj->rowAttrs[$key];
				if(!is_null($rowAttrs)){
					if(property_exists($rowAttrs, 'row100vh') && $rowAttrs->row100vh == true){
						$class .= ' lg-100vh';
					}
					if(property_exists($rowAttrs, 'classes')){
						$class .= ' '.$rowAttrs->classes;
					}
					$html_id = "";
					if(property_exists($rowAttrs, 'html_id') && $rowAttrs->html_id != ""){
						$html_id = $rowAttrs->html_id;
					}

					// row backgrounds
					if(property_exists($rowAttrs, 'bgvideo') && is_object($rowAttrs->bgvideo) && $rowAttrs->bgvideo->mp4 != ""){
						$poster = (property_exists($rowAttrs->bgvideo, 'image') && $rowAttrs->bgvideo->image !== null) ? LayGridderFrontend::get_static_url($rowAttrs->bgvideo->image->full->url) : '';
						$rowBgVideo = 
						'<div class="lg-row-bg-video">
							<video autoplay muted loop poster="'.$poster.'">
								<source src="'.LayGridderFrontend::get_static_url($rowAttrs->bgvideo->mp4).'" type="video/mp4">
							</video>
						</div>';
					}
					if(property_exists($rowAttrs, 'bgimage') && $rowAttrs->bgimage != ""){
						$srcset = wp_get_attachment_image_srcset($rowAttrs->bgimage->attid);
						$alt = get_post_meta($rowAttrs->bgimage->attid, '_wp_attachment_image_alt', true);
						$img = '';

						$is_gif = substr($rowAttrs->bgimage->sizes->full->url, -4) == ".gif" ? true : false;

						if($is_gif){
							$img = '<img src="'.LayGridderFrontend::get_static_url($rowAttrs->bgimage->sizes->full->url).'" alt="'.$alt.'">';
						}else{
							$img = '<img srcset="'.$srcset.'" sizes="100vw" src="'.LayGridderFrontend::get_static_url($rowAttrs->bgimage->sizes->full->url).'" alt="'.$alt.'">';
						}
						
						$rowBgImage = '<div class="lg-row-bg-image">'.$img.'</div>';
					}
					if(property_exists($rowAttrs, 'bgcolor') && $rowAttrs->bgcolor != ""){
						$rowStyle .= 'background-color:'.$rowAttrs->bgcolor.';';
					}			
				}
			}

			if(count($row) == 0){
				$class .= ' lg-row-empty';
			}else if(count($row) == 1){
				$class .= ' lg-one-col-row';
			}

			$class = apply_filters('lg_frontend_rowclass', $class);

			// .lg-row
			$markup .= '<div class="lg-row lg-row-'.$key.''.$class.'" '.($html_id != "" ? 'id="'.$html_id.'"' : "").' '.($rowStyle != "" ? 'style="'.$rowStyle.'"' : "").'>';
			$markup .= '<div class="lg-row-inner">';
				$markup .= '<div class="lg-column-wrap">'.implode($row).'</div>';
			$markup .= '</div>';

			if($rowBgVideo != false){
				$markup .= $rowBgVideo;
			}
			if($rowBgImage != false){
				$markup .= $rowBgImage;
			}

			// close .lg-row
			$markup .= '</div>';
		}
		$markup .= '</div>';

		return $markup;
	}

	private static function get_element_markup(&$rowsArr, &$element, &$html_id, &$obj, $row = null, $parentWidth = null) {
		$type = $element->type;
		$rowIx = $element->row;
		if (!is_null($row)) {
			$rowIx = $row;
		}

		// add space top, space bottom to col
		$style = '';

		if($element->spaceabove != 0){
			$spaceabove = $element->spaceabove;
			if (!is_null($parentWidth)) {
				$spaceabove = $spaceabove * 100 / $parentWidth;
			}
			$style .= 'padding-top:'.$spaceabove.'vw;';
		}
		if($element->spacebelow != 0){
			$spacebelow = $element->spacebelow;
			if (!is_null($parentWidth)) {
				$spacebelow = $spacebelow * 100 / $parentWidth;
			}
			$style .= 'padding-bottom:'.$spacebelow.'vw;';
		}
		if($element->offsetx != 0 || $element->offsety != 0){
			$x = $element->offsetx;
			$y = $element->offsety;
			$style .= 'transform:translate('.$x.'vw,'.$y.'vw);-webkit-transform:translate('.$x.'vw,'.$y.'vw);';
		}
		if (!is_null($parentWidth)) {
			$style .= 'width:'.(100*LayGridderFrontend::getChildWidth($obj, $element->colspan, $parentWidth)).'%;';
			$style .= 'margin-left:'.(100*LayGridderFrontend::getChildMarginLeft($obj, $element->col, $parentWidth)).'%;';
		}

		$frameOverflow = "";
		if(property_exists($element, 'frameOverflow') && $element->frameOverflow != ""){
			switch($element->frameOverflow) {
				case 'left':
					$frameOverflow = " frame-overflow-left";
					break;
				case 'both':
					$frameOverflow = " frame-overflow-left frame-overflow-right";
					break;
				case 'right':
					$frameOverflow =                     " frame-overflow-right";
					break;
			}
		}

		$classes = "";
		if(property_exists($element, 'classes') && $element->classes != ""){
			$classes = $element->classes;
		}
		$html_id = "";
		if(property_exists($element, 'html_id') && $element->html_id != ""){
			$html_id = $element->html_id;
		}

		$classes = apply_filters('lg_frontend_elclass', $classes, $element);


		$col = '<div '.($html_id != "" ? 'id="'.$html_id.'"' : "").' class="lg-col lg-span-'.$element->colspan.' lg-push-'.$element->push.' lg-align-'.$element->align.' lg-type-'.$type.' '.$classes.$frameOverflow.'" '.($style != "" ? 'style="'.$style.'"' : "").'>';

		// element content
		switch($type){
			case 'postThumbnail':
				$thumbnail = "";
				$attid = get_post_thumbnail_id($element->postid);
				$alt = get_post_meta($attid, '_wp_attachment_image_alt', true);
				$srcset = wp_get_attachment_image_srcset($attid);
				$vw = LayGridderFrontend::get_vw($obj, $element->colspan);
				// is viewport bigger than breakpoint? use $vw as "sizes", otherwise use 100vw
				$sizes = '(min-width: '.(LayGridderOptions::$phone_breakpoint+1).'px) '.$vw.'vw, 100vw';
				$url = wp_get_attachment_url($attid);

				$is_gif = substr($url, -4) == ".gif" ? true : false;

				$openingAnchorTag = '<a href="'.get_permalink($element->postid).'" data-id="'.$element->postid.'" data-slug="'.get_post_field('post_name', $element->postid).'" data-title="'.htmlentities(get_the_title($element->postid)).'">';
				$args = (object)array('url' => get_permalink($element->postid), 'element' => $element);
				$openingAnchorTag = apply_filters('lg_frontend_postthumbnail_opening_anchortag', $openingAnchorTag, $args);
				
				$thumbnail .= $openingAnchorTag.'<div class="lg-placeholder" style="padding-bottom:'.($element->ar*100).'%;">';
				if($is_gif){
					$thumbnail .= '<img src="'.$url.'" alt="'.$alt.'">';
				}else{
					$thumbnail .= '<img srcset="'.$srcset.'" sizes="'.$sizes.'" src="'.$url.'" alt="'.$alt.'">';
				}
				$thumbnail .= 
					'</div>
				</a>';

				$thumbnail .= 
				'<div class="lg-caption lg-textformat-parent"><p>'.get_the_title($element->postid).'</p></div>';

				$thumbnail = apply_filters('lg_frontend_postthumbnail', $thumbnail, $element, $obj);
				$col .= $thumbnail;
			break;
			case 'img':
				$img = "";
				$attid = $element->attid;
				$alt = get_post_meta($attid, '_wp_attachment_image_alt', true);

				// responsive images
				// http://alistapart.com/article/responsive-images-in-practice
				// https://make.wordpress.org/core/2015/11/10/responsive-images-in-wordpress-4-4/
				$srcset = wp_get_attachment_image_srcset($attid);
				$full_src = wp_get_attachment_url( $attid );

				// this vw does not take into account the space around the laygrid (which might be determined by the theme)
				// this vw expects the laygrid to be inside a 100% width container
				// while this might lead to bigger images loading than necessary, it is still better than loading the full image
				$vw = LayGridderFrontend::get_vw($obj, $element->colspan);
				// is viewport bigger than breakpoint? use $vw as "sizes", otherwise use 100vw
				$sizes = '(min-width: '.(LayGridderOptions::$phone_breakpoint+1).'px) '.$vw.'vw, 100vw';

				$is_gif = substr($full_src, -4) == ".gif" ? true : false;

				$img .= 
					'<div class="lg-placeholder" style="padding-bottom:'.($element->h/$element->w*100).'%;">';
				if($is_gif){
					$img .= '<img src="'.$full_src.'" alt="'.$alt.'">';
				}else{
					$img .= '<img srcset="'.$srcset.'" sizes="'.$sizes.'" src="'.$full_src.'" alt="'.$alt.'">';
				}
				$img .=
				'</div>';
				$img = apply_filters('lg_frontend_img', $img, $element, $obj);

				// add link
				$link_obj = property_exists($element, 'imagelink') && $element->imagelink != '' ? $element->imagelink : false;
				if( $link_obj != false && property_exists($link_obj, 'url') ){
					$target = '';
					if($link_obj->newtab == true){
						$target = 'target="_blank"';
					}
					$url = $link_obj->url;
					if($link_obj->id != "" && $link_obj->type != ""){
						$url = get_permalink($link_obj->id);
					}
					$opening_anchor_tag = '<a href="'.$url.'" '.$target.'>';
					$args = (object)array('url' => $url, 'target' => $target, 'element' => $element);
					$opening_anchor_tag = apply_filters('lg_frontend_img_opening_anchortag', $opening_anchor_tag, $args );
					$img = $opening_anchor_tag.$img.'</a>';
				}

				$col .= $img;
			break;
			case 'video':
				$video = '<div class="lg-placeholder" style="padding-bottom:'.($element->h/$element->w*100).'%;">'.$element->iframe.'</div>';
				$video = apply_filters('lg_frontend_video', $video, $element, $obj);
				$col .= $video;
			break;
			case 'text':
				$text = '<div class="lg-textformat-parent">'.$element->cont.'</div>';
				$text = apply_filters('lg_frontend_text', $text, $element, $obj);
				// make shortcodes work
				$text = do_shortcode($text);
				$col .= $text;
			break;
			case 'embed':
				$embed = $element->cont;
				$embed = apply_filters('lg_frontend_embed', $embed, $element, $obj);
				$col .= $embed;
			break;
			case 'html':
				$html = $element->cont;
				$html = apply_filters('lg_frontend_html', $html, $element, $obj);
				$html = do_shortcode($html);
				$col .= $html;
			break;
			case 'shortcode':
				$shortcode = $element->cont;
				$shortcode = apply_filters('lg_frontend_shortcode', $shortcode, $element, $obj);
				$shortcode = do_shortcode($shortcode);
				$col .= $shortcode;
			break;
			case 'hr':
				$hr_markup = '<div class="lay-hr"></div>';
				$hr_markup = apply_filters('lg_frontend_hr', $hr_markup, $element, $obj);
				$col .= $hr_markup;
			break;
			case 'html5video':
				$mute = $element->mute == true ? 'muted' : '';
				$autoplay = $element->autoplay == true ? 'autoplay' : '';
				$controls = $element->controls == true ? 'controls' : '';
				$loop = $element->loop == true ? 'loop' : '';

				$video = 
				'<video poster="'.LayGridderFrontend::get_static_url($element->cont).'" '.$mute.' '.$autoplay.' '.$controls.' '.$loop.'>
					<source src="'.LayGridderFrontend::get_static_url($element->mp4).'" type="video/mp4">
				</video>';

				$html5video_markup = '<div class="lg-placeholder" style="padding-bottom:'.($element->h/$element->w*100).'%;">'.$video.'</div>';
				$html5video_markup = apply_filters('lg_frontend_html5video', $html5video_markup, $element, $obj);
				$col .= $html5video_markup;
			break;
			case 'stack':
				$rowsArr[$rowIx][] = $col;
				$col = '';
				foreach ($element->cont as $inner_element) {
					$rowsArr[$rowIx][] = '<div class="lay-stack-element">';
					$stackWidth = LayGridderFrontend::getWidth($obj, $element->colspan);
					LayGridderFrontend::get_element_markup($rowsArr, $inner_element, $html_id, $obj, $element->row, $stackWidth);
					$rowsArr[$rowIx][] = '</div>';
				}
				break;
			default:
				// cmb2 element
				if( has_filter('lg_frontend_'.$type) ){
					$col .= apply_filters('lg_frontend_'.$type, $element, $obj);
				}
			break;
		}

		// caption
		if(property_exists($element, 'caption') && $element->caption != ""){
			$caption = '<div class="lg-caption lg-textformat-parent">'.$element->caption.'</div>';
			$caption = apply_filters('lg_frontend_caption', $caption, $element);

			// add image's link
			$link_obj = property_exists($element, 'imagelink') && $element->imagelink != '' ? $element->imagelink : false;
			if( $link_obj != false && property_exists($link_obj, 'url') ){
				$target = '';
				if($link_obj->newtab == true){
					$target = 'target="_blank"';
				}
				$url = $link_obj->url;
				if($link_obj->id != "" && $link_obj->type != ""){
					$url = get_permalink($link_obj->id);
				}
				$openingAnchorTag = '<a href="'.$url.'" '.$target.'>';
				$args = (object)array('element' => $element, 'url' => $url, 'target' => $target);
				$openingAnchorTag = apply_filters('lg_frontend_img_caption_opening_anchortag', $openingAnchorTag, $args);
				$caption = $openingAnchorTag.$caption.'</a>';
			}
			$col .= $caption;
		}

		$col .= '</div>';
		$rowsArr[$rowIx] []= $col;

		// add "contains element" classes to row
		if( array_key_exists($rowIx, $obj->rowAttrs) ){
			if( is_object($obj->rowAttrs[$rowIx]) ){
				if(property_exists( $obj->rowAttrs[$rowIx], "classes" )){
					if( strpos($obj->rowAttrs[$rowIx]->classes, "lg-contains-".$type) === false ){
						$obj->rowAttrs[$rowIx]->classes .= " lg-contains-".$type;
					}
				}else{
					$obj->rowAttrs[$rowIx]->classes = "lg-contains-".$type;
				}
			}else{
				$obj->rowAttrs[$rowIx] = new stdClass();
				$obj->rowAttrs[$rowIx]->classes = "lg-contains-".$type;
			}
		}
	}

}
new LayGridderFrontend();

function get_laygrid($id="", $type=""){
	// type can be "post/page", "", custom taxonomy slug, "category"
	return LayGridderFrontend::get_laygrid($id, $type);
}

function the_laygrid($id="", $type=""){
	// type can be "post/page", "", custom taxonomy slug, "category"
	echo LayGridderFrontend::get_laygrid($id, $type);
}

function lg_get_background_color($id="", $type=""){
	return LayGridderFrontend::lg_get_background_color($id, $type);
}