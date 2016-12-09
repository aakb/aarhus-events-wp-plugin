<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://dokk1.dk/hvem-bor-her/itk
 * @since      1.0.0
 *
 * @package    Aarhus_Events_Wp_Plugin
 * @subpackage Aarhus_Events_Wp_Plugin/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Aarhus_Events_Wp_Plugin
 * @subpackage Aarhus_Events_Wp_Plugin/includes
 * @author     Ture Gjørup <tug@aarhus.dk>
 */
class Aarhus_Events_Wp_Plugin_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

    // find out when the last event was scheduled
    $timestamp = wp_next_scheduled('aarhus_events_cron_event');
    // unschedule previous event if any
    wp_unschedule_event($timestamp, 'aarhus_events_cron_event');

	}

}
