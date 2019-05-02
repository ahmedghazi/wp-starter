<?php

/*

TODO: Remove thumbnail json from carousel if post is trashed

*/

// this class updates post thumbnails and thumbnails inside carousels and stacks when a post is saved

// DIFFERENCES TO LAYTHEME:
/* Has to work with:
  - custom post types
  - with posts that have no featured image
  - for custom taxonomies
  - laygridder has no mouseover image, no video thumbnail, no project description
  - category gridder json is saved as term meta, not as option
  - we need relative paths instead of absolute paths!

// lg post thumbnail element:
/*
    {
      "type": "postThumbnail",
      "cont": "/wp-content/uploads/2018/01/19932159_135755903673551_350670434607300608_n.jpg",
      "align": "top",
      "row": 0,
      "col": 1,
      "colspan": 2,
      "offsetx": 0,
      "offsety": 0,
      "spaceabove": 0,
      "spacebelow": 0,
      "push": 1,
      "relid": 14,
      "title": "Hello world!",
      "attid": "9",
      "postid": 1,
      "ar": 1.25,
      "sizes": {
        "full": {
          "url": "/wp-content/uploads/2018/01/19932159_135755903673551_350670434607300608_n.jpg",
          "width": 640,
          "height": 800,
          "orientation": "portrait"
        },
        "thumbnail": {
          "url": "/wp-content/uploads/2018/01/19932159_135755903673551_350670434607300608_n-150x150.jpg",
          "width": 150,
          "height": 150,
          "orientation": "portrait"
        },
        "medium": {
          "url": "/wp-content/uploads/2018/01/19932159_135755903673551_350670434607300608_n-240x300.jpg",
          "width": 240,
          "height": 300,
          "orientation": "portrait"
        }
      },
      "link": "/2018/01/08/hello-world/"
    },
*/

class LGUpdateThumbnails{

	public static function init(){
		add_filter( 'post_updated', 'LGUpdateThumbnails::update_thumbnail_everywhere', 10, 3 );
		add_filter( 'wp_trash_post', 'LGUpdateThumbnails::remove_thumbnail_from_everywhere', 10, 1 );
	}

