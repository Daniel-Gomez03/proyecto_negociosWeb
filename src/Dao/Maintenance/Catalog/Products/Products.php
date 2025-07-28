<?php
namespace Dao\Maintenance\Catalog\Products;
use Dao\Table;

class Products extends Table
{
    public static function getProducts(
        string $partialName = "",
        string $status = "",
        string $orderBy = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ) {
        $sqlstr = "SELECT 
            p.productId,
            p.productName,
            p.productDescription,
            p.productPrice,
            p.productStock,
            p.productImgUrl,
            p.productStatus,
            b.brandName,
            c.categoryName,
            CASE 
                WHEN g.gunplaId IS NOT NULL THEN 'gunpla'
                WHEN l.legoId IS NOT NULL THEN 'lego'
                WHEN bl.blokeesId IS NOT NULL THEN 'blokees'
                ELSE 'otro'
            END AS productType
        FROM products p
        INNER JOIN brands b ON p.productBrandId = b.brandId
        INNER JOIN categories c ON p.productCategoryId = c.categoryId
        LEFT JOIN products_gunpla g ON p.productId = g.gunplaProductId
        LEFT JOIN products_lego l ON p.productId = l.legoProductId
        LEFT JOIN products_blokees bl ON p.productId = bl.blokeesProductId";
        
        $sqlstrCount = "SELECT COUNT(*) as count FROM products p";
        $conditions = [];
        $params = [];

        if ($partialName != "") {
            $conditions[] = "p.productName LIKE :partialName";
            $params["partialName"] = "%" . $partialName . "%";
        }

        if (!in_array($status, ["ACT", "INA", ""])) {
            throw new \Exception("Error Processing Request Status has invalid value");
        }

        if ($status != "") {
            $conditions[] = "p.productStatus = :status";
            $params["status"] = $status;
        }

        if (count($conditions) > 0) {
            $sqlstr .= " WHERE " . implode(" AND ", $conditions);
            $sqlstrCount .= " WHERE " . implode(" AND ", $conditions);
        }

        if (!in_array($orderBy, ["productId", "productName", "productPrice", ""])) {
            throw new \Exception("Error Processing Request OrderBy has invalid value");
        }

        if ($orderBy != "") {
            $sqlstr .= " ORDER BY " . $orderBy;
            if ($orderDescending) {
                $sqlstr .= " DESC";
            }
        }

        $numeroDeRegistros = self::obtenerUnRegistro($sqlstrCount, $params)["count"];
        $pagesCount = ceil($numeroDeRegistros / $itemsPerPage);

        if ($page > $pagesCount - 1) {
            $page = $pagesCount - 1;
        }

        $sqlstr .= " LIMIT " . $page * $itemsPerPage . ", " . $itemsPerPage;

        $registros = self::obtenerRegistros($sqlstr, $params);
        return ["products" => $registros, "total" => $numeroDeRegistros, "page" => $page, "itemsPerPage" => $itemsPerPage];
    }

    public static function getProductById(int $productId)
    {
        $sqlstr = "SELECT 
            p.productId,
            p.productName,
            p.productPrice,
            p.productStock,
            p.productBrandId,
            p.productCategoryId,
            p.productDescription,
            p.productImgUrl,
            p.productStatus,
            b.brandName,
            c.categoryName,
            g.gunplaGrade,
            g.gunplaScale,
            g.gunplaPremiumBandai,
            g.gunplaGundamBase,
            l.legoLine,
            l.legoSetNumber,
            l.legoPieceCount,
            bl.blokeesVersion,
            bl.blokeesSize,
            CASE 
                WHEN g.gunplaId IS NOT NULL THEN 'gunpla'
                WHEN l.legoId IS NOT NULL THEN 'lego'
                WHEN bl.blokeesId IS NOT NULL THEN 'blokees'
                ELSE 'otro'
            END AS productType
        FROM products p
        INNER JOIN brands b ON p.productBrandId = b.brandId
        INNER JOIN categories c ON p.productCategoryId = c.categoryId
        LEFT JOIN products_gunpla g ON p.productId = g.gunplaProductId
        LEFT JOIN products_lego l ON p.productId = l.legoProductId
        LEFT JOIN products_blokees bl ON p.productId = bl.blokeesProductId
        WHERE p.productId = :productId";
        
        $params = ["productId" => $productId];
        return self::obtenerUnRegistro($sqlstr, $params);
    }

