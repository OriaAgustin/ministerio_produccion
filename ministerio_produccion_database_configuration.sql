CREATE DATABASE ministerio_produccion;

USE ministerio_produccion;

CREATE TABLE usuarios_internos ( email varchar(50) NOT NULL, nombre_completo varchar(50) NOT NULL, password varchar(100) NOT NULL, PRIMARY KEY (email) );

CREATE TABLE empresas ( cuit int unsigned NOT NULL, nombre varchar(50) NOT NULL, password varchar(100) NOT NULL, PRIMARY KEY (cuit ) );

CREATE TABLE empleados ( dni int unsigned NOT NULL, nombre_completo varchar(50) NOT NULL, cuit int unsigned NOT NULL, PRIMARY KEY (dni), FOREIGN KEY (cuit) REFERENCES empresas(cuit) ON DELETE CASCADE );

INSERT INTO empresas ( cuit, nombre, password ) VALUES ( 522155485, 'STARBUCKS', PASSWORD('cafecafe') );
INSERT INTO empresas ( cuit, nombre, password ) VALUES ( 778811546, 'TIERRA DE NADIE', PASSWORD('burgerburger') );

INSERT INTO empleados ( dni, nombre_completo, cuit ) VALUES ( 32378944, 'LOPEZ JUAN', 522155485 );
INSERT INTO empleados ( dni, nombre_completo, cuit ) VALUES ( 31522567, 'HERNANDEZ ARIEL', 522155485 );
INSERT INTO empleados ( dni, nombre_completo, cuit ) VALUES ( 33888791, 'SUAREZ MARTIN', 778811546 );
INSERT INTO empleados ( dni, nombre_completo, cuit ) VALUES ( 34795215, 'GOMEZ SANTIAGO', 778811546 );

INSERT INTO usuarios_internos ( email, nombre_completo, password ) VALUES ( 'agustin.oria@gmail.com', 'ORIA AGUSTIN', PASSWORD('a') );
