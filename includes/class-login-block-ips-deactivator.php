<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Login_Block_IPs
 * @subpackage Login_Block_IPs/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Login_Block_IPs
 * @subpackage Login_Block_IPs/includes
 * @author     Your Name <email@example.com>
 */
class Login_Block_IPs_Deactivator {
	public static function get_string_between($string, $start, $end){
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$len = strpos($string, $end) + strlen($end);
		return substr($string, $ini, $len);
	}
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		delete_option('login-block-ips-enabled');
		delete_option('login-block-ips-enabled-code');		
		delete_option('login-block-ips-enabled-security-code');
		for($i = 1; $i < 15; $i++){
			delete_option('login-block-ips-ip'.$i);
			delete_option('login-block-ips-desc'.$i);
        }

		$home_path = get_home_path();
		$htaccess_file = $home_path.'.htaccess';  		
        $original_htaccess = file_get_contents($htaccess_file);
        $parsed = Login_Block_IPs_Deactivator::get_string_between($original_htaccess, '# BEGIN BLOCK_LOGIN_IPS', '# END BLOCK_LOGIN_IPS');
        $original_htaccess = str_replace($parsed, "", $original_htaccess);
        file_put_contents($htaccess_file, $original_htaccess);        
	}

}
