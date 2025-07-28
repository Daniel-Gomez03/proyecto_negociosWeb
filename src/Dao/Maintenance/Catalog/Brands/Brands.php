<?php
namespace Dao\Maintenance\Catalog\Brands;
use Dao\Table;

class BrandsDao extends Table {


public static function getBrands(string $partialName = "", string $status = "", string $orderBy = "", bool $orderDescending = false, int $page = 0, int $itemsPerPage = 10)
{
    $sqlstr = "SELECT b.brandId, b.brandName, b.brandDescription, b.brandStatus, CASE WHEN b.brandStatus = 'ACT' THEN 'Activa' WHEN b.brandStatus = 'INA' THEN 'Inactiva' ELSE 'Sin Asignar' END AS brandStatusDsc FROM brands b";
    $sqlstrCount = "SELECT COUNT(*) as count FROM brands b";


    $conditions = [];
    $params = [];


    if ($partialName != "") {
        $conditions[] = "b.brandName LIKE :partialName"; 
        $params["partialName"] = "%" . $partialName . "%";
    }
    if (!in_array($status, ["ACT", "INA", ""])) {
      throw new \Exception("Error Processing Request Status has invalid value");
    }
    if ($status != "") {
        $conditions[] = "b.brandStatus = :status"; 
        $params["status"] = $status;
    }
    if (count($conditions) > 0) {
        //misma cosa pero el wherecaluse es como funcion
        $whereClause = " WHERE " . implode(" AND ", $conditions);
        $sqlstr .= $whereClause;
        $sqlstrCount .= $whereClause;
    }
    if (!in_array($orderBy, ["brandId", "brandName", ""])) {
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

        //esto hace que el incide no de las paginas no sea -1 cuando no hay registros
        //no se si les parece?
      $page = max($pagesCount - 1, 0);
    }
    $sqlstr .= " LIMIT " . $page * $itemsPerPage . ", " . $itemsPerPage;

    $registros = self::obtenerRegistros($sqlstr, $params);
    return ["brands" => $registros, "total" => $numeroDeRegistros, "page" => $page, "itemsPerPage" => $itemsPerPage];




}





public static function getBrandById(int $brandId) 
{
  $sqlstr = "SELECT b.brandId, b.brandName, b.brandDescription, b.brandStatus FROM brands b WHERE b.brandId = :brandId";
  $params = ["brandId" => $brandId];
  return self::obtenerUnRegistro($sqlstr, $params);
}





public static function insertBrand(string $brandName, string $brandDescription, string $brandStatus) 
{
  $sqlstr = "INSERT INTO brands (brandName, brandDescription, brandStatus) VALUES (:brandName, :brandDescription, :brandStatus)";
  $params = [
    "brandName" => $brandName,
    "brandDescription" => $brandDescription,
    "brandStatus" => $brandStatus
  ];
  return self::executeNonQuery($sqlstr, $params);
}





public static function updateBrand(int $brandId, string $brandName, string $brandDescription, string $brandStatus) 
{
  $sqlstr = "UPDATE brands SET brandName = :brandName, brandDescription = :brandDescription, brandStatus = :brandStatus WHERE brandId = :brandId";
  $params = [
    "brandId" => $brandId,
    "brandName" => $brandName,
    "brandDescription" => $brandDescription,
    "brandStatus" => $brandStatus
  ];
  return self::executeNonQuery($sqlstr, $params);
}





public static function deleteBrand(int $brandId) 
{
  $sqlstr = "DELETE FROM brands WHERE brandId = :brandId";
  $params = ["brandId" => $brandId];
  return self::executeNonQuery($sqlstr, $params);
}



}