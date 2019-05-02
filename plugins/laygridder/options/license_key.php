<?php
define('LG_SECRET_KEY', '5a8bf25364fa52.94667377');
define('LG_LICENSE_SERVER_URL', 'http://licensemanager.laygridder.com/');
define('LG_ITEM_REFERENCE', 'LayGridder');

// http://codex.wordpress.org/Settings_API#Examples
class LayGridderLicenseKey{

	public function __construct(){
		add_action( 'admin_menu', array($this, 'license_setup_menu'), 10 );
		add_action( 'admin_notices', array($this, 'register_lay_theme_nag'), 3 );
		add_filter( 'puc_check_now-laygridder', array($this, 'maybe_update'), 10, 3 );
	}

	public function maybe_update($shouldCheck, $lastCheck, $checkPeriod){
		if($shouldCheck && get_option('lg_license_key', '') != ''){
			return true;
		}
		return false;
	}

	public function register_lay_theme_nag(){
		$screen = get_current_screen();
		$key = get_option('lg_license_key', '');
		if($screen->id != "laygridder_page_lg-license" && $key == ""){
			$msg = sprintf( __('UNREGISTERED version of LayGridder. <a href="%1$s">Please enter your License Key</a>.'), network_admin_url( 'admin.php?page=lg-license' ) );
			echo "<div class='update-nag'>$msg</div>";
		}
	}

	public function license_setup_menu(){
		add_submenu_page( 'laygridder-options-page', 'License Key', 'License Key', 'manage_options', 'lg-license', array($this, 'license_management_page') );
	}

	public function license_management_page() {
	    echo '<div class="wrap">';
	    echo '<h2>License Key Management</h2>';

        ?>
        <div class="lay-explanation" style="max-width:100%;">
    		<div class="lay-explanation-inner" style="padding-bottom:0; padding-top:0;">
    		    <form action="" method="post">
    		        <table class="form-table">
    		            <tr>
    		                <th style="width:100px;"><label for="lg_license_key">License Key</label></th>
    		                <td ><input style="width:75%;" type="text" id="lg_license_key" name="lg_license_key"  value="<?php echo get_option('lg_license_key'); ?>" ></td>
    		            </tr>
    		        </table>
    		        <p>Please enter your LayGridder license key above and click <span class="lay-label label-info">Activate</span>.</p>
    		        <p>If your key is valid, updates will be enabled. You can purchase a key on <a target="_blank" href="http://laygridder.com">laygridder.com</a>.</p>
    		        <p class="submit">
    		            <input type="submit" name="activate_license" value="Activate" class="button-primary" />
    		            <input type="submit" name="deactivate_license" value="Deactivate" class="button" />
    		        </p>
    		    </form>
    	    </div>
        </div>
        <?php

	    /*** License activate button was clicked ***/
	    if (isset($_REQUEST['activate_license'])) {
	        $license_key = $_REQUEST['lg_license_key'];

	        // API query parameters
	        $api_params = array(
	            'slm_action' => 'slm_activate',
	            'secret_key' => LG_SECRET_KEY,
	            'license_key' => $license_key,
	            'registered_domain' => $_SERVER['SERVER_NAME'],
	            'item_reference' => urlencode(LG_ITEM_REFERENCE),
	        );

	        // Send query to the license manager server
	        $query = esc_url_raw(add_query_arg($api_params, LG_LICENSE_SERVER_URL));
	        $response = wp_remote_get($query, array('timeout' => 120, 'sslverify' => false));

	        // Check for error in the response
	        if (is_wp_error($response)){
	            echo '<p>Unexpected Error! The query returned with an error.</p>';
	            var_dump(print_r($response, true));
	            $version = curl_version();
	            echo '<br>curl version:<br>';
	            var_dump(print_r($version, true));
	        }

	        //var_dump($response);//uncomment it if you want to look at the full response
	        
	        // License data.
	        $license_data = json_decode(wp_remote_retrieve_body($response));
	        
	        // TODO - Do something with it.
	        //var_dump($license_data);//uncomment it to look at the data
	        
	        if($license_data->result == 'success'){//Success was returned for the license activation
	            
	            //Uncomment the followng line to see the message that returned from the license server
                echo 
                '<div class="lay-explanation lay-license-notification">
    				<div class="lay-explanation-inner">
    		    		<p>'.$license_data->message.'. Thank you for supporting LayGridder! :)</p>
    				</div>
    			</div>';
	            
	            //Save the license key in the options table
	            update_option('lg_license_key', $license_key); 
	        }
	        else{
	            //Show error to the user. Probably entered incorrect license key.
	            
	            //Uncomment the followng line to see the message that returned from the license server
	            if($license_data->message != ""){
		            echo 
		            '<div class="lay-explanation lay-license-notification">
						<div class="lay-explanation-inner">
				    		<p>'.$license_data->message.'.</p>
						</div>
					</div>';
	            }
	        }

	    }
	    /*** End of license activation ***/
	    
	    /*** License deactivate button was clicked ***/
	    if (isset($_REQUEST['deactivate_license'])) {
	        $license_key = $_REQUEST['lg_license_key'];

	        // API query parameters
	        $api_params = array(
	            'slm_action' => 'slm_deactivate',
	            'secret_key' => LG_SECRET_KEY,
	            'license_key' => $license_key,
	            'registered_domain' => $_SERVER['SERVER_NAME'],
	            'item_reference' => urlencode(LG_ITEM_REFERENCE),
	        );

	        // Send query to the license manager server
	        $query = esc_url_raw(add_query_arg($api_params, LG_LICENSE_SERVER_URL));
	        $response = wp_remote_get($query, array('timeout' => 120, 'sslverify' => false));

	        // Check for error in the response
	        if (is_wp_error($response)){
	            echo "<p>Unexpected Error! The query returned with an error.</p>";
	            var_dump(print_r($response, true));
	            $version = curl_version();
	            echo '<br>curl version:<br>';
	            var_dump(print_r($version, true));
	        }

	        //var_dump($response);//uncomment it if you want to look at the full response
	        
	        // License data.
	        $license_data = json_decode(wp_remote_retrieve_body($response));
	        
	        // TODO - Do something with it.
	        //var_dump($license_data);//uncomment it to look at the data
	        
	        if($license_data->result == 'success'){//Success was returned for the license activation
	            
	            //Uncomment the followng line to see the message that returned from the license server           
                echo 
                '<div class="lay-explanation lay-license-notification">
    				<div class="lay-explanation-inner">
    		    		<p>'.$license_data->message.'.</p>
    				</div>
    			</div>';

	            //Remove the licensse key from the options table. It will need to be activated again.
	            update_option('lg_license_key', '');
	        }
	        else{
	            //Show error to the user. Probably entered incorrect license key.
	            
	            //Uncomment the followng line to see the message that returned from the license server
	            if($license_data->message != ""){
    	            echo 
    	            '<div class="lay-explanation lay-license-notification">
    					<div class="lay-explanation-inner">
    			    		<p>'.$license_data->message.'.</p>
    					</div>
    				</div>';
	            }
	        }
	        
	    }
	    
	    echo '</div>';
	}

}

new LayGridderLicenseKey();