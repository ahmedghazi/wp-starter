<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package themeHandle
 */

get_header(); ?>

<section id="primary" role="main" class="">

	<article id="post-0" class="post error404 not-found">
		<h1 class="entry-title"><?php _e( 'Uh oh!', 'aeai' ); ?></h1>

		<div class="entry-content">
			<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps a search would help?', 'aeai' ); ?></p>

			<?php get_search_form(); ?>

		</div><!-- .entry-content -->
	</article><!-- #post-0 -->

</section><!-- #primary -->
<?php //get_sidebar(); ?>
<?php get_footer(); ?>