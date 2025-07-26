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

-- Table products
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


-- ===============================================
-- INSERTAR MARCAS (BRANDS)
-- ===============================================

INSERT INTO `brands` (`brandName`, `brandDescription`, `brandStatus`) VALUES
('Bandai', 'Fabricante de kits de modelo Gundam y coleccionables premium', 'ACT'),
('LEGO', 'Compañía danesa de juguetes de construcción con bloques y sets', 'ACT'),
('Blokees', 'Juguetes de bloques de construcción con personajes y temas populares', 'ACT'),
('Kotobukiya', 'Fabricante japonés de kits de modelo y figuras', 'ACT'),
('Good Smile Company', 'Fabricante japonés de figuras y artículos coleccionables', 'ACT');

-- ===============================================
-- INSERTAR CATEGORÍAS (CATEGORIES)
-- ===============================================

INSERT INTO `categories` (`categoryName`, `categoryDescription`, `categoryStatus`) VALUES
('Kits de Modelo', 'Kits de modelo de plástico para armado y personalización', 'ACT'),
('Sets de Construcción', 'Juguetes de construcción con piezas entrelazadas', 'ACT'),
('Coleccionables', 'Artículos de edición limitada y coleccionables', 'ACT'),
('Figuras de Acción', 'Figuras posables y modelos de personajes', 'ACT'),
('Accesorios', 'Herramientas, soportes y piezas de mejora', 'ACT');

-- ===============================================
-- INSERTAR PRODUCTOS PRINCIPALES
-- ===============================================

-- Productos GUNPLA (Bandai)
INSERT INTO `products` (`productName`, `productPrice`, `productStock`, `productBrandId`, `productCategoryId`, `productDescription`, `productImgUrl`, `productStatus`) VALUES
('RX-78-2 Gundam', 25.99, 15, 1, 1, 'Gundam icónico de la serie original Mobile Suit Gundam', 'https://placehold.co/300x300?text=RX-78-2+Gundam&font=roboto', 'ACT'),
('Strike Freedom Gundam', 89.99, 8, 1, 1, 'Kit Perfect Grade de alta gama con piezas de efectos especiales', 'https://placehold.co/300x300?text=Strike+Freedom&font=roboto', 'ACT'),
('Barbatos Lupus Rex', 45.99, 12, 1, 1, 'De la serie Iron-Blooded Orphans con armazón interno detallado', 'https://placehold.co/300x300?text=Barbatos+Rex&font=roboto', 'ACT'),
('Nu Gundam Ver Ka', 65.99, 6, 1, 1, 'Master Grade con elementos de rediseño de Katoki Hajime', 'https://placehold.co/300x300?text=Nu+Gundam&font=roboto', 'ACT'),
('Unicorn Gundam', 55.99, 10, 1, 1, 'Kit transformable de modo Unicorn a modo Destroy', 'https://placehold.co/300x300?text=Unicorn+Gundam&font=roboto', 'ACT');

-- Productos LEGO
INSERT INTO `products` (`productName`, `productPrice`, `productStock`, `productBrandId`, `productCategoryId`, `productDescription`, `productImgUrl`, `productStatus`) VALUES
('Halcón Milenario', 159.99, 5, 2, 2, 'Serie Ultimate Collectors del Halcón Milenario con minifiguras', 'https://placehold.co/300x300?text=Millennium+Falcon&font=roboto', 'ACT'),
('Castillo de Hogwarts', 399.99, 3, 2, 2, 'Masivo Castillo de Hogwarts con Gran Salón y torres', 'https://placehold.co/300x300?text=Hogwarts+Castle&font=roboto', 'ACT'),
('Lamborghini Technic', 349.99, 4, 2, 2, 'Detallado Lamborghini Sián FKP 37 con funciones de trabajo', 'https://placehold.co/300x300?text=Technic+Lambo&font=roboto', 'ACT'),
('Autobús de Londres Creator', 89.99, 8, 2, 2, 'Autobús rojo de dos pisos de Londres con interior detallado', 'https://placehold.co/300x300?text=London+Bus&font=roboto', 'ACT'),
('Batmobile de Batman', 229.99, 6, 2, 2, 'Serie Ultimate Collectors del Batmobile de 1989', 'https://placehold.co/300x300?text=Batmobile&font=roboto', 'ACT');

-- Productos BLOKEES
INSERT INTO `products` (`productName`, `productPrice`, `productStock`, `productBrandId`, `productCategoryId`, `productDescription`, `productImgUrl`, `productStatus`) VALUES
('Set de Construcción Pikachu', 29.99, 20, 3, 3, 'Figura adorable de Pikachu con partes movibles y accesorios', 'https://placehold.co/300x300?text=Pikachu+Blokees&font=roboto', 'ACT'),
('Construcción Mega Charizard', 49.99, 12, 3, 3, 'Charizard a gran escala con piezas de efecto de fuego', 'https://placehold.co/300x300?text=Charizard+Blokees&font=roboto', 'ACT'),
('Set Mario Bros', 35.99, 15, 3, 3, 'Set de construcción de Super Mario con accesorios de power-ups', 'https://placehold.co/300x300?text=Mario+Blokees&font=roboto', 'ACT'),
('Construcción Sonic Speed', 32.99, 18, 3, 3, 'Sonic the Hedgehog con piezas de pista de loop', 'https://placehold.co/300x300?text=Sonic+Blokees&font=roboto', 'ACT'),
('Minecraft Steve', 24.99, 25, 3, 3, 'Figura de Minecraft Steve con accesorios de bloques', 'https://placehold.co/300x300?text=Minecraft+Steve&font=roboto', 'ACT');

