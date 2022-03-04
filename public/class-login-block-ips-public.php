<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Login_Block_IPs
 * @subpackage Login_Block_IPs/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Login_Block_IPs
 * @subpackage Login_Block_IPs/public
 * @author     Your Name <email@example.com>
 */
class Login_Block_IPs_Public {

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
		 * defined in Login_Block_IPs_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Login_Block_IPs_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/login-block-ips-public.css', array(), $this->version, 'all' );

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
		 * defined in Login_Block_IPs_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Login_Block_IPs_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/login-block-ips-public.js', array( 'jquery' ), $this->version, false );

	}


	public function check_if_is_admin_page() {
	    if(!session_id()) {
	        session_start();
	    }
	    // Once we are logged in admin area, remove the security code from session.
	    $_SESSION["login-block-ips"] = "";
	    unset($_SESSION["login-block-ips"]);
	}


	public function check_is_login_page(){

	    if(!session_id()) {
	        session_start();
	    }

		if($this->is_wplogin()){
			if((isset($_GET["login-block-ips"]) && $_GET["login-block-ips"] == get_option('login-block-ips-enabled-security-code')) || 
				(isset($_SESSION["login-block-ips"]) && $_SESSION["login-block-ips"] == get_option('login-block-ips-enabled-security-code'))
				){
					// Save the security code in session to permit de submit.
					$_SESSION["login-block-ips"] = get_option('login-block-ips-enabled-security-code');					
			}
			else{
				$currentIp = isset($_SERVER['HTTP_CLIENT_IP']) 
				    ? $_SERVER['HTTP_CLIENT_IP'] 
				    : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) 
				      ? $_SERVER['HTTP_X_FORWARDED_FOR'] 
				      : $_SERVER['REMOTE_ADDR']);

			    $enabled_code = get_option('login-block-ips-enabled-code');
			    if($enabled_code){

			    	$allowedIps = array();
			        for($i = 1; $i < 15; $i++){
			            if(get_option('login-block-ips-ip'.$i) != ""){
			            	$allowedIps[] = get_option('login-block-ips-ip'.$i);
			            }
			        }    

			        if(!in_array($currentIp, $allowedIps)){
				        header("HTTP/1.1 401 Unauthorized");
						wp_die( __( 'Access denied', 'login-block-ips') );
			        }

			    }
			}
		}

	}

	public function is_wplogin(){
	    $ABSPATH_MY = str_replace(array('\\','/'), DIRECTORY_SEPARATOR, ABSPATH);
	    return ((in_array($ABSPATH_MY.'wp-login.php', get_included_files()) || in_array($ABSPATH_MY.'wp-register.php', get_included_files()) ) || (isset($_GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'wp-login.php') || $_SERVER['PHP_SELF']== '/wp-login.php');
	}	

}
