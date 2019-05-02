<?php
class LayGridderLocation{

	public static $gridder_located_here = false;
	public static $hide_content_editor_here = false;

	public static $default =
	'[  
		{
			"type": "post_type",
			"val": "post",
			"hide_editor": false
		},
		{
			"type": "post_type",
			"val": "page",
			"hide_editor": false
		}
	]';

	public function __construct(){
		add_action( 'admin_enqueue_scripts', array( $this, 'location_scripts' ), 10 );
		add_action( 'admin_enqueue_scripts', array( $this, 'location_styles' ) );

		add_action( 'current_screen', array($this, 'set_gridder_located_here') );

		add_action( 'admin_head', array($this, 'maybe_hide_content_editor') );
		add_action( 'admin_init', array($this, 'register_settings') );
	}

	public function register_settings() {
		register_setting( 'laygridder-options-page', 'lg_locations_json' );
	}

	public function maybe_hide_content_editor(){
		if(LayGridderLocation::$gridder_located_here && LayGridderLocation::$hide_content_editor_here){
			echo '<style id="lg-hide-content-editor">#postdivrich{display:none;}</style>';
		}
	}

	public static function set_gridder_located_here(){
		LayGridderLocation::$gridder_located_here = LayGridderLocation::is_gridder_located_here();
	}

	public static function is_gridder_located_here(){
		$return = false;
		LayGridderLocation::$hide_content_editor_here = false;

		$screen = get_current_screen();
		
		if($screen->post_type == "laygridder_template" && LayGridderTemplates::$isActive){
			return true;
		}

		$json = get_option( 'lg_locations_json', LayGridderLocation::$default );
		$obj = json_decode($json);
		$post_id = isset($_GET['post']) ? $_GET['post'] : '';

		foreach ($obj as $location) {
			switch($location->type){
				case 'post_type':
					if($screen->base == "post" && $screen->post_type == $location->val){
						$return = $location;
						break;
					}
				break;
				case 'post_format':
					if($screen->base == "post"){
						// https://codex.wordpress.org/Function_Reference/get_post_format
						// Note also that the default format (i.e., a normal post) returns false, but this is also referred in some places as the 'standard' format. In some cases, developers may wish to do something like the following to maintain consistency:

						$format = get_post_format($post_id) ? : 'standard';
						if($location->val == $format){
							$return = $location;
							break;
						}
					}
				break;
				case 'post_category':
					if($screen->base == "post"){
						$cat_id = (int)$location->val;
						$has_cat = has_category($cat_id, $post_id);
						if($has_cat == true){
							$return = $location;
							break;
						}
					}
				break;
				case 'post_taxonomy':
					if($screen->base == "post"){
						$term_id = (int)$location->val;
						$term_obj = get_term($term_id);
						$has_cat = has_term($term_id, $term_obj->taxonomy, $post_id);
						if($has_cat == true){
							$return = $location;
							break;
						}
					}
				break;
				case 'post':
					if($screen->base == "post"){
						if($post_id == (int)$location->val){
							$return = $location;
							break;
						}
					}
				break;
				case 'page':
					if($screen->base == "post"){
						if($post_id == (int)$location->val){
							$return = $location;
							break;
						}
					}
				break;
				case 'tax_term':
					if($screen->base == "term"){
						// if location is "all", $return = $location for all terms but not for post_tag
						// tags probably don't need a gridder
						if($location->val == "all" && $screen->taxonomy != "post_tag"){
							$return = $location;
							break;
						}else if($screen->taxonomy == $location->val){
							$return = $location;
							break;
						}
					}
				break;
			}
		}

		// $return is the current $location where the laygridder is located
		// set hide content editor
		if($return != false){			
			if(property_exists($return, 'hide_editor') && $screen->base == "post"){
				LayGridderLocation::$hide_content_editor_here = $return->hide_editor;
			}
			return true;
		}

		return false;
	}

	public static function location_setting(){	
		$json = get_option( 'lg_locations_json', LayGridderLocation::$default );
		echo 
		'<textarea id="lg_locations_json" name="lg_locations_json">'.$json.'</textarea>';
	}

	public function location_scripts($hook){
		if ( $hook == 'toplevel_page_laygridder-options-page' ) {
			wp_enqueue_script( 'location-marionette', LG_PLUGIN_URL.'/assets/js/backbone.marionette.min.js', array( 'jquery', 'underscore', 'backbone', 'json2' ), LG_VER);
			wp_enqueue_script( 'location-app', LG_PLUGIN_URL."/location/assets/js/location.app.min.js", array( 'jquery', 'underscore', 'backbone', 'json2' ), LG_VER);
		}
	}

	public function location_styles($hook) {
		if ( $hook == 'toplevel_page_laygridder-options-page' ) {
			wp_enqueue_style( 'lg-location-bootstrap', LG_PLUGIN_URL.'/assets/css/bootstrap.css', array(), LG_VER );
			wp_enqueue_style( 'lg-location-application', LG_PLUGIN_URL.'/location/assets/css/location.style.css', array(), LG_VER );
		}
	}

	public static function get_select_view(){
		// get custom post types
		$custom_post_types = get_post_types( array('public' => true, '_builtin' => false), 'objects' );

		// remove laygridder_template post type from the list
		if(LayGridderTemplates::$isActive == true){
			foreach ($custom_post_types as $key => $post_type) {
				if($post_type->name == "laygridder_template"){
					unset($custom_post_types[$key]);
					$custom_post_types = array_values($custom_post_types);
					break;
				}
			}
		}

		// terms of taxonomy category
		$category_terms = get_terms( array(
		    'taxonomy' => 'category',
		    'hide_empty' => false,
		) );

		$post_formats = get_theme_support('post-formats');

		// get custom taxonomies
		$custom_taxonomies = get_taxonomies( array('public' => true, '_builtin' => false), 'objects' );

		// terms of custom taxonomies
		$customtax_terms = array();
		if(count($custom_taxonomies)>0){
			foreach ($custom_taxonomies as $tax) {
				$terms = get_terms( array(
				    'taxonomy' => $tax->name,
				    'hide_empty' => false,
				) );
				$customtax_terms[$tax->name] = $terms;
			}
		}

		// get all posts of post type "post"
		$args = array(
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'post_type' => 'post'
		);
		$all_posts_query = new WP_Query( $args );

		// get all pages
		$args = array(
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'post_type' => 'page'
		);
		$pages_query = new WP_Query( $args );

		// get all posts of custom post types
		$posts_of_custom_post_types = array();
		foreach ($custom_post_types as $post_type) {

			$args = array(
				'posts_per_page' => -1,
				'orderby' => 'menu_order',
				'post_type' => $post_type->name
			);
			$custom_posts_query = new WP_Query( $args );
			if($custom_posts_query->have_posts()){
				$posts_of_custom_post_types[$post_type->name] = $custom_posts_query->posts;
			}
		}

		$markup = 
		'<select class="" id="lg-type-select">
			<optgroup label="Post">
				<option value="post_type" selected="selected">Post Type</option>';
				if($post_formats != false){
					$markup .= 
					'<option value="post_format">Post Format</option>';
				}
				if ( count($category_terms) > 0 ) {
					$markup .= 
					'<option value="post_category">Post Category</option>';
				}
			$markup .= '<option value="post_taxonomy">Post Taxonomy</option>
				<option value="post">Post</option>
			</optgroup>
			<optgroup label="Page">
				<option value="page">Page</option>
			</optgroup>
			<optgroup label="Taxonomy Term">
				<option value="tax_term">Taxonomy Term</option>
			</optgroup>
		</select><span class="lg-location-equal-to"> is equal to </span>';

		// post type
		$markup .=
		'<select class="lg-location-select" id="lg-post_type">
			<option value="post">Post</option>
			<option value="page">Page</option>';
		foreach ($custom_post_types as $custom_post_type) {
			$markup .= 
			'<option value="'.$custom_post_type->name.'">'.$custom_post_type->labels->singular_name.'</option>';
		}
		$markup .=	
		'</select>';

		// post formats
		if($post_formats != false){
			$markup .=
			'<select class="lg-location-select" id="lg-post_format">
				<option value="standard">Standard</option>';
				foreach ($post_formats[0] as $post_format) {
					$markup .= 
					'<option value="'.$post_format.'">'.ucfirst($post_format).'</option>';
				}
			$markup .=	
			'</select>';
		}

		// post category
		if ( count($category_terms) > 0 ) {
			$markup .= '<select class="lg-location-select" id="lg-post_category">';
			foreach ($category_terms as $term){
				$markup .= 
				'<option value="'.$term->term_id.'">'.$term->name.'</option>';
			}
			$markup .= '</select>';
		};

		// post taxonomies
		// category terms
		$markup .= '<select class="lg-location-select" id="lg-post_taxonomy">';
		if ( count($category_terms) > 0 ) {
			$optgroup_inner = "";
			foreach ($category_terms as $term) {
				$optgroup_inner .= 
				'<option value="'.$term->term_id.'">'.$term->name.'</option>';
			}
			$markup .= '<optgroup label="Category">'.$optgroup_inner.'</optgroup>';
		}
		// custom taxonomy terms
		$optgroups = "";
		if( count($custom_taxonomies) > 0 ) {
			foreach ($custom_taxonomies as $custom_taxonomy) {
				if(count($customtax_terms[$custom_taxonomy->name]) > 0){
					$optgroups .= '<optgroup label="'.$custom_taxonomy->label.'">';
					foreach ($customtax_terms[$custom_taxonomy->name] as $customtax_term) {
						$optgroups .= 
						'<option value="'.$customtax_term->term_id.'">'.$customtax_term->name.'</option>';
					}
					$optgroups .= '</optgroup>';
				}
			}
		}
		$markup .= $optgroups.'</select>';

		// posts
		// posts of type post
		$markup .= '<select class="lg-location-select" id="lg-post">';
		if($all_posts_query->have_posts()){
			$optgroup_inner = "";
			foreach ($all_posts_query->posts as $post){
				$optgroup_inner .= 
				'<option value="'.$post->ID.'">'.$post->post_title.'</option>';
			}
			$markup .= '<optgroup label="Post">'.$optgroup_inner.'</optgroup>';
		}
		// posts of custom post types
		$optgroups = "";
		if( count($custom_post_types) > 0 ) {
			foreach ($custom_post_types as $post_type) {
				if(count($posts_of_custom_post_types[$post_type->name]) > 0){
					$optgroups .= '<optgroup label="'.$post_type->label.'">';
					foreach ($posts_of_custom_post_types[$post_type->name] as $post) {
						$optgroups .= 
						'<option value="'.$post->ID.'">'.$post->post_title.'</option>';
					}
					$optgroups .= '</optgroup>';
				}
			}
		}
		$markup .= $optgroups.'</select>';

		// pages
		$markup .= '<select class="lg-location-select" id="lg-page">';
		if($pages_query->have_posts()){
			foreach ($pages_query->posts as $post){
				$markup .= 
				'<option value="'.$post->ID.'">'.$post->post_title.'</option>';
			}
		}
		$markup .= '</select>';

		// taxonomy term
		$markup .= '<select class="lg-location-select" id="lg-tax_term">';
		$markup .= '<option value="all">All</option>';
		$markup .= '<option value="category">Category</option>';
		foreach ($custom_taxonomies as $tax) {
			$markup .= '<option value="'.$tax->name.'">'.$tax->label.'</option>';
		}
		$markup .= '</select>';

		// delete button
		$markup .= '<button type="button" class="btn btn-default js-delete-location btn-sm"><span class="glyphicon glyphicon-remove"></span> Remove</button>';

		return $markup;
	}

}
new LayGridderLocation();