<?php
//incluir la respueta
generar_respuesta($this->nusoap_server,
    'Resp_ConfirmarPagoCompra',
    array(
        'success'   =>	array(
            'name' => 'success',
            'type' => 'xsd:boolean',
        ),
        'cod_error'	=>	array(
            'name' => 'cod_error',
            'type' => 'xsd:string'
        ),
        'message_error'	=>	array(
            'name' => 'message_error',
            'type' => 'xsd:string'
        ),
        'message_success'	=>	array(
            'name' => 'message_success',
            'type' => 'xsd:string'
        )
    ));

//Register a method that has parameters and return types
$this->nusoap_server->register(

// method name:
    'wsConfirmarPagoCompra',
    // parameter list:
    array(
        'login'		=> 'xsd:string',
        'password' 	=> 'xsd:string',
        'codAgente' => 'xsd:string',
        'llaveCnx' 	=> 'xsd:string',
        'session_id'	    => 'xsd:string',
        'token'	    => 'xsd:string',
    ),
    // return value(s):
    array('return'=>'tns:Resp_ConfirmarPagoCompra'),
    // namespace:
    config_item('namespace'),
    // soapaction: (use default)
    false,
    // style: rpc or document
    'rpc',
    // use: encoded or literal
    'encoded',
    // description: documentation for the method
    'Get the balance of the wallet');

function wsConfirmarPagoCompra($login, $password, $codAgente, $llaveCnx, $sessionId, $token) {
    $respuesta = WSRespuesta::instanciar();

    try {
        $validationValues = array(
            'session_id' => $sessionId,
            'token' => $token
        );

        $validation = Seguridad::checkRequired($validationValues);
        if (count($validation) > 0) {
            throw new Exception(implode(', ', $validation), WSRespuesta::SERVICIO_ERROR_VALIDATION);
        }

        require(MODELS_PATH . 'usuario.php');
        $usuario = Usuario::instanciar();
        $usuarioValido = $usuario->puedeIngresarWSEpayco($login, $password, $codAgente, $llaveCnx);
        if (!$usuarioValido) {
            throw new Exception("Login error with data provided", WSRespuesta::SERVICIO_NEGACION);
        }

        require(MODELS_PATH . 'ubdatos.php');
        $ubdatos = new Ubdatos();
        $response = $ubdatos->confirmarPagoCompra($sessionId, $token);
        $respuesta->setRespuesta($response);

    } catch (Exception $ex){
        $respuesta->setServiceResponse(false, $ex->getCode(), $ex->getMessage());
        LOG::write_error('wsConfirmarPagoCompra - ' . $ex->getMessage());
    }
    return $respuesta->getRespuesta();
}