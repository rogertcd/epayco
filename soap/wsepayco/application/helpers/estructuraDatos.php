<?php

//incluir la respuesta generica
include_once('respuesta_generica.php');


//Array de Enteros
generar_array_tipo($this->nusoap_server,
    'arrEnteros',
    'int'
);

//Array de Dobles
generar_array_tipo($this->nusoap_server,
    'arrDobles',
    'double'
);


//Array de Cadenas
generar_array_tipo($this->nusoap_server,
    'arrCadenas',
    'string'
);


//Array de Fechas
generar_array_tipo($this->nusoap_server,
    'arrFechas',
    'date'
);


//Array de Horas
generar_array_tipo($this->nusoap_server,
    'arrHoras',
    'time'
);

generar_array_tipo($this->nusoap_server,
    'arrBooleanos',
    'boolean'
);
