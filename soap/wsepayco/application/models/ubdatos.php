<?php
class Ubdatos {

    public function registrarCliente($document, $name, $email, $phone)
    {
        $error = '00';
        $success = true;
        $mensajeError = '';
        try {
            $database = Dao::get_database();
            $checkUniqueUser = $this->checkUniqueUser($document, $email, $phone);
            if (empty($checkUniqueUser)) {
                // Iniciando transacciones
                $database->begin_transaction();
                $sql = "INSERT INTO users(document, name, email, phone) VALUES(?, ?, ?, ?)";
                $params = array(
                    $document,
                    ucwords($name),
                    $email,
                    $phone
                );
                $idInsertado = $database->insert($sql, $params);
                if ($database->exists_error()) {// Verificando si no hay errores
                    $error = 500;
                    $success = false;
                    $database->rollback();
                } else {
                    $walletInsert = $this->registrarWallet($idInsertado, $phone);
                    if ($walletInsert == 0) {
                        $error = 400;
                        $success = false;
                        $mensajeError = "The wallet hast not been saved";
                    }
                    $database->commit();
                }
                // Finalizando transacciones
                $database->end_transaction();
            } else {
                $error = 400;
                $success = false;
                $mensajeError = "The document, email and phone must be unique.";
            }
            $database->desconectar();
        } catch (Exception $ex) {
            LOG::write_error("registrarCliente($document, $name, $email, $phone), ERROR: ". $ex->getMessage());
            $error = 500;
            $success = false;
            $mensajeError = 'The cliente has not been saved';
        }

        return array(
            'success' => $success,
            'cod_error' => $error,
            'message_error' => $mensajeError
        );
    }

    public function checkUniqueUser($document, $email, $phone) {
        try {
            $database = Dao::get_database();
            $sql = 'SELECT user_id';
            $sql .= " FROM users";
            $sql .= ' WHERE document = ? OR email = ? OR phone = ?';
            return $database->get_one($sql, array($document, $email, $phone));
        } catch (Exception $ex) {
            LOG::write_error(print_r("checkUniqueUser($document, $email, $phone), error: " . $ex->getMessage(), 1));
            return FALSE;
        }
    }

    public function registrarWallet($userId, $phone)
    {
        try {
            $database = Dao::get_database();
            $sql = "INSERT INTO wallets(phone, balance, user_id) VALUES(?, ?, ?)";
            $params = array(
                $phone,
                0,
                $userId
            );
            $idInsertado = $database->insert($sql, $params);
        } catch (Exception $ex) {
            LOG::write_error("registrarWallet($userId, $phone), ERROR: ". $ex->getMessage());
            $idInsertado = 0;
        }
        return $idInsertado;
    }

    public function recargarCredito($document, $phone, $amount)
    {
        $error = '00';
        $success = true;
        $mensajeError = '';
        try {
            $database = Dao::get_database();
            $userId = $this->getUserId($document, $phone);
            if (!empty($userId)) {
                $walletId = $this->getWalletId($userId, $phone);
                if (!empty($walletId)) {
                    // Iniciando transacciones
                    $database->begin_transaction();
                    $sql = "INSERT INTO charges(amount, wallet_id) VALUES(?, ?)";
                    $params = array(
                        $amount,
                        $walletId
                    );
                    $idInsertado = $database->insert($sql, $params);
                    if ($database->exists_error()) {// Verificando si no hay errores
                        $error = 500;
                        $success = false;
                        $database->rollback();
                    } else {
                        $this->updateBalanceWallet($walletId, $amount);
                        $database->commit();
                    }
                    // Finalizando transacciones
                    $database->end_transaction();
                } else {
                    $error = 400;
                    $success = false;
                    $mensajeError = "Document and phone do not have a registered wallet.";
                }
            } else {
                $error = 400;
                $success = false;
                $mensajeError = "The document and phone combination is not registered.";
            }

            $database->desconectar();
        } catch (Exception $ex) {
            LOG::write_error("recargarCredito($document, $phone, $amount), ERROR: ". $ex->getMessage());
            $error = 500;
            $success = false;
            $mensajeError = 'The credit could not be registered to the wallet';
        }

        return array(
            'success' => $success,
            'cod_error' => $error,
            'message_error' => $mensajeError
        );
    }

