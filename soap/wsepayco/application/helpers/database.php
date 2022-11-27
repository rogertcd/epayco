<?php 
require_once (LIB_PATH."adodb5/adodb.inc.php");
require_once (LIB_PATH."adodb5/adodb-errorhandler.inc.php");

class Database {

    private $conn = NULL;
    private $db;

    private const ASOCIAR_NUMEROS = ADODB_FETCH_NUM;
    private const ASOCIAR_COLUMNAS = ADODB_FETCH_ASSOC;

    public function __construct() {
//      self::__construct();
    }

    public function debug($debug = TRUE){
        $this->conn->debug = $debug;
    }

//  public function instanciar() {
//    return new Database();
//  }

    private function get_db() {
        return $this->db;
    }

    private function get_conn() {
        if ($this->conn == NULL){
            $this->conectar();
        }
        return $this->conn;
    }

    public function last_id(){
        return  $this->get_conn()->Insert_ID();
    }

    public function set_autocommit ($set){
        if ($set)
            $this->get_conn()->Execute('SET AUTOCOMMIT = 1');
        else
            $this->get_conn()->Execute('SET AUTOCOMMIT = 0');
    }

    public function lock_tables ($tableList){

        $cant = count($tableList);
        if ($cant == 0) return;

        $i = 0;
        $sql = 'LOCK TABLES ';

        foreach ($tableList as $table){
            $sql .= "$table AS r$table READ, $table WRITE";
            if ($i != $cant -1)
                $sql .= ', ';
            $i++;
        }

        $this->query($sql);
    }

    public function unlock_tables (){
        $sql = 'UNLOCK TABLES';
        $this->query($sql);
    }



    public function get_one($sql, $params = NULL, $modo = ADODB_FETCH_NUM){

        $this->get_conn()->SetFetchMode($modo);

        $res = NULL;
        if ($params != NULL)
            $res = &$this->get_conn()->GetOne($sql, $params);
        else
            $res = &$this->get_conn()->GetOne($sql);

        if (is_bool($res) && ! $res)
            throw new Exception($this->conn->ErrorMsg('Error al obtener el dato en la consulta'));

        return $res;
    }

    public function get_row($sql, $params = NULL, $modo = ADODB_FETCH_NUM){

        $this->get_conn()->SetFetchMode($modo);

        $res = NULL;
        if ($params != NULL)
            $res = &$this->get_conn()->GetRow($sql, $params);
        else
            $res = &$this->get_conn()->GetRow($sql);

        if (is_bool($res) && ! $res)
            throw new Exception($this->conn->ErrorMsg('Error al obtener los datos de la consulta'));

        return $res;
    }

    public function query($sql, $params = NULL, $modo = ADODB_FETCH_NUM) {
        $this->get_conn()->SetFetchMode($modo);

        $recordSet = NULL;

        if ($params != NULL)
            $recordSet = &$this->get_conn()->Execute($sql, $params);
        else
            $recordSet = &$this->get_conn()->Execute($sql);

        if (!$recordSet){
            throw new Exception($this->conn->ErrorMsg('Error al ejecutar la consulta'));
        }

        return $recordSet;
    }

    public function insert($sql, $params = NULL){
        //$this->debug();
        if ($params != NULL)
            $rs = $this->get_conn()->Execute($sql, $params);
        else
            $rs = $this->get_conn()->Execute($sql);

        if (!is_object($rs) && !$rs)
            throw new Exception($this->conn->ErrorMsg('Error al insertar los nuevos datos'));

        return $this->get_conn()->Insert_ID();
    }

    public function update($sql, $params = NULL){
        //$this->debug();
        if ($params != NULL)
            $rs = $this->get_conn()->Execute($sql, $params);
        else
            $rs = $this->get_conn()->Execute($sql);

        if (!is_object($rs) && !$rs)
            throw new Exception($this->conn->ErrorMsg('Error al actualizar los datos de la consulta'));

        return $this->get_conn()->Affected_Rows();
    }

    public function delete($sql, $params = NULL){
        //$this->debug();
        if ($params != NULL)
            $rs = $this->get_conn()->Execute($sql, $params);
        else
            $rs = $this->get_conn()->Execute($sql);

        if (!is_object($rs) && !$rs)
            throw new Exception($this->conn->ErrorMsg('Error al eliminar los datos'));

        return $this->get_conn()->Affected_Rows();
    }

    public function begin_transaction() {
        //$this->set_autocommit(FALSE);
        $this->get_conn()->StartTrans();
    }

    public function exists_error() {
        return $this->get_conn()->HasFailedTrans();
    }

    public function end_transaction() {
        $this->get_conn()->CompleteTrans();
        //$this->set_autocommit(TRUE);
    }

    public function rollback() {
        $this->get_conn()->FailTrans(); //fuerza el rollback
    }

    public function commit() {
        $this->get_conn()->commitTrans();
    }

    private function conectar() {
        $this->conn = ADONewConnection(config_item('db_driver'));

        $this->db = $this->conn->Connect(config_item('bd_url'), config_item('bd_user'), config_item('bd_psw'), config_item('bd_database_name'));
        if (!$this->db) {
            LOG::write_log('No se pudo conectar con la Base de Datos');
            throw new Exception('No se pudo conectar con la Base de Datos');
        }
        //$this->conn->autoRollback = true; # default is false
    }

    public function desconectar() {
        //if ($this->conn)
        if ($this->conn != NULL){
            $this->conn->Close(); # optional
            $this->conn = NULL;
        }
    }

    
}
