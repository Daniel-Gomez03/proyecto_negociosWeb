<?php
namespace Dao\Maintenance\Roles;
use Dao\Table;

class Roles extends Table
{
    public static function getRoles(
        string $partialCode = "",
        string $status = "",
        string $orderBy = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ) {
        $page = max(0, $page);
        $itemsPerPage = max(1, $itemsPerPage);

        $sqlstr = "SELECT 
            rolescod, 
            rolesdsc, 
            rolesest,
            CASE 
                WHEN rolesest = 'ACT' THEN 'Activo'
                WHEN rolesest = 'INA' THEN 'Inactivo'
                ELSE 'Desconocido'
            END as rolesestDsc
        FROM roles";

        $sqlstrCount = "SELECT COUNT(*) as count FROM roles";
        $conditions = [];
        $params = [];

        if ($partialCode != "") {
            $conditions[] = "rolescod LIKE :partialCode";
            $params["partialCode"] = "%" . $partialCode . "%";
        }

        if (!in_array($status, ["ACT", "INA", ""])) {
            throw new \Exception("Error Processing Request Status has invalid value");
        }

        if ($status != "") {
            $conditions[] = "rolesest = :status";
            $params["status"] = $status;
        }

        if (count($conditions) > 0) {
            $whereClause = " WHERE " . implode(" AND ", $conditions);
            $sqlstr .= $whereClause;
            $sqlstrCount .= $whereClause;
        }

        if (!in_array($orderBy, ["rolescod", "rolesdsc", ""])) {
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
            "roles" => $registros,
            "total" => $numeroDeRegistros,
            "page" => $page,
            "itemsPerPage" => $itemsPerPage
        ];
    }
    
    public static function getRolesById(string $rolescod)
    {
        $sqlstr = "SELECT * from roles where rolescod = :rolescod;";
        return self::obtenerUnRegistro($sqlstr, ["rolescod" => $rolescod]);
    }
    
    public static function newRole(string $rolescod, string $rolesdsc, string $rolesest)
    {
        $sqlstr = "INSERT INTO roles (rolescod, rolesdsc, rolesest) values (:rolescod, :rolesdsc, :rolesest);";
        return self::executeNonQuery(
            $sqlstr,
            [
                "rolescod" => $rolescod,
                "rolesdsc" => $rolesdsc,
                "rolesest" => $rolesest
            ]
        );
    }
    
    public static function updateRole(string $rolescod, string $rolesdsc, string $rolesest)
    {
        $sqlstr = "UPDATE roles set rolesdsc = :rolesdsc, rolesest = :rolesest where rolescod = :rolescod;";
        return self::executeNonQuery(
            $sqlstr,
            [
                "rolesdsc" => $rolesdsc,
                "rolesest" => $rolesest,
                "rolescod" => $rolescod
            ]
        );
    }
    
    public static function deleteRole(string $rolescod)
    {
        $sqlstr = "DELETE FROM roles where rolescod = :rolescod;";
        return self::executeNonQuery(
            $sqlstr,
            [
                "rolescod" => $rolescod
            ]
        );
    }
}