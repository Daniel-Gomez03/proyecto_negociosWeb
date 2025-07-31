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
            p.productDetails,
            p.productPrice,
            p.productImgUrl,
            p.productStock,
            p.productStatus,
            b.brandName,
            c.categoryName
        FROM products p
        INNER JOIN brands b ON p.productBrandId = b.brandId
        INNER JOIN categories c ON p.productCategoryId = c.categoryId";
        
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
            p.productDetails,
            p.productImgUrl,
            p.productStatus,
            b.brandName,
            c.categoryName
        FROM products p
        INNER JOIN brands b ON p.productBrandId = b.brandId
        INNER JOIN categories c ON p.productCategoryId = c.categoryId
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
        string $productDetails,
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
            productDetails,
            productImgUrl, 
            productStatus
        ) VALUES (
            :productName, 
            :productPrice, 
            :productStock, 
            :productBrandId, 
            :productCategoryId, 
            :productDescription, 
            :productDetails,
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
            "productDetails" => $productDetails,
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
        string $productDetails,
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
            productDetails = :productDetails,
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
            "productDetails" => $productDetails,
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
        
        $params = [];
        return self::obtenerRegistros($sqlstr, $params);
    }
}