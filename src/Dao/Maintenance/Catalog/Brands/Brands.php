<?php
namespace Dao\Maintenance\Catalog\Brands;

use Dao\Table;

class Brands extends Table
{
    public static function getBrands(
        string $partialName = "",
        string $status = "",
        string $orderBy = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ) {
        $sqlstr = "SELECT 
            brandId, 
            brandName, 
            brandDescription, 
            brandStatus,
            CASE 
                WHEN brandStatus = 'ACT' THEN 'Active'
                WHEN brandStatus = 'INA' THEN 'Inactive'
                ELSE 'Unknown'
            END as brandStatusDsc
        FROM brands";

        $sqlstrCount = "SELECT COUNT(*) as count FROM brands";
        $conditions = [];
        $params = [];

        if ($partialName != "") {
            $conditions[] = "brandName LIKE :partialName";
            $params["partialName"] = "%" . $partialName . "%";
        }

        if (!in_array($status, ["ACT", "INA", ""])) {
            throw new \Exception("Error Processing Request Status has invalid value");
        }

        if ($status != "") {
            $conditions[] = "brandStatus = :status";
            $params["status"] = $status;
        }

        if (count($conditions) > 0) {
            $sqlstr .= " WHERE " . implode(" AND ", $conditions);
            $sqlstrCount .= " WHERE " . implode(" AND ", $conditions);
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
            $page = $pagesCount - 1;
        }

        $sqlstr .= " LIMIT " . $page * $itemsPerPage . ", " . $itemsPerPage;

        $registros = self::obtenerRegistros($sqlstr, $params);
        return ["brands" => $registros, "total" => $numeroDeRegistros, "page" => $page, "itemsPerPage" => $itemsPerPage];
    }

    public static function getBrandById(int $brandId)
    {
        $sqlstr = "SELECT 
            brandId, 
            brandName, 
            brandDescription, 
            brandStatus
        FROM brands 
        WHERE brandId = :brandId";

        $params = ["brandId" => $brandId];
        return self::obtenerUnRegistro($sqlstr, $params);
    }

    public static function insertBrand(
        string $brandName,
        string $brandDescription,
        string $brandStatus = 'ACT'
    ) {
        $sqlstr = "INSERT INTO brands (
            brandName, 
            brandDescription, 
            brandStatus
        ) VALUES (
            :brandName, 
            :brandDescription, 
            :brandStatus
        )";

        $params = [
            "brandName" => $brandName,
            "brandDescription" => $brandDescription,
            "brandStatus" => $brandStatus
        ];

        return self::executeNonQuery($sqlstr, $params);
    }

    public static function updateBrand(
        int $brandId,
        string $brandName,
        string $brandDescription,
        string $brandStatus
    ) {
        $sqlstr = "UPDATE brands SET 
            brandName = :brandName, 
            brandDescription = :brandDescription, 
            brandStatus = :brandStatus
        WHERE brandId = :brandId";

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

    public static function getActiveBrands()
    {
        $sqlstr = "SELECT brandId as value, brandName as text FROM brands 
               WHERE brandStatus = 'ACT' ORDER BY brandName";
        return self::obtenerRegistros($sqlstr, []) ?: [];
    }
}