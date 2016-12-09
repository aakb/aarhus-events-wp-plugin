<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://dokk1.dk/hvem-bor-her/itk
 * @since      1.0.0
 *
 * @package    Aarhus_Events_Wp_Plugin
 * @subpackage Aarhus_Events_Wp_Plugin/admin/partials
 */
?>

<div id="locations" class="wrap metabox-holder columns-2 aarhus-events-metaboxes">

  <h3><?php _e('Choose places to sync events from', $this->plugin_name); ?>:</h3>

  <h4><?php _e('Selected places', $this->plugin_name); ?>:</h4>
  <ul>
    <?php foreach ($places as $place) { ?>
      <?php if (in_array($place->place_id, $selected_locations)) { ?>
        <li>
          <fieldset>
            <legend class="screen-reader-text">
              <span><?php _e('Add Post, page or product slug to body class', $this->plugin_name); ?></span>
            </legend>
            <label
              for="<?php echo $this->plugin_name . '-' . $place->place_id; ?>">
              <input type="checkbox"
                     id="<?php echo $this->plugin_name . '-' . $place->place_id; ?>"
                     name="<?php echo $this->plugin_name; ?>[locations][<?php echo $place->place_id; ?>]"
                     value="1" <?php if (in_array($place->place_id, $selected_locations)) {
                checked(in_array($place->place_id, $selected_locations), TRUE);
              } ?>/>
              <span><?php echo $place->place_name . ', ' . $place->adress . ', ' . $place->postcode . ' ' . $place->city . ', (' . $place->number_of_events . ')'; ?></span>
            </label>
          </fieldset>
        </li>
      <?php } ?>
    <?php } ?>
  </ul>

  <?php submit_button(__('Save all changes', $this->plugin_name), 'primary', 'submit', TRUE); ?>

  <h4><?php _e('Select more places', $this->plugin_name); ?>:</h4>
  <ul>
    <?php foreach ($places as $place) { ?>
      <?php if (!in_array($place->place_id, $selected_locations) && $place->number_of_events > 0) { ?>
        <li>
          <fieldset>
            <legend class="screen-reader-text">
              <span><?php _e('Add Post, page or product slug to body class', $this->plugin_name); ?></span>
            </legend>
            <label
              for="<?php echo $this->plugin_name . '-' . $place->place_id; ?>">
              <input type="checkbox"
                     id="<?php echo $this->plugin_name . '-' . $place->place_id; ?>"
                     name="<?php echo $this->plugin_name; ?>[locations][<?php echo $place->place_id; ?>]"
                     value="1" <?php if (in_array($place->place_id, $selected_locations)) {
                checked(in_array($place->place_id, $selected_locations), TRUE);
              } ?>/>
              <span><?php echo $place->place_name . ', ' . $place->adress . ', ' . $place->postcode . ' ' . $place->city . ', (' . $place->number_of_events . ')'; ?></span>
            </label>
          </fieldset>
        </li>
      <?php } ?>
    <?php } ?>
  </ul>
  <hr>
  <ul>
    <?php foreach ($places as $place) { ?>
      <?php if (!in_array($place->place_id, $selected_locations ) && $place->number_of_events == 0) { ?>
        <li>
          <fieldset>
            <legend class="screen-reader-text">
              <span><?php _e('Add Post, page or product slug to body class', $this->plugin_name); ?></span>
            </legend>
            <label
              for="<?php echo $this->plugin_name . '-' . $place->place_id; ?>">
              <input type="checkbox"
                     id="<?php echo $this->plugin_name . '-' . $place->place_id; ?>"
                     name="<?php echo $this->plugin_name; ?>[locations][<?php echo $place->place_id; ?>]"
                     value="1" <?php if (in_array($place->place_id, $selected_locations)) {
                checked(in_array($place->place_id, $selected_locations), TRUE);
              } ?>/>
              <span><?php echo $place->place_name . ', ' . $place->adress . ', ' . $place->postcode . ' ' . $place->city . ', (' . $place->number_of_events . ')'; ?></span>
            </label>
          </fieldset>
        </li>
      <?php } ?>
    <?php } ?>
  </ul>

</div>