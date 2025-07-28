<?php

    namespace Dao\Maintenance\Users;

    use Dao\Table;

     class Users extends Table {
        public static function getUsers(
            string $partialName = "",
            string $status = "",
            string $orderBy = "",
            bool $orderDescending = false,
            int $page = 0,
            int $itemsPerPage = 10
        ): array {
            $sqlstr = "SELECT u.usercod, u.useremail, u.username, u.userfching, u.userest, u.usertipo, CASE u.userest WHEN 'ACT' THEN 'Activo' WHEN 'INA' THEN 'Inactivo' ELSE 'Sin Asignar' END AS userestDsc FROM usuario u";  
            $sqlstrCount = "SELECT COUNT(*) AS count FROM usuario u";
            $conditions = [];
            $params = [];
            if ($partialName !== "") {
                $conditions[] = "(u.username LIKE :partialName OR u.useremail LIKE :partialName)";
                $params["partialName"] = "%" . $partialName . "%";
            }
            if (!in_array($status, ["ACT", "INA", ""])) {
                throw new \Exception("Error Processing Request: Status has invalid value");
            }
            if ($status !== "") {
                $conditions[] = "u.userest = :status";
                $params["status"] = $status;
            }
            if (!empty($conditions)) {
                $sqlstr .= " WHERE " . implode(" AND ", $conditions);
                $sqlstrCount .= " WHERE " . implode(" AND ", $conditions);
            }
            if (!in_array($orderBy, ["usercod", "useremail", "username", "userfching", ""])) {
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
            if ($page > $pagesCount - 1) {
                $page = $pagesCount - 1;
            }
            $sqlstr .= " LIMIT " . $page * $itemsPerPage . ", " . $itemsPerPage;
            $registros = self::obtenerRegistros($sqlstr, $params);
            return [
                "users" => $registros,
                "total" => $numeroDeRegistros,
                "page" => $page,
                "itemsPerPage" => $itemsPerPage
            ];
        }

        public static function getUserById(int $usercod) {
            $sqlstr = "SELECT * FROM usuario WHERE usercod = :usercod;";
            return self::obtenerUnRegistro($sqlstr, ["usercod" => $usercod]);
        }

        public static function insertUser(
            string $useremail,
            string $username,
            string $userpswd,
            string $userest,
            string $usertipo
        ) {
            $sqlstr = "INSERT INTO usuario (useremail, username, userpswd, userfching, userest, usertipo) VALUES (:useremail, :username, :userpswd, NOW(), :userest, :usertipo)";
            $params = [
                "useremail" => $useremail,
                "username" => $username,
                "userpswd" => $userpswd,
                "userest" => $userest,
                "usertipo" => $usertipo
            ];
            return self::executeNonQuery($sqlstr, $params);
        }

        public static function updateUser(
            int $usercod,
            string $useremail,
            string $username,
            string $userest,
            string $usertipo
        ) {
            $sqlstr = "UPDATE usuario SET useremail = :useremail, username = :username, userest = :userest, usertipo = :usertipo WHERE usercod = :usercod";
            $params = [
                "usercod" => $usercod,
                "useremail" => $useremail,
                "username" => $username,
                "userest" => $userest,
                "usertipo" => $usertipo
            ];
            return self::executeNonQuery($sqlstr, $params);
        }

        public static function deleteUser(int $int) {
            $sqlstr = "DELETE FROM usuario WHERE usercod = :usercod;";
            return self::executeNonQuery($sqlstr, ["usercod" => $int]);
        }
    }
