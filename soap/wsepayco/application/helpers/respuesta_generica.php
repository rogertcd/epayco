<?php

function generar_respuesta($server, $nombre_respuesta = NULL, $parametros = NULL ){

    $nombre_respuesta_final = 'RespuestaGenerica';

    if ($nombre_respuesta != NULL)
        $nombre_respuesta_final = $nombre_respuesta;


    $parametros_final = getRespuestaGenerica();



    if ($parametros != NULL)
        $parametros_final = array_merge($parametros_final, $parametros);


    //Creo una respuestaGenerica
    $server->wsdl->addComplexType($nombre_respuesta_final,
        'complexType',
        'struct',
        'all',
        '',
        $parametros_final
    );

}

function getRespuestaGenerica(){
    return array(

        'success' => array(
            'name'=>'success',
            'type' => 'xsd:boolean',
        ),
        'cod_error' => array(
            'name'=>'cod_error',
            'type' => 'xsd:string',
        ),
        'message_error' => array(
            'name'=>'message_error',
            'type' => 'xsd:string'
        )

    );
}

function registrar_servicio ($server, $nombre_servicio, $parametros, $respuesta, $descripcion, $namespace = NULL) {

    //Register a method that has parameters and return types
    $server->register(

    // method name:
        $nombre_servicio,

        // parameter list:
        $parametros,

        // return value(s):
        array('return'=>$respuesta),

        // namespace:
        $namespace,

        // soapaction: (use default)
        false,

        // style: rpc or document
        'rpc',

        // use: encoded or literal
        'encoded',

        // description: documentation for the method
        $descripcion
    );


}

function generar_estructura($server, $nombre_estructura, $parametros){
    //Creo un parámetro complejo
    $server->wsdl->addComplexType( $nombre_estructura,
        'complexType',
        'struct',
        'all',
        '',
        $parametros
    );

}


function generar_array($server, $nombre, $tipo){
    $server->wsdl->addComplexType(
        $nombre,
        'complexType',
        'array',
        '',
        'SOAP-ENC:Array',
        array(),
        array(
            array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:' . $tipo .'[]')
        ),
        "tns:". $tipo
    );
}


function generar_array_tipo($server, $nombre, $tipo){
    $server->wsdl->addComplexType(
        $nombre,
        'complexType',
        'array',
        '',
        'SOAP-ENC:Array',
        array(),
        array(
            array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'xsd:' . $tipo .'[]')
        ),
        "xsd:". $tipo
    );
}
