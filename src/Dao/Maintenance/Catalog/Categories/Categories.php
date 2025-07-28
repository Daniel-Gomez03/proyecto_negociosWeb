<?php
namespace Dao\Maintenance\Catalog\Categories;

use Dao\Table;

class Categories extends Table
{
    public static function getCategories(
        string $partialName = "",
        string $status = "",
        string $orderBy = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ) {
        $sqlstr = "SELECT 
            categoryId, 
            categoryName, 
            categoryDescription, 
            categoryStatus,
            CASE 
                WHEN categoryStatus = 'ACT' THEN 'Active'
                WHEN categoryStatus = 'INA' THEN 'Inactive'
                ELSE 'Unknown'
            END as categoryStatusDsc
        FROM categories";

        $sqlstrCount = "SELECT COUNT(*) as count FROM categories";
        $conditions = [];
        $params = [];

        if ($partialName != "") {
            $conditions[] = "categoryName LIKE :partialName";
            $params["partialName"] = "%" . $partialName . "%";
        }

        if (!in_array($status, ["ACT", "INA", ""])) {
            throw new \Exception("Error Processing Request Status has invalid value");
        }

        if ($status != "") {
            $conditions[] = "categoryStatus = :status";
            $params["status"] = $status;
        }

        if (count($conditions) > 0) {
            $sqlstr .= " WHERE " . implode(" AND ", $conditions);
            $sqlstrCount .= " WHERE " . implode(" AND ", $conditions);
        }

        if (!in_array($orderBy, ["categoryId", "categoryName", ""])) {
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
        return ["categories" => $registros, "total" => $numeroDeRegistros, "page" => $page, "itemsPerPage" => $itemsPerPage];
    }

    public static function getCategoryById(int $categoryId)
    {
        $sqlstr = "SELECT 
            categoryId, 
            categoryName, 
            categoryDescription, 
            categoryStatus
        FROM categories 
        WHERE categoryId = :categoryId";

        $params = ["categoryId" => $categoryId];
        return self::obtenerUnRegistro($sqlstr, $params);
    }

    public static function insertCategory(
        string $categoryName,
        string $categoryDescription,
        string $categoryStatus = 'ACT'
    ) {
        $sqlstr = "INSERT INTO categories (
            categoryName, 
            categoryDescription, 
            categoryStatus
        ) VALUES (
            :categoryName, 
            :categoryDescription, 
            :categoryStatus
        )";

        $params = [
            "categoryName" => $categoryName,
            "categoryDescription" => $categoryDescription,
            "categoryStatus" => $categoryStatus
        ];

        return self::executeNonQuery($sqlstr, $params);
    }

    public static function updateCategory(
        int $categoryId,
        string $categoryName,
        string $categoryDescription,
        string $categoryStatus
    ) {
        $sqlstr = "UPDATE categories SET 
            categoryName = :categoryName, 
            categoryDescription = :categoryDescription, 
            categoryStatus = :categoryStatus
        WHERE categoryId = :categoryId";

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

    public static function getActiveCategories()
    {
        $sqlstr = "SELECT categoryId as value, categoryName as text FROM categories 
               WHERE categoryStatus = 'ACT' ORDER BY categoryName";
        return self::obtenerRegistros($sqlstr, []) ?: [];
    }
}