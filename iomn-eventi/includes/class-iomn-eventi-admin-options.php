<?php
class Iomn_Eventi_Admin_Options {
  /**
  * Holds the values to be used in the fields callbacks
  */
  private $options;

  /**
  * Start up
  */
  public function __construct()   {
    add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
    add_action( 'admin_init', array( $this, 'page_init' ) );
  }

  /**
  * Add options page
  */
  public function add_plugin_page()  {
    // This page will be under "Settings"
    add_options_page(
      'Impostazioni IOMN Eventi',         // Page title
      'IOMN Eventi',                      // Menu title
      'manage_options',                   // Capability
      'iomn-eventi-options',              // Menu slug
      array( $this, 'create_admin_page' ) // Callback function
    );
  }

  /**
  * Options page callback
  */
  public function create_admin_page() {
    // Set class property
    $this->options = get_option( 'my_option_name' );
    ?>
    <div class="wrap">
      <h2>Impostazioni IOMN Eventi</h2>
      <form method="post" action="options.php">
        <?php
        // This prints out all hidden setting fields
        settings_fields( 'iomn_eventi_options' );
        do_settings_sections( 'iomn-eventi-settings-admin' );
        submit_button();
        ?>
      </form>
    </div>
    <?php
  }

  /**
  * Register and add settings
  */
  public function page_init() {
    register_setting(
      'iomn_eventi_options', // Option group
      'iomn_eventi', // Option name
      array( $this, 'sanitize' ) // Sanitize callback
    );

    add_settings_section(
      'iomn_eventi_settings_section', // ID
      'Impostazioni', // Title
      array( $this, 'print_section_info' ), // Callback
      'iomn-eventi-settings-admin' // Page
    );

    add_settings_field(
      'id_number', // ID
      'ID Number', // Title
      array( $this, 'id_number_callback' ), // Callback
      'iomn-eventi-settings-admin', // Page
      'iomn_eventi_settings_section' // Section
    );

    add_settings_field(
      'title',
      'Title',
      array( $this, 'title_callback' ),
      'iomn-eventi-settings-admin',
      'iomn_eventi_settings_section'
    );
  }

  /**
  * Sanitize each setting field as needed
  *
  * @param array $input Contains all settings fields as array keys
  */
  public function sanitize( $input ){
    $new_input = array();
    if( isset( $input['id_number'] ) )
    $new_input['id_number'] = absint( $input['id_number'] );

    if( isset( $input['title'] ) )
    $new_input['title'] = sanitize_text_field( $input['title'] );

    return $new_input;
  }

  /**
  * Print the Section text
  */
  public function print_section_info(){
    print 'Compilare le opzioni di configurazione:';
  }

  /**
  * Get the settings option array and print one of its values
  */
  public function id_number_callback(){
    printf(
    '<input type="text" id="id_number" name="my_option_name[id_number]" value="%s" />',
    isset( $this->options['id_number'] ) ? esc_attr( $this->options['id_number']) : ''
  );
  }

  /**
  * Get the settings option array and print one of its values
  */
  public function title_callback(){
    printf(
      '<input type="text" id="title" name="my_option_name[title]" value="%s" />',
      isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
    );
}
}

// if( is_admin() )
//   $my_settings_page = new MySettingsPage();
