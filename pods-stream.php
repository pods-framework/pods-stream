<?php
/*
Plugin Name: Pods Stream Connector
Plugin URI: http://pods.io/
Description: Integrates with the WordPress Stream plugin to track changes to Pods and content
Version: 0.1
Author: Pods Framework Team, Nikhil Vimal
Author URI: http://pods.io/about/
Text Domain: pods-stream
Domain Path: /languages/

Copyright 2015  Pods Foundation, Inc  (email : contact@podsfoundation.org)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * @package Pods\Stream
 */

define( 'PODS_STREAM_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Init Stream integration
 *
 * @since 1.0
 */
function pods_stream_init() {

	// Check if the Stream Connector Class exists
	if ( class_exists( 'WP_Stream_Connector' ) ) {
		if ( ! class_exists( 'WP_Stream_Connector_Pods_Base' ) ) {
			include_once PODS_STREAM_DIR . 'WP_Stream_Connector_Pods_Base.php';
		}

		if ( ! class_exists( 'WP_Stream_Connector_Pods' ) ) {
			include_once PODS_STREAM_DIR . 'WP_Stream_Connector_Pods.php';
		}

		if ( ! class_exists( 'WP_Stream_Connector_Pods_Content' ) ) {
			include_once PODS_STREAM_DIR . 'WP_Stream_Connector_Pods_Content.php';
		}
	}
	else {
		//use the global pagenow so we can tell if we are on plugins admin page
		global $pagenow;

		if ( $pagenow == 'plugins.php' ) {
	?>
		<div class="error">
			<p><?php _e( 'Pods Stream Connector requires the WordPress Stream plugin be activated to work.', 'pods-stream' ); ?></p>
		</div>
	<?php
		}
	}

}
add_action( 'init', 'pods_stream_init', 8 );

/**
 * Add Pods internal post types to be excluded from Stream Posts Connector
 *
 * @param array $post_types Post types to exclude
 *
 * @return array
 *
 * @since 1.0
 */
function pods_stream_exclude_internal( $post_types ) {

	$post_types[] = '_pods_pod';
	$post_types[] = '_pods_field';
	$post_types[] = '_pods_group';
	$post_types[] = '_pods_page';
	$post_types[] = '_pods_template';

	return $post_types;

}
add_filter( 'wp_stream_posts_exclude_post_types', 'pods_stream_exclude_internal' );