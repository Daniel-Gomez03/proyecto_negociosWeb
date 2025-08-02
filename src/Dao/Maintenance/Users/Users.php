<?php
namespace Dao\Maintenance\Users;
use Dao\Security\Security;
use Dao\Security\UsuarioTipo;
use Dao\Table;

class Users extends Table
{
    public static function getUsers(
        string $partialName = "",
        string $status = "",
        string $orderBy = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ) {
        $page = max(0, $page);
        $itemsPerPage = max(1, $itemsPerPage);

        $sqlstr = "SELECT 
            usercod, 
            useremail, 
            username, 
            userfching,
            userpswdest,
            userpswdexp,
            userest,
            userpswdchg,
            usertipo,
            CASE 
                WHEN userest = 'ACT' THEN 'Active'
                WHEN userest = 'INA' THEN 'Inactive'
                ELSE 'Unknown'
            END as userestDsc,
            CASE 
                WHEN usertipo = 'ADM' THEN 'Administrator'
                WHEN usertipo = 'PBL' THEN 'Publico'
                WHEN usertipo = 'AUD' THEN 'Auditor'
                ELSE 'Unknown'
            END as usertipoDsc
        FROM usuario";

        $sqlstrCount = "SELECT COUNT(*) as count FROM usuario";
        $conditions = [];
        $params = [];

        if ($partialName != "") {
            $conditions[] = "(username LIKE :partialName OR useremail LIKE :partialName)";
            $params["partialName"] = "%" . $partialName . "%";
        }

        if (!in_array($status, ["ACT", "INA", ""])) {
            throw new \Exception("Error Processing Request Status has invalid value");
        }

        if ($status != "") {
            $conditions[] = "userest = :status";
            $params["status"] = $status;
        }

        if (count($conditions) > 0) {
            $whereClause = " WHERE " . implode(" AND ", $conditions);
            $sqlstr .= $whereClause;
            $sqlstrCount .= $whereClause;
        }

        if (!in_array($orderBy, ["usercod", "username", "useremail", ""])) {
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
            "users" => $registros,
            "total" => $numeroDeRegistros,
            "page" => $page,
            "itemsPerPage" => $itemsPerPage
        ];
    }


    public static function getUsuarioById(int $usercod)
    {
        $sqlstr = "SELECT * from usuario where usercod = :usercod;";
        return self::obtenerUnRegistro($sqlstr, ["usercod" => $usercod]);
    }

    public static function getUsuarioByEmail(string $useremail)
    {
        $sqlstr = "SELECT * from usuario where useremail = :useremail;";
        return self::obtenerUnRegistro($sqlstr, ["useremail" => $useremail]);
    }

    public static function newUsuario(
        string $useremail,
        string $username,
        string $userpswd,
        string $userpswdest = 'ACT',
        string $userpswdexp,
        string $userest = 'ACT',
        string $useractcod,
        string $usertipo = UsuarioTipo::PUBLICO
    ) {
        $hashedPassword = Security::hashPassword($userpswd);

        $sqlstr = "INSERT INTO usuario 
        (useremail, username, userpswd, userfching, userpswdest, userpswdexp, userest, useractcod, userpswdchg, usertipo) 
        values 
        (:useremail, :username, :userpswd, NOW(), :userpswdest, :userpswdexp, :userest, :useractcod, NOW(), :usertipo);";

        return self::executeNonQuery(
            $sqlstr,
            [
                "useremail" => $useremail,
                "username" => $username,
                "userpswd" => $hashedPassword,
                "userpswdest" => $userpswdest,
                "userpswdexp" => $userpswdexp,
                "userest" => $userest,
                "useractcod" => $useractcod,
                "usertipo" => $usertipo
            ]
        );
    }

    public static function updateUsuario(
        int $usercod,
        string $useremail,
        string $username,
        string $userpswdest,
        string $userpswdexp,
        string $userest,
        string $usertipo
    ) {
        $sqlstr = "UPDATE usuario set 
            useremail = :useremail, 
            username = :username, 
            userpswdest = :userpswdest, 
            userpswdexp = :userpswdexp,
            userest = :userest, 
            usertipo = :usertipo 
            where usercod = :usercod;";

        return self::executeNonQuery(
            $sqlstr,
            [
                "useremail" => $useremail,
                "username" => $username,
                "userpswdest" => $userpswdest,
                "userpswdexp" => $userpswdexp,
                "userest" => $userest,
                "usertipo" => $usertipo,
                "usercod" => $usercod
            ]
        );
    }

    public static function updateUsuarioPassword(
        int $usercod,
        string $userpswd,
        string $userpswdexp
    ) {
        $sqlstr = "UPDATE usuario set 
            userpswd = :userpswd,
            userpswdchg = NOW(),
            userpswdexp = :userpswdexp
            where usercod = :usercod;";

        return self::executeNonQuery(
            $sqlstr,
            [
                "userpswd" => $userpswd,
                "userpswdexp" => $userpswdexp,
                "usercod" => $usercod
            ]
        );
    }

    public static function deleteUsuario(int $usercod)
    {
        $sqlstr = "DELETE FROM usuario where usercod = :usercod;";
        return self::executeNonQuery(
            $sqlstr,
            [
                "usercod" => $usercod
            ]
        );
    }
}