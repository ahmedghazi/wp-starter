<?php

class LGQtranslateIntegration {
	public static function init(){
		// prevents invalid json
		// if some text element doesn't have a translated text, qtranslate would display a language prefix before json.
		// json would be invalid
		if (get_option( 'qtranslate_show_displayed_language_prefix', '1' ) == '1') {
			update_option( 'qtranslate_show_displayed_language_prefix', '0' );
		}
	}
}

LGQtranslateIntegration::init();