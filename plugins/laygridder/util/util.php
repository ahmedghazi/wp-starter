<?php

class LG_Util{

	public static function getRelativURL($url) {
		$siteUrl = get_site_url();

		if ($url && substr($url, 0, strlen($siteUrl)) === $siteUrl) {
			return substr($url, strlen($siteUrl));
		}
		return $url;
	}

	// gets all post types and custom post types
  public static function get_all_post_types(){
    $custom_post_types = get_post_types( array('public' => true, '_builtin' => false) );
		$post_types_to_query = array('post', 'page');

		foreach ($custom_post_types as $cpt) {
			if ($cpt != 'laygridder_template') {
				$post_types_to_query[] = $cpt;
			}
    }
    
    return $post_types_to_query;

  }

}

new LG_Util();