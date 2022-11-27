// const message = require('../components/message/network');
const client = require('../components/client/network');
const wallet = require('../components/wallet/network');

const routes = (server) => {
    server.use('/client', client);
    // server.use('/', (req, res) => {
    //     console.log('Siiii');
    //     res.send('Welcome');
    // });
    server.use('/wallet', wallet);
}

module.exports = routes;