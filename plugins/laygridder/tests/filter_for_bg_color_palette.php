<?php

function custom_bg_colors($array){
	return array('#ffffff', '#000000' ,'#e9e8e8', '#d4cfcc', '#988e86');
}
add_filter( 'lg_bg_color_palette', 'custom_bg_colors', 10, 1 );