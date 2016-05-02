<?php

/**
* The admin-specific functionality of the plugin.
*
* @link       http://photomarketing.it
* @since      1.0.0
*/

/**
* The admin-specific functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the admin-specific stylesheet and JavaScript.
*
* @author     Massimiliano Masserelli <info@photomarketing.it>
*/
class Iomn_Eventi_Admin
{
	/**
	* The ID of this plugin.
	*
	* @since    1.0.0
	*
	* @var string The ID of this plugin.
	*/
	private $plugin_name;

	/**
	* The version of this plugin.
	*
	* @since    1.0.0
	*
	* @var string The current version of this plugin.
	*/
	private $version;

	/**
	* Initialize the class and set its properties.
	*
	* @since    1.0.0
	*
	* @param string $plugin_name The name of this plugin.
	* @param string $version     The version of this plugin.
	*/
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->load_dependencies(	$this->plugin_name, $this->version );

	}

	/**
	* Loads class dependencies
	*
	* @since    1.0.0
	*
	*/
	private function load_dependencies($plugin_name, $version)
	{
		require_once ( plugin_dir_path(__FILE__) . "/../includes/class-iomn-eventi-data.php" );
	}

	/**
	* Register the stylesheets for the admin area.
	*
	* @since    1.0.0
	*/
	public function enqueue_styles()
	{

		/*
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__).'css/iomn-eventi-admin.css', array(), $this->version, 'all');
		wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
		wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
		wp_enqueue_style('jquery-timepicker-style', plugin_dir_url(__FILE__).'css/jquery.ui.timepicker.css', array('jquery-ui'), $this->version, 'all');

	}

	/**
	* Register the JavaScript for the admin area.
	*
	* @since    1.0.0
	*/
	public function enqueue_scripts()
	{

		/*
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__).'js/iomn-eventi-admin.js', array('jquery'), $this->version, false);
		// wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-spinner');
		wp_enqueue_script('jquery-ui-timepicker', plugin_dir_url(__FILE__).'js/jquery.ui.timepicker.js', array('jquery-ui-core'), $this->version, false);
		wp_enqueue_script('jquery-ui-datepicker');

	}

	/**
	* Provides meta box for editing data.
	*
	* @since    1.0.0
	*/
	public function add_meta_box()
	{
		add_meta_box($this->plugin_name, 'Eventi IOMN', array($this, 'render_meta_box'), 'iomn_eventi', 'normal', 'core');
	}

	public function save_meta_box($post_id) {
		$mbdata = new Iomn_Eventi_Data( $post_id );
		$mbdata->load_form_fields();
		$mbdata->save_data();
	}

	/**
	* Renders meta box html.
	*
	* @since    1.0.0
	*/
	public function render_meta_box($post)	{
		$evdata = new Iomn_Eventi_Data($post->ID);
		require plugin_dir_path(__FILE__).'partials/iomn-eventi-admin-render-meta-box.php';
	}
}
