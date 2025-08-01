<?php

    namespace Dao\Maintenance\Roles;

    use Dao\Table;

    class Roles extends Table {
       public static function getRoles(
    string $partialDescription = "",
    string $status = "",
    string $orderBy = "",
    bool $orderDescending = false,
    int $page = 0,
    int $itemsPerPage = 10
): array {
    $page = max(0, $page);
    $itemsPerPage = max(1, $itemsPerPage);

    $sqlstr = "SELECT rolescod, rolesdsc, rolesest,
                    CASE rolesest
                        WHEN 'ACT' THEN 'Activo'
                        WHEN 'INA' THEN 'Inactivo'
                        ELSE 'Sin Asignar'
                    END AS rolesestDsc
               FROM roles";

    $sqlstrCount = "SELECT COUNT(*) AS count FROM roles";
    $conditions = [];
    $params = [];

    if ($partialDescription !== "") {
        $conditions[] = "rolesdsc LIKE :partialDescription";
        $params["partialDescription"] = "%" . $partialDescription . "%";
    }

    if (!in_array($status, ["ACT", "INA", ""])) {
        throw new \Exception("Error Processing Request: Status has invalid value");
    }
    if ($status !== "") {
        $conditions[] = "rolesest = :status";
        $params["status"] = $status;
    }

    if (!empty($conditions)) {
        $whereClause = " WHERE " . implode(" AND ", $conditions);
        $sqlstr .= $whereClause;
        $sqlstrCount .= $whereClause;
    }

    if (!in_array($orderBy, ["rolescod", "rolesdsc", "rolesest", ""])) {
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
        "roles" => $registros,
        "total" => $numeroDeRegistros,
        "page" => $page,
        "itemsPerPage" => $itemsPerPage
    ];
}


        public static function getRoleById(string $rolescod) {
            $sqlstr = "SELECT * FROM roles WHERE rolescod = :rolescod;";
            return self::obtenerUnRegistro($sqlstr, ["rolescod" => $rolescod]);
        }

        public static function insertRole(string $rolescod, string $rolesdsc, string $rolesest) {
            $sqlstr = "INSERT INTO roles (rolescod, rolesdsc, rolesest) 
                    VALUES (:rolescod, :rolesdsc, :rolesest);";
            $params = [
                "rolescod" => $rolescod,
                "rolesdsc" => $rolesdsc,
                "rolesest" => $rolesest
            ];
            return self::executeNonQuery($sqlstr, $params);
        }

        public static function updateRole(string $oldCod, string $newCod, string $rolesdsc, string $rolesest) {
    $sqlstr = "UPDATE roles 
               SET rolescod = :newCod, rolesdsc = :rolesdsc, rolesest = :rolesest
               WHERE rolescod = :oldCod;";
    $params = [
        "newCod" => $newCod,
        "rolesdsc" => $rolesdsc,
        "rolesest" => $rolesest,
        "oldCod" => $oldCod
    ];
    return self::executeNonQuery($sqlstr, $params);
}


        public static function deleteRole(string $rolescod) {
            $sqlstr = "DELETE FROM roles WHERE rolescod = :rolescod;";
            return self::executeNonQuery($sqlstr, ["rolescod" => $rolescod]);
        }
    }