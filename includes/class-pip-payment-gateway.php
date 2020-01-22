<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/PipLabs/pip-checkout-woocommerce/
 * @since      1.0.0
 *
 * @package    Pip_Payment_Gateway
 * @subpackage Pip_Payment_Gateway/includes
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
 * @package    Pip_Payment_Gateway
 * @subpackage Pip_Payment_Gateway/includes
 * @author     Pip Labs <dev@pip.cash>
 */
class Pip_Payment_Gateway {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Pip_Payment_Gateway_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	protected $admin_obj;  // admin class object
	protected $public_obj;  // public class object
	public function __construct() {
		if ( defined( 'PIP_PAYMENT_GATEWAY_VERSION' ) ) {
			$this->version = PIP_PAYMENT_GATEWAY_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'pip-payment-gateway';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Pip_Payment_Gateway_Loader. Orchestrates the hooks of the plugin.
	 * - Pip_Payment_Gateway_i18n. Defines internationalization functionality.
	 * - Pip_Payment_Gateway_Admin. Defines all hooks for the admin area.
	 * - Pip_Payment_Gateway_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pip-payment-gateway-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pip-payment-gateway-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-pip-payment-gateway-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-pip-payment-gateway-public.php';

		/**
		 * The class responsible process payment at checkout page
		 * side of the site.
		 */
		

		$this->loader = new Pip_Payment_Gateway_Loader();
		$this->admin_obj = new Pip_Payment_Gateway_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->public_obj = new Pip_Payment_Gateway_Public( $this->get_plugin_name(), $this->get_version() );
        
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Pip_Payment_Gateway_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Pip_Payment_Gateway_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		
        
		$this->loader->add_action( 'admin_enqueue_scripts',$this->admin_obj, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin_obj, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Pip_Payment_Gateway_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $this->public_obj, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->public_obj, 'enqueue_scripts' );

	}
	/**
	 * check woocommerce is installed 
	 *
	 * @since    1.0.0
	 */

	public function is_woocommerce_installed(){
		  return  in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
	}

	public function wc_pip_payment_gateway($gateways){
		 $gateways[] = 'Woocommerce_Pip_Payment_Gateway';
		return $gateways;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
         if(!$this->is_woocommerce_installed()){
          $this->loader->add_action( 'admin_notices', $this->admin_obj, 'show_woocommerce_inactive_notice' );
         }
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-pip-payment-gateway.php';
        $this->loader->add_filter( 'woocommerce_payment_gateways', $this, 'wc_pip_payment_gateway' );
        $this->loader->add_filter( 'plugin_action_links_'.PIP_PAYMENT_GATEWAY_BASE_PATH, $this->admin_obj, 'setting_page_url' );
       
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
	 * @return    Pip_Payment_Gateway_Loader    Orchestrates the hooks of the plugin.
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
