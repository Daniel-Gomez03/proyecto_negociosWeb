<?php

namespace Dao\Cart;
use Utilities\Cart\CartFns;

class Cart extends \Dao\Table
{
    public static function getProductosDisponibles()
    {
        $sqlAllProductosActivos = "SELECT
                p.*,
                b.brandName,
                c.categoryName
            FROM products p
            INNER JOIN brands b ON p.productBrandId = b.brandId
            INNER JOIN categories c ON p.productCategoryId = c.categoryId
            WHERE p.productStatus = 'ACT'";

        $productosDisponibles = self::obtenerRegistros($sqlAllProductosActivos, array());

        // Sacar el stock de productos con carretilla autorizada
        $deltaAutorizada = CartFns::getAuthTimeDelta();
        $sqlCarretillaAutorizada = "SELECT productId, SUM(crrctd) as reserved
            FROM carretilla WHERE TIME_TO_SEC(TIMEDIFF(now(), crrfching)) <= :delta
            GROUP BY productId";
        $prodsCarretillaAutorizada = self::obtenerRegistros(
            $sqlCarretillaAutorizada,
            array("delta" => $deltaAutorizada)
        );

        // Sacar el stock de productos con carretilla no autorizada
        $deltaNAutorizada = CartFns::getUnAuthTimeDelta();
        $sqlCarretillaNAutorizada = "SELECT productId, SUM(crrctd) as reserved
            FROM carretillaanon WHERE TIME_TO_SEC(TIMEDIFF(now(), crrfching)) <= :delta
            GROUP BY productId";
        $prodsCarretillaNAutorizada = self::obtenerRegistros(
            $sqlCarretillaNAutorizada,
            array("delta" => $deltaNAutorizada)
        );

        $productosCurados = array();
        foreach ($productosDisponibles as $producto) {
            if (!isset($productosCurados[$producto["productId"]])) {
                $productosCurados[$producto["productId"]] = $producto;
            }
        }

        foreach ($prodsCarretillaAutorizada as $producto) {
            if (isset($productosCurados[$producto["productId"]])) {
                $productosCurados[$producto["productId"]]["productStock"] -= $producto["reserved"];
            }
        }

        foreach ($prodsCarretillaNAutorizada as $producto) {
            if (isset($productosCurados[$producto["productId"]])) {
                $productosCurados[$producto["productId"]]["productStock"] -= $producto["reserved"];
            }
        }

        return array_values($productosCurados);
    }

    public static function getProductoDisponible($productId)
    {
        $sqlAllProductosActivos = "SELECT
                p.*,
                b.brandName,
                c.categoryName
            FROM products p
            INNER JOIN brands b ON p.productBrandId = b.brandId
            INNER JOIN categories c ON p.productCategoryId = c.categoryId
            WHERE p.productStatus = 'ACT' AND p.productId = :productId";

        $productosDisponibles = self::obtenerRegistros($sqlAllProductosActivos, array("productId" => $productId));
        if (empty($productosDisponibles)) {
            return null;
        }

        $producto = $productosDisponibles[0];

        $deltaAutorizada = CartFns::getAuthTimeDelta();
        $sqlCarretillaAutorizada = "SELECT SUM(crrctd) as reserved
            FROM carretilla
            WHERE productId = :productId
            AND TIME_TO_SEC(TIMEDIFF(now(), crrfching)) <= :delta";

        $reservadoAutorizado = self::obtenerUnRegistro(
            $sqlCarretillaAutorizada,
            array("productId" => $productId, "delta" => $deltaAutorizada)
        );

        $deltaNAutorizada = CartFns::getUnAuthTimeDelta();
        $sqlCarretillaNAutorizada = "SELECT SUM(crrctd) as reserved
            FROM carretillaanon
            WHERE productId = :productId
            AND TIME_TO_SEC(TIMEDIFF(now(), crrfching)) <= :delta";

        $reservadoNoAutorizado = self::obtenerUnRegistro(
            $sqlCarretillaNAutorizada,
            array("productId" => $productId, "delta" => $deltaNAutorizada)
        );

        $reservadoTotal = ($reservadoAutorizado["reserved"] ?? 0) + ($reservadoNoAutorizado["reserved"] ?? 0);
        $producto["productStock"] -= $reservadoTotal;

        return $producto;
    }

