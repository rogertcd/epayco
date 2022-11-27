const express = require('express');
const app = express();
const server = require('http').Server(app);


const bodyParser = require('body-parser');
const router = require('./network/routes');
const config = require('./config');

app.use(bodyParser.urlencoded({extended: false}));

// app.use('/app', express.static('public'));

// socket.connect(server);
router(app);

server.listen(config.port, (error) => {
    if (error) {
        console.log('Error initializing server: ' + error);
    } else {
        console.log('Environment:', config.NODE_ENV);
        console.log(`App is ready at: ${ config.port }`);
    }
});