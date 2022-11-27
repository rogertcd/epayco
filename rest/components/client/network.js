const express = require('express');
const router = express.Router();
const response = require("../../network/response");
const controller = require('./controller');

router.post('/create', (req, res) => {
    controller.addClient(req, res)
        .then((resp) => {
            if (resp.success.$value) {
                response.success(req, res, '', 201);
            } else {
                console.log('[networkClient Soap response] error al guardar los datos');
                response.error(req, res, resp.message_error.$value, resp.cod_error.$value, resp.message_error.$value);
            }
        })
        .catch((error) => {
            console.log('[networkClient] error al guardar los datos');
            response.error(req, res, error, 400, error);
        })

});

module.exports = router;