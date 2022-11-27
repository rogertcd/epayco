const config = require('../../config');
const soap = require('soap');
const SHA256 = require("crypto-js/sha256");
const { body, validationResult } = require('express-validator');

let addClient = (req, res) => {
    return new Promise((resolve, reject) => {
        body("document").notEmpty().isLength({ min: 4 }).trim().escape();
        body("name").notEmpty().isLength({ min: 2 }).trim().escape();
        body("email").notEmpty().isEmail().normalizeEmail();
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
                name: req.body.name,
                email: req.body.email,
                phone: req.body.phone,
            };

            soap.createClient(config.soapUrl, (err, client) => {
                try {
                    // console.log({client, err});
                    client.wsRegistrarCliente(args, (error, result) => {
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
    addClient
}