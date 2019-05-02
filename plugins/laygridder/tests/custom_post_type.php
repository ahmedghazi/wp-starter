<?php
add_action('init', function(){
	register_post_type( 'custom_post_type', array(
		'label' => 'Custom Post Type',
		'public' => true,
		'supports' => array('thumbnail', 'title')
	) );
});