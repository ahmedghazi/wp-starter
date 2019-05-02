<?php 
add_action( 'init', 'register_cpt_projets' );

function register_cpt_projets() {

    $labels = array( 
        'name' => _x( 'Projets', 'Projet' ),
        'singular_name' => _x( 'Projet', 'projet' ),
        'add_new' => _x( 'Ajouter', 'projet' ),
        'add_new_item' => _x( 'Ajouter un projet', 'projet' ),
        'edit_item' => _x( 'Editer un projet', 'projet' ),
        'new_item' => _x( 'Nouveau projet', 'projet' ),
        'view_item' => _x( 'Voir le projet', 'projet' ),
        'search_items' => _x( 'Rechercher un projet', 'projet' ),
        'not_found' => _x( 'Aucune projet trouvé', 'projet' ),
        'not_found_in_trash' => _x( 'Aucun projet dans la corbeille', 'projet' ),
        'parent_item_colon' => _x( 'projet parent :', 'projet' ),
        'menu_name' => _x( 'Projets', 'projet' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        'description' => 'Les Projets.',
        'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions' ),
        'taxonomies' => array( 'category', 'post_tag' ),
        'public' => true,
        //'show_ui' => true,
        'show_in_menu' => true,
        //'menu_position' => 5,

        //'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => "projets-tous",
        'query_var' => true,
        //'can_export' => true,
        'rewrite' => true,
        //'capability_type' => 'post'
    );

    register_post_type( 'projets', $args );

    $labels = array( 
        'name' => _x( 'Publications', 'Publication' ),
        'singular_name' => _x( 'Publication', 'Publication' ),
        'add_new' => _x( 'Ajouter', 'Publication' ),
        'add_new_item' => _x( 'Ajouter un Publication', 'Publication' ),
        'edit_item' => _x( 'Editer un Publication', 'Publication' ),
        'new_item' => _x( 'Nouveau Publication', 'Publication' ),
        'view_item' => _x( 'Voir le Publication', 'Publication' ),
        'search_items' => _x( 'Rechercher un Publication', 'Publication' ),
        'not_found' => _x( 'Aucune Publication trouvé', 'Publication' ),
        'not_found_in_trash' => _x( 'Aucun Publication dans la corbeille', 'Publication' ),
        'parent_item_colon' => _x( 'Publication parent :', 'Publication' ),
        'menu_name' => _x( 'Publications', 'Publication' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        'description' => 'Les Publications.',
        'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions' ),
        //'taxonomies' => array( 'category', 'post_tag' ),
        'public' => true,
        //'show_ui' => true,
        'show_in_menu' => true,
        //'menu_position' => 5,

        //'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => "Publications-tous",
        'query_var' => true,
        //'can_export' => true,
        'rewrite' => true,
        //'capability_type' => 'post'
    );

    register_post_type( 'publications', $args );
}

/* FIN CUSTOM POST TYPE PROJETS */

function themeprefix_show_cpt_archives( $query ) {
    if( is_category() || is_tag() && empty( $query->query_vars['suppress_filters'] ) ) {
        $query->set( 'post_type', array(
            'post', 'nav_menu_item', 'projets'
        ));
        return $query;
    }
}
add_filter( 'pre_get_posts', 'themeprefix_show_cpt_archives' );

/*
register_taxonomy( 
    'vendor', 
    'produits', 
    array( 
        'hierarchical' => false, 
        'label' => 'Vendor', 
        'query_var' => true 
        ) 
    );
*/

function add_parent_url_menu_class( $classes = array(), $item = false ) {
  // Get current URL
  $current_url = current_url();

  // Get homepage URL
  $homepage_url = trailingslashit( get_bloginfo( 'url' ) );

  // Exclude 404 and homepage
  if( is_404() or $item->url == $homepage_url )
    return $classes;

  if ( get_post_type() == "projets" )
  {
    unset($classes[array_search('current_page_parent',$classes)]);
    if ( isset($item->url) )
      if ( strstr( $current_url, $item->url) )
        $classes[] = 'current-menu-item';
  }

  return $classes;
}

function current_url() {
  // Protocol
  $url = ( 'on' == $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
  $url .= $_SERVER['SERVER_NAME'];

  // Port
  $url .= ( '80' == $_SERVER['SERVER_PORT'] ) ? '' : ':' . $_SERVER['SERVER_PORT'];
  $url .= $_SERVER['REQUEST_URI'];

  return trailingslashit( $url );
}
//add_filter( 'nav_menu_css_class', 'add_parent_url_menu_class', 10, 3 );

/****************************************************************
* Templates
****************************************************************/

add_filter( 'template_include', 'include_tpl_function', 1 );
function include_tpl_function( $template_path )
{
  if ( get_post_type() == 'projets' )
  {
    if ( is_single() )
    {
      // checks if the file exists in the theme first,
      // otherwise serve the file from the plugin
      if ( $theme_file = locate_template( array('single-projet.php') ) )
      {
        $template_path = $theme_file;
      }
      else
      {
        //$template_path = ARTIST_PATH . 'templates/single-artist.php';
      }
    } 
    else if( is_archive() )
    {
      if ( $theme_file = locate_template( array('archive-projets.php') ) )
      {
        $template_path = $theme_file;
      }
      else
      {
        //$template_path = ARTIST_PATH . 'templates/archive-artist.php';
      }
    }
  }

  return $template_path;
}


add_filter('manage_posts_columns', 'ST4_columns_head');
// ADD NEW COLUMN
function ST4_columns_head($defaults) {
    $defaults['featured_image'] = 'Featured Image';
    return $defaults;
}
 
add_action('manage_posts_custom_column', 'ST4_columns_content', 10, 2);
// SHOW THE FEATURED IMAGE
function ST4_columns_content($column_name, $post_ID) {
    if ($column_name == 'featured_image') {
        $post_featured_image = ST4_get_featured_image($post_ID);
        if ($post_featured_image) {
            echo '<img src="' . $post_featured_image . '" />';
        }
    }
}

// GET FEATURED IMAGE
function ST4_get_featured_image($post_ID) {
    $post_thumbnail_id = get_post_thumbnail_id($post_ID);
    if ($post_thumbnail_id) {
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, array(100,100));
        return $post_thumbnail_img[0];
    }
}