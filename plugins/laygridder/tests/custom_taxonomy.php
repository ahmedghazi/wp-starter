<?php
add_action('init', function(){
    register_taxonomy( 'custom_taxonomy',
        array('post', 'custom_post_type'),
        array('label' => 'Custom Taxonomy')
    );
});