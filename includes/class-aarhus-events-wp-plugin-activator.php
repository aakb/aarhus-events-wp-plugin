<?php

/**
 * Fired during plugin activation
 *
 * @link       https://dokk1.dk/hvem-bor-her/itk
 * @since      1.0.0
 *
 * @package    Aarhus_Events_Wp_Plugin
 * @subpackage Aarhus_Events_Wp_Plugin/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Aarhus_Events_Wp_Plugin
 * @subpackage Aarhus_Events_Wp_Plugin/includes
 * @author     Ture Gjørup <tug@aarhus.dk>
 */
class Aarhus_Events_Wp_Plugin_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

    if( !wp_next_scheduled( 'aarhus_events_cron_event' ) ) {
      wp_schedule_event( time(), 'daily', 'aarhus_events_cron_event' );
    }

	}

}
