<?php

// Ensure edia_sideload_image() is availiable for cron tasks
require_once ABSPATH."wp-admin/includes/image.php";
require_once ABSPATH."wp-admin/includes/file.php";
require_once ABSPATH."wp-admin/includes/media.php";

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
class Aarhus_Events_Wp_Plugin_Sync_Engine {

  const AARHUS_EVENTS_API_URI = 'http://aarhusguiden.makeable.dk/wp-content/plugins/makeable-cityguide/api/';
  const AARHUS_EVENTS_API_ARGS = array(
    'api' => 'getObjectsIdAndType',
    'lastfullupdate' => 0,
    'timestamp' => 0
  );

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

  public function sync_all() {
    $this->sync_locations();
    $this->sync_events();

    $this->set_last_sync_time(date("Y-m-d H:i:s"));
  }

  /**
   * Short Description. (use period)
   *
   * Long Description.
   *
   * @since    1.0.0
   */
  public function get_events_for_locations($locations) {
    $all_events = $this->get_all_events();

    $events = array();
    foreach ($all_events as $event) {
      if (in_array($event->place_id, $locations)) {
        $events[] = $event;
      }
    }

    return $events;
  }

  /**
   * Short Description. (use period)
   *
   * Long Description.
   *
   * @since    1.0.0
   */
  public function get_all_events() {
    $events = get_transient($this->plugin_name . '_all_events');

    if ($events === FALSE) {

      // Get initial list of object (only ids / types returned)
      $response = wp_remote_post(self::AARHUS_EVENTS_API_URI, array(
        'body' => self::AARHUS_EVENTS_API_ARGS,
        'redirection' => 0,
      ));
      $items = json_decode($response['body']);

      // Filter locations from reply
      $events = array();

      if ($response && $items->success) {
        foreach ($items->data->modified as $item) {
          if ($item->type == 'event') {
            unset($item->last_modified);
            $events[] = $item;
          }
        }
      }

      // Get info on events

      $events_json = json_encode($events);

      $args = array(
        'api' => 'getDataForObjects',
        'objectIdList' => $events_json
      );
      $response = wp_remote_post(self::AARHUS_EVENTS_API_URI, array('body' => $args));

      if ($response) {
        $items = json_decode($response['body']);
        $events = $items->data->events;

        set_transient($this->plugin_name . '_all_events', $events, 60 * 60);
      }
    }

    return $events;
  }

  /**
   * Short Description. (use period)
   *
   * Long Description.
   *
   * @since    1.0.0
   */
  public function get_all_locations() {
    $locations = get_transient($this->plugin_name . '_all_locations');

    if ($locations === FALSE) {
      // Get initial list of object (only ids / types returned)
      $response = wp_remote_post(self::AARHUS_EVENTS_API_URI, array(
        'body' => self::AARHUS_EVENTS_API_ARGS,
        'redirection' => 0,
      ));

      if (!is_wp_error($response)) {
        $items = json_decode($response['body']);

        // Filter locations from reply
        $places = array();

        if ($response && $items->success) {
          foreach ($items->data->modified as $item) {
            if ($item->type == 'place') {
              unset($item->last_modified);
              $places[] = $item;
            }
          }
        }

        // Get info on places

        $places_json = json_encode($places);

        $args = array(
          'api' => 'getDataForObjects',
          'objectIdList' => $places_json
        );
        $response = wp_remote_post(self::AARHUS_EVENTS_API_URI, array('body' => $args));

        if (!is_wp_error($response)) {
          $items = json_decode($response['body']);
          $locations = $items->data->places;

          set_transient($this->plugin_name . '_all_locations', $locations, 60 * 60);
        }
      }
    }

    // List is not sorted
    usort($locations, function ($a, $b) {
      return strcmp($a->place_name, $b->place_name);
    });

    return $locations;
  }

  /**
   * Short Description. (use period)
   *
   * Long Description.
   *
   * @since    1.0.0
   */
  public function get_selected_locations() {
    $locations = get_transient($this->plugin_name . '_selected_locations');

    if ($locations === FALSE) {
      $options = get_option($this->plugin_name);
      $selected_locations_ids = isset($options['locations']) ? $options['locations'] : array();

      $selected_places = array();
      if ($selected_locations_ids) {

        foreach ($selected_locations_ids as $location) {
          $place = new stdClass();
          $place->ID = $location;
          $place->type = 'place';
          $selected_places[] = $place;
        }
      }

      $places_json = json_encode($selected_places);
      $args = array(
        'api' => 'getDataForObjects',
        'objectIdList' => $places_json
      );

      $response = wp_remote_post(self::AARHUS_EVENTS_API_URI, array('body' => $args));

      if ($response) {
        $items = json_decode($response['body']);
        $locations = isset($items->data->places) ? $items->data->places : array();

        set_transient($this->plugin_name . '_selected_locations', $locations, 60 * 60);
      }
    }

    return $locations;
  }

  public function get_selected_location_ids() {
    $locations = $this->get_selected_locations();

    $ids = array();
    foreach ($locations as $location) {
      $ids[] = $location->place_id;
    }

    return $ids;
  }

  private function get_location_id_from_aarhus_venue_id($venue_id) {
    $args = array(
      'meta_key' => 'AarhusVenueID',
      'meta_value' => $venue_id,
      'post_status' => 'publish',
      'post_type' => 'tribe_venue',
      'posts_per_page' => -1
    );
    $posts = get_posts($args);

    if (empty($posts)) {
      return FALSE;
    }

    return $posts[0]->ID;
  }

