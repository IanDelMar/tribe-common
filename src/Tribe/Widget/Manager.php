<?php
/**
 * Widgets manager for Tribe plugins.
 *
 * @since   TBD
 *
 * @package Tribe\Widget
 */

namespace Tribe\Widget;

/**
 * Class Widget Manager.
 *
 * @since  TBD
 *
 * @package Tribe\Widget
 */
class Manager {

	/**
	 * Get the list of widgets available for handling.
	 *
	 * @since  TBD
	 *
	 * @return array An associative array of widgets in the shape `[ <slug> => <class> ]`.
	 */
	public function get_registered_widgets() {
		$widgets = [];

		/**
		 * Allow the registering of widgets from other plugins.
		 *
		 * @since  TBD
		 *
		 * @var array<string,string> An associative array of widgets in the shape `[ <slug> => <class> ]`.
		 */
		$widgets = apply_filters( 'tribe_widgets', $widgets );

		return $widgets;
	}

	/**
	 * Verifies if a given widget slug is registered for handling.
	 *
	 * @since  TBD
	 *
	 * @param  string $slug The widget slug we are checking for registration.
	 *
	 * @return bool Whether the widget is registered or not.
	 */
	public function is_widget_registered( $slug ) {
		$registered_widgets = $this->get_registered_widgets();

		return isset( $registered_widgets[ $slug ] );
	}

	/**
	 * Verifies if a given widget class name is registered for handling.
	 *
	 * @since  TBD
	 *
	 * @param  string $class_name The widget class name we are checking for registration.
	 *
	 * @return bool Whether the widget is registered, by class.
	 */
	public function is_widget_registered_by_class( $class_name ) {
		$registered_widgets = $this->get_registered_widgets();

		return in_array( $class_name, $registered_widgets, true );
	}

	/**
	 * Add new widgets handler to ensure our list of widget slugs is registered by class name.
	 *
	 * @since  TBD
	 */
	public function register_widgets_with_wp() {
		$registered_widgets = $this->get_registered_widgets();

		// Add to WordPress all of the registered Widgets.
		foreach ( $registered_widgets as $widget => $class_name ) {
			register_widget( $class_name );
		}
	}

	/**
	 * Remove Widget from WordPress widget register by class name.
	 *
	 * @since  TBD
	 *
	 * @param string $class_name The class name of the widget to unregister.
	 */
	public function unregister_widget_from_wp( $class_name ) {
		unregister_widget( $class_name );
	}
}
