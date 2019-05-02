<div class="wrap">
    <h2>Gridder Options</h2>

    <?php if(isset( $_GET['settings-updated'])) { ?>
    <div class="updated">
        <p>Gridder Options updated.</p>
    </div>
    <?php } ?>

    <div id="locationmanager"></div>
    
	<form method="POST" action="options.php">
	<?php 
	settings_fields( 'laygridder-options-page' );	//pass slug name of page, also referred
	                                        //to in Settings API as option group name
	do_settings_sections( 'laygridder-options-page' ); 	//pass slug name of page
	LayGridderLocation::location_setting();
	submit_button();
	?>
	</form>
</div>

<?php
require LG_PLUGIN_PATH.'location/location_markup.php';