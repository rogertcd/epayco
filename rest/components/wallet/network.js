const express = require('express');
const router = express.Router();
const response = require("../../network/response");
const controller = require('./controller');

router.get('/balance', (req, res) => {
    controller.balanceQuery(req, res)
        .then((resp) => {
            if (resp.success.$value) {

                response.success(req, res, {'balance': resp.balance.$value}, 200);
            } else {
                console.log('[networkWallet Soap response] error realizar la consulta de saldo');
                response.error(req, res, resp.message_error.$value, resp.cod_error.$value, resp.message_error.$value);
            }
        })
        .catch((error) => {
            console.log('[networkWallet] error realizar la consulta de saldo');
            response.error(req, res, error, 500, error);
        })

});

router.post('/charge', (req, res) => {
    controller.creditCharge(req, res)
        .then((resp) => {
            if (resp.success.$value) {
                response.success(req, res, '', 201);
            } else {
                console.log('[networkWallet Soap response] error al recargar credito');
                response.error(req, res, resp.message_error.$value, resp.cod_error.$value, resp.message_error.$value);
            }
        })
        .catch((error) => {
            console.log('[networkWallet] error al recargar credito');
            response.error(req, res, error, 400, error);
        })

});

router.post('/payorder', (req, res) => {
    controller.payOrder(req, res)
        .then((resp) => {
            if (resp.success.$value) {
                response.success(req, res, {'message_success': resp.message_success.$value}, 201);
            } else {
                console.log('[networkWallet Soap response] error al pagar la orden');
                response.error(req, res, resp.message_error.$value, resp.cod_error.$value, resp.message_error.$value);
            }
        })
        .catch((error) => {
            console.log('[networkWallet] error al pagar la orden');
            response.error(req, res, error, 400, error);
        })

});

router.post('/payconfirm', (req, res) => {
    controller.payConfirm(req, res)
        .then((resp) => {
            if (resp.success.$value) {
                response.success(req, res, {'message_success': resp.message_success.$value}, 201);
            } else {
                console.log('[networkWallet Soap response] error al confirmar el pago');
                response.error(req, res, resp.message_error.$value, resp.cod_error.$value, resp.message_error.$value);
            }
        })
        .catch((error) => {
            console.log('[networkWallet] error al confirmar el pago');
            response.error(req, res, error, 400, error);
        })

});

module.exports = router;