    public static function getFeaturedProducts()
    {
        $sqlstr = "SELECT 
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
        WHERE h.highLightStart <= NOW() AND h.highLightEnd >= NOW() AND p.productStatus = 'ACT'";
        
        $productosDestacados = self::obtenerRegistros($sqlstr, []);

        if (empty($productosDestacados)) {
            return [];
        }

        $deltaAutorizada = CartFns::getAuthTimeDelta();
        $sqlCarretillaAutorizada = "SELECT productId, SUM(crrctd) as reserved
            FROM carretilla WHERE TIME_TO_SEC(TIMEDIFF(now(), crrfching)) <= :delta
            GROUP BY productId";
        $prodsCarretillaAutorizada = self::obtenerRegistros($sqlCarretillaAutorizada, ["delta" => $deltaAutorizada]);

        $deltaNAutorizada = CartFns::getUnAuthTimeDelta();
        $sqlCarretillaNAutorizada = "SELECT productId, SUM(crrctd) as reserved
            FROM carretillaanon WHERE TIME_TO_SEC(TIMEDIFF(now(), crrfching)) <= :delta
            GROUP BY productId";
        $prodsCarretillaNAutorizada = self::obtenerRegistros($sqlCarretillaNAutorizada, ["delta" => $deltaNAutorizada]);

        $productosCurados = [];
        foreach ($productosDestacados as $producto) {
            $productosCurados[$producto["productId"]] = $producto;
        }

        foreach ($prodsCarretillaAutorizada as $reserva) {
            if (isset($productosCurados[$reserva["productId"]])) {
                $productosCurados[$reserva["productId"]]["productStock"] -= $reserva["reserved"];
            }
        }

        foreach ($prodsCarretillaNAutorizada as $reserva) {
            if (isset($productosCurados[$reserva["productId"]])) {
                $productosCurados[$reserva["productId"]]["productStock"] -= $reserva["reserved"];
            }
        }
        
        return array_values($productosCurados);
    }

    public static function getNewProducts()
    {
        $sqlstr = "SELECT 
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
        WHERE p.productStatus = 'ACT' 
        ORDER BY p.productId DESC 
        LIMIT 3";
        
        $nuevosProductos = self::obtenerRegistros($sqlstr, []);
        
        if (empty($nuevosProductos)) {
            return [];
        }

        $deltaAutorizada = CartFns::getAuthTimeDelta();
        $sqlCarretillaAutorizada = "SELECT productId, SUM(crrctd) as reserved FROM carretilla WHERE TIME_TO_SEC(TIMEDIFF(now(), crrfching)) <= :delta GROUP BY productId";
        $prodsCarretillaAutorizada = self::obtenerRegistros($sqlCarretillaAutorizada, ["delta" => $deltaAutorizada]);

        $deltaNAutorizada = CartFns::getUnAuthTimeDelta();
        $sqlCarretillaNAutorizada = "SELECT productId, SUM(crrctd) as reserved FROM carretillaanon WHERE TIME_TO_SEC(TIMEDIFF(now(), crrfching)) <= :delta GROUP BY productId";
        $prodsCarretillaNAutorizada = self::obtenerRegistros($sqlCarretillaNAutorizada, ["delta" => $deltaNAutorizada]);

        $productosCurados = [];
        foreach ($nuevosProductos as $producto) {
            $productosCurados[$producto["productId"]] = $producto;
        }

        foreach ($prodsCarretillaAutorizada as $reserva) {
            if (isset($productosCurados[$reserva["productId"]])) {
                $productosCurados[$reserva["productId"]]["productStock"] -= $reserva["reserved"];
            }
        }

        foreach ($prodsCarretillaNAutorizada as $reserva) {
            if (isset($productosCurados[$reserva["productId"]])) {
                $productosCurados[$reserva["productId"]]["productStock"] -= $reserva["reserved"];
            }
        }

        return array_values($productosCurados);
    }

