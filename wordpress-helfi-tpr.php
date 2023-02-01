<?php

/**
  * Plugin Name: Helsinki TPR
  * Description: Integration with the Helsinki TPR API.
  * Version: 1.1.0
  * License: MIT
  * Requires at least: 5.7
  * Requires PHP:      7.1
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

	/**
	  * Constants
	  */
	define( __NAMESPACE__ . '\\PLUGIN_VERSION', '1.1.0' );
	define( __NAMESPACE__ . '\\PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
	define( __NAMESPACE__ . '\\PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	define( __NAMESPACE__ . '\\PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

	/**
	  * Plugin parts
	  */
	require_once 'functions.php';
	textdomain();

	spl_autoload_register( __NAMESPACE__ . '\\autoloader' );

	require_once 'block/unit.php';
	require_once 'ajax/unit.php';
  	require_once 'cpt/unit-config.php';
  	require_once 'cpt/unit-search-table.php';

	/**
	  * Actions & filters
	  */
	//add_action( 'init', __NAMESPACE__ . '\\textdomain' );

	/**
	  * Plugin ready
	  */
	do_action( 'helsinki_tpr_init' );
}
