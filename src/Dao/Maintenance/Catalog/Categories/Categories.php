<?php
namespace Dao\Maintenance\Catalog\Categoreies;
use Dao\Table;

class Categoreies extends Table {


public static function getCategories(string $partialName = "", string $status = "", string $orderBy = "", bool $orderDescending = false, int $page = 0, int $itemsPerPage = 10)
{
     $sqlstr = "SELECT c.categoryId, c.categoryName, c.categoryDescription, c.categoryStatus, CASE
                WHEN c.categoryStatus = 'ACT' THEN 'Activa'
                WHEN c.categoryStatus = 'INA' THEN 'Inactiva'
                ELSE 'Sin Asignar'
                END AS categoryStatusDsc
                FROM categories c";

    $sqlstrCount = "SELECT COUNT(*) as count FROM categories c";


    $conditions = [];
    $params = [];


    if ($partialName != "") {
        $conditions[] = "c.categoryName LIKE :partialName"; 
        $params["partialName"] = "%" . $partialName . "%";
    }
    if (!in_array($status, ["ACT", "INA", ""])) {
      throw new \Exception("Error Processing Request Status has invalid value");
    }
    if ($status != "") {
        $conditions[] = "c.categoryStatus = :status"; 
        $params["status"] = $status;
    }
    if (count($conditions) > 0) {
        //misma cosa pero el wherecaluse es como funcion
        $whereClause = " WHERE " . implode(" AND ", $conditions);
        $sqlstr .= $whereClause;
        $sqlstrCount .= $whereClause;
    }
    if (!in_array($orderBy, ["categoryId", "categoryName", ""])) {
        throw new \Exception("Error Processing Request: OrderBy has invalid value");
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

        //esto hace que el incide no de las paginas no sea -1 cuando no hay registros
        //no se si les parece?
      $page = max($pagesCount - 1, 0);
    }
    $sqlstr .= " LIMIT " . $page * $itemsPerPage . ", " . $itemsPerPage;

    $registros = self::obtenerRegistros($sqlstr, $params);


    return [
        "categories" => $registros,
        "total" => $numeroDeRegistros,
        "page" => $page,
        "itemsPerPage" => $itemsPerPage
    ];
}


public static function getCategoryById(int $categoryId) 
{
  $sqlstr = "SELECT c.categoryId, c.categoryName, c.categoryDescription, c.categoryStatus
             FROM categories c
             WHERE c.categoryId = :categoryId";
  $params = ["categoryId" => $categoryId];
  return self::obtenerUnRegistro($sqlstr, $params);
}



public static function insertCategory(string $categoryName, string $categoryDescription, string $categoryStatus) 
{
  $sqlstr = "INSERT INTO categories (categoryName, categoryDescription, categoryStatus) VALUES (:categoryName, :categoryDescription, :categoryStatus)";
  $params = [
    "categoryName" => $categoryName,
    "categoryDescription" => $categoryDescription,
    "categoryStatus" => $categoryStatus
  ];
  return self::executeNonQuery($sqlstr, $params);
}



public static function updateCategory(int $categoryId, string $categoryName, string $categoryDescription, string $categoryStatus) 
{
  $sqlstr = "UPDATE categories SET categoryName = :categoryName, categoryDescription = :categoryDescription, categoryStatus = :categoryStatus WHERE categoryId = :categoryId";
  $params = [
    "categoryId" => $categoryId,
    "categoryName" => $categoryName,
    "categoryDescription" => $categoryDescription,
    "categoryStatus" => $categoryStatus
  ];
  return self::executeNonQuery($sqlstr, $params);
}


public static function deleteCategory(int $categoryId) 
{
  $sqlstr = "DELETE FROM categories WHERE categoryId = :categoryId";
  $params = ["categoryId" => $categoryId];
  return self::executeNonQuery($sqlstr, $params);
}


}