    public function consultaSaldo($document, $phone)
    {
        $error = '00';
        $success = true;
        $mensajeError = '';
        $balanceAmount = 0;
        try {
            $database = Dao::get_database();
            $userId = $this->getUserId($document, $phone);
            if (!empty($userId)) {
                $walletId = $this->getWalletId($userId, $phone);
                if (!empty($walletId)) {
                    $balanceAmount = $this->getCurrentWalletBalance($walletId);
//                    $balance[] = array(
//                        'balance' => $balanceAmount
//                    );
                } else {
                    $error = 400;
                    $success = false;
                    $mensajeError = "Document and phone do not have a registered wallet.";
                }
            } else {
                $error = 400;
                $success = false;
                $mensajeError = "The document and phone combination is not registered.";
            }

            $database->desconectar();
        } catch (Exception $ex) {
            LOG::write_error("consultaSaldo($document, $phone), ERROR: ". $ex->getMessage());
            $error = 500;
            $success = false;
            $mensajeError = 'Error while get the balance from the wallet';
        }

        return array(
            'success' => $success,
            'cod_error' => $error,
            'message_error' => $mensajeError,
            'balance' => $balanceAmount,
        );
    }

    public function realizarPagoCompra($document, $phone, $description, $price)
    {
        $error = '00';
        $success = true;
        $mensajeError = '';
        $messageSuccess = '';
        $balanceAmount = 0;
        try {
            $database = Dao::get_database();
            $userId = $this->getUserId($document, $phone);
            if (!empty($userId)) {
                $walletId = $this->getWalletId($userId, $phone);
                if (!empty($walletId)) {
                    $balanceAmount = $this->getCurrentWalletBalance($walletId);
                    if ($balanceAmount >= $price) {
                        $generatedSession = $this->generarSessionId();
                        if ($generatedSession['session_id'] > 0) {
                            $mailer = new PHPMailerUse();
                            $messages = new MailMessages();
                            $mensaje = $messages->makeMessage(
                                openssl_digest($generatedSession['session_id'], 'SHA256'),
                                $generatedSession['token'],
                                $price,
                                $description
                            );
                            $userEmail = $this->getUserMail($userId);
                            $asunto = 'CONFIRMACION DE PAGO';
                            $mailer->sendMail($userEmail, $asunto, $mensaje);
                            $messageSuccess = "Please check your mail inbox in order to confirm the payment.";

                            // Iniciando transacciones
                            $database->begin_transaction();
                            $sql = "INSERT INTO payments(amount, description, session_id, wallet_id) VALUES(?, ?, ?, ?)";
                            $params = array(
                                $price,
                                $description,
                                $generatedSession['session_id'],
                                $walletId
                            );
                            $idInsertado = $database->insert($sql, $params);
                            if ($database->exists_error()) {// Verificando si no hay errores
                                $error = 500;
                                $success = false;
                                $database->rollback();
                            } else {
                                $database->commit();
                            }
                            // Finalizando transacciones
                            $database->end_transaction();
                        } else {
                            $error = 400;
                            $success = false;
                            $mensajeError = "An error ocurred while the token code was generating.";
                        }
                    } else {
                        $error = 400;
                        $success = false;
                        $mensajeError = "The wallet does not have the enough balance for the required pay.";
                    }
                } else {
                    $error = 400;
                    $success = false;
                    $mensajeError = "Document and phone do not have a registered wallet.";
                }
            } else {
                $error = 400;
                $success = false;
                $mensajeError = "The document and phone combination is not registered.";
            }

            $database->desconectar();
        } catch (Exception $ex) {
            LOG::write_error("consultaSaldo($document, $phone), ERROR: ". $ex->getMessage());
            $error = 500;
            $success = false;
            $mensajeError = 'An error ocurred while the transacction was doing.';
        }

        return array(
            'success' => $success,
            'cod_error' => $error,
            'message_error' => $mensajeError,
            'message_success' => $messageSuccess,
        );
    }

