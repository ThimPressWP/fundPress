<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'TP_DONATE_INSTALLING' ) ) {
	exit();
}

global $wpdb;
$sql = $wpdb->prepare("
		SELECT post.ID FROM $wpdb->posts AS post WHERE post.post_type = %s
	", 'dn_donate' );
$donated = $wpdb->get_results( $sql );

if ( ! $donated ) return;

foreach( $donated as $donate ) {
	var_dump($donate); die();
}