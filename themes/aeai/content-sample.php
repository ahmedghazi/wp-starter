<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package themeHandle
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
	</div><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
		
	</div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->
