<?php

/**
 * Fired during plugin activation
 *
 * @link       http://photomarketing.it
 * @since      1.0.0
 *
 * @package    Iomn_Eventi
 * @subpackage Iomn_Eventi/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Iomn_Eventi
 * @subpackage Iomn_Eventi/includes
 * @author     Massimiliano Masserelli <info@photomarketing.it>
 */
class Iomn_Eventi_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$iomn_eventi_db_version = 4.0;
		$installed_version = get_option('iomn_eventi_db_version');
		if ( $installed_version != $iomn_eventi_db_version ) {
			$table_name = $wpdb->prefix . 'iomn_eventi_prenotazioni';
			$sql = "CREATE TABLE $table_name (
								id mediumint(9) NOT NULL AUTO_INCREMENT,
								time datetime  NOT NULL,
								id_evento bigint(20) NOT NULL,
								id_user bigint(20) NOT NULL,
								specialty VARCHAR(80),
								UNIQUE KEY id (id)
			);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			update_option('iomn_eventi_db_version', $iomn_eventi_db_version);
		}

	}

}
