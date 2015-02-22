<?php
/**
 * Class WP_Stream_Connector_Pods_Base
 *
 * This class can be extended by other classes to generalize more functionality
 */
abstract class WP_Stream_Connector_Pods_Base extends WP_Stream_Connector {

	/**
	 * Connector name/slug
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public static $name = '';

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
	 * Register all context hooks
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function register_init() {

		/**
		 * Filter Stream Actions
		 *
		 * Note: Stream only supports up to 5 arguments passed via action (2015-02-12)
		 *
		 * @since 0.1.0
		 *
		 * @param string $name Connector name/slug
		 * @param array $actions Connector actions
		 */
		static::$actions = apply_filters( static::$name . '_stream_wp_actions', static::$actions );

		/**
		 * Filter Stream Context labels
		 *
		 * @since 0.1.0
		 *
		 * @param string $name Connector name/slug
		 * @param array $actions Connector actions
		 */
		static::$context_labels = apply_filters( static::$name . '_stream_context_labels', static::$context_labels );
		/**
		 * Filter Stream Action labels
		 *
		 * @since 0.1.0
		 *
		 * @param string $name Connector name/slug
		 * @param array $actions Connector actions
		 */
		static::$action_labels = apply_filters( static::$name . '_stream_action_labels', static::$action_labels );

	}

	/**
	 * Return translated connector label
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public static function get_label() {

		return static::$connector_label;

	}

	/**
	 * Return translated context labels
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public static function get_context_labels() {

		return static::$context_labels;

	}

	/**
	 * Return translated action labels
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public static function get_action_labels() {

		return static::$action_labels;

	}

	/**
	 * Handle context methods via filter
	 *
	 * @since 0.1.0
	 *
	 * @param string $name
	 * @param array $args
	 *
	 * @return void|object
	 */
	public static function __callStatic( $name, $args ) {

		// Callbacks
		if ( 0 === strpos( $name, 'callback_' ) ) {
			$class = get_called_class();

			// Get action from callback method name
			$action = str_replace( 'callback_', '', $name );

			if ( in_array( $action, static::$actions ) ) {

				/**
				 * Call Stream Log args
				 *
				 * @since 0.1.0
				 *
				 * @param array $value
				 * @param array $args The log args array
				 */
				$call_args = apply_filters( static::$name . '_stream_call_args_' . $action, array(), $args );

				// Log activity
				if ( ! empty( $call_args ) ) {
					call_user_func_array( array( $class, 'log' ), $call_args );
				}
			}
		}

		return null;

	}

	/**
	 * Log handler (to avoid internal notices)
	 *
	 * @since 0.1.0
	 *
	 * @param  string $message   sprintf-ready error message string
	 * @param  array  $args      sprintf (and extra) arguments to use
	 * @param  int    $object_id Target object id
	 * @param  string $context   Context of the event
	 * @param  string $action    Action of the event
	 * @param  int    $user_id   User responsible for the event
	 *
	 * @return bool
	 */
	/*public static function log( $message, $args, $object_id, $context, $action, $user_id = null ) {

		// Log to parent class so it picks up get_called_class properly to map to this connector
		return parent::log( $message, $args, $object_id, $context, $action, $user_id );

	}*/

	/**
	 * Register this connector for Stream by adding filter
	 *
	 * @since 0.1.0
	 *
	 * Add this code after including the class file
	 * to register the class automatically:
	 *
	 * WP_Stream_Connector_XYZ::register_connector();
	 */
	public static function register_connector() {

		$class = get_called_class();

		$class::register_init();

		add_filter( 'wp_stream_connectors', array( $class, '_register_stream_connector' ) );

	}

	/**
	 * Add this connector to the list of connectors loaded up
	 *
	 * @since 0.1.0
	 *
	 * @filter wp_stream_connectors
	 *
	 * @param array $classes Array of connector class names
	 *
	 * @return array
	 *
	 * @private
	 */
	public static function _register_stream_connector( $classes ) {

		$class = get_called_class();

		$classes[] = $class;

		return $classes;

	}

}
