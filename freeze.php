<?php
/*
Plugin Name: Freeze
Description: It freezes your installation to protect it even if you have old software.
Author: Jose Mortellaro
Author URI: https://josemortellaro.com
Domain Path: /languages/
Version: 0.0.2
*/
/*  This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if( defined( 'FREEZE_OFF' ) && FREEZE_OFF ) return;

//Definitions
define( 'EOS_FREEZE_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define( 'EOS_FREEZE_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
define( 'EOS_FREEZE_PLUGIN_BASE_NAME', untrailingslashit( plugin_basename( __FILE__ ) ) );
define( 'EOS_FREEZE_PLUGIN_VERSION', '0.0.1' );

if( is_admin() && eos_freeze_is_freeze_page() ){
	require_once EOS_FREEZE_PLUGIN_DIR.'/admin/freeze-admin.php';
}
if( eos_freeze_is_ajax() ){
	if( isset( $_REQUEST['action'] ) && false !== strpos( sanitize_text_field( $_REQUEST['action'] ),'eos_freeze_' ) ){
		require_once EOS_FREEZE_PLUGIN_DIR.'/admin/freeze-ajax.php';
	}
	else{
		//Freeze Ajax
		die();
		exit;
	}
}

add_action( 'admin_init',function(){
	if( !eos_freeze_is_freeze_page() ){
		if( !eos_freeze_can_freeze() ) return;
		//Redirect to Freeze in the backend
		$freeze_key = md5( uniqid() );
		set_site_transient( 'freeze_key',$freeze_key );
		wp_redirect( add_query_arg( 'freeze_key',$freeze_key,admin_url( 'admin.php?page=eos-freeze' ) ) );
		die();
		exit;
	}
} );

//Check if redirection is allowed
function eos_freeze_can_freeze(){
	if( isset( $_GET['unfreeze'] ) ){
		if( wp_verify_nonce( sanitize_text_field( $_GET['unfreeze'] ),'eos_unfreeze' ) ){
			return false;
		}
	}
	if( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( $_REQUEST['_wpnonce'] ),'upgrade-plugin_'.EOS_FREEZE_PLUGIN_BASE_NAME ) ){
		return false;
	}
	if(  isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['nonce']  ),'eos_freeze_rename_nonce' ) ){
		return;
	}
	return true;
}

//Completely disable comments
add_filter('comments_open','__return_false', 20, 2);
add_filter('pings_open','__return_false', 20, 2);
add_filter('comments_array','_return_empty_array', 10, 2);

//Disable updates
add_filter( 'site_transient_update_themes','freeze_last_checked' ); //freeze theme update for multisites
add_filter( 'site_transient_update_plugins','freeze_last_checked' ); //freeze plugins update for multisites
add_filter( 'site_transient_update_core','freeze_last_checked' ); //freeze core update for multisites

//Return $object to disable updates
function freeze_last_checked( $transient ){
	if( isset( $transient->response ) ){
		$response = $transient->response;
		if( isset( $response[EOS_FREEZE_PLUGIN_BASE_NAME] ) ){
			//Display update notificatin only for Freeze
			$response = array( EOS_FREEZE_PLUGIN_BASE_NAME => $response[EOS_FREEZE_PLUGIN_BASE_NAME] );
			$transient->response = $response;
			set_site_transient( 'freeze_update_available','true' );
			return $transient;
		}
	}
	$transient = new stdClass();
	return $transient;
}

//Check if it's an ajax request
function eos_freeze_is_ajax(){
	if( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) return true;
	if( defined( 'DOING_AJAX' ) && DOING_AJAX ) return true;
	if( isset( $_REQUEST['wc-ajax'] ) ) return true;
	if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && !empty($_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ){
		return true;
	}
	return false;
}

//Check if it's a settings page of Freeze
function eos_freeze_is_freeze_page(){
	if(
		is_admin()
		&& isset( $_GET['page'] )
		&& false !== strpos( sanitize_text_field( $_GET['page'] ),'eos-freeze' )
		&& isset( $_GET['freeze_key'] )
		&& get_site_transient( 'freeze_key')
		&& sanitize_text_field( $_GET['freeze_key'] ) === get_site_transient( 'freeze_key' )
	){
		return true;
	}
	return false;
}

add_action( 'init',function(){
	if( !eos_freeze_is_freeze_page() ){
		if( !eos_freeze_can_freeze() ) return;
		if( isset( $_REQUEST['log'] ) && isset( $_REQUEST['pwd'] ) ){
			?>
			<div style="padding:20px">
			<p>This installation frozen! You can unfreeze it by adding the following line of code in wp-config.php before the comment /* That's all, stop editing! Happy blogging. */:</p>
			<pre style="padding:20px;background-color:#F5F5F5">
			define( 'FREEZE_OFF','true' );
			</pre>
			</div>
			<?php
			exit;
		}
		$_GET = $_POST = $_REQUEST = $_SESSION = $_FILES = $_ENV = array();
	}
	if( isset( $_SERVER["PHP_SELF"] ) ){
		$_SERVER["PHP_SELF"] = htmlspecialchars( $_SERVER["PHP_SELF"] ); //prevent XSS
	}
	if( !defined( 'DISABLE_WP_CRON' ) ){
		//Disable cron
		define( 'DISABLE_WP_CRON',true );
	}
	wp_deregister_script('heartbeat');
	add_filter( 'xmlrpc_enabled','__return_false' );
	add_filter( 'rest_authentication_errors','eos_freeze_disable_rest_api' );
} );

function eos_freeze_disable_rest_api(){
	return new WP_Error(
		'rest_disabled',
		esc_html__( 'Rest API disabled','freeze' ),
		array( 'status' => 401 )
	);
}

//It removes unnecessary actions on the frontend
foreach( array(
	'rsd_link' => array( 'wp_head',10 ),
	'wp_generator' => array( 'wp_head',10 ),
	'wlwmanifest_link' => array( 'wp_head',10 ),
	'rest_output_link_wp_head' => array( 'wp_head',10 ),
	'rest_output_link_header' => array( 'template_redirect',11 )
) as $action => $arr ){
	remove_action( $arr[0],$action,$arr[1] );
}


$plugin = EOS_FREEZE_PLUGIN_BASE_NAME;

//It adds a settings link to the action links in the plugins page
add_filter( "plugin_action_links_$plugin", 'eos_freeze_plugin_add_settings_link' );

//It adds a settings link to the action links in the plugins page
function eos_freeze_plugin_add_settings_link( $links ) {
  $settings_link = '<a class="eos-dp-setts" href="'.wp_nonce_url( admin_url( 'admin.php?page=eos-freeze' ),'freeze_page','freeze_page' ).'">' .esc_html__( 'Feeze','freeze' ). '</a>';
  array_push( $links, $settings_link );
	return $links;
}

add_action( 'upgrader_process_complete', 'eos_freeze_after_upgrade',10,2 );
//Delete transient after upgrade
function eos_freeze_after_upgrade( $upgrader_object, $options ) {
  if( isset( $options['plugins'] ) && is_array( $options['plugins'] ) && !empty( $options['plugins'] ) && isset( $options['action'] ) && 'update' === $options['action'] && isset( $options['type'] ) && 'plugin' === $options['type'] ) {
     foreach( $options['plugins'] as $plugin ) {
        if( EOS_FREEZE_PLUGIN_BASE_NAME === $plugin  ){
          delete_site_transient( 'freeze_update_available' );
          break;
        }
     }
  }
}