    public function confirmarPagoCompra($sessionId, $token)
    {
        $error = '00';
        $success = true;
        $mensajeError = '';
        $messageSuccess = '';
        $balanceAmount = 0;
        try {
            $database = Dao::get_database();
            $tokenEncrypted = openssl_digest($token, 'SHA256');
            $sessionIdDataBase = $this->getSessionByToken($tokenEncrypted);
            $sessionIdEncrypted = openssl_digest($sessionIdDataBase, 'SHA256');
            if ($sessionIdDataBase > 0 && $sessionIdEncrypted == $sessionId) {
                $payment = $this->getPaymentBySession($sessionIdDataBase);
                if (!empty($payment) && count($payment) > 0) {
                    $balanceAmount = $this->getCurrentWalletBalance($payment[2]);
                    if ($balanceAmount >= $payment[0]) {
                        // Iniciando transacciones
                        $database->begin_transaction();
                        $sql = "UPDATE payments SET confirmed_at = now() WHERE payment_id = ?";
                        $params = array(
                            $payment[4]
                        );
                        $database->update($sql, $params);

                        if ($database->exists_error()) {// Verificando si no hay errores
                            $error = 500;
                            $success = false;
                            $database->rollback();
                        } else {
                            // Update token
                            $this->updateToken($sessionIdDataBase);
                            // Update wallet balance
                            $this->updateBalanceWallet($payment[2], $payment[0], 'payment');
                            $database->commit();
                            $messageSuccess = "The payment has made correctly.";
                        }
                        // Finalizando transacciones
                        $database->end_transaction();
                    } else {
                        $error = 400;
                        $success = false;
                        $mensajeError = "The wallet does not have the enough balance for the required pay.";
                    }
                } else {
                    $error = 400;
                    $success = false;
                    $mensajeError = "Sorry, the payment does not exist.";
                }
            } else {
                $error = 400;
                $success = false;
                $mensajeError = "Incorrect token or/and the session is expired.";
            }

            $database->desconectar();
        } catch (Exception $ex) {
            LOG::write_error("confirmarPagoCompra($sessionId, $token), ERROR: ". $ex->getMessage());
            $error = 500;
            $success = false;
            $mensajeError = 'An error ocurred while the transacction was confirmed.';
        }

        return array(
            'success' => $success,
            'cod_error' => $error,
            'message_error' => $mensajeError,
            'message_success' => $messageSuccess,
        );
    }

    public function getWalletId($userId, $phone) {
        try {
            $database = Dao::get_database();
            $sql = 'SELECT wallet_id';
            $sql .= " FROM wallets";
            $sql .= ' WHERE user_id = ? AND phone = ?';
            return $database->get_one($sql, array($userId, $phone));
        } catch (Exception $ex) {
            LOG::write_error(print_r("getWalletId($userId, $phone), error: " . $ex->getMessage(), 1));
            return FALSE;
        }
    }

    public function getPaymentBySession($sessionId) {
        try {
            $database = Dao::get_database();
            $sql = 'SELECT amount, description, wallet_id, session_id, payment_id';
            $sql .= " FROM payments";
            $sql .= ' WHERE session_id = ? AND confirmed_at IS NULL AND delete_at IS NULL';
            return $database->get_row($sql, array($sessionId));
        } catch (Exception $ex) {
            LOG::write_error(print_r("getPaymentBySession($sessionId), error: " . $ex->getMessage(), 1));
            return null;
        }
    }

