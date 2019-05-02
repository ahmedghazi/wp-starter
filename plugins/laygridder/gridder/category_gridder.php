<?php
// scripts and styles are enqueued in gridder.php
// https://www.dougv.com/2014/06/25/hooking-wordpress-taxonomy-changes-with-the-plugins-api/

class LayCategoryGridder {

	public static function init() {
		// calling this on admin_init in order to make "get_taxonomies" work
		add_action( 'admin_init', 'LayCategoryGridder::init_category_gridder' );
	}

	public static function init_category_gridder() {
		// add gridder to terms based on location settings
		$location_json = get_option( 'lg_locations_json', LayGridderLocation::$default );
		$obj = json_decode($location_json);
		foreach ($obj as $location) {
			if($location->type == "tax_term"){
				$taxname = $location->val;
				if($taxname == "all"){
					// register hooks for all custom taxonomies and for "category" taxonomy
					$custom_taxonomies = get_taxonomies( array('public' => true, '_builtin' => false) );
					foreach ($custom_taxonomies as $custom_tax_name) {
						add_action( $custom_tax_name.'_edit_form', 'LayCategoryGridder::add_category_gridder', 10, 2 );
						add_action( 'edited_'.$custom_tax_name, 'LayCategoryGridder::save_category_gridder_json', 10, 2 );
					}
					add_action( 'category_edit_form', 'LayCategoryGridder::add_category_gridder', 10, 2 );
					add_action( 'edited_category', 'LayCategoryGridder::save_category_gridder_json', 10, 2 );

				}else{
					add_action( $taxname.'_edit_form', 'LayCategoryGridder::add_category_gridder', 10, 2 );
					add_action( 'edited_'.$taxname, 'LayCategoryGridder::save_category_gridder_json', 10, 2 );
				}
			}
		}
	}

	public static function save_category_gridder_json($term_id, $tt_id) {
		// Check if our nonce is set.
		if ( ! isset( $_POST['category_gridder_json_field_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['category_gridder_json_field_nonce'], 'category_gridder_json_field' ) ) {
			return;
		}

		// save last data
		$gridderval = get_term_meta( $term_id, "_gridder_json", true );
		$gridderval = wp_slash($gridderval);
		update_term_meta( $term_id, '_gridder_json_last', $gridderval );

		$phonegridderval = get_term_meta( $term_id, "_phone_gridder_json", true );
		$phonegridderval = wp_slash($phonegridderval);
		update_term_meta( $term_id, "_phone_gridder_json_last", $phonegridderval );
		
		// $term_id is id of "instance" of taxonomy
		// $tt_id is id of taxonomy, in this case the taxonomy is category
		if ( isset($_POST['category_gridder_json']) ){
			$json = $_POST['category_gridder_json'];
			update_term_meta( $term_id, '_gridder_json', $json );
		}

		if ( isset($_POST['phone_category_gridder_json']) ){
			$json = $_POST['phone_category_gridder_json'];
			update_term_meta( $term_id, '_phone_gridder_json', $json );
		}
	}

	public static function add_category_gridder($tag, $taxonomy) {
		// $tag Current taxonomy term object.
		// $taxonomy Current taxonomy slug.
		
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'category_gridder_json_field', 'category_gridder_json_field_nonce' );

		$term_id = $tag->term_id;
		$json = get_term_meta($term_id, '_gridder_json', true );
		echo '<textarea name="category_gridder_json" id="gridder_json_field">';
		echo htmlspecialchars($json);
		echo '</textarea>';

		$json = get_term_meta($term_id, '_phone_gridder_json', true );
		echo '<textarea name="phone_category_gridder_json" id="phone_gridder_json_field">';
		echo htmlspecialchars($json);
		echo '</textarea>';

		LayGridder::gridder_metabox_callback();
	}
}

LayCategoryGridder::init();