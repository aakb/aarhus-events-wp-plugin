<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://dokk1.dk/hvem-bor-her/itk
 * @since      1.0.0
 *
 * @package    Aarhus_Events_Wp_Plugin
 * @subpackage Aarhus_Events_Wp_Plugin/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Aarhus_Events_Wp_Plugin
 * @subpackage Aarhus_Events_Wp_Plugin/includes
 * @author     Ture Gjørup <tug@aarhus.dk>
 */
class Aarhus_Events_Wp_Plugin_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'aarhus-events-wp-plugin',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
