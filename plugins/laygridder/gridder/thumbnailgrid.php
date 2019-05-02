<?php
// todo: make project prevnext work with this project order

class Thumbnailgrid{

	public static function init(){
		if ( is_admin() ) {
			add_action('wp_ajax_get_thumbnails_for_thumbnailgrid', array($this, 'get_thumbnails_for_thumbnailgrid'));
		}

		add_action('wp_ajax_get_thumbnails_for_thumbnailgrid_frontend', array($this, 'get_thumbnails_for_thumbnailgrid_frontend'));
		add_action('wp_ajax_nopriv_get_thumbnails_for_thumbnailgrid_frontend', array($this, 'get_thumbnails_for_thumbnailgrid_frontend'));

		add_action('wp_ajax_get_order_list_for_thumbnailgrid', array($this, 'get_order_list_for_thumbnailgrid'));
		add_action('wp_ajax_thumbgrid_save_project_order', array($this, 'thumbgrid_save_project_order'));

		add_action( 'wp_trash_post', array($this, 'remove_post_from_project_order'), 10, 1 );

	}

	public static function remove_post_from_project_order($postid){
		$cat = get_the_category($postid);
		$cat = $cat[0];
		$cat_id = $cat->term_id;
		$thumb_order_ids = get_term_meta($cat_id, 'project_order', true);

		if( is_array($thumb_order_ids) ){
			$offset = array_search($postid, $thumb_order_ids);
			if($offset != false){
				array_splice($thumb_order_ids, $offset, 1);
				$ids_str = json_encode($thumb_order_ids);
				update_term_meta($cat_id, 'project_order', $ids_str);
			}
		}
	}

	public static function thumbgrid_save_project_order(){
		$obj = $_POST['project_order'];
		update_term_meta($obj["termid"], 'project_order', $obj["ids"]);
	}

	public static function get_project_ids_possibly_with_order($cat_id){
		// post ids of projects of category with cat_id
		$query_ids = array();

		$args = array(
			'posts_per_page' => -1,
			'orderby' => 'post_date',
			'order' => 'ASC',
			'post_type' => 'post',
			'cat' => $cat_id,
			'fields' => 'ids'
		);

		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			foreach ($query->posts as $post_id){
				$query_ids []= $post_id;
			}
		}

		// if project order was saved before as meta (it is an array of project ids), see if the query above has new projects. if so, prepend them and return resulting array
		$thumb_order_ids = get_term_meta($cat_id, 'project_order', true);

		if( is_array($thumb_order_ids) ){
			$diff = array_diff($query_ids, $thumb_order_ids);
			if(count($diff) > 0){
				return array_merge($diff, $thumb_order_ids);
			}
			return $thumb_order_ids;
		}

		// if theres no project order term meta, just return $query_ids
		return $query_ids;
	}

	public static function get_order_list_for_thumbnailgrid(){
		$cat_id = $_POST['lay_catid'];
		$ids = Thumbnailgrid::get_project_ids_possibly_with_order($cat_id);

		$array = array();

		foreach ($ids as $post_id){
			$attid = get_post_thumbnail_id($post_id);
			$arr = wp_get_attachment_image_src($attid, '_265');
			$array []= array("post_id" => $post_id, "src" => $arr[0], "title" => get_the_title($post_id));
		}

		wp_send_json($array);
		die();
	}

	public static function get_thumbnails_for_thumbnailgrid(){
		$cat_id = $_POST['lay_catid'];
		$ids = Thumbnailgrid::get_project_ids_possibly_with_order($cat_id);

		$array = array();

		// generating an array of objects that contain all the info needed to view a Marionette thumbnail_view
		// ar, cont, sizes, sizes._1024, title, descr,
		foreach ($ids as $post_id){
			$attid = get_post_thumbnail_id($post_id);
			$_512 = wp_get_attachment_image_src($attid, '_512');
			$full = wp_get_attachment_image_src($attid, 'full');

			$array []= array(
				"ar" => $full[2]/$full[1],
				"cont" => $full[0],
				"sizes" => array("_512" => $_512[0]),
				"title" => get_the_title($post_id),
				"descr" => get_post_meta($post_id, 'lay_project_description', true)
			);
		}

		wp_send_json($array);
		die();
	}

	public static function get_thumbnails_for_thumbnailgrid_frontend(){
		$cat_id = $_POST['lay_catid'];
		$ids = Thumbnailgrid::get_project_ids_possibly_with_order($cat_id);

		$array = array();

		// generating an array of objects that contain all the info needed to view a Marionette thumbnail_view for frontend
		foreach ($ids as $post_id){

			$attid = get_post_thumbnail_id($post_id);

			$sizes = array();
			for ($i=0; $i < count(Setup::$sizes); $i++) {
				$attachment = wp_get_attachment_image_src($attid, Setup::$sizes[$i]);
				$sizes[Setup::$sizes[$i]] = $attachment[0];
			}
			$full = wp_get_attachment_image_src($attid, 'full');
			$sizes['full'] = $full[0];

			$ar = 0;
			if($full){
				if($full[1] != 0){
					$ar = $full[2] / $full[1];
				}
			}


			// mouseover thumbnail image
			$mouseOverThumbSizesArr = "";
			$mouseOverThumbWidth = "";
			$mouseOverThumbHeight = "";
			if(ImageMouseoverThumbnails::$active == true){
				$mouseover_thumbnail_id = get_post_meta( $post_id, '_lay_thumbnail_mouseover_image', true );

				if ( $mouseover_thumbnail_id && get_post( $mouseover_thumbnail_id ) ) {
					$mouseOverThumbSizesArr = array();
					for ($i=0; $i < count(Setup::$sizes); $i++) {
						$attachment = wp_get_attachment_image_src($mouseover_thumbnail_id, Setup::$sizes[$i]);
						$mouseOverThumbSizesArr[Setup::$sizes[$i]] = $attachment[0];
					}
					$full = wp_get_attachment_image_src($mouseover_thumbnail_id, 'full');
					$mouseOverThumbSizesArr['full'] = $full[0];
					$mouseOverThumbWidth = $full[1];
					$mouseOverThumbHeight = $full[2];
				}
			}
			// video thumbnail
			$video_url = "";
			$video_meta = array('width'=>'', 'height'=>'');
			if(VideoThumbnails::$active == true){
				$video_id = get_post_meta( $post_id, '_lay_thumbnail_video', true );
				if($video_id != ""){
					$video_url = wp_get_attachment_url($video_id);
					$video_meta = wp_get_attachment_metadata($video_id);
				}
			}


			$array []= array(
				"type" => "img",
				"title" => get_the_title($post_id),
				"attid" => $attid,
				"postid" => $post_id,
				"ar" => $ar,
				'sizes' => $sizes,
				"link" => get_permalink($post_id),
				"descr" => get_post_meta( $post_id, 'lay_project_description', true ),
				"mo_sizes" => $mouseOverThumbSizesArr,
				"mo_w" => $mouseOverThumbWidth,
				"mo_h" => $mouseOverThumbHeight,
				"video_url" => $video_url,
				"video_w" => $video_meta["width"],
				"video_h" => $video_meta["height"],
			);
		}

		wp_send_json($array);
		die();

	}

}
Thumbnailgrid::init();
