const config = require('../../config');
const soap = require('soap');
const SHA256 = require("crypto-js/sha256");
const { body, validationResult } = require('express-validator');

let balanceQuery = (req, res) => {
    return new Promise((resolve, reject) => {
        body("document").notEmpty().isLength({ min: 4 }).trim().escape();
        body("phone").notEmpty().isLength({ min: 5 }).trim().escape();
        const errors = validationResult(req);
        if (!errors.isEmpty()) {
            reject(errors.array());
        } else {
            const args = {
                login: config.soapLogin,
                password: SHA256(config.soapPassword).toString(),
                codAgente: config.soapAgente,
                llaveCnx: SHA256(`${config.soapAgente}~${config.soapLogin}~${SHA256(config.soapPassword)}`).toString(),
                document: req.body.document,
                phone: req.body.phone,
            };

            soap.createClient(config.soapUrl, (err, client) => {
                try {
                    // console.log({client, err});
                    client.wsConsultaSaldo(args, (error, result) => {
                        if (error) {
                            reject(`Error: ${error}`);
                        } else {
                            resolve(result.return);
                        }
                    });
                } catch (error) {
                    reject(`Error: ${error}`);
                }
            });
        }
    })
}

let creditCharge = (req, res) => {
    return new Promise((resolve, reject) => {
        body("document").notEmpty().isLength({ min: 4 }).trim().escape();
        body("phone").notEmpty().isLength({ min: 5 }).trim().escape();
        body("amount").notEmpty().trim().escape();
        const errors = validationResult(req);
        if (!errors.isEmpty()) {
            reject(errors.array());
        } else {
            const args = {
                login: config.soapLogin,
                password: SHA256(config.soapPassword).toString(),
                codAgente: config.soapAgente,
                llaveCnx: SHA256(`${config.soapAgente}~${config.soapLogin}~${SHA256(config.soapPassword)}`).toString(),
                document: req.body.document,
                phone: req.body.phone,
                amount: req.body.amount,
            };

            soap.createClient(config.soapUrl, (err, client) => {
                try {
                    client.wsRecargarCredito(args, (error, result) => {
                        if (error) {
                            reject(`Error: ${error}`);
                        } else {
                            resolve(result.return);
                        }
                    });
                } catch (error) {
                    reject(`Error: ${error}`);
                }
            });
        }
    })
}

let payOrder = (req, res) => {
    return new Promise((resolve, reject) => {
        body("document").notEmpty().isLength({ min: 4 }).trim().escape();
        body("phone").notEmpty().isLength({ min: 5 }).trim().escape();
        body("description_order").isLength({ min: 5 }).trim().escape();
        body("price_order").notEmpty().trim().escape();
        const errors = validationResult(req);
        if (!errors.isEmpty()) {
            reject(errors.array());
        } else {
            const args = {
                login: config.soapLogin,
                password: SHA256(config.soapPassword).toString(),
                codAgente: config.soapAgente,
                llaveCnx: SHA256(`${config.soapAgente}~${config.soapLogin}~${SHA256(config.soapPassword)}`).toString(),
                document: req.body.document,
                phone: req.body.phone,
                description_order: req.body.description_order,
                price_order: req.body.price_order,
            };

            soap.createClient(config.soapUrl, (err, client) => {
                try {
                    client.wsRealizarPagoCompra(args, (error, result) => {
                        if (error) {
                            reject(`Error: ${error}`);
                        } else {
                            resolve(result.return);
                        }
                    });
                } catch (error) {
                    reject(`Error: ${error}`);
                }
            });
        }
    })
}

let payConfirm = (req, res) => {
    return new Promise((resolve, reject) => {
        body("session_id").notEmpty().isLength({ min: 64 }).trim().escape();
        body("token").notEmpty().isLength({ min: 6 }).trim().escape();
        const errors = validationResult(req);
        if (!errors.isEmpty()) {
            reject(errors.array());
        } else {
            const args = {
                login: config.soapLogin,
                password: SHA256(config.soapPassword).toString(),
                codAgente: config.soapAgente,
                llaveCnx: SHA256(`${config.soapAgente}~${config.soapLogin}~${SHA256(config.soapPassword)}`).toString(),
                session_id: req.body.session_id,
                token: req.body.token,
            };

            soap.createClient(config.soapUrl, (err, client) => {
                try {
                    client.wsConfirmarPagoCompra(args, (error, result) => {
                        if (error) {
                            reject(`Error: ${error}`);
                        } else {
                            resolve(result.return);
                        }
                    });
                } catch (error) {
                    reject(`Error: ${error}`);
                }
            });
        }
    })
}


module.exports = {
    balanceQuery,
    creditCharge,
    payOrder,
    payConfirm
}