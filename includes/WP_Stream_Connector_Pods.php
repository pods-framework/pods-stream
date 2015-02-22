<?php
/**
 * Class WP_Stream_Connector_Pods
 *
 * This class can be extended by other classes to generalize more functionality
 *
 * Add a ::register_init method to easily run things on register without dealing
 * with the ramifications of multiple levels of class extending and parent::
 */

class WP_Stream_Connector_Pods extends WP_Stream_Connector_Pods_Base {

	/**
	 * Connector name/slug
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public static $name = 'pods';

	/**
	 * Connector actions
	 *
	 * @since 0.1.0
	 *
	 * @var array
	 */
	public static $actions = array();

	/**
	 * Connector label
	 *
	 * @since 0.1.0
	 *
	 * For i18n you should do this in ::register_init()
	 *
	 * @var string
	 */
	public static $connector_label = '';

	/**
	 * Connector context labels
	 *
	 * @since 0.1.0
	 *
	 * For i18n you should do this in ::register_init()
	 *
	 * @var array
	 */
	public static $context_labels = array();

	/**
	 * Connector action labels
	 *
	 * @since 0.1.0
	 *
	 * For i18n you should do this in ::register_init()
	 *
	 * @var array
	 */
	public static $action_labels = array();

	/**
	 * Holds tracked plugin minimum version required
	 *
	 * @since 0.1.0
	 *
	 * @const string
	 */
	const PLUGIN_MIN_VERSION = '2.0.0';

	/**
	 * Check if plugin dependencies are satisfied and add an admin notice if not
	 *
	 * @since 0.1.0
	 *
	 * @return bool
	 */
	public static function is_dependency_satisfied() {

		// Check if Pods is loaded and setup
		if ( defined( 'PODS_VERSION' ) && version_compare( PODS_VERSION, self::PLUGIN_MIN_VERSION, '>=' ) ) {
			return true;
		}

		return false;

	}



	/**
	 * Register all context hooks
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function register_init() {
		// Add Pods-specific data that needs to be built via PHP -- like i18n strings
		self::$connector_label = __( 'Pods', 'pods' );

		self::$context_labels = array(
			'pod'      => __( 'Pod', 'pods' ),
			'field'    => __( 'Pod Field', 'pods' ),
			'group'    => __( 'Pod Group', 'pods' ),
			'settings' => __( 'Settings', 'pods' )
		);

		self::$action_labels = array(
			'created'       => __( 'Created', 'pods' ),
			'updated'       => __( 'Updated', 'pods' ),
			'deleted'       => __( 'Deleted', 'pods' ),
			'reset'         => __( 'Reset', 'pods' ),
			'cache-cleared' => __( 'Cache Cleared', 'pods' )
		);

		parent::register_init();

	}


	/**
	 * Add action links to Stream drop row in admin list screen
	 *
	 * @since 0.1.0
	 *
	 * @filter wp_stream_action_links_{connector}
	 *
	 * @param  array  $links     Previous links registered
	 * @param  object $record    Stream record
	 *
	 * @return array             Action links
	 */
	public static function action_links( $links, $record ) {

		if ( $record->object_id && 'deleted' != $record->action ) {
			if ( 'pod' == $record->context ) {
				$link = 'admin.php?page=pods&action=edit&id=%d';

				$pod_id = $record->object_id;

				$links[ __( 'Edit Pod', 'pods' ) ] = sprintf( $link, $pod_id );
			} elseif ( 'field' == $record->context ) {
				// @todo update with Group action / ID
				$link = 'admin.php?page=pods&action=edit&id=%d';

				$pod_id = $record->stream_meta->pod_id;

				$group_id = $record->stream_meta->group_id;

				$links[ __( 'Edit Pod', 'pods' ) ] = sprintf( $link, $pod_id, $group_id );
			} elseif ( 'group' == $record->context ) {
				// @todo update with Group action / ID
				$link = 'admin.php?page=pods&action=edit&id=%d';

				$pod_id = $record->stream_meta->pod_id;

				$group_id = $record->object_id;

				$links[ __( 'Edit Pod Group', 'pods' ) ] = sprintf( $link, $pod_id, $group_id );
			}
		} elseif ( 'settings' == $record->context ) {
			$links[ __( 'Edit Settings', 'pods' ) ] = 'admin.php?page=pods-settings';
		}

		return $links;

	}

}

// Register connector
WP_Stream_Connector_Pods::register_connector();
