<?php 
class Dao {

    /*--------------------------------------------------------------------------------*/
    /*Atributos*/
    /*--------------------------------------------------------------------------------*/

    
    private static $database = NULL;

    
    /*--------------------------------------------------------------------------------*/
    /*Propiedades*/
    /*--------------------------------------------------------------------------------*/
    
    public static function get_database() {
        if (self::$database == NULL){
        	self::$database = new Database();
        }
            
        return self::$database;
    }

    
}