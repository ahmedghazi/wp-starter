<?php
class PostThumbnail{
	public static $sizes = array('thumbnail', 'medium', 'large');
	public static $smallest_available_size = false;

	public static function init() {
		add_action('wp_ajax_get_post_thumbnail_json', 'PostThumbnail::get_post_thumbnail_json');
		PostThumbnail::$smallest_available_size = PostThumbnail::get_smallest_available_size();
	}

	// returns smallest size name
	private static function get_smallest_available_size(){
		$sizes = PostThumbnail::get_image_sizes();
		$smallest = false;
		$temp = 9999999999999;

		foreach($sizes as $key => $size){
			if($size['width'] != 0 && $size['width'] < $temp){
				$smallest = $key;
				$temp = $size['width'];
			}

		}

		return $smallest;
	}

	// https://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
	/**
	 * Get size information for all currently-registered image sizes.
	 *
	 * @global $_wp_additional_image_sizes
	 * @uses   get_intermediate_image_sizes()
	 * @return array $sizes Data for all currently-registered image sizes.
	 */
	private static function get_image_sizes() {
		global $_wp_additional_image_sizes;
	
		$sizes = array();
	
		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
				$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
				$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
				$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array(
					'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
				);
			}
		}
	
		return $sizes;
	}

	public static function get_post_thumbnail_json() {
		$posts = array();

		// custom post types
		$post_types = LG_Util::get_all_post_types();

		$queryParam = array( 'posts_per_page' => '-1', 'post_type' => $post_types );
		$currentScreenId = $_GET['screenId'];
		if ($currentScreenId != null && $currentScreenId != 'edit-category') {
			$queryParam['post__not_in'] = array( $_GET['id'] );
		}
		$query = new WP_Query( $queryParam );

		if ( $query->have_posts() ) {
			foreach ($query->posts as $post){
				if ($post->post_status == "publish") {
					$title = get_the_title( $post->ID );
					$link = get_permalink( $post->ID );
					$cats = get_the_category($post->ID);

					$sizes = array();
					$attid = get_post_thumbnail_id( $post->ID );
					if ($attid == '') {
						continue;
					}
					$full_attachment = wp_get_attachment_image_src( $attid, 'full' );
					$sizes['full'] = array(
						'url' => $full_attachment[0],
						'width' => $full_attachment[1],
						'height' => $full_attachment[2],
						'orientation' => $full_attachment[1] > $full_attachment[2] ? 'landscape' : 'portrait'
					);
					
					foreach ( PostThumbnail::$sizes as $size ) {
						$attachment = wp_get_attachment_image_src( $attid, $size );
						/*
						Problem:
						The width of the thumbnail attachment is smaller than full attachment width but it's url is the full URL.
						I do not want to use the full URL as the thumbnail.
						This happens, when ppl set thumbnail image width to 0 in Admin panel -> Settings -> Media.
						In this case, use another smallest size
						thumbnail size is used in laygridder for post thumbnails modal
						*/
						if($size == 'thumbnail' && $attachment[0] === $full_attachment[0]){
							if(PostThumbnail::$smallest_available_size != false){
								$attachment = wp_get_attachment_image_src( $attid, PostThumbnail::$smallest_available_size );
							}else{
								$attachment = $full_attachment;
							}
						}

						$sizes[$size] = array(
							'url' => $attachment[0],
							'width' => $attachment[1],
							'height' => $attachment[2],
							'orientation' => $attachment[1] > $attachment[2] ? 'landscape' : 'portrait'
						);
					}
					$ar = $sizes['full']['height'] / $sizes['full']['width'];

					$posts[] = array(
						'postid' => $post->ID,
						'title' => $title,
						'link' => $link,
						'cats' => $cats,
						'attid' => $attid,
						'sizes' => $sizes,
						'ar' => $ar,
					);
				}
			}
		}

		wp_send_json($posts);
		die();
	}
}

PostThumbnail::init();