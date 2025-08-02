-- Active: 1754104335999@@127.0.0.1@3306@hasbunstore
-- Table brands
CREATE TABLE `brands` (
    `brandId` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `brandName` VARCHAR(255) NOT NULL,
    `brandDescription` VARCHAR(255) NOT NULL,
    `brandStatus` CHAR(3) NOT NULL DEFAULT 'ACT',
    UNIQUE (`brandName`)
) ENGINE=InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET=utf8mb4;

-- Tab`le categories
CREATE TABLE `categories` (
    `categoryId` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `categoryName` VARCHAR(255) NOT NULL,
    `categoryDescription` VARCHAR(255) NOT NULL,
    `categoryStatus` CHAR(3) NOT NULL DEFAULT 'ACT',
    UNIQUE (`categoryName`)
) ENGINE=InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET=utf8mb4;

-- Table products
-- CREATE TABLE `products` (
--     `productId` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
--     `productName` VARCHAR(255) NOT NULL,
--     `productPrice` DECIMAL(10,2) NOT NULL,
--     `productStock` INT NOT NULL DEFAULT 0,
--     `productBrandId` INT NOT NULL,
--     `productCategoryId` INT NOT NULL,
--     `productDescription` VARCHAR(255) NOT NULL,
--     `productImgUrl` VARCHAR(255) NOT NULL,
--     `productStatus` CHAR(3) NOT NULL DEFAULT 'ACT',
--     UNIQUE (`productName`, `productBrandId`),
--     CONSTRAINT `fk_products_brands` FOREIGN KEY (`productBrandId`) REFERENCES `brands`(`brandId`) ON DELETE CASCADE ON UPDATE CASCADE,
--     CONSTRAINT `fk_products_categories` FOREIGN KEY (`productCategoryId`) REFERENCES `categories`(`categoryId`) ON DELETE CASCADE ON UPDATE CASCADE
-- ) ENGINE=InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `products` (
    `productId` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `productName` VARCHAR(255) NOT NULL,
    `productPrice` DECIMAL(10,2) NOT NULL,
    `productStock` INT NOT NULL DEFAULT 0,
    `productBrandId` INT NOT NULL,
    `productCategoryId` INT NOT NULL,
    `productDescription` VARCHAR(255) NOT NULL,
    `productDetails` TEXT NOT NULL,
    `productImgUrl` VARCHAR(255) NOT NULL,
    `productStatus` CHAR(3) NOT NULL DEFAULT 'ACT',
    UNIQUE (`productName`, `productBrandId`),
    CONSTRAINT `fk_products_brands` FOREIGN KEY (`productBrandId`) REFERENCES `brands`(`brandId`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_products_categories` FOREIGN KEY (`productCategoryId`) REFERENCES `categories`(`categoryId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `sales` (
    `saleId` int(11) NOT NULL AUTO_INCREMENT,
    `productId` int(11) NOT NULL,
    `salePrice` decimal(10, 2) NOT NULL,
    `saleStart` datetime NOT NULL,
    `saleEnd` datetime NOT NULL,
    PRIMARY KEY (`saleId`),
    KEY `fk_sales_products_idx` (`productId`),
    CONSTRAINT `fk_sales_products` FOREIGN KEY (`productId`) REFERENCES `products` (`productId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4;

CREATE TABLE `highlights` (
    `highlightId` int(11) NOT NULL AUTO_INCREMENT,
    `productId` int(11) NOT NULL,
    `highlightStart` datetime NOT NULL,
    `highlightEnd` datetime NOT NULL,
    PRIMARY KEY (`highlightId`),
    KEY `fk_highlights_products_idx` (`productId`),
    CONSTRAINT `fk_highlights_products` FOREIGN KEY (`productId`) REFERENCES `products` (`productId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4;

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

INSERT INTO `products` (`productName`, `productPrice`, `productStock`, `productBrandId`, `productCategoryId`, `productDescription`, `productImgUrl`, `productStatus`, `productDetails`) VALUES
('RX-78-2 Gundam', 25.99, 15, 1, 1, 'Gundam icónico de la serie original Mobile Suit Gundam', 'https://www.gundam.my/images/sell_products/interactive/818/1.jpg', 'ACT', 'Grade: Real Grade (HG), Escala: 1/144, Premium Bandai: No, Gundam Base: No'),
('Strike Freedom Gundam', 89.99, 8, 1, 1, 'Kit Perfect Grade de alta gama con piezas de efectos especiales', 'https://www.hlj.com/productimages/ban/ban965506_1.jpg', 'ACT', 'Grade: Perfect Grade (PG), Escala: 1/60, Premium Bandai: Sí, Gundam Base: No'),
('Barbatos Lupus Rex', 45.99, 12, 1, 1, 'De la serie Iron-Blooded Orphans con armazón interno detallado', 'https://m.media-amazon.com/images/I/61Q2FYU3W2L._AC_SL1500_.jpg', 'ACT', 'Grade: High Grade (HG), Escala: 1/144, Premium Bandai: Sí, Gundam Base: No'),
('Nu Gundam Ver Ka', 65.99, 6, 1, 1, 'Master Grade con elementos de rediseño de Katoki Hajime', 'https://blogger.googleusercontent.com/img/a/AVvXsEiG3QHIcaGbDX2Uv3frKdH2xhQpfTBYQrDfAhmBbEDInh-_vpLSKeR0bUSptLeB3EyYjdJPE7alg5dYz8WM1bArCa_0gRRRqVy3A-1mDV27qu4jtdzBTM4Ufq7l-jGEcV3d7xwm1UFgjbzmAlB6O-L8zUlvVlNzk5CWuFhbhCDxXdeWX2pRSHAu9uD0', 'ACT', 'Grade: Master Grade (MG), Escala: 1/100, Ver. Ka, Premium Bandai: No, Gundam Base: Sí'),
('Unicorn Gundam', 55.99, 10, 1, 1, 'Kit transformable de modo Unicorn a modo Destroy', 'https://shop.bandaicollectors.com.mx/cdn/shop/files/71SaoEnu-1L._AC_SL1500.jpg?v=1751663510', 'ACT', 'Grade: Real Grade (RG), Escala: 1/144, Premium Bandai: No, Gundam Base: No');

-- Productos LEGO
INSERT INTO `products` (`productName`, `productPrice`, `productStock`, `productBrandId`, `productCategoryId`, `productDescription`, `productImgUrl`, `productStatus`, `productDetails`) VALUES
('Halcón Milenario', 159.99, 5, 2, 2, 'Serie Ultimate Collectors del Halcón Milenario con minifiguras', 'https://www.lego.com/cdn/cs/set/assets/blt2b018e5370428f71/75375_alt4.png?format=webply&fit=bounds&quality=75&width=800&height=800&dpr=1', 'ACT', 'Línea: Star Wars, Set: 75375, Piezas: 1351'),
('Castillo de Hogwarts', 399.99, 3, 2, 2, 'Masivo Castillo de Hogwarts con Gran Salón y torres', 'https://www.lego.com/cdn/cs/set/assets/blt08668e770aaef16e/71043_alt1.jpg?format=webply&fit=bounds&quality=75&width=800&height=800&dpr=1', 'ACT', 'Línea: Harry Potter, Set: 71043, Piezas: 6020'),
('Lamborghini Technic', 349.99, 4, 2, 2, 'Detallado Lamborghini Sián FKP 37 con funciones de trabajo', 'https://www.lego.com/cdn/cs/set/assets/bltf3078e79b1a18a79/42115_alt18.jpg?format=webply&fit=bounds&quality=75&width=800&height=800&dpr=1', 'ACT', 'Línea: Technic, Set: 42115, Piezas: 3696'),
('Autobús de Londres Creator', 89.99, 8, 2, 2, 'Autobús rojo de dos pisos de Londres con interior detallado', 'https://www.lego.com/cdn/cs/set/assets/blt1bf79616d93267b9/10258_alt1.jpg?format=webply&fit=bounds&quality=75&width=800&height=800&dpr=1', 'ACT', 'Línea: Creator Expert, Set: 10258, Piezas: 1686'),
('Batmobile de Batman', 229.99, 6, 2, 2, 'Serie Ultimate Collectors del Batmobile de 1989', 'https://www.lego.com/cdn/cs/set/assets/blt1c14142c56a279d8/76240_alt1.jpg?format=webply&fit=bounds&quality=75&width=800&height=800&dpr=1', 'ACT', 'Línea: DC Comics, Set: 76240, Piezas: 2049');

-- Productos Blokees
INSERT INTO `products` (`productName`, `productPrice`, `productStock`, `productBrandId`, `productCategoryId`, `productDescription`, `productImgUrl`, `productStatus`, `productDetails`) VALUES
('Blokees Transformers Galaxy V07 One Wave 2', 24.99, 30, 3, 3, 'Figura sorpresa de Transformers One con articulación mejorada de 20° y accesorios únicos. Incluye personajes como Megatron, Orion Pax COGGED, Elita-1 y más', 'https://blokees.com/cdn/shop/files/img_v3_02lj_025b44ce-b084-4f05-95c8-2c6f0103279g_1_800x_crop_center.png?v=1753078477', 'ACT', 'Tamaño: 8 cm, Versión: Galaxy V07 One Wave 2'),
('Blokees Transformers Galaxy V06 Parallel Universe', 26.99, 25, 3, 3, 'Colección de figuras IDW Transformers con acabado metálico galaxy y articulación de 20 puntos. Incluye IDW Optimus Prime, Megatron y personajes únicos', 'https://blokees.com/cdn/shop/files/img_v3_02d3_d74a4fc1-b89c-4735-9851-7a5a04d8b8fg_2_800x_crop_center.png?v=1753077784', 'ACT', 'Tamaño: 8 cm, Versión: Galaxy V06 Parallel Universe'),
('Blokees Transformers Galaxy V01 Roll Out', 22.99, 20, 3, 3, 'Primera edición de la colección Galaxy con personajes clásicos de Transformers en formato de construcción premium', 'https://blokees.com/cdn/shop/files/GV01_1_800x_crop_center.png?v=1753076833', 'ACT', 'Tamaño: 8 cm, Versión: Galaxy V01 Roll Out'),
('Blokees Transformers Galaxy V03 Autobot Run', 23.99, 22, 3, 3, 'Colección centrada en los Autobots con figuras de alta calidad y accesorios especializados para cada personaje', 'https://blokees.com/cdn/shop/files/GV03_1_51e85b58-803f-473a-89b3-5f3816b8e03a_800x_crop_center.png?v=1753076956', 'ACT', 'Tamaño: 8 cm, Versión: Galaxy V03 Autobot Run'),
('Blokees Transformers Galaxy V02 SOS', 23.99, 18, 3, 3, 'Segunda ola de la serie Galaxy con personajes de rescate y combate, incluyendo efectos especiales y armas únicas', 'https://blokees.com/cdn/shop/files/GV02_2_800x_crop_center.png?v=1753076914', 'ACT', 'Tamaño: 8 cm, Versión: Galaxy V02 SOS');

INSERT INTO `sales` (`productId`, `salePrice`, `saleStart`, `saleEnd`) VALUES
(2, 74.99, '2025-07-28 00:00:00', '2025-08-04 23:59:59'),  -- Strike Freedom Gundam
(6, 139.99, '2025-07-29 00:00:00', '2025-08-05 23:59:59'),  -- Halcón Milenario
(13, 19.99, '2025-07-30 00:00:00', '2025-08-06 23:59:59');  -- Blokees Galaxy V01

INSERT INTO `highlights` (`productId`, `highlightStart`, `highlightEnd`) VALUES
(1, '2025-07-28 00:00:00', '2025-08-10 23:59:59'),  -- RX-78-2 Gundam
(7, '2025-07-29 00:00:00', '2025-08-12 23:59:59'),  -- Castillo de Hogwarts
(15, '2025-07-30 00:00:00', '2025-08-13 23:59:59'); -- Blokees Galaxy V02 SOS


-- Consulta para obtener TODOS los productos con sus detalles específicos
-- (Gunpla, LEGO, Blokees) 
SELECT 
    p.productId,
    p.productName,
    p.productDescription,
    p.productDetails,
    p.productPrice,
    p.productImgUrl,
    p.productStock,
    p.productStatus,
    b.brandName,
    c.categoryName
FROM products p
INNER JOIN brands b ON p.productBrandId = b.brandId
INNER JOIN categories c ON p.productCategoryId = c.categoryId
WHERE p.productStatus = 'ACT'
ORDER BY p.productId LIMIT 100;

SELECT 
            p.productId, 
            p.productName, 
            p.productDescription, 
            p.productDetails,
            p.productPrice,
            p.productStock,
            p.productImgUrl, 
            p.productStatus,
            b.brandName,
            c.categoryName
        FROM products p 
        INNER JOIN brands b ON p.productBrandId = b.brandId
        INNER JOIN categories c ON p.productCategoryId = c.categoryId
        INNER JOIN highlights h ON p.productId = h.productId 
        WHERE h.highLightStart <= NOW() AND h.highLightEnd >= NOW()