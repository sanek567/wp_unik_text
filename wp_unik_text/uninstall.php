<?php
if(!defined('WP_UNINSTALL_PLUGIN')){
    exit;
}else{

delete_option( 'wpuniktext_settings_options' );
global $wpdb;
$sql = "DROP TABLE" . $wpdb->prefix . "wpuniktext_youbase";
	$wpdb->query( $sql );
}