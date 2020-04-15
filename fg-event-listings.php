<?php
/**
 * Flare Guide event listings
 *
 * @package      fg-event-listings
 * @author       Igor Honhoff
 * @copyright    2020 Igor Honhoff
 * @license      GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Event listings
 * Description:       Create listings of service providers and programs for an event
 * Version:           0.1.0
 * Author:            Igor Honhoff
 * Text Domain:       fg-listings
 * License:           GPL-3.0-or-later
 * Requires PHP:      7.0
 * Requires WP:       5.3
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'FG_VERSION', '0.1.0' );
// define( 'FG_PREFIX', 'fg_');
define( 'FG_TD', 'fg-listings' );

// Load cependencies
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/cmb2/cmb2/init.php';
require_once __DIR__ . '/vendor/dutchigor/cmb2-image-map/CMB2-image-map.php';
require_once __DIR__ . '/vendor/dutchigor/cmb2-rest-query/CMB2-rest-query.php';
require_once __DIR__ . '/vendor/jcchavezs/cmb2-conditionals/cmb2-conditionals.php';

// Load plugin classes
require_once __DIR__ . '/src/FG_plugin_options.php';
require_once __DIR__ . '/src/FG_listing_init.php';
require_once __DIR__ . '/src/FG_REST_List_field.php';
require_once __DIR__ . '/src/FG_Tag_field.php';
require_once __DIR__ . '/src/FG_listing_cmb.php';
require_once __DIR__ . '/src/FG_listings_app.php';
