-- Table brands
CREATE TABLE `brands` (
    `brandId` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `brandName` VARCHAR(255) NOT NULL,
    `brandDescription` VARCHAR(255) NOT NULL,
    `brandStatus` CHAR(3) NOT NULL DEFAULT 'ACT',
    UNIQUE (`brandName`)
) ENGINE=InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET=utf8mb4;

-- Table categories
CREATE TABLE `categories` (
    `categoryId` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `categoryName` VARCHAR(255) NOT NULL,
    `categoryDescription` VARCHAR(255) NOT NULL,
    `categoryStatus` CHAR(3) NOT NULL DEFAULT 'ACT',
    UNIQUE (`categoryName`)
) ENGINE=InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET=utf8mb4;

-- Tabla productos
CREATE TABLE `products` (
    `productId` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `productName` VARCHAR(255) NOT NULL,
    `productPrice` DECIMAL(10,2) NOT NULL,
    `productStock` INT NOT NULL DEFAULT 0,
    `productBrandId` INT NOT NULL,
    `productCategoryId` INT NOT NULL,
    `productDescription` VARCHAR(255) NOT NULL,
    `productImgUrl` VARCHAR(255) NOT NULL,
    `productStatus` CHAR(3) NOT NULL DEFAULT 'ACT',
    UNIQUE (`productName`, `productBrandId`),
    CONSTRAINT `fk_products_brands` FOREIGN KEY (`productBrandId`) REFERENCES `brands`(`brandId`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_products_categories` FOREIGN KEY (`productCategoryId`) REFERENCES `categories`(`categoryId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET=utf8mb4;

-- Table products_gunpla
CREATE TABLE `products_gunpla` (
    `gunplaId` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `gunplaProductId` INT NOT NULL,
    `gunplaGrade` VARCHAR(255) NOT NULL,
    `gunplaScale` VARCHAR(255) NOT NULL,
    `gunplaPremiumBandai` BOOLEAN NOT NULL DEFAULT FALSE,
    `gunplaGundamBase` BOOLEAN NOT NULL DEFAULT FALSE,
    `gunplaStatus` CHAR(3) NOT NULL DEFAULT 'ACT',
    CONSTRAINT `fk_gunpla_products` FOREIGN KEY (`gunplaProductId`) REFERENCES `products`(`productId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET=utf8mb4;

-- Table products_lego
CREATE TABLE `products_lego` (
    `legoId` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `legoProductId` INT NOT NULL,
    `legoLine` VARCHAR(255) NOT NULL,
    `legoSetNumber` VARCHAR(255) NOT NULL UNIQUE,
    `legoPieceCount` BIGINT NOT NULL,
    `legoStatus` CHAR(3) NOT NULL DEFAULT 'ACT',
    CONSTRAINT `fk_lego_products` FOREIGN KEY (`legoProductId`) REFERENCES `products`(`productId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET=utf8mb4;

-- Table products_blokees
CREATE TABLE `products_blokees` (
    `blokeesId` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `blokeesProductId` INT NOT NULL,
    `blokeesVersion` VARCHAR(255) NOT NULL,
    `blokeesSize` VARCHAR(255) NOT NULL,
    `blokeesStatus` CHAR(3) NOT NULL DEFAULT 'ACT',
    CONSTRAINT `fk_blokees_products` FOREIGN KEY (`blokeesProductId`) REFERENCES `products`(`productId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Datos iniciales
-- =============================================

-- brands
INSERT INTO brands (brandName, brandDescription) VALUES 
('Bandai', 'Fabricante japonés líder en Gunpla'),
('LEGO', 'Empresa danesa de bloques de construcción'),
('Blokees', 'Marca de figuras coleccionables de bloques');

-- Categorías
INSERT INTO categories (categoryName, categoryDescription) VALUES
('Gunpla', 'Modelos de Gundam para armar'),
('Sets LEGO', 'Conjuntos de construcción LEGO'),
('Figuras Blokees', 'Figuras coleccionables de bloques');

-- Productos
INSERT INTO products (productName, productPrice, productStock, productBrandId, productCategoryId, productDescription, ) VALUES
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
INSERT INTO products_gunpla (gunplaProductId, gunplaGrade, gunplaScale, gunplaPremiumBandai, gunplaGundamBase) VALUES
(1, 'MG', '1/100', FALSE, TRUE),
(2, 'RG', '1/144', TRUE, FALSE),
(3, 'PG', '1/60', FALSE, TRUE);

-- Detalles LEGO
INSERT INTO products_lego (legoProductId, legoLine, legoSetNumber, legoPieceCount) VALUES
(4, 'Ultimate Collector Series', '75192', 7541),
(5, 'Technic', '42083', 3599),
(6, 'Creator Expert', '10264', 2569);

-- Detalles Blokees
INSERT INTO products_blokees (blokeesProductId, blokeesVersion, blokeesSize) VALUES
(7, 'Standard', 'Mediano'),
(8, 'Special Edition', 'Grande'),
(9, 'Classic', 'Pequeño');