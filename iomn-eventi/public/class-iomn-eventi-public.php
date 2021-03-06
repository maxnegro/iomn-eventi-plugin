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

		add_action('init', array($this, 'init'));

	}

	public function init() {
		add_feed('iomn-eventi-json', array($this, 'json_feed'));
		add_shortcode('iomn-calendar', array($this, 'fullcalendar_shortcode'));
		add_filter('single_template', array($this, 'get_single_iomn_eventi_template'));

		// Reserve form ajax function
		// add_action( 'wp_ajax_nopriv_ajaxreserve_send_mail', array($this, 'iomn_reserve_ajax') );
		add_action( 'wp_ajax_ajaxreserve_send_mail', array($this, 'iomn_reserve_ajax') );
		add_action( 'wp_ajax_ajaxcancel', array($this, 'iomn_cancel_ajax') );

	}

  public function get_single_iomn_eventi_template($single_template) {
		global $post;
		if ($post->post_type == 'iomn_eventi') {
			$single_template = dirname( __FILE__ ) . '/../templates/single-iomn_eventi.php';
		}
		return $single_template;
	}

	public function json_feed() {
		$user = isset($_POST['userId']) ? get_userdata($_POST['userId']) : false;

		// - grab date barrier -
    $today = strftime('%F %R', strtotime('today 0:00'));

    $args = array('post_type' => 'iomn_eventi', 'post_status' => 'publish');
    $query = new WP_query($args);

    $jsonevents = array(
        // array(
            // 'title' => 'Prova 1',
            // 'start' => '2015-11-21T09:00Z',
            // 'end' => '2015-11-21T17:00Z',
            // 'url' => 'http://www.google.com/',
            // 'description' => "Tutto il resto, \nanche su più righe"
        // )
    );

    $posts = $query->get_posts();
    foreach ($posts as $post) {
			$mbdata = new Iomn_Eventi_Data( $post->ID );
			// var_dump($mbdata);
			if ($mbdata->sessions() > 0) {
				$tdinfo = "";
				$vacancies = 0;
				for ($i = 0; $i < $mbdata->sessions(); $i++) {
					$ce = $mbdata->get_session($i);
					$tdinfo .= "<strong>". date('d/m/Y', $ce['date']) . "</strong> " . $ce['from'] . "-" . $ce['to'] . "<br />";
					if ($ce['date'] < time()) {
						$vacancies = -1;
					}
				}
				if (($vacancies == 0) && $user) {
					$vacancies = $mbdata->vacancies($user->get('specialty')) + $mbdata->vacancies('generici');
					// echo $vacancies . "\n";
				}
				$evdata = array(
					'dove' => $mbdata->get_location(),
					'quando' => $tdinfo,
					'descrizione' => $post->post_content
				);
				for ($i = 0; $i < $mbdata->sessions(); $i++) {
					$ce = $mbdata->get_session($i);
					$start = date('Y-m-d', $ce['date']) . 'T' . $ce['from'] . 'Z';
					$end = date('Y-m-d', $ce['date']) . 'T' . $ce['to'] . 'Z';

					// - json items -
					if ($vacancies < 0) {
						$color = '#999999';
					} elseif ($mbdata->reservedby($user->ID)) {
						$color = '#C0A000';
					} elseif ($vacancies == 0) {
						$color = '#990000';
					} else {
						$color = '#009900';
					}
					$jsonevents[] = array(
							'title' => $post->post_title, // . " (preoperatoria)",
							'allDay' => false, // <- true by default with FullCalendar
							'start' => $start,
							'end' => $end,
							'url' => get_permalink($post->ID),
							'description' => $this->_renderEventDescription($evdata),
							'color' => $color,
							'vacancies' => $vacancies,
							'attending' => $mbdata->reservedby($user->ID)
					);
				}
			}
    }
    // - fire away -
    wp_send_json($jsonevents);
	}

	public function _renderEventDescription($data) {
		$html = "";
	  $html .= "<div>" .$data['quando'] . "</div>\n";
	  $html .= "<div style=\"margin-top: 1em; margin-bottom: 1em;\">Presso: " . $data['dove'] . "</div>\n";
	  // $html .= "<div style=\"margin-bottom: 1em;\">" . $data['descrizione'] . "</div>\n";
		// $html .= "<div>\n";
		// if ($data['gentot'] + $data['tnfptot'] + $data['medtot'] > 0) {
		// 	$html .= "Posti disponibili: ";
		// }
		// if ($data['gentot'] > 0) {
		// 	$html .= sprintf("Generici: %s/%s ", $data['gendispo'], $data['gentot']);
		// }
		// if ($data['tnfptot'] > 0) {
		// 	$html .= sprintf("TNFP: %s/%s ", $data['tnfpdispo'], $data['tnfptot']);
		// }
		// if ($data['medtot'] > 0) {
		// 	$html .= sprintf("Medici: %s/%s ", $data['meddispo'], $data['medtot']);
		// }
	  // <div>Posti disponibili: TNFP: {$data['tnfpdispo']}/{$data['tnfptot']} Medici: {$data['meddispo']}/{$data['medtot']}</div>
		// $html .= "</div>\n";
	  return $html;
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
		wp_enqueue_style('iomn-bootstrap-css', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
		wp_enqueue_style('iomn-fullcalendar-css', plugin_dir_url( __FILE__ ) . 'css/fullcalendar.min.css', array(), $this->version, 'all' );
    wp_enqueue_style('iomn-fullcalendar-print-css', plugin_dir_url( __FILE__ ) . 'css/fullcalendar.print.css', array('iomn-fullcalendar-css'), $this->version, 'print');
		wp_enqueue_style('iomn-eventi-post', plugin_dir_url( __FILE__ ) . 'css/iomn-eventi-post.css', array(), $this->version, 'all');

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

	public function iomn_reserve_ajax() {
		$user = wp_get_current_user();
		$post_id = $_POST['acfpostid'];
		$post = get_post($post_id);
		$evdata = new Iomn_Eventi_Data($post_id);
	  $results = '';
	  $error = 0;
	  $name = $user->get('first_name') . " " . $user->get('last_name');
		if (empty($name)) {
			$name = $user->get('display_name');
		}
	  $email = $_POST['acfemail'];
	  $typedecode = array('medici' => 'medico', 'tnfp' => 'TNFP', 'generici' => 'generico');
	  $type = $_POST['acftype'];
		$subject = "Prenotazione attività formativa IOMN";
	  // $confirmurl = add_query_arg(array("iomnconfirm" => "12345"), home_url());
	  $contents  = "\n";
		$contents .= sprintf("Salve %s,\n", $name);
		$contents .= sprintf("  grazie per aver effettuato la prenotazione per l'evento formativo \"%s\" in qualità di studente %s.\n", $post->post_title, $typedecode[$type]);
		$contents .= "\n";
		$contents .= sprintf("Ti ricordiamo che l'evento si terrà nei seguenti giorni ed orari presso %s.\n", $evdata->get_location());
		$contents .= "\n";
		for ($i=0; $i<$evdata->sessions(); $i++) {
			$session = $evdata->get_session($i);
			$contents .= sprintf("  %s dalle %s alle %s\n", date('d/m/Y', $session['date']), $session['from'], $session['to']);
		}
		$contents .= "\n";
		$contents .= "Grazie ancora e buona giornata.\n";
		$contents .= "-- \n";
		$contents .= "Lo staff\n";

	  $admin_email = get_option('admin_email');
		if ('publish' != get_post_status($post_id)) {
			$results = "Evento sconosciuto, contattare l'amministratore di sistema.\n";
			$error = 1;
		}
	  // elseif( strlen($name) == 0 )
	  // {
	  //   $results = "È necessario compilare il campo \"Nome e cognome\".";
	  //   $error = 1;
	  // }
		elseif ( 'true' != $email) {
			$results = "Spuntare la casella di verifica dell'indirizzo email per procedere con la prenotazione.";
			$error = 1;
		}
	  elseif (!filter_var($user->get('user_email'), FILTER_VALIDATE_EMAIL))
	  {
	    $results = $user->get('user_email')." : indirizzo email non valido, modificarlo nel proprio profilo o contattare la segreteria.";
	    $error = 1;
	  }

	  if($error == 0)
	  {
			$evdata->subscribe($user->ID, $type); // TODO check return value
			$headers = 'From:'.$admin_email. "\r\n";

	    if(wp_mail($user->get('user_email'), $subject, $contents, $headers)) {
	      $results = "Grazie per la prenotazione. Arriverà una mail all'indirizzo indicato con la conferma dell'avvenuta prenotazione. Non sono necessarie altre azioni da parte tua.";
	    } else {
	      $results = "Non è stato possibile inviare la mail di prenotazione.";
	      $error = 1;
	    }
			$adminNotifyText  = "";
			$adminNotifyText .= sprintf("È stata effettuata una nuova prenotazione per l'evento \"%s\" da parte dell'utente %s %s.\n", $post->post_title, $user->first_name, $user->last_name);
			$adminNotifyText .= "\n";
			$adminNotifyText .= "L'evento è in programma per i seguenti giorni ed orari:\n";
			$adminNotifyText .= "\n";
			for ($i=0; $i<$evdata->sessions(); $i++) {
				$adminNotifyText = $evdata->get_session($i);
				$adminNotifyText .= sprintf("  %s dalle %s alle %s\n", date('d/m/Y', $session['date']), $session['from'], $session['to']);
			}
			$adminNotifyText .= "\n";
			if ($error == 0) {
				$adminNotifyText .= "All'utente è stata inviata una mail di notifica.\n\n";
			} else {
				$adminNotifyText .= "Non è stato possibile inviare una mail di notifica all'utente.\n\n";
			}
			$adminNotifyText .= "Ulteriori dettagli sono disponibili nella console di amministrazione del sito.\n";
			$adminNotifyText .= "-- \n";
			$adminNotifyText .= "Il sistema di prenotazione online\n";
			$adminAddresses = get_option("notify");
			if (is_array($adminAddresses)) {
				wp_mail($adminAddresses, $subject, $adminNotifyText, $headers);
			}
	  }

		// Return the result message as a JSON array
	  header( "Content-Type: application/json" );
	  die(json_encode(array( 'isValid' => $error == 0, 'message' => $results)));
	}

	public function iomn_cancel_ajax() {
		$user = wp_get_current_user();
		$post_id = $_POST['acfpostid'];
		$post = get_post($post_id);
		$evdata = new Iomn_Eventi_Data($post_id);
	  $results = '';
	  $error = 0;
	  $name = $user->get('first_name') . " " . $user->get('last_name');
		if (empty($name)) {
			$name = $user->get('display_name');
		}
	  $check = $_POST['acfcheck'];
		$notes = $_POST['acfnotes'];
		$subject = "Cancellazione prenotazione attività formativa IOMN";
	  // $confirmurl = add_query_arg(array("iomnconfirm" => "12345"), home_url());
	  $contents  = "\n";
		$contents .= sprintf("Salve %s,\n", $name);
		$contents .= sprintf("  ti inviamo questo messaggio per confermare l'avvenuta disdetta della prenotazione per l'evento formativo \"%s\".\n", $post->post_title);
		$contents .= "\n";
		$contents .= sprintf("Hai accompagnato la disdetta con queste note:\n%s\n", $notes);
		$contents .= "----------\n\n";
		$contents .= "La disdetta è definitiva e non è necessaria altra attività da parte tua. Tuttavia se ritieni che questo messaggio ti sia arrivato per errore ti invitiamo a contattare immediatamente la segreteria della scuola.\n";
		$contents .= "\n";
		$contents .= "Grazie ancora e buona giornata.\n";
		$contents .= "-- \n";
		$contents .= "Lo staff\n";

	  $admin_email = get_option('admin_email');
		if ('publish' != get_post_status($post_id)) {
			$results = "Evento sconosciuto, contattare l'amministratore di sistema.\n";
			$error = 1;
		}
	  // elseif( strlen($name) == 0 )
	  // {
	  //   $results = "È necessario compilare il campo \"Nome e cognome\".";
	  //   $error = 1;
	  // }
		elseif ( 'true' != $check) {
			$results = "Spuntare la casella di verifica per procedere con la cancellazione.";
			$error = 1;
		}
		elseif (empty($notes)) {
			$results = "Compilare il campo Note con le ragioni della cancellazione.";
			$error = 1;
		}
	  elseif (!filter_var($user->get('user_email'), FILTER_VALIDATE_EMAIL))
	  {
	    $results = $user->get('user_email')." : indirizzo email non valido, modificarlo nel proprio profilo o contattare la segreteria.";
	    $error = 1;
	  }

		$evdata->unsubscribe($user->ID, $type); // TODO check return value

	  if($error == 0)
	  {
	    $headers = 'From:'.$admin_email. "\r\n";
	    if(wp_mail($user->get('user_email'), $subject, $contents, $headers)) {
	      $results = "Grazie per la comunicazione. Arriverà una mail all'indirizzo indicato con la conferma di avvenuta cancellazione della prenotazione.";
	    } else {
	      $results = "Non è stato possibile inviare la mail di conferma.";
	      $error = 1;
	    }
	  }
	  // Return the String
	  header( "Content-Type: application/json" );
	  die(json_encode(array( 'isValid' => $error == 0, 'message' => $results)));
	}

}
