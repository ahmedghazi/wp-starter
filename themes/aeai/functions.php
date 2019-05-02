<?php
/**
 * Theme functions
 *
 * Sets up the theme and provides some helper functions.
 *
 * @package themeHandle
 */


/* OEMBED SIZING
 ========================== */
 
if ( ! isset( $content_width ) )
	$content_width = 600;
	
	
/* THEME SETUP
 ========================== */
 
if ( ! function_exists( 'themeFunction_setup' ) ):
function themeFunction_setup() {
	show_admin_bar(false);

	add_filter( 'emoji_svg_url', '__return_false' );
	// REMOVE WP EMOJI
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('wp_print_styles', 'print_emoji_styles');

	// Available for translation
	load_theme_textdomain( 'aeai', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to <head>.
	//add_theme_support( 'automatic-feed-links' );

	// Add custom nav menu support
	register_nav_menu( 'primary', __( 'Primary Menu', 'aeai' ) );
	register_nav_menu( 'secondary', __( 'Secondary Menu', 'aeai' ) );
	register_nav_menu( 'mobile', __( 'mobile Menu', 'aeai' ) );
	
	// Add featured image support
	add_theme_support( 'post-thumbnails' );
	
	// Enable support for HTML5 markup.
	add_theme_support( 'html5', array(
		'comment-list',
		'search-form',
		'comment-form',
		'gallery',
	) );
	
	// Add custom image sizes
	// add_image_size( 'name', 500, 300 );
}
endif;
add_action( 'after_setup_theme', 'themeFunction_setup' );


/* SIDEBARS & WIDGET AREAS
 ========================== */
function themeFunction_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Sidebar', 'aeai' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'themeFunction_widgets_init' );


/* ENQUEUE SCRIPTS & STYLES
 ========================== */
function themeFunction_scripts() {
	$templatedir = get_template_directory_uri();
	// theme style.css file
	//wp_enqueue_style( 'aeai-style', get_stylesheet_uri(), array(), '1.0' );    
	wp_enqueue_style( 'aeai-style', get_stylesheet_uri(), array(), rand() );    

	wp_deregister_script('modernizr');
	//wp_register_script('modernizr', ($templatedir."/dist/js/vendor/modernizr.min.js"), false);
	//wp_enqueue_script('modernizr');

	wp_deregister_script('jquery');
	//wp_register_script('vendor', ($templatedir."/dist/js/vendor.min.js"), false);
	//wp_enqueue_script('vendor');
	wp_enqueue_script(
		'vendor',
		$templatedir . '/assets/js/vendor.min.js',
		array(),
		rand(), 
		true
	);

	// theme scripts
	wp_enqueue_script(
		'app',
		$templatedir . '/assets/js/app.min.js',
		array(),
		rand(), 
		true
	);
}    
add_action('wp_enqueue_scripts', 'themeFunction_scripts');


/* MISC EXTRAS
 ========================== */
 
// Comments & pingbacks display template
include('inc/functions/utils.php');
include('inc/functions/comments.php');
include('inc/functions/data.php');

// Optional Customizations
// Includes: TinyMCE tweaks, admin menu & bar settings, query overrides
//include('inc/functions/customizations.php');


