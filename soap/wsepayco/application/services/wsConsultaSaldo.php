<?php
//incluir la respueta
generar_respuesta($this->nusoap_server,
    'Resp_ConsultaSaldo',
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
        'balance'	=>	array(
            'name' => 'balance',
            'type' => 'xsd:double'
        )
    ));

//Estructura para datos de la data
//generar_array($this->nusoap_server,
//    'tnsdata',
//    'tnsregdata'
//);
//
//generar_estructura($this->nusoap_server,
//    'tnsregdata',
//    array(
//        'balance'=> array(
//            'name' => 'balance',
//            'type' => 'xsd:double'
//        )
//    )
//);

//Register a method that has parameters and return types
$this->nusoap_server->register(

// method name:
    'wsConsultaSaldo',
    // parameter list:
    array(
        'login'		=> 'xsd:string',
        'password' 	=> 'xsd:string',
        'codAgente' => 'xsd:string',
        'llaveCnx' 	=> 'xsd:string',
        'document'	=> 'xsd:string',
        'phone'	    => 'xsd:string'
    ),
    // return value(s):
    array('return'=>'tns:Resp_ConsultaSaldo'),
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

function wsConsultaSaldo($login, $password, $codAgente, $llaveCnx, $document, $phone) {
    $respuesta = WSRespuesta::instanciar();

    try {
        $validationValues = array(
            'document' => $document,
            'phone' => $phone
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
        $response = $ubdatos->consultaSaldo($document, $phone);
        $respuesta->setRespuesta($response);

    } catch (Exception $ex){
        $respuesta->setServiceResponse(false, $ex->getCode(), $ex->getMessage());
        LOG::write_error('wsConsultaSaldo - ' . $ex->getMessage());
    }
    return $respuesta->getRespuesta();
}