exports.success = ((req, res, message, status) => {
    res.status(status || 200).send({
        success: true,
        cod_error: '00',
        message_error: '',
        data: message
    })
});

exports.error = ((req, res, message, status, error) => {
    if (error) {
        console.log(error);
    }
    res.status(status || 500).send({
        success: false,
        cod_error: status || 500,
        message_error: message,
        data: null
    })
});
