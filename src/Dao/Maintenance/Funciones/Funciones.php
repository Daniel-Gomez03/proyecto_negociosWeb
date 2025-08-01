<?php

namespace Dao\Maintenance\Funciones;

use Dao\Table;

class Funciones extends Table {
    public static function getFunciones(
        string $partialDescription = "",
        string $status = "",
        string $type = "",
        string $orderBy = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ): array {
        $page = max(0, $page);
        $itemsPerPage = max(1, $itemsPerPage);

        $sqlstr = "SELECT fncod, fndsc, fnest, fntyp,
                    CASE fnest
                        WHEN 'ACT' THEN 'Activo'
                        WHEN 'INA' THEN 'Inactivo'
                        ELSE 'Sin Asignar'
                    END AS fnestDsc,
                    CASE fntyp
                        WHEN 'CNT' THEN 'Controlador'
                        WHEN 'SYS' THEN 'Sistema'
                        WHEN 'DSB' THEN 'Dashboard'
                        ELSE 'Sin Asignar'
                    END AS fntypDsc
                FROM funciones";
        
        $sqlstrCount = "SELECT COUNT(*) AS count FROM funciones";
        $conditions = [];
        $params = [];

        if ($partialDescription !== "") {
            $conditions[] = "fndsc LIKE :partialDescription OR fncod LIKE :partialDescription";
            $params["partialDescription"] = "%" . $partialDescription . "%";
        }

        if (!in_array($status, ["ACT", "INA", ""])) {
            throw new \Exception("Error Processing Request: Status has invalid value");
        }
        if ($status !== "") {
            $conditions[] = "fnest = :status";
            $params["status"] = $status;
        }

        if ($type !== "") {
            $conditions[] = "fntyp = :type";
            $params["type"] = $type;
        }

        if (!empty($conditions)) {
            $whereClause = " WHERE " . implode(" AND ", $conditions);
            $sqlstr .= $whereClause;
            $sqlstrCount .= $whereClause;
        }

        if (!in_array($orderBy, ["fncod", "fndsc", "fnest", "fntyp", ""])) {
            throw new \Exception("Error Processing Request: OrderBy has invalid value");
        }
        if ($orderBy !== "") {
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

    public static function getFuncionById(string $fncod) {
        $sqlstr = "SELECT * FROM funciones WHERE fncod = :fncod;";
        return self::obtenerUnRegistro($sqlstr, ["fncod" => $fncod]);
    }

    public static function insertFuncion(string $fncod, string $fndsc, string $fnest, string $fntyp) {
        $sqlstr = "INSERT INTO funciones (fncod, fndsc, fnest, fntyp)
                    VALUES (:fncod, :fndsc, :fnest, :fntyp);";
        $params = [
            "fncod" => $fncod,
            "fndsc" => $fndsc,
            "fnest" => $fnest,
            "fntyp" => $fntyp
        ];
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function updateFuncion(string $fncod, string $fndsc, string $fnest, string $fntyp) {
        $sqlstr = "UPDATE funciones 
                    SET fndsc = :fndsc, fnest = :fnest, fntyp = :fntyp 
                    WHERE fncod = :fncod;";
        $params = [
            "fncod" => $fncod,
            "fndsc" => $fndsc,
            "fnest" => $fnest,
            "fntyp" => $fntyp
        ];
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function deleteFuncion(string $fncod) {
        $sqlstr = "DELETE FROM funciones WHERE fncod = :fncod;";
        return self::executeNonQuery($sqlstr, ["fncod" => $fncod]);
    }
}