-- ===============================================
-- INSERTAR DETALLES ESPECÍFICOS GUNPLA
-- ===============================================

INSERT INTO `products_gunpla` (`gunplaProductId`, `gunplaGrade`, `gunplaScale`, `gunplaPremiumBandai`, `gunplaGundamBase`, `gunplaStatus`) VALUES
(1, 'Real Grade', '1/144', FALSE, FALSE, 'ACT'),
(2, 'Perfect Grade', '1/60', TRUE, FALSE, 'ACT'),
(3, 'High Grade', '1/144', FALSE, FALSE, 'ACT'),
(4, 'Master Grade', '1/100', FALSE, TRUE, 'ACT'),
(5, 'Real Grade', '1/144', FALSE, FALSE, 'ACT');

-- ===============================================
-- INSERTAR DETALLES ESPECÍFICOS LEGO
-- ===============================================

INSERT INTO `products_lego` (`legoProductId`, `legoLine`, `legoSetNumber`, `legoPieceCount`, `legoStatus`) VALUES
(6, 'Star Wars', '75375', 1351, 'ACT'),
(7, 'Harry Potter', '71043', 6020, 'ACT'),
(8, 'Technic', '42115', 3696, 'ACT'),
(9, 'Creator Expert', '10258', 1686, 'ACT'),
(10, 'DC', '76240', 2049, 'ACT');

-- ===============================================
-- INSERTAR DETALLES ESPECÍFICOS BLOKEES
-- ===============================================

INSERT INTO `products_blokees` (`blokeesProductId`, `blokeesVersion`, `blokeesSize`, `blokeesStatus`) VALUES
(11, 'Edición Estándar', 'Mediano (15cm)', 'ACT'),
(12, 'Edición Deluxe', 'Grande (25cm)', 'ACT'),
(13, 'Edición Clásica', 'Mediano (15cm)', 'ACT'),
(14, 'Edición Speed', 'Mediano (18cm)', 'ACT'),
(15, 'Edición Pixel', 'Pequeño (12cm)', 'ACT');

-- ===============================================
-- PRODUCTOS ADICIONALES PARA VARIEDAD
-- ===============================================

-- Más productos Gunpla
INSERT INTO `products` (`productName`, `productPrice`, `productStock`, `productBrandId`, `productCategoryId`, `productDescription`, `productImgUrl`, `productStatus`) VALUES
('Sazabi Ver Ka', 75.99, 4, 1, 1, 'Traje móvil personalizado de Char con psycho-frame detallado', 'https://placehold.co/300x300?text=Sazabi&font=roboto', 'ACT'),
('Wing Gundam Zero EW', 35.99, 14, 1, 1, 'Versión de Endless Waltz con efectos de alas de ángel', 'https://placehold.co/300x300?text=Wing+Zero&font=roboto', 'ACT');

INSERT INTO `products_gunpla` (`gunplaProductId`, `gunplaGrade`, `gunplaScale`, `gunplaPremiumBandai`, `gunplaGundamBase`, `gunplaStatus`) VALUES
(16, 'Master Grade', '1/100', FALSE, TRUE, 'ACT'),
(17, 'Real Grade', '1/144', FALSE, FALSE, 'ACT');

-- Más productos LEGO
INSERT INTO `products` (`productName`, `productPrice`, `productStock`, `productBrandId`, `productCategoryId`, `productDescription`, `productImgUrl`, `productStatus`) VALUES
('Transbordador Espacial NASA', 199.99, 7, 2, 2, 'Transbordador Discovery con Telescopio Espacial Hubble', 'https://placehold.co/300x300?text=Space+Shuttle&font=roboto', 'ACT'),
('Taj Mahal', 369.99, 2, 2, 2, 'Obra maestra arquitectónica con increíble detalle', 'https://placehold.co/300x300?text=Taj+Mahal&font=roboto', 'ACT');

INSERT INTO `products_lego` (`legoProductId`, `legoLine`, `legoSetNumber`, `legoPieceCount`, `legoStatus`) VALUES
(18, 'Creator Expert', '10283', 2354, 'ACT'),
(19, 'Architecture', '21056', 2022, 'ACT');

-- Más productos Blokees
INSERT INTO `products` (`productName`, `productPrice`, `productStock`, `productBrandId`, `productCategoryId`, `productDescription`, `productImgUrl`, `productStatus`) VALUES
('Goku Super Saiyan', 42.99, 11, 3, 3, 'Goku de Dragon Ball Z en transformación Super Saiyan', 'https://placehold.co/300x300?text=Goku+SSJ&font=roboto', 'ACT'),
('Set Link Zelda', 38.99, 13, 3, 3, 'Link de Legend of Zelda con Espada Maestra y escudo', 'https://placehold.co/300x300?text=Link+Zelda&font=roboto', 'ACT');

INSERT INTO `products_blokees` (`blokeesProductId`, `blokeesVersion`, `blokeesSize`, `blokeesStatus`) VALUES
(20, 'Edición Power', 'Grande (22cm)', 'ACT'),
(21, 'Edición Héroe', 'Mediano (16cm)', 'ACT');