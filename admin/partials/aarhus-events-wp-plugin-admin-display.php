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

<div class="wrap">

  <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

  <h2 class="nav-tab-wrapper">
    <a href="#locations"
       class="nav-tab nav-tab-active"><?php _e('Locations', $this->plugin_name); ?></a>
    <a href="#events"
       class="nav-tab"><?php _e('Events', $this->plugin_name); ?></a>
    <a href="#options"
       class="nav-tab"><?php _e('Options', $this->plugin_name); ?></a>
  </h2>

  <form method="post" name="cleanup_options" action="options.php">

    <?php

    // Set up hidden fields
    settings_fields($this->plugin_name);
    do_settings_sections($this->plugin_name);

    // Include tabs partials
    require_once('aarhus-events-wp-plugin-admin-locations.php');
    require_once('aarhus-events-wp-plugin-admin-events.php');
    require_once('aarhus-events-wp-plugin-admin-options.php');

    ?>

    <?php submit_button(__('Save all changes', $this->plugin_name), 'primary', 'btnSubmit', TRUE); ?>

  </form>

</div>

