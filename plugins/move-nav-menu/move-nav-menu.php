<?php
/*
Plugin Name: Move Nav-Menu
Description: Moves the "Menus"-tab (nav menus) to the main menu (out of Appearance)
Plugin URI: http://tormorten.no
Author: Tor Morten Jensen
Author URI: http://tormorten.no
Version: 1.0.2
License: GPL2
Text Domain: move-menu
Domain Path: lang
*/

/*

    Copyright (C) 2015  Tor Morten Jensen  tormorten@tormorten.no

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Removes nav-menu and adds a new menu page
 * @return void
 */
function mm_move_menu() {

    global $submenu, $menu;
    
    if(isset($submenu['themes.php'])) {

        foreach($submenu['themes.php'] as $key => $item) {
            if($item[2] === 'nav-menus.php') {
                unset($submenu['themes.php'][$key]);
            }
        }
    }

    $user = wp_get_current_user();

    if(in_array('editor', $user->roles)) {
        unset($menu[60]);
    }
    
    
    add_menu_page( __('Menus'), __('Menus'), 'delete_others_pages', 'nav-menus.php', '', 'dashicons-list-view', 61 );

}

add_action('admin_menu', 'mm_move_menu');

/**
 * Changes the parent page for nav-menus
 * @param  string $parent_file The current parent file
 * @return string              The new current parent file
 */
function mm_menu_parent_file($parent_file){
    global $current_screen;

    // Set correct active/current menu and submenu in the WordPress Admin menu for the "example_cpt" Add-New/Edit/List
    if($current_screen->base == 'nav-menus') {
        $parent_file = 'nav-menus.php';
    }

    return $parent_file;
}

add_filter('parent_file', 'mm_menu_parent_file');

/**
 * Fixes the problem where the capability is wrong
 * @return void
 **/
function mm_has_cap($caps, $cap, $args, $user) {
    $url = $_SERVER['REQUEST_URI'];

    if(strpos($url, 'nav-menus.php') !== false && in_array('edit_theme_options', $cap) && in_array('editor', $user->roles)) {
        $caps['edit_theme_options'] = true;
    }
    return $caps;
}

add_filter('user_has_cap', 'mm_has_cap', 20, 4);