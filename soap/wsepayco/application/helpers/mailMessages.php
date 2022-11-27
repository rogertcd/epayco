<?php

class MailMessages
{
    public function makeMessage($sessionId, $token, $monto, $compra) {
        return '<b>CONFIRME EL PAGO</b><br/><br/>' .
            'Por favor confirme el pago que se est&aacute; intentando realizar por la siguiente compra:<br/>' .
            '<b>DETALLE: </b>' . $compra . '<br/>' .
            '<b>MONTO: </b>' . $monto . '<br/><br/><br/>' .
            '<b>SESSION_ID: </b>' . $sessionId . '<br/>' .
            '<b>TOKEN: </b>' . $token . '<br/><br/><br/>'.
            '<b>Por favor confirme en el pago dentro de los siguientes 5 minutos<br/>';
    }
}