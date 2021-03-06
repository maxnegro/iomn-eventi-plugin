<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://photomarketing.it
 * @since             1.0.0
 * @package           Iomn_Eventi
 *
 * @wordpress-plugin
 * Plugin Name:       Gestione eventi Scuola IOMN ER
 * Plugin URI:        http://photomarketing.it/iomn-eventi/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Massimiliano Masserelli
 * Author URI:        http://photomarketing.it/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       iomn-eventi
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-iomn-eventi-activator.php
 */
function activate_iomn_eventi() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-iomn-eventi-activator.php';
	Iomn_Eventi_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-iomn-eventi-deactivator.php
 */
function deactivate_iomn_eventi() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-iomn-eventi-deactivator.php';
	Iomn_Eventi_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_iomn_eventi' );
register_deactivation_hook( __FILE__, 'deactivate_iomn_eventi' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-iomn-eventi.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_iomn_eventi() {

	$plugin = new Iomn_Eventi();
	$plugin->run();

}
run_iomn_eventi();
