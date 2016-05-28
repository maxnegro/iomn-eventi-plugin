<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://photomarketing.it
 * @since      1.0.0
 *
 * @package    Iomn_Eventi
 * @subpackage Iomn_Eventi/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Iomn_Eventi
 * @subpackage Iomn_Eventi/includes
 * @author     Massimiliano Masserelli <info@photomarketing.it>
 */
class Iomn_Eventi {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Iomn_Eventi_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'iomn-eventi';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_post_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Iomn_Eventi_Loader. Orchestrates the hooks of the plugin.
	 * - Iomn_Eventi_i18n. Defines internationalization functionality.
	 * - Iomn_Eventi_Admin. Defines all hooks for the admin area.
	 * - Iomn_Eventi_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-iomn-eventi-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-iomn-eventi-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-iomn-eventi-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-iomn-eventi-public.php';

		$this->loader = new Iomn_Eventi_Loader();

	}

	/**
	 * Register custom post type.
	 *
	 * Registers custom post type used for storing events. Registers also a
	 * taxonomy used for storing event locations.
	 *
	 * @since    1.0.0
	 */
	public function register_post_type() {
			$labels = array(
					'name' => _x('Eventi', 'Post Type General Name', 'iomn_text_domain'),
					'singular_name' => _x('Evento', 'Post Type Singular Name', 'iomn_text_domain'),
					'menu_name' => __('Eventi IOMN', 'iomn_text_domain'),
					'name_admin_bar' => __('Eventi IOMN', 'iomn_text_domain'),
					'parent_item_colon' => __('Parent Item:', 'iomn_text_domain'),
					'all_items' => __('Tutti gli Eventi', 'iomn_text_domain'),
					'add_new_item' => __('Nuovo Evento', 'iomn_text_domain'),
					'add_new' => __('Nuovo', 'iomn_text_domain'),
					'new_item' => __('Nuovo Evento', 'iomn_text_domain'),
					'edit_item' => __('Modifica Evento', 'iomn_text_domain'),
					'update_item' => __('Aggiorna Evento', 'iomn_text_domain'),
					'view_item' => __('Visualizza Evento', 'iomn_text_domain'),
					'search_items' => __('Cerca Evento', 'iomn_text_domain'),
					'not_found' => __('Nessun elemento trovato', 'iomn_text_domain'),
					'not_found_in_trash' => __('Nessun elemento trovato nel Cestino', 'iomn_text_domain'),
			);
			$args = array(
					'label' => __('Evento', 'iomn_text_domain'),
					'description' => __('IOMN Event Type', 'iomn_text_domain'),
					'labels' => $labels,
					'supports' => array('title', 'editor'),
					// 'supports'            => array( 'title', 'editor', 'custom-fields', ),
					'taxonomies' => array('iomn_strutture'),
					// 'taxonomies'          => array( 'category', 'post_tag' ),
					'hierarchical' => false,
					'public' => true,
					'show_ui' => true,
					'show_in_menu' => true,
					'menu_position' => 5,
					'menu_icon' => 'dashicons-calendar',
					'show_in_admin_bar' => true,
					'show_in_nav_menus' => false,
					'can_export' => true,
					'has_archive' => 'eventi',
					'exclude_from_search' => false,
					'publicly_queryable' => true,
					'capability_type' => 'post',
			);

			register_post_type('iomn_eventi', $args);
			register_taxonomy('iomn_strutture', 'iomn_eventi', array(
					'label' => __('Ospedali'),
					'rewrite' => array('slug' => 'ospedali'),
					'hierarchical' => true,
					'meta_box_cb' => false,
	//        'capabilities' => array(
	//            'assign_terms' => 'edit_guides',
	//            'edit_terms' => 'publish_guides'
	//        ),
			));

		}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Iomn_Eventi_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Iomn_Eventi_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	private function define_post_hooks() {
		$this->loader->add_action( 'init', $this, 'register_post_type' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Iomn_Eventi_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_box' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_meta_box');
		$this->loader->add_action( 'show_user_profile', $plugin_admin, 'add_user_specialty');
		$this->loader->add_action( 'edit_user_profile', $plugin_admin, 'add_user_specialty');
		$this->loader->add_action( 'user_new_form', $plugin_admin, 'add_user_specialty');
		$this->loader->add_action( 'personal_options_update', $plugin_admin, 'save_user_specialty');
		$this->loader->add_action( 'edit_user_profile_update', $plugin_admin, 'save_user_specialty');
		$this->loader->add_action( 'user_register', $plugin_admin, 'save_user_specialty');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Iomn_Eventi_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Iomn_Eventi_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
