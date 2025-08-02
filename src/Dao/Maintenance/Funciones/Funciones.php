<?php
namespace Dao\Maintenance\Funciones;

use Dao\Table;

class Funciones extends Table {
    public static function getFunciones(
        string $partialCode = "",
        string $status = "",
        string $type = "",
        string $orderBy = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ) {
        $page = max(0, $page);
        $itemsPerPage = max(1, $itemsPerPage);

        $sqlstr = "SELECT 
            fncod, 
            fndsc, 
            fnest,
            fntyp,
            CASE 
                WHEN fnest = 'ACT' THEN 'Activo'
                WHEN fnest = 'INA' THEN 'Inactivo'
                ELSE 'Desconocido'
            END as fnestDsc,
            CASE 
                WHEN fntyp = 'FNC' THEN 'Funcion'
                WHEN fntyp = 'CTR' THEN 'Controller'
                WHEN fntyp = 'MNU' THEN 'Menu'
                ELSE 'Desconocido'
            END as fntypDsc
        FROM funciones";

        $sqlstrCount = "SELECT COUNT(*) as count FROM funciones";
        $conditions = [];
        $params = [];

        if ($partialCode != "") {
            $conditions[] = "fncod LIKE :partialCode";
            $params["partialCode"] = "%" . $partialCode . "%";
        }

        if (!in_array($status, ["ACT", "INA", ""])) {
            throw new \Exception("Error Processing Request Status has invalid value");
        }

        if ($status != "") {
            $conditions[] = "fnest = :status";
            $params["status"] = $status;
        }

        if (!in_array($type, ["FUN", "CRT", ""])) {
            throw new \Exception("Error Processing Request Type has invalid value");
        }

        if ($type != "") {
            $conditions[] = "fntyp = :type";
            $params["type"] = $type;
        }

        if (count($conditions) > 0) {
            $whereClause = " WHERE " . implode(" AND ", $conditions);
            $sqlstr .= $whereClause;
            $sqlstrCount .= $whereClause;
        }

        if (!in_array($orderBy, ["fncod", "fndsc", ""])) {
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

        if ($page > max(0, $pagesCount - 1)) {
            $page = max(0, $pagesCount - 1);
        }

        $offset = $page * $itemsPerPage;
        $sqlstr .= " LIMIT $offset, $itemsPerPage";

        $registros = self::obtenerRegistros($sqlstr, $params);

        return [
            "funciones" => $registros,
            "total" => $numeroDeRegistros,
            "page" => $page,
            "itemsPerPage" => $itemsPerPage
        ];
    }
    
    public static function getFuncionById(string $fncod)
    {
        $sqlstr = "SELECT * from funciones where fncod = :fncod;";
        return self::obtenerUnRegistro($sqlstr, ["fncod" => $fncod]);
    }
    
    public static function newFuncion(string $fncod, string $fndsc, string $fnest, string $fntyp)
    {
        $sqlstr = "INSERT INTO funciones (fncod, fndsc, fnest, fntyp) 
                   values (:fncod, :fndsc, :fnest, :fntyp);";
        return self::executeNonQuery(
            $sqlstr,
            [
                "fncod" => $fncod,
                "fndsc" => $fndsc,
                "fnest" => $fnest,
                "fntyp" => $fntyp
            ]
        );
    }
    
    public static function updateFuncion(string $fncod, string $fndsc, string $fnest, string $fntyp)
    {
        $sqlstr = "UPDATE funciones set fndsc = :fndsc, fnest = :fnest, fntyp = :fntyp 
                   where fncod = :fncod;";
        return self::executeNonQuery(
            $sqlstr,
            [
                "fndsc" => $fndsc,
                "fnest" => $fnest,
                "fntyp" => $fntyp,
                "fncod" => $fncod
            ]
        );
    }
    
    public static function deleteFuncion(string $fncod)
    {
        $sqlstr = "DELETE FROM funciones where fncod = :fncod;";
        return self::executeNonQuery($sqlstr, ["fncod" => $fncod]);
    }
}