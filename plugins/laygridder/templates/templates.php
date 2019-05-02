<?php
class LayGridderTemplates{

	public static $isActive;

	public function __construct(){
		$val = get_option( 'gridder_options_templates', "" );
		if($val == "on"){
			LayGridderTemplates::$isActive = true;
		}else{
			LayGridderTemplates::$isActive = false;
		}

		if(LayGridderTemplates::$isActive){
			add_action('init', array($this, 'register_template_posttype'));
		}

		add_action('wp_ajax_get_lg_templates', array($this, 'get_lg_templates'));
		add_action('wp_ajax_get_lg_templates_json', array($this, 'get_lg_templates_json'));
		add_action('wp_ajax_get_lg_template_json_by_id', array($this, 'get_lg_template_json_by_id'));
		add_action('wp_ajax_get_lg_cpl_template_json_by_id', array($this, 'get_lg_cpl_template_json_by_id'));
	}

	public static function get_lg_cpl_template_json_by_id(){
		$id = intval( $_POST['lg_id'] );
		$json = get_post_meta( $id, '_phone_gridder_json', true );
		
		echo $json;
		die();
	}

	public static function get_lg_template_json_by_id(){
		$id = intval( $_POST['lg_id'] );
		$json = get_post_meta( $id, '_gridder_json', true );
		
		echo $json;
		die();
	}

	public static function get_lg_templates_json(){
		$args = array(
			'posts_per_page' => -1,
			'orderby' => 'title',
			'post_type' => 'laygridder_template'
		);

		$ix = 0;
		$query = new WP_Query( $args );
		$json = array();

		if ( $query->have_posts() ) {
			foreach ($query->posts as $post){
				$id = $post->ID;
				$title = $post->post_title;
				$permalink = get_permalink($post);

				$template = (object)array();
				$template->id = $id;
				$template->title = $title;
				$template->permalink = $permalink;
				$template->date = date('Y/m/d', strtotime($post->post_date));
				
				$json[$ix] = $template;
				$ix++;
			}
		}

		echo json_encode($json);
		die();
	}

	public static function get_lg_templates(){
		$args = array(
			'posts_per_page' => -1,
			'orderby' => 'title',
			'post_type' => 'laygridder_template'
		);

		$ix = 0;
		$query = new WP_Query( $args );

		$markup = '<div id="wp-link"><ul>';

		if ( $query->have_posts() ) {
			foreach ($query->posts as $post){
				$id = $post->ID;
				$title = $post->post_title;
				$permalink = get_permalink($post);

				$alternate = $ix % 2 == 0 ? ' class="alternate"' : '';
				$info = date('Y/m/d', strtotime($post->post_date));

				$markup .= 
				'<li'.$alternate.'>
					<input type="hidden" class="item-permalink" value="'.$permalink.'">
					<input type="hidden" class="item-id" value="'.$id.'">
					<span class="item-title">'.$post->post_title.'</span>
					<span class="item-info">'.$info.'</span>
				</li>'; 
				$ix++;
			}
		}

		$markup .= '</ul></div>';

		echo '<div class="lg-template-posts-wrap">'.$markup.'</div>';
		die();
	}

	public static function register_template_posttype(){
		register_post_type( 'laygridder_template',
			array(
				'label' => 'Templates',
				'public' => true,
				'has_archive' => false,
				'exclude_from_search' => true,
				'publicly_queryable' => false,
				'show_in_nav_menus' => false,
				'show_in_menu' => 'laygridder-options-page'
			)
		);		

		remove_post_type_support( 'laygridder_template', 'editor' );
	}

}
new LayGridderTemplates();