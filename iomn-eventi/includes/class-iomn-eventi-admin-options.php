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
    // update_option('iomn_eventi', array(
    //   'notify' => array(
    //     'BAZINGA@example.com',
    //     'un.o@example.com',
    //     'due@example.com'
    //   )
    //  ));
    // Set class property
    $this->options = get_option( 'iomn_eventi', array('BAZINGA') );
    // var_dump($this->options);
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
      'Notifica prenotazioni eventi', // Title
      array( $this, 'print_section_info' ), // Callback
      'iomn-eventi-settings-admin' // Page
    );

    add_settings_field(
      'notify', // ID
      'Indirizzi email', // Title
      array( $this, 'notify_callback' ), // Callback
      'iomn-eventi-settings-admin', // Page
      'iomn_eventi_settings_section' // Section
    );

  }

  /**
  * Sanitize each setting field as needed
  *
  * @param array $input Contains all settings fields as array keys
  */
  public function sanitize( $input ){
    $new_input = array();

    if ( isset($input['notify'])) {
      if (is_array($input['notify'])) {
        foreach ($input['notify'] as $value) {
          $value = sanitize_email($value);
          if (is_email($value)) {
            $new_input['notify'][] = $value;
          }
        }
      }
    }

    return $new_input;
  }

  /**
  * Print the Section text
  */
  public function print_section_info(){
    print 'Specificare gli indirizzi email a cui inviare un messaggio ogni volta che viene effettuata o cancellata una prenotazione:';
  }

  /**
  * Get the settings option array and print one of its values
  */
  public function notify_callback(){
    if (isset($this->options['notify']) && is_array($this->options['notify'])) {
      foreach ($this->options['notify'] as $value) {
        printf('<div><input type="text" id="notify" name="iomn_eventi[notify][]" value="%s" /> <button onclick="jQuery(this).closest(\'div\').remove(); return false;"><i class="fa fa-minus-circle"></i></button></div>',
          esc_attr( $value )
        );
      }
    }
    printf('<div><input type="text" id="notify" name="iomn_eventi[notify][]" value="%s" />',
      ''
    );
    printf(' <button id="iomn_ev_add">Aggiungi <i class="fa fa-plus-circle"></i></button></div>');

  }

}

// if( is_admin() )
//   $my_settings_page = new MySettingsPage();
