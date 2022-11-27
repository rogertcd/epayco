<?php 
class Log {

    const INFO = 'INFO';
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';
    const DEBUG = 'DEBUG';
    
	public static function write_error($msg){
		self::write_log($msg, self::ERROR);	
	}
	
	public static function print_r($value){
 		$value = print_r($value, TRUE);
		self::write_log($value, self::DEBUG);
 	}
	
	public static function var_dump($value){
 		ob_start();
  		var_dump($value);
  		$value = ob_get_clean();
		self::write_log($value, self::DEBUG);
 	}
	
	public static function debug($value){
		LOG::write_log('', self::DEBUG);
		LOG::write_line();

		self::write_log($value, self::DEBUG);	
		self::print_r($value);
		self::var_dump($value);
		
		self::write_line();
		LOG::write_log('', self::DEBUG);
	}
	
    /**
     * Original from CodeIgniter 1.5
     * Write Log File
     *
     * Generally this function will be called using the global log_message() function
     *
     * @access	public
     * @param	string	the error level
     * @param	string	the error message
     * @param	bool	whether the error is a native PHP error
     * @return	bool
     */
    public static function write_log($msg, $level = self::INFO ) {
        $level = strtoupper($level);

        $filepath = BASE_PATH.'log/'.'log-'.date('Y-m-d').'.php';

        $message = '';

        if (!file_exists($filepath)) {
            $message .= "<"."?php  if ( ! defined('BASE_PATH')) exit('No direct script access allowed'); ?".">\n\n";
        }

		//$filepath = str_replace('\\', '/', $filepath);
        if (!$fp = @fopen($filepath, 'ab')) {
            return FALSE;
        }

		$fecha = date("Y-m-d H:i:s", time() - date("Z") + -4*3600);
        $message .= $level.' '.(($level == 'INFO') ? ' -' : '-').' '. $fecha .' --> '.$msg."\n";

        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);

        @chmod($filepath, 0666);
        return TRUE;
    }
 
 	public static function clear(){
 		$filepath = BASE_PATH.'log/'.'log-'.date('Y-m-d').'.php';
		if (file_exists($filepath)) 
			unlink($filepath);
 	}
 
 	public static function write_label ($name){
 		LOG::write_line();	
 		self::write_log($name, LOG::DEBUG);
		LOG::write_line();
 	}

 	public static function write_value ($value, $name = 'VALUE: '){
 		self::write_log($name, LOG::DEBUG);
		self::print_r($value);
 	}

 	public static function write_line (){
 		LOG::write_log('-------------------------------------------------------------', self::DEBUG);
 	}



}
