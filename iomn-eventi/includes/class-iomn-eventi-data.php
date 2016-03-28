<?php

/**
 * Used for event data handling
 *
 * @link       http://photomarketing.it
 * @since      1.0.0
 *
 * @package    Iomn_Eventi
 * @subpackage Iomn_Eventi/includes
 */

/**
 * Used for event data handling.
 *
 * This class defines all code necessary to treat event related data.
 *
 * @since      1.0.0
 * @package    Iomn_Eventi
 * @subpackage Iomn_Eventi/includes
 * @author     Massimiliano Masserelli <info@photomarketing.it>
 */
class Iomn_Eventi_Data {

	/**
	 * Event details array.
	 *
	 * Contains all event related info, in a key value multi dimensional array.
   * Structure is like:
   *  when => array
   *    0 => array
   *      description
   *      date
   *      from
   *      to
   *      facility
   *    ..n => array
   *      see above
   *  who => array
   *    type1 => array
   *      0 => attendant
   *      ..n => attendant
   *    ..typen => array
   *  seats => array
   *    type1 => int
   *    ..typen => int
	 *
	 * @since    1.0.0
	 */
	private $event;

  private $post_id;

  /**
	 * Inits event attribute.
	 *
   * @since 1.0.0
   */
  public function __construct( $post_id = NULL) {
    $this->event = array(
      'when' => array(),
      'who' => array(),
      'seats' => array()
    );
    $this->post_id = NULL;

    if ( ! empty ( $post_id )) {
      $this->post_id = $post_id;
      $this->load_data();
    }
  }

  /**
	 * Loads event data from wordpress DB.
	 *
   * @since 1.0.0
   */
  private function load_data() {
    if (empty($this->post_id)) {
      return false;
    }
    $data = get_post_meta ( $this->post_id, 'iomn_eventi_data', 'true');
    if (!empty($data)) {
      $this->event = $data;
      return true;
    }
    return false;
  }

  /**
	 * Saves event data to wordpress DB.
	 *
   * @since 1.0.0
   */
  private function save_data() {
    if (!empty($this->post_id)) {
      return update_post_meta( $this->post_id, 'iomn_eventi_data', $this->event );
    } else {
      return false;
    }
  }

  public function sessions () {
    return count($this->event['when']);
  }

  public function get_session( $session_id ) {
    if ( defined($this->event['when'][$session_id]) ) {
      return $this->event['when'][$session_id];
    } else {
      return NULL;
    }
  }

  public function get_location() {
    if (empty($this->post_id)) {
      return NULL;
    }
    $ospedali = get_terms('iomn_strutture', 'hide_empty=0');
    $selezione = wp_get_object_terms($this->post_id, 'iomn_strutture');
    $dove = "";
    foreach ($ospedali as $ospedale) {
      if (!is_wp_error($selezione) && !empty($selezione) && !strcmp($ospedale->slug, $selezione[0]->slug)) {
        $dove = $ospedale->name;
      }
    }
    return $dove;
  }

  public function locations() {
    if (empty($this->post_id)) {
      return NULL;
    }
    $ospedali = get_terms('iomn_strutture', 'hide_empty=0');
    $selezione = wp_get_object_terms($this->post_id, 'iomn_strutture');
    $dove = "";
    foreach ($ospedali as &$ospedale) {
      if (!is_wp_error($selezione) && !empty($selezione) && !strcmp($ospedale->slug, $selezione[0]->slug)) {
        $ospedale->selected = true;
      }
    }
    return $ospedali;
  }

  public function attendees( $type ) {
    if (defined($this->event['who'] && defined($this->event['who'][$type]))) {
      return count($this->event['who'][$type]);
    }
    return NULL;
  }

  public function seats( $type ) {
    if (defined($this->event['seats'] && defined($this->event['seats'][$type]))) {
      return $this->event['seats'][$type];
    }
    return NULL;
  }

}
