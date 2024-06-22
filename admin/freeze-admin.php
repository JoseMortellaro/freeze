<?php
defined( 'ABSPATH' ) || exit; //prefent direct access

//File including aLl the scripts for the Backend


//Add admin menu item under the plugins menu
function eos_freeze_plugin_menu() {
    add_menu_page(
      esc_html__( 'Freeze','freeze' ),
      esc_html__( 'Freeze','freeze' ),
      'manage_options',
      'eos-freeze',
      'eos_freeze_settings_do_page',
      EOS_FREEZE_PLUGIN_URL.'/admin/assets/img/freeze-icon.png',
      40
    );
}
add_action( 'admin_menu','eos_freeze_plugin_menu' );

add_action( 'admin_enqueue_scripts','eos_freeze_enqueue_assets' );
//Enqueue assets for the settings paged
function eos_freeze_enqueue_assets(){
  if( isset( $_GET['page'] ) && 'eos-freeze' === $_GET['page'] ){
    ?>
    <style>
    #adminmenumain,#wpadminbar{
      pointer-events: none !important;
      cursor: default !important;
    }
    #freeze-section article{
      margin-bottom:48px
    }
    </style>
    <?php
    wp_enqueue_script( 'freeze',EOS_FREEZE_PLUGIN_URL.'/admin/assets/js/freeze-admin.js','jquery',EOS_FREEZE_PLUGIN_VERSION );
    wp_enqueue_style( 'freeze',EOS_FREEZE_PLUGIN_URL.'/admin/assets/css/freeze-admin.css',null,EOS_FREEZE_PLUGIN_VERSION );
  }
}


