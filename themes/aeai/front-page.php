<?php
/**
 * Home Page
 *
 * @package themeHandle
 */

get_header(); ?>

<section id="primary" role="main" class="">

<?php while ( have_posts() ) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="entry-header">
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</div><!-- .entry-header -->
	
		<div class="entry-content">
			<?php the_content(); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'aeai' ) . '</span>', 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->
	</article><!-- #post-<?php the_ID(); ?> -->

<?php endwhile; // end of the loop. ?>

</section><!-- #primary -->
<?php get_footer(); ?>