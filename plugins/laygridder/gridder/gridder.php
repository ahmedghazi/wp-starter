<?php

class LayGridder {
	public static $tinymceSettings;
	public static $maxFontsize = 300;
	public static $rowgutter_mu;
	public static $topframe_mu;
	public static $frame_mu;
	public static $bottomframe_mu;

	public static function init() {
		add_action( 'add_meta_boxes', 'LayGridder::add_gridder_meta_boxes' );
		add_action( 'admin_init', 'LayGridder::register_gridder_iris' );
		add_action( 'admin_enqueue_scripts', 'LayGridder::gridder_scripts' );
		add_action( 'admin_enqueue_scripts', 'LayGridder::gridder_styles' );
		LayGridder::initTinymce();

		add_action( 'save_post', 'LayGridder::save_desktop_json' );
		add_action( 'save_post', 'LayGridder::save_phone_json' );

		// ajax for hiding texteditor notices
		add_action( 'wp_ajax_set_notice_hidden_via_ajax', 'LayGridder::set_notice_hidden_via_ajax' );

		add_action( 'wp_ajax_get_embed', 'LayGridder::get_embed' );
		add_action( 'wp_ajax_nopriv_get_embed', 'LayGridder::get_embed' );

		add_action( 'wp_ajax_get_posts_for_imagelink', 'LayGridder::get_posts_for_imagelink' );
		add_action( 'wp_ajax_get_posts_for_imagelink_json', 'LayGridder::get_posts_for_imagelink_json' );

		// revision
		add_action( 'wp_ajax_get_gridder_revision', 'LayGridder::get_gridder_revision' );

		LayGridder::$rowgutter_mu = get_option( 'gridder_defaults_rowgutter_mu', LG_Constants::gridder_defaults_rowgutter_mu );
		LayGridder::$topframe_mu = get_option( 'gridder_defaults_topframe_mu', LG_Constants::gridder_defaults_topframe_mu );
		LayGridder::$frame_mu = get_option( 'gridder_defaults_frame_mu', LG_Constants::gridder_defaults_frame_mu );
		LayGridder::$bottomframe_mu = get_option( 'gridder_defaults_bottomframe_mu', LG_Constants::gridder_defaults_bottomframe_mu );
	}

	// TINYMCE
	// TODO: move tinymce to its own file
	private static function initTinymce() {
		// cannot put "apply_filters" in this constrcutor because otherwise no filter for this will ever be applied
		add_action( 'init', 'LayGridder::add_filter_for_tinymce_settings' );
		add_filter( 'tiny_mce_before_init', 'LayGridder::tinymce_fontsizes' );
		add_filter( 'tiny_mce_before_init', 'LayGridder::unhide_kitchensink' );
		add_filter( 'tiny_mce_before_init', 'LayGridder::tinymce_config' );

		add_filter( 'mce_external_plugins', 'LayGridder::tinymce_external_plugins' );
		add_action( 'admin_init', 'LayGridder::my_deregister_editor_expand' );

		add_filter( 'tiny_mce_plugins', 'LayGridder::tinymce_remove_default_plugins' );

		$toolbar1 = 'styleselect, undo, redo, link, unlink, fontselect, fontsizeselect, lineheightselect, letterspacingselect, table';
		$toolbar2 = 'forecolor, bold, italic, underline, alignleft, aligncenter, alignright, removeformat, charmap, nonbreaking, softhyphen, visualblocks, code';
		$minimal_buttons = get_option( 'gridder_options_minimal_wysiwyg_buttons', "" );
		if($minimal_buttons == "on"){
			$toolbar1 = implode(LayGridderSetup::$minimal_tinymce_buttons, ",");
			$toolbar2 = "";
		}

		LayGridder::$tinymceSettings = array(
			'media_buttons' => false,
			'quicktags' => false,
			'tinymce' => array( 'toolbar1' => $toolbar1, 'toolbar2' => $toolbar2, 'toolbar3' => '', 'toolbar4' => '' )
		);
	}

	public static function add_filter_for_tinymce_settings() {
		LayGridder::$tinymceSettings = apply_filters('lg_tinymce_settings', LayGridder::$tinymceSettings);
	}

