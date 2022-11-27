<?php

class Usuario {


    /*--------------------------------------------------------------------------------*/
    /*Atributos*/
    /*--------------------------------------------------------------------------------*/

    private $id_usuario;
    private $nombre;
    private $id_grupo;
    private $grupo;

    private $login;
    private $clave;
    private $codAgente;
    private $llaveCnx;
    private $id_punto_venta;



    /*--------------------------------------------------------------------------------*/
    /*Propiedades*/
    /*--------------------------------------------------------------------------------*/


    /**
     * Returns $grupo.
     *
     * @see Usuario::$grupo
     */
    public function get_grupo() {
        return $this->grupo;
    }

    /**
     * Returns $id_grupo.
     *
     * @see Usuario::$id_grupo
     */
    public function get_id_grupo() {
        return $this->id_grupo;
    }

    /**
     * Returns $id_usuario.
     *
     * @see Usuario::$id_usuario
     */
    public function get_id_usuario() {
        return $this->id_usuario;
    }

    /**
     * Returns $nombre.
     *
     * @see Usuario::$nombre
     */
    public function get_nombre() {
        return $this->nombre;
    }

    /**
     * Returns $clave.
     *
     * @see Usuario::$clave
     */
    public function get_clave() {
        return $this->clave;
    }

    /**
     * Returns $login.
     *
     * @see Usuario::$login
     */
    public function get_login() {
        return $this->login;
    }


    public function get_id_punto_venta() {
        return $this->id_punto_venta;
    }

    public function set_id_punto_venta($id) {
        $this->id_punto_venta = $id;
    }

    /**
     * Returns $codAgente.
     *
     * @see Usuario::$codAgente
     */
    public function get_codAgente() {
        return $this->codAgente;
    }

    public function set_codAgente($codAgente) {
        $this->codAgente = $codAgente;
    }

    /**
     * Returns $llaveCnx.
     *
     * @see Usuario::$llaveCnx
     */
    public function get_llaveCnx() {
        return $this->llaveCnx;
    }

    public function set_llaveCnx($llaveCnx) {
        $this->llaveCnx = $llaveCnx;
    }

    /*--------------------------------------------------------------------------------*/
    /*Métodos*/
    /*--------------------------------------------------------------------------------*/


    public static function instanciar() {
        return new Usuario();
    }

    public function puedeIngresarWSEpayco($login, $clave, $codAgente, $llaveCnx) {

        $login = trim($login);
        $this->login = $login;
        $clave = trim($clave);
        $this->clave = $clave;
        $codAgente = trim($codAgente);
        $this->codAgente = $codAgente;
        $llaveCnx = trim($llaveCnx);
        $this->llaveCnx = $llaveCnx;

        //seteamos default
        $codigoAgente = '-1';
        $login_user = 'x';
        $contrasenia = '-';
        $firma = "...";
//        LOG::write_error("$login, $clave, $codAgente, $llaveCnx");
        switch ($codAgente)
        {
            case '100':{	//ePayco
                $codigoAgente = '100';
                $login_user = 'ePayco';
                $contrasenia = openssl_digest('ePayco', 'sha256');
                $firma = "$codigoAgente~$login_user~$contrasenia";
                $firma = openssl_digest($firma, 'sha256');
                break;
            }
        }
//        LOG::write_error("$contrasenia, $firma");

        return ($this->login == $login_user && $this->clave == $contrasenia && $this->codAgente == $codigoAgente && $llaveCnx == $firma);
    }

}