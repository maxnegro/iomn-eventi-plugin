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
  public function load_data() {
    if (empty($this->post_id)) {
      return false;
    }
    $data = get_post_meta ( $this->post_id, 'iomn_eventi_data', 'true');
    if (!empty($data)) {
      $this->event = $data;
    }
    $this->where = wp_get_object_terms($this->post_id, 'iomn_strutture');
    return true;
  }

  /**
  * Saves event data to wordpress DB.
  *
  * @since 1.0.0
  */
  public function save_data() {
    if (!empty($this->post_id)) {
      $retcode = update_post_meta( $this->post_id, 'iomn_eventi_data', $this->event );
      wp_set_object_terms($this->post_id, $this->where ,'iomn_strutture');
    } else {
      return false;
    }
  }

  public function load_form_fields() {
    if (empty($this->post_id)) {
      return false;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;
    if (!isset($_POST['iomn_eventi_nonce']) || !wp_verify_nonce($_POST['iomn_eventi_nonce'], '_iomn_eventi_nonce'))
    return;
    if (!current_user_can('edit_post', $post_id))
    return;

    if (isset($_POST['iomn_dove'])) {
      $this->where = esc_attr($_POST['iomn_dove']);
      // wp_set_object_terms($this->post_id, esc_attr($_POST['iomn_dove']) ,'iomn_strutture');
    }
    if (isset($_POST['iomn_medici'])) {
      $this->event['seats']['medici'] = esc_attr($_POST['iomn_medici']);
    }
    if (isset($_POST['iomn_tnfp'])) {
      $this->event['seats']['tnfp'] = esc_attr($_POST['iomn_tnfp']);
    }
    if (isset($_POST['iomn_generici'])) {
      $this->event['seats']['generici'] = esc_attr($_POST['iomn_generici']);
    }

    if (isset($_POST['iomn_ev_data'])) {
      $this->event['when'] = array();
      for ($i = 0; $i < count($_POST['iomn_ev_data']); $i++) {
        $this->event['when'][$i]=array(
          'type' => NULL,
          'date' => NULL,
          'from' => NULL,
          'to' => NULL,
          'location' => NULL
        );
        $date_timestamp = DateTime::createFromFormat('d/m/Y', $_POST['iomn_ev_data'][$i]);
        if (is_object($date_timestamp)) {
          $this->event['when'][$i]['date']=$date_timestamp->getTimeStamp();
        }
        if (isset($_POST['iomn_ev_tipo']) && isset ($_POST['iomn_ev_tipo'][$i])) {
          $this->event['when'][$i]['type']=esc_attr($_POST['iomn_ev_tipo'][$i]);
        }
        if (isset($_POST['iomn_ev_dalle']) && isset ($_POST['iomn_ev_dalle'][$i])) {
          $this->event['when'][$i]['from']=esc_attr($_POST['iomn_ev_dalle'][$i]);
        }
        if (isset($_POST['iomn_ev_alle']) && isset ($_POST['iomn_ev_alle'][$i])) {
          $this->event['when'][$i]['to']=esc_attr($_POST['iomn_ev_alle'][$i]);
        }
        if (isset($_POST['iomn_ev_sala']) && isset ($_POST['iomn_ev_sala'][$i])) {
          $this->event['when'][$i]['location']=esc_attr($_POST['iomn_ev_sala'][$i]);
        }
      }
    }
  }

  public function sessions () {
    return count($this->event['when']);
  }

  public function get_session( $session_id ) {
    if ( isset($this->event['when'][$session_id]) ) {
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
    return 0;
  }

  public function seats( $type ) {
    if (isset($this->event['seats']) && isset($this->event['seats'][$type])) {
      return $this->event['seats'][$type];
    }
    return 0;
  }

  public function vacancies ( $type ) {
    return $this->seats($type) - $this->attendees ($type);
  }

}
