<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Iomn_Eventi
 * @subpackage Iomn_Eventi/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Iomn_Eventi
 * @subpackage Iomn_Eventi/public
 * @author     Your Name <email@example.com>
 */
class Iomn_Eventi_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_shortcode("iomn-calendar", array($this, 'fullcalendar_shortcode'));
	}


	/**
	* Register calendar shortcode
	**/
	public function fullcalendar_shortcode() {
		// do something useful here, maybe call a partial
		include('partials/iomn-eventi-public-fullcalendar-shortcode.php');
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Iomn_Eventi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Iomn_Eventi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/iomn-eventi-public.css', array(), $this->version, 'all' );
		wp_enqueue_style('iomn-fullcalendar-css', plugin_dir_url( __FILE__ ) . 'css/fullcalendar.css', array(), $this->version, 'all' );
    wp_enqueue_style('iomn-fullcalendar-print-css', plugin_dir_url( __FILE__ ) . 'css/fullcalendar.print.css', array('iomn-fullcalendar-css'), $this->version, 'print');

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Iomn_Eventi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Iomn_Eventi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/iomn-eventi-public.js', array( 'jquery' ), $this->version, false );
    wp_enqueue_script("iomn-bootstrap", plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array('jquery'), $this->version, false );
		wp_enqueue_script('moment', plugin_dir_url( __FILE__ ) . 'js/moment.min.js', array('jquery'), $this->version, false );
    wp_enqueue_script('iomn-fullcalendar-js', plugin_dir_url( __FILE__ ) . 'js/fullcalendar.min.js', array('jquery', 'jquery-ui-core', 'moment'), $this->version, false);
    wp_enqueue_script('iomn-fullcalendar-it-js', plugin_dir_url( __FILE__ ) . 'js/fullcalendar-it.js', array('iomn-fullcalendar-js'), $this->version, false);

	}

}