    public function getUserId($document, $phone) {
        try {
            $database = Dao::get_database();
            $sql = 'SELECT user_id';
            $sql .= " FROM users";
            $sql .= ' WHERE document = ? AND phone = ?';
            return $database->get_one($sql, array($document, $phone));
        } catch (Exception $ex) {
            LOG::write_error(print_r("getUserId($document, $phone), error: " . $ex->getMessage(), 1));
            return FALSE;
        }
    }

    public function getUserMail($userId) {
        try {
            $database = Dao::get_database();
            $sql = 'SELECT email';
            $sql .= " FROM users";
            $sql .= ' WHERE user_id = ?';
            return $database->get_one($sql, array($userId));
        } catch (Exception $ex) {
            LOG::write_error(print_r("getUserMail($userId), error: " . $ex->getMessage(), 1));
            return '';
        }
    }

    public function getCurrentWalletBalance($walletId) {
        try {
            $database = Dao::get_database();
            $sql = 'SELECT SUM(amount) AS balance';
            $sql .= " FROM charges";
            $sql .= ' WHERE wallet_id = ? AND delete_at IS NULL';
            $charges = $database->get_one($sql, array($walletId));
            $sql = 'SELECT SUM(amount) AS pay';
            $sql .= " FROM payments";
            $sql .= ' WHERE wallet_id = ? AND (confirmed_at IS NOT NULL AND session_id IS NOT NULL AND delete_at IS NULL)';
            $payments = $database->get_one($sql, array($walletId));
            return $charges - $payments;
        } catch (Exception $ex) {
            LOG::write_error(print_r("getCurrentWalletBalance($walletId), error: " . $ex->getMessage(), 1));
            return 0;
        }
    }

    public function updateBalanceWallet($walletId, $amount, $operation = 'plus') {
        try {
            $database = Dao::get_database();
            $sql = 'UPDATE wallets SET';
            $sql .= ($operation == 'plus') ? ' balance = balance + ' . $amount : ' balance = balance - ' . $amount ;
            $sql .= ' WHERE wallet_id = ?';
            return $database->update($sql, array($walletId));
        } catch (Exception $ex) {
            LOG::write_error(print_r("updateBalanceWallet($walletId, $amount, $operation), error: " . $ex->getMessage(), 1));
            return 0;
        }
    }

    public function updateToken($sessionId) {
        try {
            $database = Dao::get_database();
            $sql = 'UPDATE tokens SET';
            $sql .= ' confirmed_at = now() WHERE session_id = ?';
            return $database->update($sql, array($sessionId));
        } catch (Exception $ex) {
            LOG::write_error(print_r("updateToken($sessionId), error: " . $ex->getMessage(), 1));
            return 0;
        }
    }

    public function generarSessionId()
    {
        $newToken = '';
        try {
            $newToken = Seguridad::generateToken(6);
            $database = Dao::get_database();
            $sql = "INSERT INTO tokens(token, insert_at, valid_until) VALUES(?, now(), DATE_ADD(now(), INTERVAL 5 MINUTE))";
            $params = array(
                openssl_digest($newToken, 'SHA256')
            );
            $idInsertado = $database->insert($sql, $params);
        } catch (Exception $ex) {
            LOG::write_error("generarSessionId(), ERROR: ". $ex->getMessage());
            $idInsertado = 0;
        }
        return array(
            'session_id' => $idInsertado,
            'token' => $newToken,
        );
    }

    public function getSessionByToken($token) {
        try {
            $database = Dao::get_database();
            $sql = 'SELECT session_id';
            $sql .= " FROM tokens";
            $sql .= ' WHERE token = ? AND now() < valid_until AND confirmed_at IS NULL';
            return $database->get_one($sql, array($token));
        } catch (Exception $ex) {
            LOG::write_error(print_r("getSessionByToken($token), error: " . $ex->getMessage(), 1));
            return 0;
        }
    }

}