# Descripción

Reto de programación backend para ePayco

## SOAP
Proyecto desarrollado en PHP 7.4, utilizando nuSOAP con una Base de Datos MySQL 8

## REST
Proyecto desarrollado en node 19, utilizando Express

## Herramientas y tecnologías utilizadas

- Node 19
- PHP 7.4
- MySQL 8
- Docker y docker-compose
- Github
- Postman

## Requerimientos

- Docker y docker-compose instalados
- Git instalado

## Instalación

1. Abrir la terminal o línea de comandos de su preferencia y clonar el proyecto desde Github
```
git clone https://github.com/rogertcd/epayco.git
```
2. Ingrese al directorio que se acaba de crear llamado `epayco`
```
cd epayco
```
3. Crear un archivo llamado `.env` y copiar el contenido del archivo `.env.example` a `.env`


4. Ingrese al directorio `rest`
```
cd rest
```
5. Crear un archivo llamado `.env` y copiar el contenido del archivo `.env.example` a `.env`


6. Vuelva al directorio raíz `epayco`
```
cd ..
```
7. Ejecute el siguiente comando de docker-compose
```
docker-compose up -d
```

8. Para realizar pruebas de los endpoints, importe la colección del archivo `ePayco.postman_collection.json` con Postman 



## Tiempo de desarrollo

48 horas aproximadamente