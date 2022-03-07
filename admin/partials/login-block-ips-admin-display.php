<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Login_Block_IPs
 * @subpackage Login_Block_IPs/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $len = strpos($string, $end) + strlen($end);
    return substr($string, $ini, $len);
}

$currentIp = isset($_SERVER['HTTP_CLIENT_IP']) 
    ? $_SERVER['HTTP_CLIENT_IP'] 
    : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) 
      ? $_SERVER['HTTP_X_FORWARDED_FOR'] 
      : $_SERVER['REMOTE_ADDR']);

$saved = false;
if (isset($_POST['login-block-ips-form'])) {

    $home_path = get_home_path();
    $htaccess_file = $home_path.'.htaccess';    

    $almostOneIsAdded = false;
    for($i = 1; $i < 15; $i++){
        if(isset($_POST['ip'.$i]) && sanitize_text_field($_POST['ip'.$i]) != ""){
            $ip = trim(sanitize_text_field($_POST['ip'.$i]));
            if(filter_var($ip, FILTER_VALIDATE_IP)){
                $almostOneIsAdded = true;
                update_option('login-block-ips-ip'.$i, sanitize_text_field($_POST['ip'.$i]));
                if(isset($_POST['ipdesc'.$i]) && sanitize_text_field($_POST['ipdesc'.$i]) != ""){
                    update_option('login-block-ips-desc'.$i, sanitize_text_field($_POST['ipdesc'.$i]));
                }                      
            }                
        }  
        else{
			delete_option('login-block-ips-ip'.$i);
			delete_option('login-block-ips-desc'.$i);            
        }
    }

    update_option('login-block-ips-enabled', sanitize_text_field($_POST['enabled']));
    update_option('login-block-ips-enabled-code', sanitize_text_field($_POST['enabled_code']));
    update_option('login-block-ips-enabled-security-code', sanitize_text_field($_POST['security_code']));
    if($almostOneIsAdded && $_POST['enabled']){
        $original_htaccess = file_get_contents($htaccess_file);
        $parsed = get_string_between($original_htaccess, '# BEGIN BLOCK_LOGIN_IPS', '# END BLOCK_LOGIN_IPS');
        $original_htaccess = str_replace($parsed, "", $original_htaccess);

        $file_data = "";
        $file_data .= "# BEGIN BLOCK_LOGIN_IPS\n";
        $file_data .= "<Files wp-login.php>\n";
        $file_data .= "Order Deny,Allow\n";
        $file_data .= "Deny from all\n";

        for($i = 1; $i < 15; $i++){
            if(get_option('login-block-ips-ip'.$i) != ""){
                $file_data .= "Allow from " . esc_attr(get_option('login-block-ips-ip'.$i)) . "\n";
            }
        }    

        
        $file_data .= "</Files>\n";
        $file_data .= "# END BLOCK_LOGIN_IPS\n";

        $file_data .= $original_htaccess;
        file_put_contents($htaccess_file, $file_data); 
    }      
    else{
        $original_htaccess = file_get_contents($htaccess_file);
        $parsed = get_string_between($original_htaccess, '# BEGIN BLOCK_LOGIN_IPS', '# END BLOCK_LOGIN_IPS');
        $original_htaccess = str_replace($parsed, "", $original_htaccess);
        file_put_contents($htaccess_file, $original_htaccess);  
    }

    $saved = true;
    
}

$enabled = get_option('login-block-ips-enabled');
$enabled_code = get_option('login-block-ips-enabled-code');
$security_code = get_option('login-block-ips-enabled-security-code');
if($security_code == ""){
    $security_code = md5(time() + rand());
}

?>