  public function match_location_to_place_id($places, $place_id) {
    foreach ($places as $place) {
      if($place->place_id == $place_id) {
        return $place;
      }
    }

    return FALSE;
  }

  public function get_number_of_events_for_place_id($events, $place_id) {
    $result = 0;
    foreach ($events as $event) {
      if($event->place_id == $place_id) {
        $result += 1;
      }
    }

    return $result;
  }

  public function update_location($location) {
    $user_id = $this->get_sync_user_id();
    $venue = new stdClass();

    //Mapping
    $venue->_VenueAddress = $location->adress;
    $venue->_VenueCity = $location->city;
    $venue->_VenueCountry = "Denmark";
    $venue->_VenueZip = $location->postcode;
    $venue->_VenuePhone = $location->phone;
    $venue->_VenueURL = $location->website;

    $venue->AarhusVenueID = $location->place_id;

    $postarr = array();
    $postarr['post_title'] = $location->place_name;
    $postarr['post_content'] = $location->description;
    $postarr['post_status'] = 'publish';
    $postarr['post_type'] = 'tribe_venue';
    $postarr['post_author'] = $user_id;

    // Do we have the location allready?
    $post_id = $this->get_location_id_from_aarhus_venue_id($location->place_id);

    if (!$post_id) {
      $post_id = wp_insert_post($postarr);
    } else {
      $args['ID'] = $post_id;
    }

    // Update wp meta
    foreach ($venue as $key => $value) {
      update_post_meta($post_id, $key, $value);
    }
  }

  public function sync_locations() {
    $locations = $this->get_selected_locations();

    foreach ($locations as $location) {
      $this->update_location($location);
    }
  }

  public function update_event($event) {
    $user_id = $this->get_sync_user_id();
    $venue_id = $this->get_location_id_from_aarhus_venue_id($event->place_id);

    foreach ($event->event_dates as $event_dates) {
      $tribe_event = new stdClass();

      //Mapping
      $tribe_event->AarhusEventID = $event->event_id;
      $tribe_event->_EventOrigin = "events-calendar";

      //Date mapping
      $start = new DateTime();
      $start->setTimezone(new DateTimeZone("Europe/Copenhagen"));
      $start->setTimestamp($event_dates->start_datetime);
      $end = new DateTime();
      $end->setTimezone(new DateTimeZone("Europe/Copenhagen"));
      $end->setTimestamp($event_dates->end_datetime);

      $tribe_event->_EventTimezone = "Europe/Copenhagen";
      $tribe_event->_EventTimezoneAbbr = "CET";
      $tribe_event->_EventStartDate = $start->format("Y-m-d H:i:s");
      $tribe_event->_EventEndDate = $end->format("Y-m-d H:i:s");
      $tribe_event->_EventDuration = $event_dates->end_datetime - $event_dates->start_datetime;

      //Venue mapping
      if($venue_id) {
        $tribe_event->_EventVenueID = $venue_id;
      }

      $postaar = array();
      $postarr['post_title'] = $event->name;
      $postarr['post_content'] = empty($event->description) ? '' : html_entity_decode($event->description);
      $postarr['post_status'] = 'publish';
      $postarr['post_type'] = 'tribe_events';
      $postarr['post_author'] = $user_id;

      // Do we have the event allready?
      $sql = 'SELECT p.ID FROM `wp_posts` p LEFT JOIN `wp_postmeta` pm ON p.ID = pm.post_id WHERE pm.meta_key = "AarhusEventID" AND pm.meta_value = "' . $event->event_id . '" AND p.post_status = "publish" ';
      global $wpdb;
      $posts = $wpdb->get_results($sql);

      if (empty($posts)) {
        $post_id = wp_insert_post($postarr);
      }
      else {
        $post_id = $posts[0]->ID;
        $args['ID'] = $post_id;
      }

      // Load image
      if (!empty($event->promopic)) {
        // magic sideload image returns an HTML image, not an ID
        $media = media_sideload_image($event->promopic, $post_id);

        // therefore we must find it so we can set it as featured ID
        if (!empty($media) && !is_wp_error($media)) {
          $args = array(
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'post_parent' => $post_id
          );

          // reference new image to set as featured
          $attachments = get_posts($args);

          if (isset($attachments) && is_array($attachments)) {
            foreach ($attachments as $attachment) {
              // grab source of full size images (so no 300x150 nonsense in path)
              $image = wp_get_attachment_image_src($attachment->ID, 'full');
              // determine if in the $media image we created, the string of the URL exists
              if (strpos($media, $image[0]) !== FALSE) {
                // if so, we found our image. set it as thumbnail
                set_post_thumbnail($post_id, $attachment->ID);
                // only want one image
                break;
              }
            }
          }
        }
      }

      // Update wp meta
      foreach ($tribe_event as $key => $value) {
        update_post_meta($post_id, $key, $value);
      }
    }
  }

  public function sync_events() {
    $locations = $this->get_selected_location_ids();
    $events = $this->get_events_for_locations($locations);

    foreach ($events as $event) {
      $this->update_event($event);
    }
  }

  private function get_sync_user_id() {
    $options = get_option($this->plugin_name);
    $user_id = isset($options['sync_user_account_id']) ? $options['sync_user_account_id'] : 1;

    return $user_id;
  }

  private function set_last_sync_time($time) {
    $option_name = 'aarhus_events_last_sync' ;

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
