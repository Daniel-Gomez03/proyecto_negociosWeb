INSERT INTO `roles` (
    `rolescod`,
    `rolesdsc`,
    `rolesest`
)
VALUES
    ('admin', 'administrador', 'ACT'),
    ('pbl', 'publico', 'ACT');




INSERT INTO `funciones` (
    `fncod`,
    `fndsc`,
    `fnest`,
    `fntyp`
)
VALUES
    ('Brands_table', 'Brands_table', 'ACT', 'MNU'),
    ('Categories_table', 'Categories_table', 'ACT', 'MNU'),
    ('Controllers\\Maintenance\\Catalog\\Brands\\Brand', 'Controllers\\Maintenance\\Catalog\\Brands\\Brand', 'ACT', 'CTR'),
    ('Controllers\\Maintenance\\Catalog\\Brands\\Brands', 'Controllers\\Maintenance\\Catalog\\Brands\\Brands', 'ACT', 'CTR'),
    ('Controllers\\Maintenance\\Catalog\\Categories\\Categories', 'Controllers\\Maintenance\\Catalog\\Categories\\Categories', 'ACT', 'CTR'),
    ('Controllers\\Maintenance\\Catalog\\Categories\\Category', 'Controllers\\Maintenance\\Catalog\\Categories\\Category', 'ACT', 'CTR'),
    ('Controllers\\Maintenance\\Catalog\\Products\\Product', 'Controllers\\Maintenance\\Catalog\\Products\\Product', 'ACT', 'CTR'),
    ('Controllers\\Maintenance\\Catalog\\Products\\Products', 'Controllers\\Maintenance\\Catalog\\Products\\Products', 'ACT', 'CTR'),
    ('Controllers\\Maintenance\\Funciones\\Funcion', 'Controllers\\Maintenance\\Funciones\\Funcion', 'ACT', 'CTR'),
    ('Controllers\\Maintenance\\Funciones\\Funciones', 'Controllers\\Maintenance\\Funciones\\Funciones', 'ACT', 'CTR'),
    ('Controllers\\Maintenance\\Roles\\Rol', 'Controllers\\Maintenance\\Roles\\Rol', 'ACT', 'CTR'),
    ('Controllers\\Maintenance\\Roles\\Roles', 'Controllers\\Maintenance\\Roles\\Roles', 'ACT', 'CTR'),
    ('Controllers\\Maintenance\\Users\\User', 'Controllers\\Maintenance\\Users\\User', 'ACT', 'CTR'),
    ('Controllers\\Maintenance\\Users\\Users', 'Controllers\\Maintenance\\Users\\Users', 'ACT', 'CTR'),
    ('Menu_Catalog', 'Menu_Catalog', 'ACT', 'MNU'),
    ('Menu_Maintenance_Catalog_Brands_Brand', 'Menu_Maintenance_Catalog_Brands_Brand', 'ACT', 'MNU'),
    ('Menu_Maintenance_Catalog_Brands_Brands', 'Menu_Maintenance_Catalog_Brands_Brands', 'ACT', 'MNU'),
    ('Menu_Maintenance_Catalog_Categories_Categories', 'Menu_Maintenance_Catalog_Categories_Categories', 'ACT', 'MNU'),
    ('Menu_Maintenance_Catalog_Categories_Category', 'Menu_Maintenance_Catalog_Categories_Category', 'ACT', 'MNU'),
    ('Menu_Maintenance_Catalog_Products_Product', 'Menu_Maintenance_Catalog_Products_Product', 'ACT', 'MNU'),
    ('Menu_Maintenance_Catalog_Products_Products', 'Menu_Maintenance_Catalog_Products_Products', 'ACT', 'MNU'),
    ('Menu_Maintenance_Funciones_Funcion', 'Menu_Maintenance_Funciones_Funcion', 'ACT', 'MNU'),
    ('Menu_Maintenance_Funciones_Funciones', 'Menu_Maintenance_Funciones_Funciones', 'ACT', 'MNU'),
    ('Menu_Maintenance_Roles_Rol', 'Menu_Maintenance_Roles_Rol', 'ACT', 'MNU'),
    ('Menu_Maintenance_Roles_Roles', 'Menu_Maintenance_Roles_Roles', 'ACT', 'MNU'),
    ('Menu_Maintenance_Users_User', 'Menu_Maintenance_Users_User', 'ACT', 'MNU'),
    ('Menu_Maintenance_Users_Users', 'Menu_Maintenance_Users_Users', 'ACT', 'MNU');



INSERT INTO `funciones_roles` (
    `rolescod`,
    `fncod`,
    `fnrolest`,
    `fnexp`
)
VALUES
    ('admin', 'Controllers\\Maintenance\\Catalog\\Brands\\Brand', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Controllers\\Maintenance\\Catalog\\Brands\\Brands', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Controllers\\Maintenance\\Catalog\\Categories\\Categories', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Controllers\\Maintenance\\Catalog\\Categories\\Category', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Controllers\\Maintenance\\Catalog\\Products\\Product', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Controllers\\Maintenance\\Catalog\\Products\\Products', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Controllers\\Maintenance\\Funciones\\Funcion', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Controllers\\Maintenance\\Funciones\\Funciones', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Controllers\\Maintenance\\Roles\\Rol', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Controllers\\Maintenance\\Roles\\Roles', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Controllers\\Maintenance\\Users\\User', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Controllers\\Maintenance\\Users\\Users', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Menu_Maintenance_Catalog_Brands_Brand', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Menu_Maintenance_Catalog_Brands_Brands', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Menu_Maintenance_Catalog_Categories_Categories', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Menu_Maintenance_Catalog_Categories_Category', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Menu_Maintenance_Catalog_Products_Product', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Menu_Maintenance_Catalog_Products_Products', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Menu_Maintenance_Funciones_Funcion', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Menu_Maintenance_Funciones_Funciones', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Menu_Maintenance_Roles_Rol', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Menu_Maintenance_Roles_Roles', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Menu_Maintenance_Users_User', 'ACT', '2026-07-31 00:00:00'),
    ('admin', 'Menu_Maintenance_Users_Users', 'ACT', '2026-07-31 00:00:00');