	public static function get_gridder_json_values($from_posts = true) {
		global $wpdb;
		$table_name = 'postmeta';
		if (!$from_posts) {
			$table_name = 'termmeta';
		}

		$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}{$table_name} WHERE meta_key LIKE '%_gridder_json'");
		return $results;
	}

	public static function remove_thumbnail_from_everywhere($post_id) {
		$category_gridder_json_values = LGUpdateThumbnails::get_gridder_json_values(false);
		foreach($category_gridder_json_values as $category_gridder_json_value) {
			LGUpdateThumbnails::remove_thumbnail_from_category_json($category_gridder_json_value, $post_id);
		}

		$post_gridder_json_values = LGUpdateThumbnails::get_gridder_json_values();
		foreach($post_gridder_json_values as $post_gridder_json_value) {
			LGUpdateThumbnails::remove_thumbnail_from_post_json($post_gridder_json_value, $post_id);
		}
	}

	private static function remove_thumbnail_from_category_json($category_gridder_json_value, $post_id) {
		if ($category_gridder_json_value->meta_value) {
			try {
				$jsonObject = json_decode($category_gridder_json_value->meta_value, true);
				$needsUpdate = false;

				LGUpdateThumbnails::remove_thumbnail_from_json_recursive($jsonObject['cont'], $needsUpdate, $post_id);

				if ($needsUpdate) {
					LGUpdateThumbnails::update_push($jsonObject);

					$jsonString = json_encode($jsonObject);
					if ($jsonString != false && $jsonString != "") {
						$jsonString = wp_slash( $jsonString );
						update_term_meta( $category_gridder_json_value->term_id, $category_gridder_json_value->meta_key, $jsonString );
					}
				}
			} catch (Exception $e) {
				// Nothing - json is invalid
			}
		}
	}

	private static function remove_thumbnail_from_post_json($post_gridder_json_value, $post_id) {
		if ($post_gridder_json_value->meta_value) {
			try {
				$jsonObject = json_decode($post_gridder_json_value->meta_value, true);
				$needsUpdate = false;
	
				LGUpdateThumbnails::remove_thumbnail_from_json_recursive($jsonObject['cont'], $needsUpdate, $post_id);
	
				if ($needsUpdate) {
					LGUpdateThumbnails::update_push($jsonObject);
					// save json back to db
					// http://stackoverflow.com/a/22208945/3159159
					$jsonString = json_encode($jsonObject);
					if ($jsonString != false && $jsonString != "") {
						$jsonString = wp_slash( $jsonString );
						update_post_meta( $post_gridder_json_value->post_id, $post_gridder_json_value->meta_key, $jsonString );
					}
				}
			} catch (Exception $e) {
				// Nothing - json is invalid
			}
		}
	}

	private static function remove_thumbnail_from_json_recursive(&$array, &$needsUpdate, $post_id) {
		if (is_array($array)) {
			for ($i = 0; $i < count($array); $i++) {
				if (array_key_exists('postid', $array[$i])) {
					if ($array[$i]['postid'] == $post_id) {
						// delete column
						unset($array[$i]);
						// http://stackoverflow.com/questions/369602/delete-an-element-from-an-array/369761#369761
						$array = array_values($array);
						$needsUpdate = true;
					}
				}
				// else if ($array[$i]['type'] == 'carousel') {
				// 	LGUpdateThumbnails::remove_thumbnail_from_json_recursive($array[$i]['carousel'], $needsUpdate, $post_id);
				// }
				else if ($array[$i]['type'] == 'elementgrid' && isset($array[$i]['config'])) {
					LGUpdateThumbnails::remove_thumbnail_from_json_recursive($array[$i]['config']['elements'], $needsUpdate, $post_id);
				}
				else if ($array[$i]['type'] == 'stack') {
					LGUpdateThumbnails::remove_thumbnail_from_json_recursive($array[$i]['cont'], $needsUpdate, $post_id);
				}
			}
		}
	}

	// push needs to be different for texts in 100vh rows cause they are pos absolute
	private static function update_push($rowix, &$jsonObj) {
		$right = 0;

		for ($i = 0, $rowix = 0; $i < count($jsonObj['cont']); $i++) {
			if ($jsonObj['cont'][$i]['row'] != $rowix) {
				$right = 0;
			}

			$is100vh = LGUpdateThumbnails::row_is_100vh($rowix, $jsonObj);
			$type = $jsonObj['cont'][$i]['type'];

			$left = $jsonObj['cont'][$i]['col'];

			if ($is100vh && $type == 'text') {
				$push = $left;
			} else {
				$push = $left - $right;
				$right = $jsonObj['cont'][$i]['col'] + $jsonObj['cont'][$i]['colspan'];
			}

			$jsonObj['cont'][$i]['push'] = $push;
		}
	}

	private static function row_is_100vh($rowix, $jsonObj) {
		if (array_key_exists($rowix, $jsonObj['rowAttrs'])) {
			if (!is_null($jsonObj['rowAttrs'][$rowix])) {
				if (array_key_exists('row100vh', $jsonObj['rowAttrs'][$rowix]) && $jsonObj['rowAttrs'][$rowix]['row100vh'] == true) {
					return true;
				}
			}
		}
		return false;
	}


	// update the post values in all category, page and post jsons
	// http://wordpress.stackexchange.com/questions/134664/what-is-correct-way-to-hook-when-update-post
	public static function update_thumbnail_everywhere($post_id, $post_after, $post_before) {
		$poststatus = get_post_status($post_id);
		if ($poststatus != 'publish') {
			return;
		}

		$all_post_types = LG_Util::get_all_post_types();
		// check for posttype. cause post_updated also gets triggered when nav_menu_item updates / a navigation menu is saved
		if ( in_array(get_post_type($post_id), $all_post_types) ) {

			// attributes to update:
			$sizesArr = null;
			$featuredImgId = -1;
			$featuredImgUrl = '';
			$ar = 1;
			$permalink = LG_Util::getRelativURL(get_permalink($post_id));
			$title = stripslashes($post_after->post_title);

			if (has_post_thumbnail($post_id)) {
				$sizesArr = array();
				$featuredImgId = get_post_thumbnail_id($post_id);
				$featuredImgObj = wp_get_attachment_image_src($featuredImgId, 'full');

				$featuredImgUrl = LG_Util::getRelativURL($featuredImgObj[0]);
				$ar = $featuredImgObj[2]/$featuredImgObj[1];
				$image_sizes = get_intermediate_image_sizes();
				array_push($image_sizes, 'full');

				for ($i=0; $i < count($image_sizes); $i++) {
					$attachment = wp_get_attachment_image_src($featuredImgId, $image_sizes[$i]);

					$array = array();
					$array['url'] = LG_Util::getRelativURL($attachment[0]);
					$array['width'] = $attachment[1];
					$array['height'] = $attachment[2];
					$array['orientation'] = $attachment[1] > $attachment[2] ? 'landscape' : 'portrait';

					$sizesArr[$image_sizes[$i]] = $array;
				}
			}

			$category_gridder_json_values = LGUpdateThumbnails::get_gridder_json_values(false);
			foreach($category_gridder_json_values as $category_gridder_json_value) {
				LGUpdateThumbnails::update_thumbnail_in_category_json($category_gridder_json_value, $post_id, $permalink, $title, $featuredImgId, $featuredImgUrl, $ar, $sizesArr);
			}
	
			$post_gridder_json_values = LGUpdateThumbnails::get_gridder_json_values();
			foreach($post_gridder_json_values as $post_gridder_json_value) {
				LGUpdateThumbnails::update_thumbnail_in_post_json($post_gridder_json_value, $post_id, $permalink, $title, $featuredImgId, $featuredImgUrl, $ar, $sizesArr);
			}
		}
	}

	private static function update_thumbnail_in_post_json($post_gridder_json_value, $post_id, $permalink, $title, $featuredImgId, $featuredImgUrl, $ar, $sizesArr) {

		if ($post_gridder_json_value->meta_value) {
			try {
				$jsonObject = json_decode($post_gridder_json_value->meta_value, true);
				$needsUpdate = false;

				LGUpdateThumbnails::update_thumbnail_in_json_recursive($jsonObject['cont'], $needsUpdate, $post_id, $permalink, $title, $featuredImgId, $featuredImgUrl, $ar, $sizesArr);

				if ($needsUpdate) {
					// save json back to db
					// http://stackoverflow.com/a/22208945/3159159
					$jsonString = json_encode($jsonObject);
					if ($jsonString != false && $jsonString != '') {
						$jsonString = wp_slash( $jsonString );
						update_post_meta( $post_gridder_json_value->post_id, $post_gridder_json_value->meta_key, $jsonString );
					}
				}
			} catch (Exception $e) {
				// Nothing - json is invalid
			}
		}
	}

	private static function update_thumbnail_in_category_json($category_gridder_json_value, $post_id, $permalink, $title, $featuredImgId, $featuredImgUrl, $ar, $sizesArr) {

		if ($category_gridder_json_value->meta_value) {
			try {
				$jsonObject = json_decode($category_gridder_json_value->meta_value, true);
				$needsUpdate = false;

				LGUpdateThumbnails::update_thumbnail_in_json_recursive($jsonObject['cont'], $needsUpdate, $post_id, $permalink, $title, $featuredImgId, $featuredImgUrl, $ar, $sizesArr);

				if ($needsUpdate) {
						// save json back to db
					$jsonString = json_encode($jsonObject);
					if ($jsonString != false && $jsonString != "") {
						$jsonString = wp_slash( $jsonString );
						update_term_meta( $category_gridder_json_value->term_id, $category_gridder_json_value->meta_key, $jsonString );
					}
				}
			} catch (Exception $e) {
				// Nothing - json is invalid
			}
		}
	}

	private static function update_thumbnail_in_json_recursive(&$array, &$needsUpdate, $post_id, $permalink, $title, $featuredImgId, $featuredImgUrl, $ar, $sizesArr) {
		if (is_array($array)) {
			for ($i = 0; $i < count($array); $i++) {
				// element with type of thumbnail has 'postid' attribute .. but it's actual element type could be still 'img' instead of 'postThumbnail'
				if (array_key_exists( 'postid', $array[$i] )) {
					// does element have postid of current post being saved?
					if ($array[$i]['postid'] == $post_id) {
						$needsUpdate = true;
						$array[$i]['link'] = $permalink;
						$array[$i]['title'] = $title;
						$array[$i]['cont'] = $featuredImgUrl;
						$array[$i]['ar'] = $ar;
						$array[$i]['sizes'] = $sizesArr;
						$array[$i]['attid'] = $featuredImgId;
					}
				}
				// update project thumbnails in carousels
				// else if ($array[$i]['type'] == 'carousel') {
				// 	LGUpdateThumbnails::update_thumbnail_in_json_recursive($array[$i]['carousel'], $needsUpdate, $post_id, $permalink, $title, $featuredImgId, $featuredImgUrl, $ar, $sizesArr,
				// 		$projectDescr, $mouseOverThumbSizesArr, $mouseOverThumbWidth, $mouseOverThumbHeight, $video_url, $video_meta);
				// }
				// update project thumbnails in elementgrid
				else if ($array[$i]['type'] == 'elementgrid' && isset($array[$i]['config'])) {
					LGUpdateThumbnails::update_thumbnail_in_json_recursive($array[$i]['config']['elements'], $needsUpdate, $post_id, $permalink, $title, $featuredImgId, $featuredImgId, $featuredImgUrl, $ar, $sizesArr);
				}
				// update project thumbnails in type stack
				else if ($array[$i]['type'] == 'stack' ) {
					LGUpdateThumbnails::update_thumbnail_in_json_recursive($array[$i]['cont'], $needsUpdate, $post_id, $permalink, $title, $featuredImgId, $featuredImgId, $featuredImgUrl, $ar, $sizesArr);
				}
			}
		}
	}
}

LGUpdateThumbnails::init();