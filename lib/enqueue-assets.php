<?php
    function unplugged_migs_scripts() {
    	wp_enqueue_media();
    	wp_register_script('unplugged-migs-scripts', plugins_url('js/upload-image.js' , __FILE__ ), array('jquery'));
    	wp_enqueue_script( 'unplugged-migs-scripts' );
    }
    add_action('admin_enqueue_scripts', 'unplugged_migs_scripts');
?>