const config = {
    soapUrl: process.env.SOAP_URL || 'http://epayco-soap/wsepayco/index.php?wsdl',
    soapLogin: process.env.SOAP_LOGIN || 'ePayco',
    soapPassword: process.env.SOAP_PASSWORD || 'ePayco',
    soapAgente: process.env.SOAP_AGENTE || 100,
    appUrl: process.env.APP_URL || 'localhost',
    port: process.env.PORT || 4000,
}

module.exports = config;