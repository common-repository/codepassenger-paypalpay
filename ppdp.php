<?php 
/*
Plugin Name: Paypal Pay Donation And Payment
Plugin URI: https://codepassenger.com/wp/plugins
Description: Collect donation or sell your products online. No Codeing required.
Author: Codepassenger
Author URI: https://codepassenger.com/
Text Domain: ppdp
Domain Path: /languages/
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Version: 1.0
*/
define("PPDP_PREFIX", "ppdp_");

class Ppdp {
	
	protected static $instance = null;
	
	private $plugin_path;
	private $plugin_settings_page;
	private $plugin_prefix;
    private $settings_class;
    /**
     * [instance description]
     * @return [object] 
     */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function __construct(){
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->plugin_settings_page = 'ppdp_settings';
		$this->plugin_prefix = PPDP_PREFIX;
		
        add_action( 'admin_init', array($this, 'admin_init'),20);
        add_action( 'admin_menu', array($this, 'admin_menu_item') );
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__) , array( $this, 'plugin_settings_link' ) );
		add_action( 'init', array( $this, 'file_include' ),20);
		add_action( 'plugins_loaded', array($this,'pluginsLoaded'),9);
	}

	/**
	 * [pluginsLoaded ]
	 * @return [null] [initialize Language File ]
	 */
	public function pluginsLoaded(){
		load_plugin_textdomain( 'ppdp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
	}
	
	/**
	 * [file_include ]
	 * @return [null] [Load File]
	 */
	public function file_include(){
		require_once dirname( __FILE__ ) . '/include/ppdp-setting.php';
		require_once dirname( __FILE__ ) . '/include/ppdp-helper.php';
		require_once dirname( __FILE__ ) . '/settings/ppdp-wordPress-plugin-settings-class.php';
	}
	/**
	 * [admin_init]
	 * @return [null] 
	 * Load Plugin Setting Option
	 */
	public function admin_init() {
		$this->settings_class = new Ppdp_wordPress_Plugin_Settings_class($this->plugin_path .'settings/ppdp-settings-fields.php', $this->plugin_settings_page, $this->plugin_prefix );
        $this->settings_class->admin_init();
    }
	
	/**
	 * [admin_menu_item]
	 * @return [null] 
	 * Create Admin Menu for Plugin 
	 */
	public function admin_menu_item() {
        $page = add_menu_page(  esc_html__( 'Ppdp Setting', 'ppdp' ) ,  esc_html__( 'Ppdp Setting', 'ppdp' ) , 'manage_options' , $this->plugin_settings_page ,  array( $this, 'settings_page' ) );
    }
	
	/**
	 * [plugin_settings_link]
	 * @param  [type] $links 
	 * @return [array]  
	 * Add Plugin Setting Link      
	 */
	public function plugin_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page='.$this->plugin_settings_page.'">' .  esc_html__( 'Settings', 'ppdp' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * [settings_page ]
	 * @return [null] 
	 * Create Plugin Setting Page
	 */
    function settings_page() {
        $this->settings_class->display_settings();
    }
}

Ppdp::instance();
