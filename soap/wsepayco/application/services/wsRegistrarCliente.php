<?php
//incluir la respueta
generar_respuesta($this->nusoap_server,
    'Resp_RegistrarCliente',
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
    'wsRegistrarCliente',
    // parameter list:
    array(
        'login'		=> 'xsd:string',
        'password' 	=> 'xsd:string',
        'codAgente' => 'xsd:string',
        'llaveCnx' 	=> 'xsd:string',
        'document'	=> 'xsd:string',
        'name'	    => 'xsd:string',
        'email' 	=> 'xsd:string',
        'phone'	    => 'xsd:string'
    ),
    // return value(s):
    array('return'=>'tns:Resp_RegistrarCliente'),
    // namespace:
    config_item('namespace'),
    // soapaction: (use default)
    false,
    // style: rpc or document
    'rpc',
    // use: encoded or literal
    'encoded',
    // description: documentation for the method
    'Register data for a new client');

function wsRegistrarCliente($login, $password, $codAgente, $llaveCnx, $document, $name, $email, $phone) {
//    LOG::write_log("Entrando a: wsRegistrarCliente($login, $password, $codAgente, $llaveCnx, $document, $name, $email, $phone)", LOG::ERROR);
    $respuesta = WSRespuesta::instanciar();

    try {
//        $login = Seguridad::limpiar($login);
//        $password = Seguridad::limpiar($password);
//        $codAgente = Seguridad::limpiar($codAgente);
//        $llaveCnx = Seguridad::limpiar($llaveCnx);
//        $document = Seguridad::limpiar($document);
//        $name = Seguridad::limpiar($name);
//        $email = Seguridad::limpiar($email);
//        $phone = Seguridad::limpiar($phone);

        $validationValues = array(
            'document' => $document,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        );
//        LOG::write_error('validationValues ' . print_r($validationValues, 1));

        $validation = Seguridad::checkRequired($validationValues);
        if (count($validation) > 0) {
            throw new Exception(implode(', ', $validation), WSRespuesta::SERVICIO_ERROR_VALIDATION);
        }

        $validation = Seguridad::checkValidEmail(array('email' => $email));
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
        $response = $ubdatos->registrarCliente($document, $name, $email, $phone);
        $respuesta->setRespuesta($response);
//        $respuesta->setServiceResponse($response['success'], $response['cod_error'], $response['message_error'], $response['data']);

    } catch (Exception $ex){
        $respuesta->setServiceResponse(false, $ex->getCode(), $ex->getMessage());
        LOG::write_error('wsRegistrarCliente - ' . $ex->getMessage());
    }
    return $respuesta->getRespuesta();
//    LOG::write_error('wsRegistrarCliente - resp1' . print_r($resp, 1));
//    return $resp;
}