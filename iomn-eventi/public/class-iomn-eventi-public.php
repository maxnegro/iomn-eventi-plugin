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
		add_filter('the_content', array($this, 'single_post_filter'));
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
					$tdinfo .= date('d/m/Y', $ce['date']) . " " . $ce['from'] . "-" . $ce['to'] . " " . $ce['location'] . "<br />";
				}
				$evdata = array(
					'dove' => $mbdata->get_location(),
					'quando' => $tdinfo,
					'descrizione' => $post->post_content,
					'gentot' => $mbdata->seats('generici'),
					'gendispo' => $mbdata->seats('generici') - $mbdata->attendees('generici'),
					'tnfptot' => $mbdata->seats('tnfp'),
					'tnfpdispo' => $mbdata->seats('tnfp') - $mbdata->attendees('tnfp'),
					'medtot' => $mbdata->seats('medici'),
					'meddispo' => $mbdata->seats('medici') - $mbdata->attendees('medici')
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
		// - fire away -
		wp_send_json($jsonevents);
	}

	public function _renderEventDescription($data) {
		$html = "";
		$html .= "<div>" .$data['quando'] . "</div>\n";
		$html .= "<div style=\"margin-top: 1em; margin-bottom: 1em;\">" . $data['dove'] . "</div>\n";
		$html .= "<div style=\"margin-bottom: 1em;\">" . $data['descrizione'] . "</div>\n";
		$html .= "<div>\n";

		if ($data['gentot'] + $data['tnfptot'] + $data['medtot'] > 0) {
			$html .= "Posti disponibili: ";
		}
		if ($data['gentot'] > 0) {
			$html .= sprintf("Generici: %s/%s ", $data['gendispo'], $data['gentot']);
		}
		if ($data['tnfptot'] > 0) {
			$html .= sprintf("TNFP: %s/%s ", $data['tnfpdispo'], $data['tnfptot']);
		}
		if ($data['medtot'] > 0) {
			$html .= sprintf("Medici: %s/%s ", $data['meddispo'], $data['medtot']);
		}
		// <div>Posti disponibili: TNFP: {$data['tnfpdispo']}/{$data['tnfptot']} Medici: {$data['meddispo']}/{$data['medtot']}</div>
		$html .= "</div>\n";
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

	public function single_post_filter($content) {
		if (is_singular('iomn_eventi')) {
			$new_content = $this->single_post_html();
			$content = $new_content;
		}
		return $content;
	}

	public function single_post_html() {
		wp_enqueue_style('iomn-bootstrap-css', plugins_url('css/bootstrap.min.css', __FILE__));
		wp_enqueue_script("iomn-bootstrap", plugins_url('js/bootstrap.min.js', __FILE__), array('jquery'));
		wp_enqueue_script('ajaxreserve', plugins_url('js/ajaxreserveajax.js', __FILE__), array('jquery'));
		wp_localize_script('ajaxreserve', 'iomn_reserve_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

		global $post;
		$evdata = new Iomn_Eventi_Data($post->ID);
		$data["pre_data"] = iomn_get_meta('iomn_pre_data');
		$data["pre_dalle"] = iomn_get_meta('iomn_pre_dalle');
		$data["pre_alle"] = iomn_get_meta('iomn_pre_alle');
		$data["pre_sala"] = iomn_get_meta('iomn_pre_sala');
		$data["op_data"] = iomn_get_meta('iomn_op_data');
		$data["op_dalle"] = iomn_get_meta('iomn_op_dalle');
		$data["op_alle"] = iomn_get_meta('iomn_op_alle');
		$data["op_sala"] = iomn_get_meta('iomn_op_sala');
		$data["descrizione"] = get_the_content();
		$ospedali = get_terms('iomn_strutture', 'hide_empty=0');
		$selezione = wp_get_object_terms($post->ID, 'iomn_strutture');
		foreach ($ospedali as $ospedale) {
			if (!is_wp_error($selezione) && !empty($selezione) && !strcmp($ospedale->slug, $selezione[0]->slug)) {
				$data["ospedale"] = $ospedale->name; // L'ospedale dovrebbe essere unico
			}
		}
		$prenotazione = new IomnPrenotazione($post->ID);
		$data['tnfptot'] = $prenotazione->disponibili('tnfp');
		$data['tnfpdispo'] = $prenotazione->disponibili('tnfp') - $prenotazione->iscritti('tnfp');
		$data['medtot'] = $prenotazione->disponibili('medici');
		$data['meddispo'] = $prenotazione->disponibili('medici') - $prenotazione->iscritti('medici');

		$html = <<<"ENDHTML"
		<div class="iomn-container">
		<div class="iomn-location-detail"><big>
		{$evdata["ospedale"]}
		</big></div>
		<div class="iomn-date-detail">
		<div><strong><big>{$data["pre_data"]}</big></strong></div>
		<div>{$data["pre_dalle"]}-{$data["pre_alle"]}</div>
		<div class="iomn-op">Pre-operatorio</div>
		<div><em>{$data["pre_sala"]}</em></div>
		</div>
		<div  class="iomn-date-detail">
		<div><strong><big>{$data["op_data"]}</big></strong></div>
		<div>{$data["op_dalle"]}-{$data["op_alle"]}</div>
		<div class="iomn-op">Operatorio</div>
		<div><em>{$data["op_sala"]}</em></div>
		</div>
		</div>
		<hr />
		<div>
		{$data["descrizione"]}
		</div>
		<hr />
		<div>
		<h4>Posti disponibili</h4>
		Medici: {$data["meddispo"]}/{$data["medtot"]} <button id="iomn_button_reserve_med" class="btn btn-success" onclick="{
			jQuery('#modalTitle').html('Prenotazione Medico');
			jQuery('#ajaxcontacttype').val('med');
			jQuery('#ajaxcontact-form').show();
			jQuery('#ajaxSubmit').show();
			jQuery('#ajaxcontact-response').html('');
			jQuery('#iomnReserveModal').modal();
		};">Prenota</button>
		-
		TNFP: {$data["tnfpdispo"]}/{$data["tnfptot"]} <button id="iomn_button_reserve_med" class="btn btn-success" onclick="{
			jQuery('#modalTitle').html('Prenotazione TNFP');
			jQuery('#ajaxcontacttype').val('tnfp');
			jQuery('#ajaxcontact-form').show();
			jQuery('#ajaxSubmit').show();
			jQuery('#ajaxcontact-response').html('');
			jQuery('#iomnReserveModal').modal();
		};">Prenota</button>
		</div>
		<div id="iomnReserveModal" class="modal fade">
		<div class="modal-dialog">
		<div class="modal-content">
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
		<h4 id="modalTitle" class="modal-title"></h4>
		</div>
		<div id="modalBody" class="modal-body">
		<div id="ajaxcontact-response" style="background-color:#E6E6FA ;color:blue;"></div>
		<div id="ajaxcontact-form">
		<form id="iomn-ajax-form" action="" method="post" enctype="multipart/form-data">
		<input id="ajaxcontacttype" type="hidden" name="ajaxcontacttype" value="">
		<div id="ajaxcontact-text">
		<strong>Nome e cognome </strong> <br/>
		<input type="text" id="ajaxcontactname" name="ajaxcontactname"/><br />
		<br/>
		<strong>Email </strong> <br/>
		<input type="text" id="ajaxcontactemail" name="ajaxcontactemail"/><br />
		<br/>
		</div>
		</form>
		</div>
		</div>
		<div class="modal-footer">
		<button  style="width: initial; " type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
		<a class="btn btn-primary" role="button" id="ajaxSubmit" onclick="ajaxformsendmail(ajaxcontactname.value,ajaxcontactemail.value,ajaxcontacttype.value);">Prenota</a>
		</div>
		</div>
		</div>
		</div>
ENDHTML;
		return $html;

	}
}