//Callbakk for the settings page
function eos_freeze_settings_do_page(){
  wp_nonce_field( 'eos_freeze_rename_nonce','eos_freeze_rename_nonce' );
  delete_site_transient( 'freeze_key' );
  $folder_name = defined( 'WP_PLUGIN_DIR' ) ? basename( WP_PLUGIN_DIR ) : 'plugins';
  $unfreeze_url = wp_nonce_url( wp_nonce_url(
    add_query_arg(
      array(
        'action' => 'deactivate',
        'plugin' => EOS_FREEZE_PLUGIN_BASE_NAME,
        'plugin_status' => 'all',
        'paged' => '1'
      ),
      admin_url( 'plugins.php' )
    ),
    'deactivate-plugin_'.EOS_FREEZE_PLUGIN_BASE_NAME
  ),'eos_unfreeze','unfreeze' );

  $update_url = wp_nonce_url(
    add_query_arg(
      array(
        'action' => 'upgrade-plugin',
        'plugin' => EOS_FREEZE_PLUGIN_BASE_NAME
      ),
      admin_url( 'update.php' )
    ),
    'upgrade-plugin_'.EOS_FREEZE_PLUGIN_BASE_NAME
  );
  $freeze_transient = get_site_transient( 'freeze_update_available' );
  ?>
  <section id="freeze-section" style="margin-top:32px">
    <?php
    if( defined( 'WP_DEBUG' ) && WP_DEBUG ){ ?>
    <article>
      <div class="notice notice-warning" style="padding:10px"><p><?php esc_html_e( 'It is strongly recommended that you disable the debug in wp-config.php. When an installation is frozen your plugins may generate a lot of warnings, and the file debug.log may become drammatically big.','freeze' ); ?></p></div>
    </article>
    <?php } ?>
    <?php
    if( $freeze_transient && 'true' === $freeze_transient ){ ?>
    <article>
      <div class="notice notice-warning" style="padding:10px"><p><?php esc_html_e( 'A new versin of Freeze is available.','freeze' ); ?></p></div>
      <div><a class="button" href="<?php echo esc_url( $update_url ); ?>"><?php esc_html_e( 'Update Freeze','freeze' ); ?></a></div>
    </article>
    <?php } ?>
    <h1><?php esc_html_e( 'Your installation is frozen!', 'freeze' ); ?></h1>
    <article>
      <p><?php esc_html_e( 'Freeze will protect your installation even if the other plugins, the theme, and the core are not updated. Your entire backend is now frozen. You will not be able to do any action.' ); ?></p>
      <p><?php esc_html_e( 'On the frontend, the pages will load, but no action will be possible. It will not be possible for instance to submit comments, forms, and so on.','freeze' ); ?></p>
      <p><?php esc_html_e( 'Remember that this plugin is for static websites. It is useful for freezing the state of the installation when you want to avoid the software update and at the same time you do not want to risk the exploit of the vulnerabilities of old plugins. If you need that the users can do something on this website, unfreeze it, and do not use this plugin. But in that case, better you keep your software updated.','freeze' ); ?></p>
      <p><?php esc_html_e( 'If you want to unfreeze the website you need to click on the button below to disable the plugin.','freeze' ); ?></p>
      <p><?php esc_html_e( 'If you want to freeze it again, just activate again the plugin.' ); ?></p>
      <div><a class="update-link button" href="<?php echo esc_url( $unfreeze_url ); ?>"><?php esc_html_e( 'Unfreeze','freeze' ); ?></a></div>
    </article>
    <article>
      <h3><?php esc_html_e( 'Rename Plugins Folder','freeze' ); ?></h3>
      <p><?php esc_html_e( "If you want an extra layer of protectin, rename the plugins folder, and bad robots will not find your old plugins.","freeze" ); ?></p>
      <label for="freeze-folder-name"><?php esc_html_e( 'Type a new name for your plugins folder','freeze' ); ?></label>
      <input type="text" id="freeze-folder-name" value="<?php echo esc_attr( $folder_name ); ?>" />
      <input type="submit" id="freeze-rename-submit" class="button" value="<?php esc_attr_e( 'Rename','freeze' ); ?>" />
      <div>
        <p><?php esc_html_e( "But be careful! Renaming the plugins folder may cause serious issues if one of your plugin or your theme don't follow the best practices to refer to the plugins folder.","freeze" ); ?></p>
        <p><?php esc_html_e( "Before pressing the button, make a backup of your wp-config.php, or even better, a full backup.","freeze" ); ?></p>
        <p><?php esc_html_e( "If after renaming the plugins folder something goes wrong and you don't know how to solve it, follow these steps:.","freeze" ); ?></p>
        <ol style="margin-<?php echo is_rtl() ? 'right' : 'left'; ?>:15px">
          <li><?php esc_html_e( 'Restore the file wp-config.php that you backed up.','freeze' ); ?></li>
          <li><?php esc_html_e( 'Manually rename the plugins folder via FTP restoring the original name "plugins".','freeze' ); ?></li>
        </ol>
      </div>
      <div id="freeze-message-success" class="freeze-hidden notice notice-success"><?php esc_html_e( 'Plugins folder renamed successfully','freeze' ); ?></div>
      <div id="freeze-message-fail" class="freeze-hidden notice notice-error"><?php esc_html_e( 'Something went wrong. Plugins folder not renamed.','freeze' ); ?></div>
      <div id="freeze-message-no-access" class="freeze-hidden notice notice-error"><?php esc_html_e( 'It looks you have no writing rights. Impossible to rename the plugins folder in this case.','freeze' ); ?></div>
    </article>
  </section>
  <footer style="margin-top:32px"><a href="<?php echo esc_url( get_home_url() ); ?>" target="_blank"><?php esc_html_e( 'Visit the website homepage','freeze' ); ?></a></footer>
  <?php
}

add_action( 'admin_head','eos_freeze_admin_notices' );
//Remove all admmin notices on the settings page
function eos_freeze_admin_notices(){
  if( isset( $_GET['page'] ) && 'eos-freeze' === $_GET['page'] ){
    remove_all_actions( 'admin_notices' );
    remove_all_actions( 'all_admin_notices' );
  }
}
