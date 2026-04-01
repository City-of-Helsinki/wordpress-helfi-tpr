<?php

/**
  * Plugin Name: Helsinki TPR
  * Description: Integration with the Helsinki TPR API.
  * Version: 3.0.0
  * License: GPLv3
  * Requires at least: 6.9
  * Requires PHP:      8.0
  * Author: Broomu Digitals
  * Author URI: https://www.broomudigitals.fi
  * Text Domain: helsinki-tpr
  * Domain Path: /languages
  */

namespace CityOfHelsinki\WordPress\TPR;

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\init', 100 );
function init() {
	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	$pluginData = get_plugin_data( __FILE__, false, false );
	$dir = plugin_dir_path( __FILE__ );

	/**
	  * Constants
	  */
	define( __NAMESPACE__ . '\\PLUGIN_VERSION', $pluginData['Version'] );
	define( __NAMESPACE__ . '\\PLUGIN_PATH', $dir );
	define( __NAMESPACE__ . '\\PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	define( __NAMESPACE__ . '\\PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

	/**
	  * Plugin parts
	  */
	require_once $dir . 'functions.php';

	spl_autoload_register( __NAMESPACE__ . '\\autoloader' );

	require_once $dir . 'features/blocks/register.php';
	require_once $dir . 'features/ajax/unit.php';
  	require_once $dir . 'cpt/providers.php';
  	require_once $dir . 'cpt/unit-config.php';

	/**
	  * Plugin ready
	  */
	do_action( 'helsinki_tpr_init' );
}

add_action( 'init', __NAMESPACE__ . '\\textdomain' );