	// http://www.wpexplorer.com/wordpress-tinymce-tweaks/
	public static function tinymce_fontsizes($initArray) {

		$sizes = '';
		$space = '';

		for($i=1; $i<LayGridder::$maxFontsize; $i++){
			if($i>1){
				$space = ' ';
			}
			$sizes .= $space.$i.'px';
		}

		$initArray['fontsize_formats'] = $sizes;
		return $initArray;
	}

	public static function unhide_kitchensink($args) {
		$args['wordpress_adv_hidden'] = false;
		return $args;
	}

	public static function tinymce_config($init) {
		unset( $init['wp_autoresize_on'] );
		$init['paste_remove_styles'] = true;
		$init['paste_remove_spans'] = true;
		return $init;
	}

	public static function tinymce_external_plugins($plugins) {
		$plugins['height'] = LG_PLUGIN_URL.'/gridder/assets/js/tinymce-plugins/height/plugin.js';
		$plugins['letterspacingselect'] = LG_PLUGIN_URL.'/gridder/assets/js/tinymce-plugins/letterspacing/plugin.js';
		$plugins['lineheightselect'] = LG_PLUGIN_URL.'/gridder/assets/js/tinymce-plugins/lineheight/plugin.js';
		$plugins['code'] = LG_PLUGIN_URL.'/gridder/assets/js/tinymce-plugins/code/plugin.min.js';
		$plugins['nonbreaking'] = LG_PLUGIN_URL.'/gridder/assets/js/tinymce-plugins/nonbreaking/plugin.min.js';
		$plugins['softhyphen'] = LG_PLUGIN_URL.'/gridder/assets/js/tinymce-plugins/softhyphen/plugin.js';
		$plugins['table'] = LG_PLUGIN_URL.'/gridder/assets/js/tinymce-plugins/table/plugin.min.js';
		$plugins['visualblocks'] = LG_PLUGIN_URL.'/gridder/assets/js/tinymce-plugins/visualblocks/plugin.min.js';
		return $plugins;
	}

	// no auto resize and no expand for tinymce
	// https://core.trac.wordpress.org/ticket/29360#comment:10
	public static function my_deregister_editor_expand() {
		wp_deregister_script('editor-expand');
	}

	public static function tinymce_remove_default_plugins($plugins){
		unset( $plugins['wpemoji'] );
		unset( $plugins['wpgallery'] );
		unset( $plugins['fullscreen'] );
		return $plugins;
	}
	// TINYMCE

	// not for terms
	public static function add_gridder_meta_boxes() {
		if (LayGridderLocation::$gridder_located_here) {
			// desktop gridder json
			add_meta_box( 'gridder-json-metabox', 'Gridder JSON', 'LayGridder::gridder_json_metabox_callback', null, 'normal', 'high' );

			// phone gridder json
			add_meta_box( 'gridder-phone-json-metabox', 'Gridder Phone JSON', 'LayGridder::gridder_phone_json_metabox_callback', null, 'normal', 'high' );

			// gridder metabox
			add_meta_box( 'gridder-metabox', 'LayGridder', 'LayGridder::gridder_metabox_callback', null, 'normal', 'high' );
		}
	}

	public static function gridder_metabox_callback() {
		require_once( LG_PLUGIN_PATH . '/gridder/markup.php' );
		// TODO: do_action('lay_before_projectpage_gridder_modals');
	}

	public static function gridder_json_metabox_callback( $post ) {
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'gridder_json_metabox', 'gridder_json_metabox_nonce' );

		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */
		$value = get_post_meta( $post->ID, '_gridder_json', true );
		
