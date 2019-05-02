<?php
add_action( 'wp_ajax_get_posts_by_paged', 'get_posts_by_paged' );
add_action( 'wp_ajax_nopriv_get_posts_by_paged', 'get_posts_by_paged'); 
function get_posts_by_paged() {
    $html = '';
	$paged = $_REQUEST["paged"];
	//qtranxf_setLanguage($_REQUEST["lang"]);
	$GLOBALS['q_config']['language'] = $_REQUEST["lang"];
	//global $q_config;
	//$q_config['lang'] = $_REQUEST["lang"];

    $args_ = array(
        "post_type"         => "projets",
        "post_status"       => "publish",
        'posts_per_page' 	=> get_option( 'posts_per_page' ),
		'paged'          	=> $paged,
		"orderby"           => "menu_order", 
        "order"             => "DESC",
        //'s' 				=> '[:'.$_REQUEST["lang"].']',
    );
    //trace($args_);
    //return;
    $q_ = new WP_Query( $args_ );
    while ( $q_->have_posts() ) : $q_->the_post();
        ob_start();
        //include(TEMPLATEPATH.'/_/inc/template-typologie.php');
        include(locate_template('inc/card-projets.php'));
        $html .= ob_get_clean();
    endwhile;
    wp_reset_query();

    echo json_encode(array(
        "html" => $html,
    ));

    exit();
}

add_action( 'wp_ajax_oembed_get', 'handle_oembed_get' );
add_action( 'wp_ajax_nopriv_oembed_get', 'handle_oembed_get'); 

function handle_oembed_get() {
    $url = $_REQUEST["video"];
    $embed_code = wp_oembed_get( $url );
    //echo $embed_code;
    //exit();
    echo json_encode(array(
        "html" => $embed_code,
    ));
    exit();
}