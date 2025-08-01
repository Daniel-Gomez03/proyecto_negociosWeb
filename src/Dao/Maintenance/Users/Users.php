<?php

    namespace Dao\Maintenance\Users;

    use Dao\Table;

    

    class Users extends Table
    {
        // Función para hashear contraseña
        private static function _hashPassword($password)
        {
            return password_hash($password, PASSWORD_DEFAULT);
        }

        // Obtener listado de usuarios con paginación, filtros y orden
        public static function getUsers(
            string $partialName = "",
            string $status = "",
            string $orderBy = "",
            bool $orderDescending = false,
            int $page = 0,
            int $itemsPerPage = 10
        ): array {
            $sqlstr = "SELECT 
                            u.usercod, 
                            u.useremail, 
                            u.username, 
                            u.userfching, 
                            u.userest, 
                            u.usertipo, 
                            CASE u.userest 
                                WHEN 'ACT' THEN 'Activo' 
                                WHEN 'INA' THEN 'Inactivo' 
                                ELSE 'Sin Asignar' 
                            END AS userestDsc 
                    FROM usuario u";

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
                $whereClause = " WHERE " . implode(" AND ", $conditions);
                $sqlstr .= $whereClause;
                $sqlstrCount .= $whereClause;
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

        // Obtener un usuario por su ID
        public static function getUserById(int $usercod)
        {
            $sqlstr = "SELECT * FROM usuario WHERE usercod = :usercod;";
            return self::obtenerUnRegistro($sqlstr, ["usercod" => $usercod]);
        }

        // Insertar un nuevo usuario
        public static function insertUser(
    string $useremail,
    string $username,
    string $userpswd,
    string $userest,
    string $usertipo,
    string $userpswdexp,
    string $userpswdest,
    string $useractcod,
    $userpswdchg = null 
) {
    $sqlstr = "INSERT INTO usuario 
        (useremail, username, userpswd, userfching, userpswdexp, userpswdest, useractcod, userpswdchg, userest, usertipo) 
        VALUES 
        (:useremail, :username, :userpswd, NOW(), :userpswdexp, :userpswdest, :useractcod, :userpswdchg, :userest, :usertipo)";
    
    $params = [
        "useremail"     => $useremail,
        "username"      => $username,
        "userpswd"      => password_hash($userpswd, PASSWORD_BCRYPT),
        "userpswdexp"   => $userpswdexp,
        "userpswdest"   => $userpswdest,
        "useractcod"    => $useractcod,
        "userpswdchg"   => $userpswdchg,
        "userest"       => $userest,
        "usertipo"      => $usertipo
    ];

    return self::executeNonQuery($sqlstr, $params);
}



        // Actualizar usuario
        public static function updateUser(
    int $usercod,
    string $useremail,
    string $username,
    string $userpswd,
    string $userest,
    string $usertipo
) {
    $hashedPassword = self::_hashPassword($userpswd); 

   
    $userpswdest = "ACT";
    $userpswdexp = date('Y-m-d', time() + 7776000); 
    $useractcod = hash("sha256", $useremail . time());
    $userpswdchg = date('Y-m-d H:i:s'); 
    $sqlstr = "UPDATE usuario SET 
                useremail = :useremail,
                username = :username,
                userest = :userest,
                usertipo = :usertipo,
                userpswd = :userpswd,
                userpswdest = :userpswdest,
                userpswdexp = :userpswdexp,
                useractcod = :useractcod,
                userpswdchg = :userpswdchg
               WHERE usercod = :usercod";

    $params = [
        "usercod"       => $usercod,
        "useremail"     => $useremail,
        "username"      => $username,
        "userest"       => $userest,
        "usertipo"      => $usertipo,
        "userpswd"      => $hashedPassword,
        "userpswdest"   => $userpswdest,
        "userpswdexp"   => $userpswdexp,
        "useractcod"    => $useractcod,
        "userpswdchg"   => $userpswdchg
    ];

    return self::executeNonQuery($sqlstr, $params);
}


        // Eliminar un usuario
        public static function deleteUser(int $usercod)
        {
            $sqlstr = "DELETE FROM usuario WHERE usercod = :usercod;";
            return self::executeNonQuery($sqlstr, ["usercod" => $usercod]);
        }
    }
