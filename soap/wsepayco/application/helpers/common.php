<?php 
/**
 * Loads the main config.php file
 *
 * @access	private
 * @return	array
 */
function get_config() {
    static $main_conf;
    
    if (!isset($main_conf)) {
    
        if (!file_exists(CONFIGS_PATH.'config.php')) {
            exit('The configuration file config.php does not exist.');
        }
        
		require (CONFIGS_PATH.'config.php');
		
        if (!isset($config) OR !is_array($config)) {
            exit('Your config file does not appear to be formatted correctly.');
        }
        
        $main_conf[0] = &$config;
    }
    return $main_conf[0];
}


/**
 * Gets a config item
 *
 * @access	public
 * @return	mixed
 */
function config_item($item) {
    static $config_item = array();
	
    if (!isset($config_item[$item])) {
        $get_config = get_config();
        $config = &$get_config;
		
        if (!isset($config[$item]))
            return FALSE;
            
        $config_item[$item] = $config[$item];
    }
    
    return $config_item[$item];
}
