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
		add_filter('manage_iomn_eventi_posts_columns', array($this, 'add_iomn_eventi_columns'));
		add_action('manage_iomn_eventi_posts_custom_column', array($this, 'custom_iomn_eventi_posts_column'), 10, 2);
		// function add_scp_columns($columns) {
		//   return array_merge($columns,
		//   array('scp_category' => 'Categoria')
		// );
		// }
		// add_filter('manage_sc_portfolio_posts_columns', 'add_scp_columns');
		//
		// function custom_sc_portfolio_column($column, $post_id) {
		//   switch ($column) {
		//     case 'scp_category':
		//     $terms = get_the_term_list( $post_id, 'scp_category', '', ',', '');
		//     if (is_string($terms)) {
		//       echo $terms;
		//     } else {
		//       echo "--";
		//     }
		//     break;
		//   }
		//   return;
		// }
		// add_action('manage_sc_portfolio_posts_custom_column', 'custom_sc_portfolio_column', 10, 2);
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

	public function add_user_specialty( $user ) {
		require plugin_dir_path(__FILE__).'partials/iomn-eventi-admin-add-user-specialty.php';
	}

	public function save_user_specialty( $user_id ) {
		if ( !current_user_can( 'edit_user', $user_id ) )
			return false;

		/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
		update_usermeta( $user_id, 'specialty', $_POST['specialty'] );
	}

  public function add_iomn_eventi_columns($columns) {
		return array(
			'title' => 'Titolo',
			'iomn_when' => 'Date',
			'iomn_where' => 'Ospedale',
			'iomn_who' => 'Prenotazioni',
			'date' => 'Inserimento'
		);
	}

	public function custom_iomn_eventi_posts_column($column, $post_id) {
		$evdata = new Iomn_Eventi_Data($post_id);
		switch ($column) {
			case 'iomn_when':
			  for ($i=0; $i<$evdata->sessions(); $i++) {
					$session = $evdata->get_session($i);
					echo date('d/m/Y', $session['date']) . "<br />\n";
				}
			break;
			case 'iomn_where':
				echo $evdata->get_location();
			break;
			case 'iomn_who':
			  if ($evdata->seats('medici') > 0) {
			  	printf("Medici: %d/%d<br />\n", $evdata->attendees('medici'), $evdata->seats('medici'));
				}
				if ($evdata->seats('tnfp') > 0) {
					printf("TNFP: %d/%d<br />\n", $evdata->attendees('tnfp'), $evdata->seats('tnfp'));
				}
				if ($evdata->seats('generici') > 0) {
					printf("Generici: %d/%d<br />\n", $evdata->attendees('generici'), $evdata->seats('generici'));
				}
			break;
		}
	}
}
