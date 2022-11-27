<?php
//incluir la respueta
generar_respuesta($this->nusoap_server,
    'Resp_RecargarCredito',
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
//        'data'	=>	array(
//            'name' => 'data',
//            'type' => 'tns:tnsdata'
//        )
    ));
//
////Estructura para datos de la data
//generar_array($this->nusoap_server,
//    'tnsdata',
//    'tnsregdata'
//);
//
//generar_estructura($this->nusoap_server,
//    'tnsregdata',
//    array(
//        'new_id'=> array(
//            'name' => 'new_id',
//            'type' => 'xsd:int'
//        )
//    )
//);

//Register a method that has parameters and return types
$this->nusoap_server->register(

// method name:
    'wsRecargarCredito',
    // parameter list:
    array(
        'login'		=> 'xsd:string',
        'password' 	=> 'xsd:string',
        'codAgente' => 'xsd:string',
        'llaveCnx' 	=> 'xsd:string',
        'document'	=> 'xsd:string',
        'phone'	    => 'xsd:string',
        'amount'	=> 'xsd:double'
    ),
    // return value(s):
    array('return'=>'tns:Resp_RecargarCredito'),
    // namespace:
    config_item('namespace'),
    // soapaction: (use default)
    false,
    // style: rpc or document
    'rpc',
    // use: encoded or literal
    'encoded',
    // description: documentation for the method
    'Register a new charge of credit to a wallet');

function wsRecargarCredito($login, $password, $codAgente, $llaveCnx, $document, $phone, $amount) {
    $respuesta = WSRespuesta::instanciar();

    try {
        $validationValues = array(
            'document' => $document,
            'phone' => $phone,
            'amount' => $amount,
        );

        $validation = Seguridad::checkRequired($validationValues);
        if (count($validation) > 0) {
            throw new Exception(implode(', ', $validation), WSRespuesta::SERVICIO_ERROR_VALIDATION);
        }

        $validation = Seguridad::checkMinMaxValue($amount);
        if (!$validation) {
            throw new Exception("The amount must be a number greather than 0", WSRespuesta::SERVICIO_ERROR_VALIDATION);
        }

        require(MODELS_PATH . 'usuario.php');
        $usuario = Usuario::instanciar();
        $usuarioValido = $usuario->puedeIngresarWSEpayco($login, $password, $codAgente, $llaveCnx);
        if (!$usuarioValido) {
            throw new Exception("Login error with data provided", WSRespuesta::SERVICIO_NEGACION);
        }

        require(MODELS_PATH . 'ubdatos.php');
        $ubdatos = new Ubdatos();
        $response = $ubdatos->recargarCredito($document, $phone, $amount);
        $respuesta->setRespuesta($response);
//        $respuesta->setServiceResponse($response['success'], $response['cod_error'], $response['message_error'], $response['data']);

    } catch (Exception $ex){
        $respuesta->setServiceResponse(false, $ex->getCode(), $ex->getMessage());
        LOG::write_error('wsRecargarCredito - ' . $ex->getMessage());
    }
    return $respuesta->getRespuesta();
//    LOG::write_error('wsRecargarCredito - resp1' . print_r($resp, 1));
//    return $resp;
}