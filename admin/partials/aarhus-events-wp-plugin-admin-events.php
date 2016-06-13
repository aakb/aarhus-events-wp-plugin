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

<div id="events" class="wrap metabox-holder columns-2 aarhus-events-metaboxes">

  <h3><?php _e('Events', $this->plugin_name); ?>:</h3>

  <?php submit_button(__('Sync events now', $this->plugin_name), 'primary', 'btnSync', TRUE); ?>

  <ul>
  <?php foreach ($events as $event) { ?>
    <li><?php echo $event->name; ?> (<?php echo $event->place->place_name; ?>)</li>
  <?php } ?>
  </ul>
</div>

