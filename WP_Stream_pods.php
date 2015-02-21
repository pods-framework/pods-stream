<?php
/**
 * Plugin Name:     Pods Stream Plugin
 * Plugin URI:      @todo
 * Description:     Pods Framework Stream Connector
 * Version:         1.0.0
 * Author:          Nikhil Vimal
 * Author URI:      http://nik.techvoltz.com
 * Text Domain:     pods-stream
 * @copyright       Copyright (c) 2014
 */

function pods_stream_include_files() {

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
add_action( 'init', 'pods_stream_include_files', 8 );