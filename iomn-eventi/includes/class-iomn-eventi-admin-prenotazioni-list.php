<?php
if( ! class_exists( 'WP_List_Table' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Iomn_Eventi_Admin_Prenotazioni_List extends WP_List_Table {

  public function get_columns() {
    return array(
      'name' => 'Nome e Cognome',
      'specialty' => "Specializzazione",
      'evento' => "Evento",
      'date' => "Date",
      'email' => "Email",
      'resdate' => "Data prenotazione",
    );
  }

  public function prepare_items() {
    $columns = $this->get_columns();
    $hidden = array();
    $sortable = array();
    $this->_column_headers = array($columns, $hidden, $sortable);

    global $wpdb;
    // $this->orderby, $this->order vanno presi in considerazione per l'ordinamento automatico
    $data = $wpdb->get_results($wpdb->prepare(
      "SELECT r.ID AS ID, r.time AS resdate, r.specialty AS specialty, p.ID AS postID, r.id_user AS userID, ".
      "       p.post_title AS evento, u.user_email AS email, CONCAT(um1.meta_value, ' ', um2.meta_value) AS name ".
      " FROM wp_iomn_eventi_prenotazioni AS r ".
      " INNER JOIN wp_posts AS p ON r.id_evento = p.ID ".
      " INNER JOIN wp_users AS u ON r.id_user = u.ID ".
      " INNER JOIN wp_postmeta AS pm ON p.ID = pm.post_id AND pm.meta_key = 'iomn_eventi_data_sort' ".
      " INNER JOIN wp_usermeta AS um1 ON u.ID = um1.user_id AND um1.meta_key = 'first_name' ".
      " INNER JOIN wp_usermeta AS um2 ON u.ID = um2.user_id AND um2.meta_key = 'last_name' ".
      " WHERE pm.meta_value >= %d ". // TODO: Verificare se ha senso nascondere la roba vecchia
      " ORDER BY pm.meta_value, p.post_title, r.time ",
      time()
    ),
    ARRAY_A);
    // var_dump($data);
    $current_page = $this->get_pagenum();
    $per_page=10;
    $total_items = count($data);
    $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
    foreach ($data as $key => $value) {
      $ev = new Iomn_Eventi_Data($value['postID']);
      $dtext = "";
      for ($i=0; $i < $ev->sessions(); $i++) {
        $dtext .= date("d/m/Y", $ev->get_session($i)['date']) . "<br />";
      }
      $data[$key]['date'] = $dtext;
    }
    $this->items = $data;
  }

  function column_default( $item, $column_name ) {
    switch( $column_name ) {
      case 'date':
      case 'evento':
      case 'specialty':
      case 'resdate':
        return $item[ $column_name ];
        break;
      case 'name':
        $actions = array(
          // 'edit'      => sprintf('<a href="?page=%s&action=%s&book=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
          'delete'    => sprintf('<a href="?post_type=iomn_eventi&page=%s&action=%s&reservation=%s">Cancella</a>',$_REQUEST['page'],'delete',$item['ID']),
        );
        return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions) );
        break;
      case 'email':
        return sprintf('<a href="mailto:%s">%s</a>', $item['email'], $item['email']);
        break;
      default:
        return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
    }
  }

}
