<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://dokk1.dk/hvem-bor-her/itk
 * @since      1.0.0
 *
 * @package    Aarhus_Events_Wp_Plugin
 * @subpackage Aarhus_Events_Wp_Plugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Aarhus_Events_Wp_Plugin
 * @subpackage Aarhus_Events_Wp_Plugin/admin
 * @author     Ture Gjørup <tug@aarhus.dk>
 */
class Aarhus_Events_Wp_Plugin_Admin {

  const AARHUS_EVENTS_API_URI = 'http://aarhusguiden.makeable.dk/wp-content/plugins/makeable-cityguide/api/';
  const AARHUS_EVENTS_API_ARGS = array('api' => 'getObjectsIdAndType', 'lastfullupdate' => 0, 'timestamp' => 0);

  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string $plugin_name The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string $version The current version of this plugin.
   */
  private $version;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string $plugin_name The name of this plugin.
   * @param      string $version The version of this plugin.
   */
  public function __construct($plugin_name, $version) {

    $this->plugin_name = $plugin_name;
    $this->version = $version;

  }

  /**
   * Register the stylesheets for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_styles() {

    /**
     * This function is provided for demonstration purposes only.
     *
     * An instance of this class should be passed to the run() function
     * defined in Aarhus_Events_Wp_Plugin_Loader as all of the hooks are defined
     * in that particular class.
     *
     * The Aarhus_Events_Wp_Plugin_Loader will then create the relationship
     * between the defined hooks and the functions defined in this
     * class.
     */

    wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/aarhus-events-wp-plugin-admin.css', array(), $this->version, 'all');

  }

  /**
   * Register the JavaScript for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts() {

    /**
     * This function is provided for demonstration purposes only.
     *
     * An instance of this class should be passed to the run() function
     * defined in Aarhus_Events_Wp_Plugin_Loader as all of the hooks are defined
     * in that particular class.
     *
     * The Aarhus_Events_Wp_Plugin_Loader will then create the relationship
     * between the defined hooks and the functions defined in this
     * class.
     */

    wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/aarhus-events-wp-plugin-admin.js', array('jquery'), $this->version, FALSE);

  }

  /**
   * Add a settings page for this plugin to the Settings menu.
   *
   * @since    1.0.0
   */
  public function add_plugin_admin_menu() {
    add_options_page('Aarhus Events Sync Options', 'Aarhus Events', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page')
    );
  }

  /**
   * Add settings action link to the plugins page.
   *
   * @since    1.0.0
   */
  public function add_action_links($links) {
    $settings_link = array(
      '<a href="' . admin_url('options-general.php?page=' . $this->plugin_name) . '">' . __('Settings', $this->plugin_name) . '</a>',
    );
    return array_merge($settings_link, $links);
  }

  /**
   * Render the settings page for this plugin.
   *
   * @since    1.0.0
   */
  public function display_plugin_setup_page() {
    $sync_engine = new Aarhus_Events_Wp_Plugin_Sync_Engine( $this->get_plugin_name(), $this->get_version() );

    // Get WP Users
    $wp_users = get_users();

    // Get cron schedules
    $cron_schedules = wp_get_schedules();

    $options = get_option($this->plugin_name);
    $options['sync_schedule'] = isset($options['sync_schedule']) ? $options['sync_schedule'] : 'twicedaily';
    $options['sync_user_account_id'] = isset($options['sync_user_account_id']) ? $options['sync_user_account_id'] : '1';

    $selected_locations = isset($options['locations']) ? $options['locations'] : array();

    $places = $sync_engine->get_all_locations();
    $selected = $sync_engine->get_selected_locations();

    $all_events = $sync_engine->get_all_events();
    $events = $sync_engine->get_events_for_locations($selected_locations);

    foreach ($events as $event) {
      $event->place = $sync_engine->match_location_to_place_id($places, $event->place_id);
    }
    foreach ($places as $place) {
      $place->number_of_events = $sync_engine->get_number_of_events_for_place_id($all_events, $place->place_id);
    }
    foreach ($selected as $place) {
      $place->number_of_events = $sync_engine->get_number_of_events_for_place_id($all_events, $place->place_id);
    }

    include_once('partials/aarhus-events-wp-plugin-admin-display.php');
  }


  /**
   * Validate options input
   *
   * @since    1.0.0
   */
  public function options_update() {
    register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate'));
  }

  /**
   * Validate options input
   *
   * @since    1.0.0
   */
  public function validate($input) {
    if(isset($_POST['btnSync'])) {
      $sync_engine = new Aarhus_Events_Wp_Plugin_Sync_Engine( $this->get_plugin_name(), $this->get_version() );
      $sync_engine->sync_all();
    }

    delete_transient( $this->plugin_name.'_selected_locations' );

    // All checkboxes inputs
    $valid = array();

    //Cleanup locations
    if(isset($input['locations']) && !empty($input['locations'])) {
      foreach($input['locations'] as $location_id => $location) {
        if(isset($input['locations'][$location_id]) && !empty($input['locations'][$location_id])) {
          $valid['locations'][] = $location_id;
        }
      }
    }

    $valid['sync_schedule'] = sanitize_text_field($input['sync_schedule']);
    $valid['sync_user_account_id'] = sanitize_text_field($input['sync_user_account_id']);

    // find out when the last event was scheduled
    $timestamp = wp_next_scheduled ('aarhus_events_cron_event');
    // unschedule previous event if any
    wp_unschedule_event ($timestamp, 'aarhus_events_cron_event');
    wp_schedule_event( time(), $valid['sync_schedule'], 'aarhus_events_cron_event' );

    return $valid;
  }

  /**
   * The name of the plugin used to uniquely identify it within the context of
   * WordPress and to define internationalization functionality.
   *
   * @since     1.0.0
   * @return    string    The name of the plugin.
   */
  public function get_plugin_name() {
    return $this->plugin_name;
  }

  /**
   * Retrieve the version number of the plugin.
   *
   * @since     1.0.0
   * @return    string    The version number of the plugin.
   */
  public function get_version() {
    return $this->version;
  }

  /**
   * THe function that runs on cron event aarhus_events_cron_sync
   */
  public function aarhus_events_cron_sync() {
    $plugin = new Aarhus_Events_Wp_Plugin();
    $plugin->sync();

    $this->set_last_cron_time();
  }

  private function set_last_cron_time() {
    $time = date("Y-m-d H:i:s");
    $option_name = 'aarhus_events_last_cron' ;

    if ( get_option( $option_name ) !== false ) {

      // The option already exists, so we just update it.
      update_option( $option_name, $time );

    } else {

      // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
      $deprecated = null;
      $autoload = 'no';
      add_option( $option_name, $time, $deprecated, $autoload );
    }
  }

}
