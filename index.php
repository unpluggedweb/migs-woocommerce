<?php
/**
 * Plugin Name: MIGS WooCommerce Integration by Unplugged
 * Plugin URI: http://unpluggedweb.com
 * Description: Adds MIGS Payment Gateway to WooCommerce.
 * Version: 1.0.0
 * Author: Unplugged
 * Author URI: http://unpluggedweb.com
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) exit('busted');

include_once('lib/gateway.php');
include_once('lib/settings.php');
include_once('lib/enqueue-assets.php');
