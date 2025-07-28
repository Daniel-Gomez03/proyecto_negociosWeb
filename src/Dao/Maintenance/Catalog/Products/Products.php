<?php
namespace Dao\Maintenance\Catalog\Products;
use Dao\Table;

class ProductsDao extends table{
    
    public static function getFeaturedBrands() 
    {
        $sqlstr = "SELECT b.brandId, b.brandName, b.brandDescription, b.brandStatus FROM brands b WHERE b.brandStatus = 'ACT'";
        $params = [];
        $registros = self::obtenerRegistros($sqlstr, $params);
        return $registros;
    }


    public static function getActiveCategories() 
    {
        $sqlstr = "SELECT c.categoryId, c.categoryName, c.categoryDescription, c.categoryStatus FROM categories c WHERE c.categoryStatus = 'ACT'";
        $params = [];
        $registros = self::obtenerRegistros($sqlstr, $params);
        return $registros;
    }


    public static function getNewProducts() {
        $sqlstr = "SELECT p.productId, p.productName, p.productDescription, p.productPrice, p.productImgUrl, p.productStatus, p.productStock, p.productBrandId,p.productCategoryId
                    FROM products p 
                    WHERE p.productStatus = 'ACT' 
                    ORDER BY p.productId DESC 
                    LIMIT 3";
        $params = [];
        $registros = self::obtenerRegistros($sqlstr, $params);
        return $registros;
}



public static function getProducts ( string $partialName = "", string $status = "", string $orderBy = "", bool $orderDescending = false, int $page = 0, int $itemsPerPage = 10 ) 
{
    $sqlstr = "SELECT p.productId, p.productName, p.productDescription, p.productPrice, p.productImgUrl, p.productStatus, p.productStock, p.productBrandId, b.brandName, p.productCategoryId, c.categoryName, CASE 
                WHEN p.productStatus = 'ACT' THEN 'Activo'
                WHEN p.productStatus = 'INA' THEN 'Inactivo'
                ELSE 'Sin Asignar' 
                END AS productStatusDsc
                FROM products p
                INNER JOIN brands b ON p.productBrandId = b.brandId
                INNER JOIN categories c ON p.productCategoryId = c.categoryId";

  $sqlstrCount = "SELECT COUNT(*) as count 
                  FROM products p
                  INNER JOIN brands b ON p.productBrandId = b.brandId
                  INNER JOIN categories c ON p.productCategoryId = c.categoryId";



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
        $whereClause = " WHERE " . implode(" AND ", $conditions);
        $sqlstr .= $whereClause;
        $sqlstrCount .= $whereClause;
    }
    if (!in_array($orderBy, ["productId", "productName", "productPrice","brandName", "categoryName", ""])) {
        throw new \Exception("Error Processing Request OrderBy has invalid value");
    }
    if ($orderBy != "") {
        $sqlstr .= " ORDER BY " . $orderBy;
        if ($orderDescending)  
        {
            $sqlstr .= " DESC";
        }
    }
    $numeroDeRegistros = self::obtenerUnRegistro($sqlstrCount, $params)["count"];
    $pagesCount = ceil($numeroDeRegistros / $itemsPerPage);
    if ($page > $pagesCount - 1) 
    {
      $page = max($pagesCount - 1, 0);
    }
    $sqlstr .= " LIMIT " . $page * $itemsPerPage . ", " . $itemsPerPage;

    $registros = self::obtenerRegistros($sqlstr, $params);
    return ["products" => $registros, "total" => $numeroDeRegistros, "page" => $page, "itemsPerPage" => $itemsPerPage];
  }


   public static function getProductById(int $productId) {
    $sqlstr = "SELECT p.productId, p.productName, p.productPrice, p.productStock, p.productBrandId, p.productCategoryId, p.productDescription, p.productImgUrl, p.productStatus
               FROM products p WHERE p.productId = :productId";
    $params = ["productId" => $productId];
    return self::obtenerUnRegistro($sqlstr, $params);
  }

  public static function insertProduct( string $productName, float $productPrice, int $productStock, int $productBrandId, int $productCategoryId, string $productDescription, string $productImgUrl, string $productStatus) 
  {
    $sqlstr = "INSERT INTO products(productName, productPrice, productStock, productBrandId, productCategoryId, productDescription, productImgUrl, productStatus)
                VALUES (:productName, :productPrice, :productStock, :productBrandId, :productCategoryId, :productDescription, :productImgUrl, :productStatus)";
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

  public static function updateProduct(int $productId, string $productName, float $productPrice, int $productStock, int $productBrandId, int $productCategoryId, string $productDescription, string $productImgUrl, string $productStatus) 
  {
    $sqlstr = "UPDATE products SET productName = :productName, productPrice = :productPrice, productStock = :productStock, productBrandId = :productBrandId, productCategoryId = :productCategoryId, productDescription = :productDescription, productImgUrl = :productImgUrl, productStatus = :productStatus WHERE productId = :productId";
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

  public static function deleteProduct(int $productId) {
    $sqlstr = "DELETE FROM products WHERE productId = :productId";
    $params = ["productId" => $productId];
    return self::executeNonQuery($sqlstr, $params);
  }



  

}