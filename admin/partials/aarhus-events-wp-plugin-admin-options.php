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

<div id="options" class="wrap metabox-holder columns-2 aarhus-events-metaboxes">

  <h3><?php _e('Options', $this->plugin_name); ?>:</h3>

  <fieldset>
  <legend class="screen-reader-text"><span><?php _e('Choose how often to sync events', $this->plugin_name);?></span></legend>
  <h4><?php esc_attr_e('Choose how often to sync events', $this->plugin_name);?></h4>
  <select name="<?php echo $this->plugin_name;?>[sync_schedule]">
    <?php foreach ($cron_schedules as $key => $schedule) { ?>
      <option value="<?php echo $key; ?>" <?php selected($key, $options['sync_schedule']);?>><?php echo $schedule['display']; ?></option>
    <?php } ?>
  </select>
  </fieldset>

  <fieldset>
    <legend class="screen-reader-text"><span><?php _e('Choose which user account should be the venue & event author', $this->plugin_name);?></span></legend>
    <h4><?php esc_attr_e('Choose which user account should be the venue & event author', $this->plugin_name);?></h4>
    <select name="<?php echo $this->plugin_name;?>[sync_user_account_id]">
      <option value="none" <?php selected("none", $options['sync_user_account_id']);?>>- <?php _e('None', $this->plugin_name);?> -</option>
      <?php foreach ($wp_users as $user) { ?>
        <option value="<?php echo $user->ID; ?>" <?php selected($user->ID, $options['sync_user_account_id']);?>><?php echo $user->display_name; ?></option>
      <?php } ?>
    </select>
  </fieldset>

</div>

