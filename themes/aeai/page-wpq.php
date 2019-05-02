<?php
/**
 * Template Name: WPQ
 * 
 * @package themeHandle
 */

get_header(); ?>

<section id="primary" class="" role="main">

	<?php while ( have_posts() ) : the_post(); ?>
		
		<?php 
		$args_ = array(
			"post_type"         => "page",
			"post_status"       => "publish",
			'posts_per_page' 	=> get_option( 'posts_per_page' ),
			'paged'          	=> $paged,
			"orderby"           => "menu_order", 
			"order"             => "DESC",
			"post_parent" => get_the_ID()
		);
		
		$q_ = new WP_Query( $args_ );
		while ( $q_->have_posts() ) : $q_->the_post();
			include(locate_template('inc/card.php'));
		endwhile;

		wp_reset_query();
		?>
	
	<?php endwhile; ?>

</section><!-- #primary -->

<?php get_footer(); ?>