    public static function insertProduct(
        string $productName,
        float $productPrice,
        int $productStock,
        int $productBrandId,
        int $productCategoryId,
        string $productDescription,
        string $productImgUrl,
        string $productStatus = 'ACT'
    ) {
        $sqlstr = "INSERT INTO products (
            productName, 
            productPrice, 
            productStock, 
            productBrandId, 
            productCategoryId, 
            productDescription, 
            productImgUrl, 
            productStatus
        ) VALUES (
            :productName, 
            :productPrice, 
            :productStock, 
            :productBrandId, 
            :productCategoryId, 
            :productDescription, 
            :productImgUrl, 
            :productStatus
        )";
        
        $params = [
            "productName" => $productName,
            "productPrice" => $productPrice,
            "productStock" => $productStock,
            "productBrandId" => $productBrandId,
            "productCategoryId" => $productCategoryId,
            "productDescription" => $productDescription,
            "productImgUrl" => $productImgUrl,
            "productStatus" => $productStatus
        ];
        
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function updateProduct(
        int $productId,
        string $productName,
        float $productPrice,
        int $productStock,
        int $productBrandId,
        int $productCategoryId,
        string $productDescription,
        string $productImgUrl,
        string $productStatus
    ) {
        $sqlstr = "UPDATE products SET 
            productName = :productName, 
            productPrice = :productPrice,
            productStock = :productStock,
            productBrandId = :productBrandId,
            productCategoryId = :productCategoryId,
            productDescription = :productDescription, 
            productImgUrl = :productImgUrl, 
            productStatus = :productStatus 
        WHERE productId = :productId";
        
        $params = [
            "productId" => $productId,
            "productName" => $productName,
            "productPrice" => $productPrice,
            "productStock" => $productStock,
            "productBrandId" => $productBrandId,
            "productCategoryId" => $productCategoryId,
            "productDescription" => $productDescription,
            "productImgUrl" => $productImgUrl,
            "productStatus" => $productStatus
        ];
        
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function deleteProduct(int $productId)
    {
        $sqlstr = "DELETE FROM products WHERE productId = :productId";
        $params = ["productId" => $productId];
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function getFeaturedProducts()
    {
        $sqlstr = "SELECT 
            p.productId, 
            p.productName, 
            p.productDescription, 
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
        WHERE h.highLightStart <= NOW() AND h.highLightEnd >= NOW()";
        
        $params = [];
        return self::obtenerRegistros($sqlstr, $params);
    }

    public static function getNewProducts()
    {
        $sqlstr = "SELECT 
            p.productId, 
            p.productName, 
            p.productDescription, 
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
        
        $params = [];
        return self::obtenerRegistros($sqlstr, $params);
    }

    public static function getDailyDeals()
    {
        $sqlstr = "SELECT 
            p.productId, 
            p.productName, 
            p.productDescription, 
            s.salePrice as productPrice,
            p.productStock,
            p.productImgUrl, 
            p.productStatus,
            b.brandName,
            c.categoryName
        FROM products p 
        INNER JOIN brands b ON p.productBrandId = b.brandId
        INNER JOIN categories c ON p.productCategoryId = c.categoryId
        INNER JOIN sales s ON p.productId = s.productId 
        WHERE s.saleStart <= NOW() AND s.saleEnd >= NOW()";
        
        $params = [];
        return self::obtenerRegistros($sqlstr, $params);
    }

    // Métodos adicionales para los tipos específicos de productos
    
    public static function insertGunplaProduct(
        int $productId,
        string $gunplaGrade,
        string $gunplaScale,
        bool $gunplaPremiumBandai = false,
        bool $gunplaGundamBase = false
    ) {
        $sqlstr = "INSERT INTO products_gunpla (
            gunplaProductId,
            gunplaGrade,
            gunplaScale,
            gunplaPremiumBandai,
            gunplaGundamBase
        ) VALUES (
            :gunplaProductId,
            :gunplaGrade,
            :gunplaScale,
            :gunplaPremiumBandai,
            :gunplaGundamBase
        )";
        
        $params = [
            "gunplaProductId" => $productId,
            "gunplaGrade" => $gunplaGrade,
            "gunplaScale" => $gunplaScale,
            "gunplaPremiumBandai" => $gunplaPremiumBandai ? 1 : 0,
            "gunplaGundamBase" => $gunplaGundamBase ? 1 : 0
        ];
        
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function insertLegoProduct(
        int $productId,
        string $legoLine,
        string $legoSetNumber,
        int $legoPieceCount
    ) {
        $sqlstr = "INSERT INTO products_lego (
            legoProductId,
            legoLine,
            legoSetNumber,
            legoPieceCount
        ) VALUES (
            :legoProductId,
            :legoLine,
            :legoSetNumber,
            :legoPieceCount
        )";
        
        $params = [
            "legoProductId" => $productId,
            "legoLine" => $legoLine,
            "legoSetNumber" => $legoSetNumber,
            "legoPieceCount" => $legoPieceCount
        ];
        
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function insertBlokeesProduct(
        int $productId,
        string $blokeesVersion,
        string $blokeesSize
    ) {
        $sqlstr = "INSERT INTO products_blokees (
            blokeesProductId,
            blokeesVersion,
            blokeesSize
        ) VALUES (
            :blokeesProductId,
            :blokeesVersion,
            :blokeesSize
        )";
        
        $params = [
            "blokeesProductId" => $productId,
            "blokeesVersion" => $blokeesVersion,
            "blokeesSize" => $blokeesSize
        ];
        
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function updateGunplaProduct(
        int $productId,
        string $gunplaGrade,
        string $gunplaScale,
        bool $gunplaPremiumBandai,
        bool $gunplaGundamBase
    ) {
        $sqlstr = "UPDATE products_gunpla SET 
            gunplaGrade = :gunplaGrade,
            gunplaScale = :gunplaScale,
            gunplaPremiumBandai = :gunplaPremiumBandai,
            gunplaGundamBase = :gunplaGundamBase
        WHERE gunplaProductId = :productId";
        
        $params = [
            "productId" => $productId,
            "gunplaGrade" => $gunplaGrade,
            "gunplaScale" => $gunplaScale,
            "gunplaPremiumBandai" => $gunplaPremiumBandai ? 1 : 0,
            "gunplaGundamBase" => $gunplaGundamBase ? 1 : 0
        ];
        
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function updateLegoProduct(
        int $productId,
        string $legoLine,
        string $legoSetNumber,
        int $legoPieceCount
    ) {
        $sqlstr = "UPDATE products_lego SET 
            legoLine = :legoLine,
            legoSetNumber = :legoSetNumber,
            legoPieceCount = :legoPieceCount
        WHERE legoProductId = :productId";
        
        $params = [
            "productId" => $productId,
            "legoLine" => $legoLine,
            "legoSetNumber" => $legoSetNumber,
            "legoPieceCount" => $legoPieceCount
        ];
        
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function updateBlokeesProduct(
        int $productId,
        string $blokeesVersion,
        string $blokeesSize
    ) {
        $sqlstr = "UPDATE products_blokees SET 
            blokeesVersion = :blokeesVersion,
            blokeesSize = :blokeesSize
        WHERE blokeesProductId = :productId";
        
        $params = [
            "productId" => $productId,
            "blokeesVersion" => $blokeesVersion,
            "blokeesSize" => $blokeesSize
        ];
        
        return self::executeNonQuery($sqlstr, $params);
    }
    
}