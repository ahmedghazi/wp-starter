<?php 

add_action('init', 'handle_init');
function handle_init() {
    //register_taxonomy_for_object_type('category', 'page');
    register_taxonomy_for_object_type('post_tag', 'page');
    //
    //add_post_type_support('page', 'category');
    //add_post_type_support( 'page', 'excerpt' );
    add_post_type_support( 'page', 'post_tag' );

    remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
}

add_image_size( "large_hdpi", 1440);
add_image_size( "large_mdpi", 1024);

function trace($o){
	echo "<pre>";
	print_r($o);
	echo "</pre>";
}

function tracel($o){
	error_log(print_r($o, TRUE));
}

// Callback function to filter the MCE settings
function my_mce_before_init_insert_formats( $init_array ) {  
	// Define the style_formats array
	$style_formats = array(  
		// Each array child is a format with it's own settings
		array(  
			'title' => 'condensed',  
			'block' => 'p',  
			'classes' => 'condensed',
			'wrapper' => true,
			
		),  
		array(  
			'title' => 'extended',  
			'block' => 'p',  
			'classes' => 'extended',
			'wrapper' => false,
		),
		array(  
			'title' => 'titraille',  
			'block' => 'p',  
			'classes' => 'titraille',
			'wrapper' => false,
		),
		array(  
			'title' => 'small',  
			'block' => 'p',  
			'classes' => 'small',
			'wrapper' => false,
		),
	);  
	// Insert the array, JSON ENCODED, into 'style_formats'
	$init_array['style_formats'] = json_encode( $style_formats );  
	
	return $init_array;  
  
} 
// Attach callback to 'tiny_mce_before_init' 
add_filter( 'tiny_mce_before_init', 'my_mce_before_init_insert_formats' ); 


function the_background_color(){
	$color = get_field("couleur");
	$css = 'style="background-color:'.$color.'"';
	echo $css;
}

function wpsites_exclude_latest_post( $q ) {
	/*if(    !is_admin() 
        && $q->is_main_query() 
        //&& $q->is_post_type_archive( 'projekte' ) 
    ){
		//$my_post_type = get_query_var( 'post_type' );
		//$q->set( 'offset', '1' );
	}*/
}

//add_action( 'pre_get_posts', 'wpsites_exclude_latest_post', 1 );

function cc_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page();
	
}

function _get_slug(){
    global $post;
    return $post->post_name;
}

function _the_slug(){
    global $post;
    echo $post->post_name;
}

function custom_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

function custom_excerpt_more( $more ) {
	return '...';
}
add_filter( 'excerpt_more', 'custom_excerpt_more' );