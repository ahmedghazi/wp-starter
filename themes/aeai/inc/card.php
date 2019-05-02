<article <?php post_class("col-xs-12 col-md-4") ?>>
	<a href="<?php the_permalink() ?>">
	<div class="thumbnail">
		<figure>
			<?php //echo wp_get_attachment_image( get_post_thumbnail_id(), "large", 0, "" );?>
			<?php echo wp_get_attachment_image( get_post_thumbnail_id(), "large", 0, "" );?>
		</figure>
	</div>
		<h2 id="post-<?php the_ID(); ?>">
			<?php the_title(); ?>
		</h2>	
	</a>
	
</article>