<?php

add_filter('lg_frontend_img_opening_anchortag', function($opening_anchor_tag, $args){
	return '<a href="'.$args->url.'" data-test="1" '.$args->target.'>';
}, 10, 2);

add_filter('lg_frontend_postthumbnail_opening_anchortag', function($openingAnchorTag, $args){
	return '<a href="'.$args->url.'" data-test="2">';
}, 10, 2);

add_filter('lg_frontend_img_caption_opening_anchortag', function($openingAnchorTag, $args){
	return '<a href="'.$args->url.'" data-test="3" '.$args->target.'>';
}, 10, 2);