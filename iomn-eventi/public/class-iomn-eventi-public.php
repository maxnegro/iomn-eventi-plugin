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
	}

	public function json_feed() {
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
				for ($i = 0; $i < $mbdata->sessions(); $i++) {
					$ce = $mbdata->get_session($i);
					$tdinfo .= date('d/m/Y', $ce['date']) . " " . $ce['from'] . " " . $ce['to'] . " " . $ce['location'] . "<br />";
				}
				$evdata = array(
					'dove' => $mbdata->get_location(),
					'quando' => $tdinfo,
					'descrizione' => $post->post_content,
					'tnfptot' => $mbdata->attendees('tnfp'),
					'tnfpdispo' => $mbdata->seats('tnfp'),
					'medtot' => $mbdata->attendees('med'),
					'meddispo' => $mbdata->seats('med')
				);
				for ($i = 0; $i < $mbdata->sessions(); $i++) {
					$ce = $mbdata->get_session($i);
					$start = date('Y-m-d', $ce['date']) . 'T' . $ce['from'] . 'Z';
					$end = date('Y-m-d', $ce['date']) . 'T' . $ce['to'] . 'Z';

					// - json items -
					$jsonevents[] = array(
							'title' => $post->post_title, // . " (preoperatoria)",
							'allDay' => false, // <- true by default with FullCalendar
							'start' => $start,
							'end' => $end,
							'url' => get_permalink($post->ID),
							'description' => $this->_renderEventDescription($evdata),
							'color' => '#000099'
					);
				}
			}
    }
// // - query -
//     global $wpdb;
//     $querystr = "
//         SELECT *
//         FROM $wpdb->posts wposts, $wpdb->postmeta metastart, $wpdb->postmeta metaend
//         WHERE (wposts.ID = metastart.post_id AND wposts.ID = metaend.post_id)
//         AND (metaend.meta_key = '_end_timestamp' AND metaend.meta_value > '$today' )
//         AND metastart.meta_key = '_start_timestamp'
//         AND wposts.post_type = 'event'
//         AND wposts.post_status = 'publish'
//         ORDER BY metastart.meta_value ASC LIMIT 500
//      ";
//
//     $events = $wpdb->get_results($querystr, OBJECT);
//
//     // - loop -
//     if ($events) {
//         global $post;
//         foreach ($events as $post) {
//             setup_postdata($post);
//
//             // - custom post type variables -
//             $custom = get_post_custom(get_the_ID());
//             $start = $custom["_start_timestamp"][0];
//             $end = $custom["_end_timestamp"][0];
//
//             $gmts = get_gmt_from_date($gmts); // this function requires Y-m-d H:i:s
//             $gmts = strtotime($gmts);
//
//             // - grab gmt for end -
//             $gmte = date('Y-m-d H:i:s', strtotime($end));
//             $gmte = get_gmt_from_date($gmte); // this function requires Y-m-d H:i:s
//             $gmte = strtotime($gmte);
//
//             // - set to ISO 8601 date format -
//             $stime = date('c', $gmts);
//             $etime = date('c', $gmte);
//
//             // - json items -
//             $jsonevents[] = array(
//                 'title' => $post->post_title,
//                 'allDay' => false, // <- true by default with FullCalendar
//                 'start' => $stime,
//                 'end' => $etime,
//                 'url' => get_permalink($post->ID)
//             );
//         }
//     }
    // - fire away -
    wp_send_json($jsonevents);
	}

	public function _renderEventDescription($data) {
	  $html = <<<"ENDHTML"
	  <div>{$data['quando']}</div>
	  <div style="margin-bottom: 1em;">{$data['dove']}</div>
	  <div style="margin-bottom: 1em;">{$data['descrizione']}</div>
	  <div>Posti disponibili: {$data['tnfpdispo']}/{$data['tnfptot']} TNFP {$data['meddispo']}/{$data['medtot']} Medici</div>
ENDHTML;
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
