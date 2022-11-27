<?php
if (!defined('EPAYCO_WEBSERVICES'))  exit('No direct script access allowed');

    
$config = array();

//Configuraciones para el wsdl
$config['namespace'] = "urn:epayco.com";
$config['service_name'] = "WSEpayco";


//Configuraciones para la base de datos
$config['db_driver'] = 'mysql';
$config['bd_url'] = 'mysql8';
$config['bd_port'] = 3306;
$config['bd_user'] = 'epayco';
$config['bd_psw'] = '3Payc0';
$config['bd_database_name'] = 'epayco';
