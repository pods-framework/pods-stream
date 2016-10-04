<?php
/**
 * Class WP_Stream_Connector_Pods_Base
 *
 * This class can be extended by other classes to generalize more functionality
 */
abstract class WP_Stream_Connector_Pods_Base extends \WP_Stream\Connector {

	public static $instance;

	/**
	 * Connector name/slug
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Connector actions
	 *
	 * @since 0.1.0
	 *
	 * @var array
	 */
	public $actions = array();

	/**
	 * Connector label
	 *
	 * For i18n you should do this in ::register_init()
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public $connector_label = '';

	/**
	 * Connector context labels
	 *
	 * For i18n you should do this in register_init()
	 *
	 * @since 0.1.0
	 * @var array
	 */
	public $context_labels = array();

	/**
	 * Connector action labels
	 *
	 * For i18n you should do this in register_init()
	 *
	 * @since 0.1.0
	 * @var array
	 */
	public $action_labels = array();

	/**
	 * Register all context hooks
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function register_init() {

		// i18n settings can go here

		/**
		 * Filter Stream Actions
		 *
		 * Note: Stream only supports up to 5 arguments passed via action (2015-02-12)
		 *
		 * @since 0.1.0
		 *
		 * @param string $name    Connector name/slug
		 * @param array  $actions Connector actions
		 */
		$this->actions = apply_filters( $this->name . '_stream_wp_actions', $this->actions );

		/**
		 * Filter Stream Context labels
		 *
		 * @since 0.1.0
		 *
		 * @param string $name    Connector name/slug
		 * @param array  $actions Connector actions
		 */
		$this->context_labels = apply_filters( $this->name . '_stream_context_labels', $this->context_labels );

		/**
		 * Filter Stream Action labels
		 *
		 * @since 0.1.0
		 *
		 * @param string $name    Connector name/slug
		 * @param array  $actions Connector actions
		 */
		$this->action_labels = apply_filters( $this->name . '_stream_action_labels', $this->action_labels );

	}

	/**
	 * Return translated connector label
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public function get_label() {

		return $this->connector_label;

	}

	/**
	 * Return translated context labels
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public function get_context_labels() {

		return $this->context_labels;

	}

	/**
	 * Return translated action labels
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public function get_action_labels() {

		return $this->action_labels;

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

			/**
			 * @var WP_Stream_Connector_Pods_Base $object
			 */
			$object = $class::get_instance();

			// Get action from callback method name
			$action = str_replace( 'callback_', '', $name );

			if ( in_array( $action, $object->actions ) ) {
				/**
				 * Call Stream Log args
				 *
				 * @since 0.1.0
				 *
				 * @param array $call_args Call args array
				 * @param array $args      Log args array
				 */
				$call_args = apply_filters( $object->name . '_stream_call_args_' . $action, array(), $args );

				// Log activity
				if ( ! empty( $call_args ) ) {
					call_user_func_array( array( $object, 'log' ), $call_args );
				}
			}
		}

		return null;

	}

	/**
	 * Handle context methods via filter
	 *
	 * @since 0.2.0
	 *
	 * @param string $name
	 * @param array $args
	 */
	public function __call( $name, $args ) {

		// Callbacks
		if ( 0 === strpos( $name, 'callback_' ) ) {
			// Get action from callback method name
			$action = str_replace( 'callback_', '', $name );

			if ( in_array( $action, $this->actions ) ) {
				// Get log args
				$call_args = apply_filters( $this->name . '_stream_call_args_' . $action, array(), $args );

				// Log activity
				if ( ! empty( $call_args ) ) {
					call_user_func_array( array( $this, 'log' ), $call_args );
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
	/*public function log( $message, $args, $object_id, $context, $action, $user_id = null ) {

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

		/**
		 * @var WP_Stream_Connector_Pods_Base $object
		 */
		$object = $class::get_instance();

		$object->register_init();

		add_filter( 'wp_stream_connectors', array( $object, '_register_stream_connector' ) );
		add_filter( 'wp_stream_log_data', array( $object, '_filter_stream_log_data' ) );

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
	public function _register_stream_connector( $classes ) {

		$classes[] = $this;

		return $classes;

	}

	/**
	 * Add this connector properly to log data
	 *
	 * @since 0.2.0
	 *
	 * @filter wp_stream_connectors
	 *
	 * @param array $data Array of log data
	 *
	 * @return array
	 *
	 * @private
	 */
	public function _filter_stream_log_data( $data ) {

		// Data values:
		// 'connector', 'message', 'args', 'object_id', 'context', 'action', 'user_id'

		$class = get_called_class();

		$connector = str_replace( 'Connector_', '', $class );

		$data['connector'] = str_replace( $connector, $this->name, $data['connector'] );

		return $data;

	}

	/**
	 * Private constructor
	 *
	 * @since 0.2.0
	 */
	private function __construct() {

		// Nope

	}

	/**
	 * Get instance
	 *
	 * @since 0.2.0
	 */
	public static function get_instance() {

		$class = get_called_class();

		if ( empty( static::$instance ) || ! is_a( static::$instance, $class ) ) {
			static::$instance = new $class;
		}

		return static::$instance;

	}

}