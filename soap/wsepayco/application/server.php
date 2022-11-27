<?php
require_once ('interfaces/iserver.php');

require_once (LIB_PATH."nuSOAP/nusoap.php");
require_once (LIB_PATH."Log/log.php");

// Para el envio de mails
require_once (LIB_PATH . "PHPMailer/PHPMailerUse.php");
require_once (HELPERS_PATH."mailMessages.php");


require_once (HELPERS_PATH."common.php");
require_once (HELPERS_PATH."seguridad.php");
require_once (HELPERS_PATH."wsrespuesta.php");
require_once (HELPERS_PATH."database.php");
require_once (HELPERS_PATH. 'dao.php');

class Server implements IServer {

    private $nusoap_server;


    public function __construct() {
        // create a new soap server
        $this->nusoap_server = new soap_server();

        // configure our WSDL
        $this->nusoap_server->configureWSDL(config_item('service_name'), config_item('namespace'));

        // set our namespace
        $this->nusoap_server->wsdl->schemaTargetNamespace = config_item('namespace');

        //configurar los servicios web
        $this->configurar_servicios();
    }


    //Devuelve la instancia del servidor
    public static function instanciar() {
        return new Server();
    }

    //Devuelve la instancia del servidor
    public function configurar_salida() {

        // Get our posted data if the service is being consumed
        // otherwise leave this data blank.
//        $POST_DATA = $GLOBALS['HTTP_RAW_POST_DATA'] ?? '';

        $this->nusoap_server->soap_defencoding = 'UTF-8';
        $this->nusoap_server->decode_utf8 = false;
//        $this->nusoap_server->encode_utf8 = true;

        // pass our posted data (or nothing) to the soap service
        $this->nusoap_server->service(file_get_contents("php://input" ));

    }


    //Acá se incluyen los servicios web a ser elegidos
    private function configurar_servicios() {
        $namespace = config_item('namespace');
        require_once (HELPERS_PATH. 'estructuraDatos.php');

        require (SERVICES_PATH.'wsRegistrarCliente.php');
        require (SERVICES_PATH.'wsRecargarCredito.php');
        require (SERVICES_PATH.'wsConsultaSaldo.php');
        require (SERVICES_PATH.'wsRealizarPagoCompra.php');
        require (SERVICES_PATH.'wsConfirmarPagoCompra.php');

    }


}