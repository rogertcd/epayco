<?php 

class Seguridad {

	private static $key = "XiTo74dOO09N48YeUmuvbL0E";
    private static  $iv = "V8DOW7Om";
    private static $patternTokens = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    private static $search = ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú',
        'ä', 'ë', 'ï', 'ö', 'ü', 'Ä', 'Ë', 'Ï', 'Ö', 'Ü', 'â', 'ã',
        'ä', 'å', 'ā', 'ă', 'ą', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ā', 'Ă',
        'Ą', 'è', 'é', 'é', 'ê', 'ë', 'ē', 'ĕ', 'ė', 'ę', 'ě', 'Ē',
        'Ĕ', 'Ė', 'Ę', 'Ě', 'ì', 'í', 'î', 'ï', 'ì', 'ĩ', 'ī', 'ĭ',
        'Ì', 'Í', 'Î', 'Ï', 'Ì', 'Ĩ', 'Ī', 'Ĭ', 'ó', 'ô', 'õ', 'ö',
        'ō', 'ŏ', 'ő', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ō', 'Ŏ', 'Ő', 'ù',
        'ú', 'û', 'ü', 'ũ', 'ū', 'ŭ', 'ů', 'Ù', 'Ú', 'Û', 'Ü', 'Ũ',
        'Ū', 'Ŭ', 'Ů', 'Ñ', 'ñ'];

    private static $characters = [
        'á' => 'a',	'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
        'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u',
        'Ä' => 'A', 'Ë' => 'E', 'Ï' => 'I', 'Ö' => 'O', 'Ü' => 'U',
        'â' => 'a', 'ã' => 'a',	'å' => 'a', 'ā' => 'a', 'ă' => 'a', 'ą' => 'a',
        'Â' => 'A', 'Ã' => 'A', 'Å' => 'A', 'Ā' => 'A', 'Ă' => 'A',	'Ą' => 'A',
        'è' => 'e', 'ê' => 'e', 'ē' => 'e', 'ĕ' => 'e', 'ė' => 'e', 'ę' => 'e', 'ě' => 'e',
        'Ē' => 'E',	'Ĕ' => 'E', 'Ė' => 'E', 'Ę' => 'E', 'Ě' => 'E',
        'ì' => 'i', 'î' => 'i', 'ĩ' => 'i', 'ī' => 'i', 'ĭ' => 'I',
        'Ì' => 'I', 'Î' => 'I', 'Ĩ' => 'I', 'Ī' => 'I', 'Ĭ' => 'I',
        'ô' => 'o', 'õ' => 'o',	'ō' => 'o', 'ŏ' => 'o', 'ő' => 'o',
        'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ō' => 'O', 'Ŏ' => 'O', 'Ő' => 'O',
        'ù' => 'u', 'û' => 'u', 'ũ' => 'u', 'ū' => 'u', 'ŭ' => 'u', 'ů' => 'u',
        'Ù' => 'U', 'Û' => 'U', 'Ũ' => 'U',	'Ū' => 'U', 'Ŭ' => 'U', 'Ů' => 'U',
        'Ñ' => 'N', 'ñ' => 'n'];

    public static function limpiar($value) {
		require_once("class.inputfilter.php");
//		LOG::write_log('viejo: ' .  $value);
        error_log('viejo' . $value);
		$ifilter = new InputFilter();
		$value = $ifilter->process($value);
//		LOG::write_log('nuevo: ' .  $value);
        error_log('nuevo' . $value);
		return $value;
    }

    public static function checkRequired($values) {
        $validation = array();
        foreach ($values as $key => $value) {
            if (empty($value)) {
                array_push($validation, "The $key field is required");
            }
        }
        return $validation;
    }

    public static function checkMinMaxLenght($value, $min, $max) {
        $length = strlen($value);
        return ( $length >= $min && $length <= $max);
    }

    public static function checkMinMaxValue($value, $min = 1, $max = 0) {
        return ($max == 0) ? ( $value >= $min) : ($value >= $min && $value <= $max);
    }

    public static function checkValidEmail($values) {
        $validation = array();
        foreach ($values as $key => $value) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                array_push($validation, "The $key field is not a valid email");
            }
        }
        return $validation;
    }

    public static function replaceStrangeCharacteres($string) {
        return str_replace(array_keys(self::$characters), self::$characters, $string);
    }

    public static function generateToken($longitud) {
        $code = '';
        $max = strlen(self::$patternTokens) - 1;
        for($i = 0; $i < $longitud; $i++) $code .= self::$patternTokens[ mt_rand(0, $max) ];
        return $code;
    }
    
}