create database api_tienda;


create table tienda (
    id int primary key auto_increment,
    tienda_nombre varchar(50) not null,
    tienda_direccion varchar(50) not null,
    tienda_telefono varchar(50) not null,
    tienda_encargado varchar(50) not null
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

create table productros(
  id int primary key auto_increment,
  prod_nombre varchar(50) not null,
  prod_codigo varchar(50) not null,
  prod_marca varchar(10) not null,
  prod_precio varchar(15) not null,
  prod_tmanio varchar(30) not null,
  tienda_id INT,
  FOREIGN KEY (tienda_id) REFERENCES tienda(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

use api_ejercicio3_tienda;
INSERT INTO tienda VALUES('','tienda','a una cuadra','099999','la vesi');
use api_ejercicio3_tienda;
INSERT INTO productros VALUES('','chispis','bhasdk','chispis','0.35','50g',3);

use api_ejercicio3_tienda;
Select * FROM tienda;

SELECT tienda.tienda_nombre, tienda.tienda_telefono, tienda.tienda_direccion, productros.prod_nombre, productros.prod_precio FROM productros JOIN tienda ON productros.tienda_id = tienda.id where tienda_nombre= "shopfanny";

use api_ejercicio3_tienda;
SELECT tienda.tienda_nombre, tienda.tienda_encargado, productros.prod_nombre, productros.prod_codigo, productros.prod_marca, productros.prod_precio, productros.prod_tmanio FROM productros JOIN tienda ON productros.tienda_id = tienda.id where tienda_id;
  where empresa_id=:empresa_id;