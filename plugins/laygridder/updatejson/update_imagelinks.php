<?php
/* 
updates imagelinks whenever a post, page or term is updated

differences to laytheme:

- term meta, instead of option
- needs to work for custom post type posts
- and custom taxonomy terms
*/

class LGUpdateImageLinks{

	public function __construct(){
		// update post and page imagelinks
		add_filter( 'post_updated', array($this, 'update_post_imagelinks_everywhere'), 9, 3 );
		// update category imagelinks
		add_action( 'edited_terms', array($this, 'update_category_imagelinks_everywhere'), 10, 2 );
	}

	public static function update_imagelink_in_post_json($metaname, $linktype_to_update, $post_id, $permalink, $title, $id){
		$jsonString = get_post_meta( $id, $metaname, true );
		if($jsonString != ""){
			$needsUpdate = false;
			$jsonObject = json_decode($jsonString, true);

			for($i=0; $i<count($jsonObject['cont']); $i++){
				LGUpdateImageLinks::update_obj($jsonObject['cont'][$i], $needsUpdate, $linktype_to_update, $post_id, $permalink, $title);
				// update linked images in stack element
				if($jsonObject['cont'][$i]['type'] == 'stack'){
					$stack_cont = &$jsonObject['cont'][$i]['cont'];
					for($ix_s=0; $ix_s<count($stack_cont); $ix_s++){
						LGUpdateImageLinks::update_obj($stack_cont[$ix_s], $needsUpdate, $linktype_to_update, $post_id, $permalink, $title);
					}
				}
			}

			if($needsUpdate){
					// save json back to db
					// http://stackoverflow.com/a/22208945/3159159
					$jsonString = json_encode($jsonObject);
					if($jsonString != false && $jsonString != ""){
							$jsonString = wp_slash( $jsonString );
							update_post_meta( $id, $metaname, $jsonString );
					}
			}
		}
	}

	public static function update_imagelink_in_category_json($term_id, $metafield_name, $linktype_to_update, $post_id, $permalink, $title){
		$jsonString = get_term_meta( $term_id, $metafield_name, true );

		if($jsonString){
				$jsonObject = json_decode($jsonString, true);
				
		    $needsUpdate = false;
		    for($i=0; $i<count($jsonObject['cont']); $i++){
		      LGUpdateImageLinks::update_obj($jsonObject['cont'][$i], $needsUpdate, $linktype_to_update, $post_id, $permalink, $title);
					// update linked images in stack element
					if($jsonObject['cont'][$i]['type'] == 'stack'){
						$stack_cont = &$jsonObject['cont'][$i]['cont'];
						for($ix_s=0; $ix_s<count($stack_cont); $ix_s++){
							LGUpdateImageLinks::update_obj($stack_cont[$ix_s], $needsUpdate, $linktype_to_update, $post_id, $permalink, $title);
						}
					}
				}

		    if($needsUpdate){
		        $jsonString = json_encode($jsonObject);
		        if($jsonString != false && $jsonString != ""){
							$jsonString = wp_slash( $jsonString );
							update_term_meta( $term_id, $metafield_name, $jsonString );
		        }
		    }
		}		
	}

	private static function update_obj(&$obj, &$needsUpdate, $linktype_to_update, $post_id, $permalink, $title){
		// element with type of thumbnail has 'postid' attribute .. but it's actual element type is still 'img'
		if( !array_key_exists( 'postid', $obj ) ){
			// make sure this is an element with type img and make sure it has imagelink attr
			if( $obj['type'] == 'img' && array_key_exists( 'imagelink', $obj ) ){
				// make sure imagelink is an array, 
				if( is_array($obj['imagelink']) ){
					// make sure the imagelink is of type $linktype_to_update
					if( in_array($obj['imagelink']['type'], $linktype_to_update) ){
						// make sure that the post id linked to is id of current post being saved
						if( $obj['imagelink']['id'] == $post_id ){
							$needsUpdate = true;
							$obj['imagelink']['url'] = $permalink;
							$obj['imagelink']['title'] = $title;

							// if image links to a project, update the catid too
							if($obj['imagelink']['type'] == "project"){
								$cat = get_the_category($post_id);
								$cat_id = $cat[0]->term_id;
								$obj['imagelink']['catid'] = $cat_id;
							}
						}
					}
				}
			}
		}
	}

