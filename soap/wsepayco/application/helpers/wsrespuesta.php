<?php

//Esta clase ayuda a transmitir los mensajes que debe tener un servicio web
//de respuesta al servidor
class WSRespuesta {

    /*--------------------------------------------------------------------------------*/
    /*Constantes*/
    /*--------------------------------------------------------------------------------*/

    const SERVICIO_NULL     = 0;
    const SERVICIO_ERROR    = 500;
    const SERVICIO_ERROR_VALIDATION    = 400;
    const SERVICIO_OK 	    = 200;
    const SERVICIO_WARNING  = 201;
    const SERVICIO_NEGACION = 202;



    /*--------------------------------------------------------------------------------*/
    /*Atributos*/
    /*--------------------------------------------------------------------------------*/

    private $respuesta;


    /*--------------------------------------------------------------------------------*/
    /*Métodos*/
    /*--------------------------------------------------------------------------------*/

    //Constructor
    public function __construct(){
        $this->respuesta = array(
            'success' => true,
            'cod_error' => self::SERVICIO_OK,
            'message_error' => '',
            'data' => null,
        );
    }

    //Instancia la respuesta
    public static function instanciar(){
        return new WSRespuesta();
    }

    //Devuelve la respuesta formateada
    public function getRespuesta() {
        return $this->respuesta;
    }

    //setea la respuesta formateada
    public function setRespuesta($response) {
        return $this->respuesta = $response;
    }

    //Adiciona un parámetro a la respuesta
    public function addParametro($name, $value){
        $this->respuesta[$name] = $value;
    }

    public function setServiceResponse($success = false, $codError = '00', $messageError = '', $data = null){
        $this->respuesta['success'] = $success;
        $this->respuesta['cod_error'] = $codError;
        $this->respuesta['message_error'] = $messageError;
        $this->respuesta['data'] = $data;
    }


}