		echo '<textarea id="gridder_json_field" name="gridder_json_field" style="width:100%;height:200px;">';
		echo htmlspecialchars($value);
		echo '</textarea>';
	}

	public static function gridder_phone_json_metabox_callback( $post ) {
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'gridder_phone_json_metabox', 'gridder_phone_json_metabox_nonce' );

		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */
		$value = get_post_meta( $post->ID, '_phone_gridder_json', true );
		
		echo '<textarea id="phone_gridder_json_field" name="phone_gridder_json_field" style="width:100%;height:200px;">';
		echo htmlspecialchars($value);
		echo '</textarea>';	
	}

	public static function save_phone_json($post_id) {
		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['gridder_phone_json_metabox_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['gridder_phone_json_metabox_nonce'], 'gridder_phone_json_metabox' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		/* OK, it's safe for us to save the data now. */
		
		// Make sure that it is set.
		if ( ! isset( $_POST['phone_gridder_json_field'] ) ) {
			return;
		}

		// get user input.
		$my_data = $_POST['phone_gridder_json_field'];

		// make sure its not a revision because a revision in wordpress removes escape backslashes and makes json invalid
		if(!wp_is_post_revision($post_id)){

			// save last meta field value
			$value = get_post_meta( $post_id, '_phone_gridder_json', true );
			$value = wp_slash($value);
			update_post_meta( $post_id, '_phone_gridder_json_last', $value );

			// update "real" meta 
			// Update the meta field in the database.
			update_post_meta( $post_id, '_phone_gridder_json', $my_data );
		}

	}

	public static function save_desktop_json($post_id) {
		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['gridder_json_metabox_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['gridder_json_metabox_nonce'], 'gridder_json_metabox' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		/* OK, it's safe for us to save the data now. */
		
		// Make sure that it is set.
		if ( ! isset( $_POST['gridder_json_field'] ) ) {
			return;
		}

		// get user input.
		$my_data = $_POST['gridder_json_field'];

		// make sure its not a revision because a revision in wordpress removes escape backslashes and makes json invalid
		if(!wp_is_post_revision($post_id)){
			// save last meta field value
			$value = get_post_meta( $post_id, '_gridder_json', true );
			$value = wp_slash($value);
			update_post_meta( $post_id, '_gridder_json_last', $value );

			// update "real" meta 
			// Update the meta field in the database.
			update_post_meta( $post_id, '_gridder_json', $my_data );
		}
	}

	public static function set_notice_hidden_via_ajax(){
		$optionname = $_POST['optionname'];
		update_option( $optionname, 'hide' );
		echo get_option( $optionname, '' );
		die();
	}

	public static function get_embed(){
		$s = $_POST['layembedval'];
		echo wp_oembed_get($s);
		die();
	}

	public static function get_posts_for_imagelink_json(){
		$post_types_to_query = LG_Util::get_all_post_types();

		$args = array(
			'posts_per_page' => -1,
			'orderby' => 'title',
			'post_type' => $post_types_to_query,
			'order' => 'ASC',
		);
		$all_posts_query = new WP_Query( $args );

		$posts = array();

		if ($all_posts_query->have_posts()) {
			foreach ($all_posts_query->posts as $post) {
				$permalink = get_permalink($post);
				$date = date('Y/m/d', strtotime($post->post_date));

				$posts[] = array(
					'url' => $permalink,
					'id' => $post->ID,
					'type' => $post->post_type,
					'title' => $post->post_title,
					'date' => $date
				);
			}
		}

		wp_send_json($posts);
		die();
	}

	// TODO: delete and test
	public static function get_posts_for_imagelink(){
		$post_types_to_query = LG_Util::get_all_post_types();

		$args = array(
			'posts_per_page' => -1,
			'orderby' => 'title',
			'post_type' => $post_types_to_query,
			'order' => 'ASC',
		);
		$all_posts_query = new WP_Query( $args );

		$markup = '<div id="wp-link"><ul>';
		$ix = 0;

		if ( $all_posts_query->have_posts() ) {
			foreach ($all_posts_query->posts as $post){
				$id = $post->ID;
				$title = $post->post_title;
				$permalink = get_permalink($post);

				$alternate = $ix % 2 == 0 ? ' class="alternate"' : '';

				$info = $post->post_type.' ';
				$info .= date('Y/m/d', strtotime($post->post_date));

				// $array []= array('id'=>$id, 'title'=>$title, 'permalink'=>$permalink, 'type'=>'post');
				$markup .= 
				'<li'.$alternate.'>
					<input type="hidden" class="item-permalink" value="'.$permalink.'">
					<input type="hidden" class="item-id" value="'.$id.'">
					<input type="hidden" class="item-type" value="'.$post->post_type.'">
					<span class="item-title">'.$post->post_title.'</span>
					<span class="item-info">'.$info.'</span>
				</li>'; 
				$ix++;
			}
		}

		$markup .= '</ul></div>';

		echo '<input type="search" name="lay-imagelink-search-input" id="lay-imagelink-search-input" placeholder="Search"><div class="lay-imagelink-postlist-wrap">'.$markup.'</div>';
		die();
	}

	public static function get_gridder_revision() {
		$id = intval($_POST['id']);
		$screen_base = $_POST['screen_base'];

		$gridderval = "";
		$phonegridderval = "";

		if ($screen_base == 'term') {
			// term
			$gridderval = get_term_meta( $id, "_gridder_json_last", true );
			$gridderval = wp_slash($gridderval);
			update_term_meta( $id, '_gridder_json', $gridderval );
			
			$phonegridderval = get_term_meta( $id, "_phone_gridder_json_last", true );
			$phonegridderval = wp_slash($phonegridderval);
			update_term_meta( $id, '_phone_gridder_json', $phonegridderval );
		} else {
			// post, page
			$gridderval = get_post_meta( $id, '_gridder_json_last', true );
			$gridderval = wp_slash($gridderval);
			update_post_meta( $id, '_gridder_json', $gridderval );

			$phonegridderval = get_post_meta( $id, '_phone_gridder_json_last', true );
			$phonegridderval = wp_slash($phonegridderval);
			update_post_meta( $id, '_phone_gridder_json', $phonegridderval );
		}
		
		$data = array(
			'gridder' => $gridderval,
			'gridder_phone' => $phonegridderval
		);

		wp_send_json($data);
		wp_die();
	}


	public static function gridder_scripts() {
		if (LayGridderLocation::$gridder_located_here) {
			// ace for html modal
			wp_enqueue_script( 'vendor-ace', LG_PLUGIN_URL.'/gridder/assets/js/vendor/ace/ace.js', array(), LG_VER );
			wp_enqueue_script( 'vendor-instagram', '//platform.instagram.com/en_US/embeds.js' );
			wp_enqueue_script( 'vendor-highlight', LG_PLUGIN_URL.'/gridder/assets/js/vendor/highlight.pack.js', array(), LG_VER);
			wp_enqueue_script( 'vendor-webfontloader', LG_PLUGIN_URL.'/gridder/assets/js/vendor/webfontloader.js', array(), LG_VER);
			wp_enqueue_script( 'vendor-bootstrap', LG_PLUGIN_URL."/assets/js/bootstrap.min.js", array( 'jquery' ), LG_VER);
			wp_enqueue_script( 'vendor-react', LG_PLUGIN_URL.'/gridder/assets/js/react.min.js', array(), LG_VER );
			wp_enqueue_script( 'vendor-react-dom', LG_PLUGIN_URL.'/gridder/assets/js/react-dom.min.js', array('vendor-react'), LG_VER );
			wp_enqueue_script( 'vendor-redux', LG_PLUGIN_URL.'/gridder/assets/js/redux.min.js', array(), LG_VER );
			wp_enqueue_script( 'vendor-react-redux', LG_PLUGIN_URL.'/gridder/assets/js/react-redux.min.js', array('vendor-redux', 'vendor-react'), LG_VER );
			wp_enqueue_script( 'vendor-redux-thunk', LG_PLUGIN_URL.'/gridder/assets/js/redux-thunk.min.js', array('vendor-redux'), LG_VER );
			wp_enqueue_media();
			
			wp_enqueue_script( 'gridder-app', LG_PLUGIN_URL."/gridder/assets/js/gridder.app.min.js", array(
					'jquery', 'underscore' , 'vendor-highlight', 
					'vendor-react', 'vendor-react-dom', 'vendor-redux', 'vendor-react-redux', 'vendor-redux-thunk', 'gridder-iris'
				), LG_VER, true );

			$rowgutter = get_option( 'gridder_defaults_row_gutter', LG_Constants::gridder_defaults_row_gutter );
			$frame = get_option( 'gridder_defaults_frame', LG_Constants::gridder_defaults_frame );
			$topframe = get_option( 'gridder_defaults_topframe', LG_Constants::gridder_defaults_topframe );
			$bottomframe = get_option( 'gridder_defaults_bottomframe', LG_Constants::gridder_defaults_bottomframe );
			$colgutter = get_option( 'gridder_defaults_column_gutter', LG_Constants::gridder_defaults_column_gutter );
			$columncount = get_option( 'gridder_defaults_columncount', LG_Constants::gridder_defaults_columncount );

			$phone_rowgutter = get_option( 'phone_gridder_defaults_row_gutter', LG_Constants::phone_gridder_defaults_row_gutter );
			$phone_frame = get_option( 'phone_gridder_defaults_frame', LG_Constants::phone_gridder_defaults_frame );
			$phone_topframe = get_option( 'phone_gridder_defaults_topframe', LG_Constants::phone_gridder_defaults_topframe );
			$phone_bottomframe = get_option( 'phone_gridder_defaults_bottomframe', LG_Constants::phone_gridder_defaults_bottomframe );
			$phone_colgutter = get_option( 'phone_gridder_defaults_column_gutter', LG_Constants::phone_gridder_defaults_column_gutter );
			$phone_columncount = get_option( 'phone_gridder_defaults_columncount', LG_Constants::phone_gridder_defaults_columncount );

			$phoneBreakpoint = get_option( 'gridder_options_breakpoint', 700 );

			$bg_color_palette = array('#000', '#fff', '#f00', '#0f0', '#00f', '#ff0', '#0ff', '#f0f');
			$bg_color_palette = apply_filters('lg_bg_color_palette', $bg_color_palette);

			$screen = get_current_screen();

			$gridder_json = '';
			$phone_gridder_json = '';
			$id = '';

			if ($screen->base == 'post') {
				// just for posts/pages/custom post types
				global $post;
				$id = $post->ID;
				$gridder_json = get_post_meta( $id, '_gridder_json', true );
				$phone_gridder_json = get_post_meta( $id, '_phone_gridder_json', true );
			} else if ($screen->base == 'term') {
				// just for terms
				global $tag;
				if (is_object($tag)) {
					$id = $tag->term_id;
					$gridder_json = get_term_meta( $tag->term_id, '_gridder_json', true );
					$phone_gridder_json = get_term_meta( $tag->term_id, '_phone_gridder_json', true );
				}
			}

			$plugins = array(
				'qTranslateX' => is_plugin_active( 'qtranslate-x/qtranslate.php' )
			);

			wp_localize_script( 'gridder-app', 'passedData', array(
				'gridder_json' => $gridder_json, // TODO: necessary?
				'phone_gridder_json' => $phone_gridder_json, // TODO: necessary?
				'siteUrl' => get_site_url(),
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'templateURI' => LG_PLUGIN_URL, // TODO: remove this line, it is duplicate of pluginUrl
				'phoneLayoutActive' => LayGridderOptions::$phoneLayoutActive,
				'templatesActive' => LayGridderTemplates::$isActive,
				'disableInputs' => LayGridderOptions::$disableInputs,
				'bgColorPalette' => $bg_color_palette,
				'id' => $id,
				'screenId' => $screen->id,
				'screenBase' => $screen->base, // TODO: necessary?
				'phoneBreakpoint' => $phoneBreakpoint,
				'plugins' => $plugins,
				'gridderDefaults' => array( 
					'colCount' => $columncount,
					'colGutter' => $colgutter,
					'frameMargin' => $frame,
					'topFrameMargin' => $topframe,
					'bottomFrameMargin' => $bottomframe,
					'rowGutter' => $rowgutter,
					'rowGutterMu' => LayGridder::$rowgutter_mu,
					'topFrameMu' => LayGridder::$topframe_mu,
					'frameMu' => LayGridder::$frame_mu,
					'bottomFrameMu' => LayGridder::$bottomframe_mu,
					'phoneRowGutter' => $phone_rowgutter,
					'phoneFrame' => $phone_frame,
					'phoneTopFrame' => $phone_topframe,
					'phoneBottomFrame' => $phone_bottomframe,
					'phoneColGutter' => $phone_colgutter,
					'phoneColCount' => $phone_columncount
				)
			));
		}
	}

	public static function register_gridder_iris(){
		// using modified version of iris to prevent scrolling when user drags inside colorpicker
		wp_register_script( 'gridder-iris', LG_PLUGIN_URL.'/assets/js/iris.js', array( 'jquery-ui-widget', 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch', 'jquery-color' ), LG_VER );
	}

	public static function gridder_styles() {
		wp_enqueue_style( 'gridder-iris', LG_PLUGIN_URL.'/assets/css/iris.css', array(), LG_VER );
		wp_enqueue_style( 'gridder-bootstrap', LG_PLUGIN_URL.'/assets/css/bootstrap.css', array(), LG_VER );
		wp_enqueue_style( 'gridder-style', LG_PLUGIN_URL.'/gridder/assets/css/gridder.style.css', array(), LG_VER );
	}
}

LayGridder::init();