CREATE TABLE `marcas` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(255) NOT NULL,
    `descripcion` VARCHAR(255) NOT NULL,
    UNIQUE (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET=utf8mb4;

-- Tabla categorias
CREATE TABLE `categorias` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(255) NOT NULL,
    `descripcion` VARCHAR(255) NOT NULL,
    UNIQUE (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET=utf8mb4;

-- Tabla productos
CREATE TABLE `productos` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(255) NOT NULL,
    `precio` DECIMAL(10,2) NOT NULL,
    `stock` INT NOT NULL,
    `marca_id` INT NOT NULL,
    `categoria_id` INT NOT NULL,
    `descripcion` VARCHAR(255) NOT NULL,
    UNIQUE (`nombre`, `marca_id`),
    CONSTRAINT `fk_productos_marcas` FOREIGN KEY (`marca_id`) REFERENCES `marcas`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_productos_categorias` FOREIGN KEY (`categoria_id`) REFERENCES `categorias`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET=utf8mb4;

-- Tabla productos_gunpla
CREATE TABLE `productos_gunpla` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `productos_id` INT NOT NULL,
    `grado` VARCHAR(255) NOT NULL,
    `escala` VARCHAR(255) NOT NULL,
    `premium_bandai` BOOLEAN NOT NULL,
    `gundam_base` BOOLEAN NOT NULL,
    CONSTRAINT `fk_gunpla_productos` FOREIGN KEY (`productos_id`) REFERENCES `productos`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET=utf8mb4;

-- Tabla productos_lego
CREATE TABLE `productos_lego` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `productos_id` INT NOT NULL,
    `linea` VARCHAR(255) NOT NULL,
    `numero_set` VARCHAR(255) NOT NULL UNIQUE,
    `numero_piezas` BIGINT NOT NULL,
    CONSTRAINT `fk_lego_productos` FOREIGN KEY (`productos_id`) REFERENCES `productos`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET=utf8mb4;

-- Tabla productos_blokees
CREATE TABLE `productos_blokees` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `productos_id` INT NOT NULL,
    `version` VARCHAR(255) NOT NULL,
    `tamano` VARCHAR(255) NOT NULL,
    CONSTRAINT `fk_blokees_productos` FOREIGN KEY (`productos_id`) REFERENCES `productos`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Datos iniciales
-- =============================================

-- Marcas
INSERT INTO marcas (nombre, descripcion) VALUES 
('Bandai', 'Fabricante japonés líder en Gunpla'),
('LEGO', 'Empresa danesa de bloques de construcción'),
('Blokees', 'Marca de figuras coleccionables de bloques');

-- Categorías
INSERT INTO categorias (nombre, descripcion) VALUES
('Gunpla', 'Modelos de Gundam para armar'),
('Sets LEGO', 'Conjuntos de construcción LEGO'),
('Figuras Blokees', 'Figuras coleccionables de bloques');

-- Productos
INSERT INTO productos (nombre, precio, stock, marca_id, categoria_id, descripcion) VALUES
-- Gunpla
('RX-78-2 Gundam Ver. Ka', 4500.00, 15, 1, 1, 'Master Grade del icónico RX-78-2'),
('Strike Freedom Gundam', 3200.00, 8, 1, 1, 'Real Grade con efectos especiales'),
('Barbatos Lupus Rex', 5800.00, 5, 1, 1, 'Perfect Grade de Iron-Blooded Orphans'),

-- LEGO
('Millennium Falcon', 7999.00, 3, 2, 2, 'Set Ultimate Collector Series'),
('Technic Bugatti Chiron', 3499.00, 7, 2, 2, 'Réplica detallada del superdeportivo'),
('Creator Casa Modular', 1299.00, 12, 2, 2, 'Casa de 3 pisos modular'),

-- Blokees
('Pikachu Blokees', 299.00, 25, 3, 3, 'Figura armable de Pikachu'),
('Mario Bros Blokees', 349.00, 20, 3, 3, 'Figura de Mario en bloques'),
('Sonic Blokees', 279.00, 18, 3, 3, 'Figura de Sonic el Erizo');

-- Detalles Gunpla
INSERT INTO productos_gunpla (productos_id, grado, escala, premium_bandai, gundam_base) VALUES
(1, 'MG', '1/100', false, true),
(2, 'RG', '1/144', true, false),
(3, 'PG', '1/60', false, true);

-- Detalles LEGO
INSERT INTO productos_lego (productos_id, linea, numero_set, numero_piezas) VALUES
(4, 'Ultimate Collector Series', '75192', 7541),
(5, 'Technic', '42083', 3599),
(6, 'Creator Expert', '10264', 2569);

-- Detalles Blokees
INSERT INTO productos_blokees (productos_id, version, tamano) VALUES
(7, 'Standard', 'Mediano'),
(8, 'Special Edition', 'Grande'),
(9, 'Classic', 'Pequeño');