    public static function addToAnonCart(
        int $productId,
        string $anonCod,
        int $amount,
        float $price
    ) {
        $validateSql = "SELECT * from carretillaanon where anoncod = :anoncod and productId = :productId";
        $producto = self::obtenerUnRegistro($validateSql, ["anoncod" => $anonCod, "productId" => $productId]);

        if ($producto) {
            if ($producto["crrctd"] + $amount <= 0) {
                $deleteSql = "DELETE from carretillaanon where anoncod = :anoncod and productId = :productId";
                return self::executeNonQuery($deleteSql, ["anoncod" => $anonCod, "productId" => $productId]);
            } else {
                $updateSql = "UPDATE carretillaanon set crrctd = crrctd + :amount, crrfching = NOW()
                             where anoncod = :anoncod and productId = :productId";
                return self::executeNonQuery($updateSql, [
                    "anoncod" => $anonCod,
                    "amount" => $amount,
                    "productId" => $productId
                ]);
            }
        } else {
            return self::executeNonQuery(
                "INSERT INTO carretillaanon (anoncod, productId, crrctd, crrprc, crrfching)
                VALUES (:anoncod, :productId, :crrctd, :crrprc, NOW())",
                [
                    "anoncod" => $anonCod,
                    "productId" => $productId,
                    "crrctd" => $amount,
                    "crrprc" => $price
                ]
            );
        }
    }

    public static function getAnonCart(string $anonCod)
    {
        return self::obtenerRegistros(
            "SELECT a.*, b.crrctd, b.crrprc, b.crrfching
             FROM products a
             INNER JOIN carretillaanon b ON a.productId = b.productId
             WHERE b.anoncod = :anoncod",
            ["anoncod" => $anonCod]
        );
    }

    public static function getAuthCart(int $usercod)
    {
        return self::obtenerRegistros(
            "SELECT a.*, b.crrctd, b.crrprc, b.crrfching
             FROM products a
             INNER JOIN carretilla b ON a.productId = b.productId
             WHERE b.usercod = :usercod",
            ["usercod" => $usercod]
        );
    }

    public static function addToAuthCart(
        int $productId,
        int $usercod,
        int $amount,
        float $price
    ) {
        $validateSql = "SELECT * from carretilla where usercod = :usercod and productId = :productId";
        $producto = self::obtenerUnRegistro($validateSql, ["usercod" => $usercod, "productId" => $productId]);

        if ($producto) {
            if ($producto["crrctd"] + $amount <= 0) {
                $deleteSql = "DELETE from carretilla where usercod = :usercod and productId = :productId";
                return self::executeNonQuery($deleteSql, ["usercod" => $usercod, "productId" => $productId]);
            } else {
                $updateSql = "UPDATE carretilla set crrctd = crrctd + :amount, crrfching = NOW()
                              where usercod = :usercod and productId = :productId";
                return self::executeNonQuery($updateSql, [
                    "usercod" => $usercod,
                    "amount" => $amount,
                    "productId" => $productId
                ]);
            }
        } else {
            return self::executeNonQuery(
                "INSERT INTO carretilla (usercod, productId, crrctd, crrprc, crrfching)
                VALUES (:usercod, :productId, :crrctd, :crrprc, NOW())",
                [
                    "usercod" => $usercod,
                    "productId" => $productId,
                    "crrctd" => $amount,
                    "crrprc" => $price
                ]
            );
        }
    }

    public static function moveAnonToAuth(string $anonCod, int $usercod)
    {
        $sqlstr = "INSERT INTO carretilla (usercod, productId, crrctd, crrprc, crrfching)
            SELECT :usercod, productId, crrctd, crrprc, NOW()
            FROM carretillaanon
            WHERE anoncod = :anoncod
            ON DUPLICATE KEY UPDATE carretilla.crrctd = carretilla.crrctd + carretillaanon.crrctd";
        $deleteSql = "DELETE FROM carretillaanon WHERE anoncod = :anoncod";

        self::executeNonQuery($sqlstr, ["anoncod" => $anonCod, "usercod" => $usercod]);
        self::executeNonQuery($deleteSql, ["anoncod" => $anonCod]);
    }

    public static function clearAuthCart(int $usercod)
    {
        $deleteSql = "DELETE FROM carretilla WHERE usercod = :usercod";
        return self::executeNonQuery($deleteSql, ["usercod" => $usercod]);
    }

    public static function clearAnonCart(string $anonCod)
    {
        $deleteSql = "DELETE FROM carretillaanon WHERE anoncod = :anoncod";
        return self::executeNonQuery($deleteSql, ["anoncod" => $anonCod]);
    }

    public static function actualizarStock(int $productId, int $quantity): bool
    {
        $sql = "UPDATE products 
                SET productStock = productStock - :quantity 
                WHERE productId = :productId 
                AND productStock >= :quantity";

        return self::executeNonQuery($sql, [
            'productId' => $productId,
            'quantity' => $quantity
        ]) > 0;
    }
}