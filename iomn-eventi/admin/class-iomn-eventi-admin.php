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
		add_action('admin_menu', array($this,'add_submenu'));

		$this->options = new Iomn_Eventi_Admin_Options();
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
		require_once ( plugin_dir_path(__FILE__) . "/../includes/class-iomn-eventi-admin-prenotazioni-list.php" );
		require_once ( plugin_dir_path(__FILE__) . "/../includes/class-iomn-eventi-admin-options.php" );
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

	public function add_submenu() {
		add_submenu_page(
			'edit.php?post_type=iomn_eventi',
			'Gestione Prenotazioni Eventi',
			'Prenotazioni',
			'edit_posts',
			'prenotazioni',
			array($this, 'render_prenotazioni')
		);
	}

	public function render_prenotazioni() {
		global $wpdb;
		$list = new Iomn_Eventi_Admin_Prenotazioni_List();
		if ('confirmdelete' == $list->current_action()) {
			if (wp_verify_nonce($_REQUEST['_confirm_delete_nonce'], 'delete')) {
				$wpdb->delete(
					$wpdb->prefix . "iomn_eventi_prenotazioni",
					array( "ID" => $_REQUEST['reservation']),
					array( "%d" )
				);
				// TODO: inviare mail di notifica ad utente ed amministratori
			}
			wp_redirect( home_url('wp-admin/edit.php?post_type=iomn_eventi&page=prenotazioni') , 301);
			exit();
		}
		echo '<div class="wrap"><h1>Prenotazioni</h1>';
		if ('delete' == $list->current_action()) {
			$data = $wpdb->get_results($wpdb->prepare(
				"SELECT r.ID AS ID, r.time AS resdate, r.specialty AS specialty, p.ID AS postID, r.id_user AS userID, ".
				"       p.post_title AS evento, u.user_email AS email, CONCAT(um1.meta_value, ' ', um2.meta_value) AS name, pm.meta_value AS evstartdate ".
				" FROM wp_iomn_eventi_prenotazioni AS r ".
				" INNER JOIN wp_posts AS p ON r.id_evento = p.ID ".
				" INNER JOIN wp_users AS u ON r.id_user = u.ID ".
				" INNER JOIN wp_postmeta AS pm ON p.ID = pm.post_id AND pm.meta_key = 'iomn_eventi_data_sort' ".
				" INNER JOIN wp_usermeta AS um1 ON u.ID = um1.user_id AND um1.meta_key = 'first_name' ".
				" INNER JOIN wp_usermeta AS um2 ON u.ID = um2.user_id AND um2.meta_key = 'last_name' ".
				" WHERE r.id = %d ",
				$_REQUEST['reservation']
			),
			ARRAY_A);
			if (count($data) > 0) {
				echo "<p>È stata richiesta la cancellazione della prenotazione:</p>\n";
				printf("<blockquote>");
				printf("Prenotazione effettuata da <b>%s</b> il %s ", $data[0]['name'], $data[0]['resdate']);
				printf("per l'evento <b>\"%s\"</b> in programma dal giorno %s.\n", $data[0]['evento'], date('d/m/Y', $data[0]['evstartdate']));
				printf("</blockquote>");
				printf("<form action=%s method=\"GET\">", 'edit.php');
				printf('<input type="hidden" name="%s" value="%s">', 'post_type', 'iomn_eventi');
				printf('<input type="hidden" name="%s" value="%s">', 'page', 'prenotazioni');
				printf('<input type="hidden" name="%s" value="%s">', 'action', 'confirmdelete');
				printf('<input type="hidden" name="%s" value="%s">', 'noheader', 'true');
				wp_nonce_field( 'delete', '_confirm_delete_nonce', false, true );
				printf('<input type="hidden" name="%s" value="%s">', 'reservation', $data[0]['ID']);
				printf('<input type="submit" name="submit" id="submit" class="button button-primary" value="Conferma cancellazione">');
				printf("</form>");
				printf("<p><b>ATTENZIONE</b>: la cancellazione è definitiva. Una volta confermata non sarà più possibile recuperarla. Sarà inviata una mail di notifica all'indirizzo %s</p>", $data[0]['email']);
			} else {
				echo "È stata richiesta la cancellazione di una prenotazione non esistente. L'operazione non può essere completata.";
			}

		}  else {
			$list->prepare_items();
			$list->display();
		}
		echo '</div>';
		return true;
	}

}
