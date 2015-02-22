<?php
/**
 * Class WP_Stream_Connector_Pods_Content
 */
class WP_Stream_Connector_Pods_Content extends WP_Stream_Connector_Pods_Base {

	/**
	 * Connector name/slug
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public static $name = 'pods-content';

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
	 * Connector context singular labels
	 *
	 * @since 0.1.0
	 *
	 * For i18n you should do this in ::register_init()
	 *
	 * @var array
	 */
	public static $context_singular_labels = array();

	/**
	 * Register all context hooks
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function register_init() {

		self::$actions = array(
			'pods_api_post_save_pod_item',
			'pods_api_post_delete_pod_item'
		);

		self::$connector_label = __( 'Pods Content', 'pods-stream' );

		// Get all ACTs
		$advanced_content_types = pods_api()->load_pods( array( 'type' => 'pod', 'table_info' => false, 'fields' => false ) );

		// Add Context labels
		self::$context_labels = array();

		foreach ( $advanced_content_types as $pod ) {
			self::$context_labels[ $pod[ 'name' ] ] = $pod[ 'label' ];

			self::$context_singular_labels[ $pod[ 'name' ] ] = __( 'Pod Item', 'pods-stream' );

			if ( ! empty( $pod[ 'options' ][ 'label_singular' ] ) ) {
				self::$context_singular_labels[ $pod[ 'name' ] ] = $pod[ 'options' ][ 'label_singular' ];
			}
		}

		self::$action_labels = array(
			'created' => __( 'Created', 'pods-stream' ),
			'updated' => __( 'Updated', 'pods-stream' ),
			'deleted' => __( 'Deleted', 'pods-stream' )
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

		if ( $record->object_id && 'deleted' != $record->action && isset( self::$context_singular_labels[ $record->context ] ) ) {
			$link = 'admin.php?page=pods-manage-%s&action=edit&id=%d';

			$text = sprintf( __( 'Edit %s', 'pods-stream' ), self::$context_singular_labels[ $record->context ] );

			$links[ $text ] = sprintf( $link, $record->context, $record->object_id );
		}

		return $links;

	}

	/**
	 * Stream ACT post-save
	 *
	 * @since 0.1.0
	 *
	 * @param array $pieces PodsAPI::save_pod_item $pieces
	 * @param boolean $is_new_item Whether item was just now created
	 * @param int $id Item ID
	 * @param PodsAPI $obj PodsAPI object
	 *
	 * @see PodsAPI::save_pod_item
	 */
	public static function callback_pods_api_post_save_pod_item( $pieces, $is_new_item, $id, $obj = null ) {

		// Get the pod
		$pod = $pieces[ 'pod' ];

		// Restrict to ACTs
		if ( ! isset( self::$context_singular_labels[ $pod[ 'name' ] ] ) ) {
			return;
		}

		// Get save action
		$action_text = __( 'updated', 'pods-stream' );
		$action = 'updated';

		if ( $is_new_item ) {
			$action_text = __( 'created', 'pods-stream' );
			$action = 'created';
		}

		self::log_action( $action, $pod[ 'name' ], $id, $action_text );

	}

	/**
	 * Stream ACT post-delete
	 *
	 * @since 0.1.0
	 *
	 * @param object $params PodsAPI::delete_pod_item $params
	 * @param array $pod Pod data
	 * @param PodsAPI $obj PodsAPI object
	 *
	 * @see PodsAPI::delete_pod_item
	 */
	public static function callback_pods_api_post_delete_pod_item( $params, $pod, $obj = null ) {

		$action_text = __( 'deleted', 'pods-stream' );
		$action = 'deleted';

		$id = $params->id;

		self::log_action( $action, $pod[ 'name' ], $id, $action_text );

	}

	/**
	 * Write action to log/
	 *
	 * @since 0.1.0
	 *
	 * @param $action
	 * @param $pod_name
	 * @param $item_id
	 * @param $action_text
	 * @param array $meta
	 */
	public static function log_action( $action, $pod_name, $item_id, $action_text, $meta = array() ) {
		//make return since self::log() is commented out.
		return;

		// Restrict to ACTs
		if ( ! isset( self::$context_singular_labels[ $pod_name ] ) ) {
			return;
		}

		// Log activity
		self::log(
			sprintf(
				__( '%s #%d %s', 'pods-stream' ),
				self::$context_singular_labels[ $pod_name ],
				$item_id,
				$action_text
			),
			$meta,
			$item_id,
			$pod_name,
			$action
		);

	}

}

// Register connector
WP_Stream_Connector_Pods_Content::register_connector();
