<div class="carousel-wrap">
	<div class="carousel" data-carousel>
		<?php foreach($galerie as $image){?>
			<div class="slide">
				<figure>
					<div class="contain" style="background-image:url(<?php echo $image['url']; ?>)"></div>
				</figure>
			</div>
		<?php }?>
	</div>
	<!-- <div class="control prev"></div>
	<div class="control next"></div> -->
	<?php if($hasClose):?>
		<div class="carousel-close"></div>
	<?php endif;?>
</div>
