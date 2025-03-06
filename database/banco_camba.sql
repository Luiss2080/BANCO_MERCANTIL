CREATE DATABASE banco_camba;
USE banco_camba;
-- --------------------------------------------------------
-- Estructura de la base de datos 
-- --------------------------------------------------------

-- Tabla Oficina
CREATE TABLE oficina (
    idOficina INT not null AUTO_INCREMENT,
    hash varchar(50) COMMENT 'hash',
    central BOOLEAN NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    CONSTRAINT PK_Oficina PRIMARY KEY (idOficina)
) Engine = InnoDB Charset = utf8;

-- Tabla Persona
CREATE TABLE persona (
    idPersona INT not null AUTO_INCREMENT,
    hash varchar(50) COMMENT 'hash',
    nombre VARCHAR(100) NOT NULL,
    apellidoMaterno VARCHAR(100) NOT NULL,
    apellidoPaterno VARCHAR(100) NOT NULL,
    direccion VARCHAR(255),
    telefono VARCHAR(20),
    email VARCHAR(100),
    fechaNacimiento DATE,
    ci VARCHAR(20),
    idOficina INT,
    CONSTRAINT PK_Persona PRIMARY KEY (idPersona),
    CONSTRAINT FK_Persona FOREIGN KEY (idOficina) REFERENCES oficina (idOficina)
) Engine = InnoDB Charset = utf8;

-- Tabla Usuario (contiene FK a Persona)
CREATE TABLE usuario (
    idUsuario INT not null AUTO_INCREMENT,
    hash varchar(50) COMMENT 'hash',
    ultimoInicioSesion TIMESTAMP NULL DEFAULT NULL,
    intentosFallido INT DEFAULT 0,
    username VARCHAR(50) NOT NULL,
    password BLOB NOT NULL,
    idPersona INT NOT NULL,
    CONSTRAINT PK_Usuario PRIMARY KEY (idUsuario),
    CONSTRAINT FK_Usuario FOREIGN KEY (idPersona) REFERENCES persona (idPersona)
) Engine = InnoDB Charset = utf8;

-- Tabla Cuenta (contiene FK a Persona)
CREATE TABLE cuenta (
    idCuenta INT not null AUTO_INCREMENT,
    hash varchar(50) COMMENT 'hash',
    tipoCuenta ENUM('cuentaAhorro', 'cuentaCorriente') NOT NULL,
    tipoMoneda ENUM('bolivianos', 'dolares') NOT NULL,
    fechaApertura DATE NOT NULL,
    estado ENUM('activa', 'inactiva') NOT NULL DEFAULT 'activa',
    nroCuenta VARCHAR(20) NOT NULL,
    saldo DECIMAL(15,2) NOT NULL DEFAULT 0.0,
    idPersona INT NOT NULL,
    CONSTRAINT PK_Cuenta PRIMARY KEY (idCuenta),
    CONSTRAINT FK_Cuenta FOREIGN KEY (idPersona) REFERENCES persona (idPersona)
) Engine = InnoDB Charset = utf8;

-- Tabla Tarjeta (contiene FK a Cuenta)
CREATE TABLE tarjeta (
    idTarjeta INT not null AUTO_INCREMENT,
    hash varchar(50) COMMENT 'hash',
    estado ENUM('activa', 'inactiva') NOT NULL DEFAULT 'activa',
	tipoTarjeta ENUM('debito', 'credito') NOT NULL,
    nroTarjeta VARCHAR(20) NOT NULL,
    cvv VARCHAR(3) NOT NULL,
    fechaExpiracion VARCHAR(7) NOT NULL,
    pin VARCHAR(4) NOT NULL,
    idCuenta INT NOT NULL,
    CONSTRAINT PK_Tarjeta PRIMARY KEY (idTarjeta),
    CONSTRAINT FK_Tarjeta FOREIGN KEY (idCuenta) REFERENCES cuenta (idCuenta)
) Engine = InnoDB Charset = utf8;

-- Tabla Transaccion (contiene FK a Cuenta)
CREATE TABLE transaccion (
    idTransaccion INT not null AUTO_INCREMENT,
    hash varchar(50) COMMENT 'hash',
    tipoTransaccion ENUM('retiro', 'deposito') NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    descripcion VARCHAR(255),
    monto DECIMAL(15,2) NOT NULL,
    idCuenta INT NOT NULL,
    CONSTRAINT PK_Transaccion PRIMARY KEY (idTransaccion),
    CONSTRAINT FK_Transaccion FOREIGN KEY (idCuenta) REFERENCES cuenta (idCuenta)
) Engine = InnoDB Charset = utf8;

-- Tabla ATM
CREATE TABLE atm (
    idATM INT not null AUTO_INCREMENT,
    hash varchar(50) COMMENT 'hash',
    ubicacion VARCHAR(255) NOT NULL,
    CONSTRAINT PK_Atm PRIMARY KEY (idATM)
) Engine = InnoDB Charset = utf8;

-- Tabla TransaccionATM (tabla de relación entre Transaccion y ATM)
CREATE TABLE transaccion_atm (
    idTransaccion INT NOT NULL,
    idATM INT NOT NULL,
    hash varchar(50) COMMENT 'hash',
    CONSTRAINT PK_TransaccionAtm PRIMARY KEY (idTransaccion, idATM),
    CONSTRAINT FK_TransaccionAtm_1 FOREIGN KEY (idTransaccion) REFERENCES transaccion (idTransaccion),
    CONSTRAINT FK_TransaccionAtm_2 FOREIGN KEY (idATM) REFERENCES atm (idATM)
) Engine = InnoDB Charset = utf8;

-- --------------------------------------------------------
-- Datos iniciales
-- --------------------------------------------------------

-- Oficina central
INSERT INTO Oficina (central, nombre, direccion, telefono) VALUES
(TRUE, 'Casa Matriz', 'Av. Irala #123, Santa Cruz de la Sierra', '3-3456789');

-- Agencias
INSERT INTO Oficina (central, nombre, direccion, telefono) VALUES
(FALSE, 'Agencia Norte', 'Av. Banzer Km 5, Santa Cruz de la Sierra', '3-3456790'),
(FALSE, 'Agencia Sur', 'Av. Santos Dumont #500, Santa Cruz de la Sierra', '3-3456791'),
(FALSE, 'Agencia Este', 'Av. Virgen de Cotoca #300, Santa Cruz de la Sierra', '3-3456792');

-- Administrador
INSERT INTO Persona (nombre, apellidoPaterno, apellidoMaterno, direccion, telefono, email, fechaNacimiento, ci, idOficina) VALUES
('Admin', 'Sistema', 'Banco', 'Oficina Central', '70012345', 'admin@bancocamba.com', '1990-01-01', '1234567', 1);

-- Usuario administrador (Contraseña: admin123)
INSERT INTO Usuario (username, password, idPersona) VALUES
('admin', '$2y$10$HMjGTQT0VjG8aI/5E4fdTeKBIxXB2Sw0p7iVYPUX/tEk.McgJ64mK', 1);

-- Cajeros automáticos
-- INSERT INTO ATM (ubicacion) VALUES

