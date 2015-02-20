<?php
/**
 * Plugin Name: Pods Stream Plugin
 */

function sinfonia_assets() {

	if ( class_exists( 'WP_Stream_Connector' ) ) {
		if ( ! class_exists( 'WP_Stream_Connector_Pods_Base' ) ) {
			require ('WP_Stream_Connector_Pods_Base.php');
		}

		if ( ! class_exists( 'WP_Stream_Connector_Pods' ) ) {
			require ( 'WP_Stream_Connector_Pods.php');
		}

		if ( ! class_exists( 'WP_Stream_Connector_Pods_Content' ) ) {
			require ('WP_Stream_Connector_Pods_Content.php');
		}

	}

}
add_action( 'init', 'sinfonia_assets', 8 );