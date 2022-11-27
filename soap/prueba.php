<?php


include_once('libraries/nuSOAP/nusoap.php');


try {
    $client = new nusoap_client("http://localhost/wsepayco/index.php?wsdl", true);

    $client->decodeUTF8(false);

    $codigoAgente = '100';
    $login_user = 'ePayco';
    $contrasenia  = openssl_digest('ePayco', 'sha256');
    $firma = "$codigoAgente~$login_user~$contrasenia";
    $firma = openssl_digest($firma, 'sha256');

    $resultado = $client->call(
        "wsRegistrarCliente",
        array(
            'login' 	=> $login_user,
            'password' 	=> $contrasenia,
            'codAgente'	=> $codigoAgente,
            'llaveCnx' 	=> $firma,
            'document' 	=> "2346499",
            'name' 	=> "Rogert",
            'email' 	=> "rogert1@test.com",
            'phone' 	=> "1234"

        )
    );

//		print_r($resultado);
    var_dump($resultado);

} catch (Exception $e) {
    echo $e->getMessage();
}