<div class="wrap">
    <h2><?php echo __( 'Login Block IPs', 'login-block-ips' ); ?></h2>
    <form method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="login-block-ips-form" value="1" />
        <?php if($saved): ?>
            <div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible"> 
            <p><strong><?php echo __( 'Settings have been saved!', 'login-block-ips' ); ?></strong></p></div>            
            
        <?php endif; ?>
        <table class="form-table" role="presentation">
            <tbody>
            <tr>
                <th scope="row"><label for="enabled"><?php echo __( 'Enabled by htaccess file', 'login-block-ips' ); ?></label></th>
                <td  >
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php echo __( 'Enabled by htaccess file', 'login-block-ips' ); ?></span></legend>
                        <input size="16" type="checkbox" id="enabled" name="enabled" <?php if($enabled){?> checked="checked" <?php } ?> value="1"><br />
                        <?php echo __( 'This option modifies the .htaccess file to block all IPs except the ones that you have added in the form.', 'login-block-ips' ); ?><br />
                        <?php echo __( 'If your IP changes and you can not access to login page, you must edit the .htaccess file and add your current IP in the corresponding block.', 'login-block-ips' ); ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="enabled_code"><?php echo __( 'Enabled by code', 'login-block-ips' ); ?></label></th>
                <td  >
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php echo __( 'Enabled by code', 'login-block-ips' ); ?></span></legend>
                        <input size="16" type="checkbox" id="enabled_code" name="enabled_code" <?php if($enabled_code){?> checked="checked" <?php } ?> value="1"><br />
                        <?php echo __( 'This option blocks all IPs by code, except the ones that you have added in the form.', 'login-block-ips' ); ?>
                    </fieldset>
                </td>
            </tr>        
            <tr>
                <th scope="row"><label for="security_code"><?php echo __( 'Security code', 'login-block-ips' ); ?></label></th>
                <td  >
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php echo __( 'Security code', 'login-block-ips' ); ?></span></legend>
                        <input size="60" type="text" id="security_code" name="security_code" value="<?php echo esc_attr($security_code); ?>"><br />
                        <?php echo __( 'If your IP changes and you can not access to login page, this is the secured URL to access:', 'login-block-ips' ); ?><br /><strong><?php echo wp_login_url(); ?>?login-block-ips=<?php echo esc_attr($security_code); ?></strong>
                        <br /><span style="color:#e74c3c;">*<?php echo __( 'This secured URL only works if the option enabled is by code.', 'login-block-ips' ); ?></span>
                    </fieldset>
                </td>
            </tr>  
            <tr>
                <th scope="row"><?php echo __( 'IPs allowed to access', 'login-block-ips' ); ?></th>
                <td  >
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php echo __( 'IPs allowed to access', 'login-block-ips' ); ?></span></legend>
                        <?php
                        echo __( 'Your current IP is', 'login-block-ips') . ': <strong>'.esc_attr($currentIp).'</strong> <br /><br />';

                        ?>
                        <table>
                            <tr><td>#</td><td><?php echo __( 'IP', 'login-block-ips' ); ?></td><td><?php echo __( 'Description', 'login-block-ips' ); ?></td></tr>
                            <?php
                            for($i = 1; $i < 15; $i++){
                                echo '<tr><td>#'.esc_attr($i).'</td><td><input size="15" type="text" name="ip'.esc_attr($i).'" value="' . esc_attr((get_option('login-block-ips-ip'.$i)) != "" ? esc_attr(get_option('login-block-ips-ip'.$i)) : "") . '" /></td><td><input size="50" type="text" name="ipdesc'.esc_attr($i).'" value="' . esc_attr((get_option('login-block-ips-desc'.$i)) != "" ? esc_attr(get_option('login-block-ips-desc'.$i)) : "") . '" /></td></tr>';
                            }
                            ?>
                        </table>
                    </fieldset>
                </td>
            </tr>
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __( 'Save', 'login-block-ips' ); ?>"></p>
    </form>
    <br /><br />
    <p><strong>Developed by</strong><br /><a href="https://gunkastudios.com" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ) . '../images/gunkastudios.png'; ?>" alt="Developed by Gunka Studios"></a> </p>
</div>
