<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package themeHandle
 */
?>

</main><!-- #main -->


<footer id="footer" role="contentinfo" class="">
	<div id="copyright" class="container">
		&copy; <?php echo date( 'Y' ); echo '&nbsp;'; echo bloginfo( 'name' ); ?><br>
		Site by <a href="designerURI" target="_blank" rel="nofollow">themeDesigner</a> &amp;
		<a href="authorURI" target="_blank" rel="nofollow">themeAuthor</a>
	</div>
</footer><!-- #colophon -->
</div><!-- #page -->
<?php wp_footer(); ?>

<!-- Asynchronous google analytics; this is the official snippet.
	 Replace UA-XXXXXX-XX with your site's ID and uncomment to enable.
	 
<script>

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-XXXXXX-XX']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
-->

</body>
</html>