CREATE DATABASE green
USE DATABASE green

CREATE TABLE recolection_location(
	id integer PRIMARY KEY AUTO_INCREMENT,
	lat DECIMAL(10, 8) NOT NULL,
	lng DECIMAL(11, 8) NOT NULL
);

CREATE TABLE recolection_types(
	id integer PRIMARY KEY AUTO_INCREMENT,
	code varchar(50) NOT NULL,
	description varchar (100) NOT NULL
);

CREATE TABLE recolection_points(
	id_location integer NOT NULL,
	id_recolection_type integer NOT NULL,
	description varchar(100) NOT NULL,
	address varchar(200) NOT NULL,
	foreign key (id_location) references recolection_location (id),
	foreign key (id_recolection_type) references recolection_types (id)
);