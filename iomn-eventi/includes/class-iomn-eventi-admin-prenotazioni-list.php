<?php
if( ! class_exists( 'WP_List_Table' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Iomn_Eventi_Admin_Prenotazioni_List extends WP_List_Table {

  public function get_columns() {
    return array(
      'date',
      'evento',
      'prenotazioni'
    );
  }

  public function prepare_items() {
    $columns = $this->get_columns();
    $hidden = array();
    $sortable = array();
    $this->_column_headers = array($columns, $hidden, $sortable);
    // $this->items = $this->example_data;;
  }

  function column_default( $item, $column_name ) {
    switch( $column_name ) {
      case 'date':
      case 'evento':
      case 'prenotazioni':
        return $item[ $column_name ];
      default:
        return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
    }
  }

}