	public static function update_post_imagelinks_everywhere($post_id, $post_after, $post_before){
		// 'post' and 'page' is just the ['imagelink']['type'] and is not reflective of a custom post type for example
		$linktype_to_update = array("page", "post");

		$poststatus = get_post_status($post_id);
		if( $poststatus == 'publish' ){
		    // check for posttype. cause post_updated also gets triggered when nav_menu_item updates / a navigation menu is saved
		    $all_post_types = LG_Util::get_all_post_types();
		    if( in_array(get_post_type($post_id), $all_post_types) ){

		    	// attributes to update:
		    	$permalink = LG_Util::getRelativURL(get_permalink($post_id));
		    	$title = get_the_title($post_id);

		    	// update category json

		    	$allCategoryIds = get_terms(array('hide_empty' => false, 'fields' => 'ids'));

		    	foreach($allCategoryIds as $categoryId){
		    	    LGUpdateImageLinks::update_imagelink_in_category_json( $categoryId, "_gridder_json", $linktype_to_update, $post_id, $permalink, $title  );
		    	    LGUpdateImageLinks::update_imagelink_in_category_json( $categoryId, "_phone_gridder_json", $linktype_to_update, $post_id, $permalink, $title  );
		    	}

		    	// update post's and page's json
		    	$query = new WP_Query(
						array(
							'post_type' => $all_post_types,
							'post_status' => 'publish',
							'posts_per_page' => -1,
							'fields' => 'ids',
						)
		    	);
		    	
		    	if ( $query->have_posts() ) {
						foreach ($query->posts as $key => $id){
								LGUpdateImageLinks::update_imagelink_in_post_json( '_gridder_json', $linktype_to_update, $post_id, $permalink, $title, $id );
								LGUpdateImageLinks::update_imagelink_in_post_json( '_phone_gridder_json', $linktype_to_update, $post_id, $permalink, $title, $id );
						}
		    	}

		    }
		}
	}

	public static function update_category_imagelinks_everywhere($term_id, $taxonomy_slug){
		$linktype_to_update = array('category');

		$term = get_term($term_id, $taxonomy_slug);
		// attributes to update:
		$permalink = LG_Util::getRelativURL(get_term_link($term_id, $taxonomy_slug));
		$title = $term->name;

		$allCategoryIds = get_terms(array('hide_empty' => false, 'fields' => 'ids'));

		foreach($allCategoryIds as $categoryId){
		    LGUpdateImageLinks::update_imagelink_in_category_json( $categoryId, "_gridder_json", $linktype_to_update, $term_id, $permalink, $title  );
		    LGUpdateImageLinks::update_imagelink_in_category_json( $categoryId, "_phone_gridder_json", $linktype_to_update, $term_id, $permalink, $title  );
		}

		// update post's and page's json
		$query = new WP_Query( 
		    array(
		        'post_type' => array('post', 'page'),
		        'post_status' => 'publish',
		        'posts_per_page' => -1,
		        'fields' => 'ids',
		    ) 
		);
		
		if ( $query->have_posts() ) {
		    foreach ($query->posts as $key => $id){
		        LGUpdateImageLinks::update_imagelink_in_post_json( '_gridder_json', $linktype_to_update, $term_id, $permalink, $title, $id );
		        LGUpdateImageLinks::update_imagelink_in_post_json( '_phone_gridder_json', $linktype_to_update, $term_id, $permalink, $title, $id );
		    }
		}
	}

}

new LGUpdateImageLinks();