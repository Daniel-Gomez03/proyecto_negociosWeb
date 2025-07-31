<?php

    namespace Dao\Maintenance\Users;
    if (version_compare(phpversion(), '7.4.0', '<')) {
        define('PASSWORD_ALGORITHM', 1);  //BCRYPT
} else {
    define('PASSWORD_ALGORITHM', '2y');  //BCRYPT
}

    use Dao\Table;

     class Users extends Table {

     private static function _hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

   



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
    string $usertipo,
    string $userpswdexp,
    string $userpswdest,
    string $useractcod,
    $userpswdchg = null // puede ser null o string (datetime si se usa)
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



        public static function updateUser(
    int $usercod,
    string $useremail,
    string $username,
    string $userpswd,
    string $userest,
    string $usertipo
) {
    $sqlstr = "UPDATE usuario SET 
        useremail = :useremail,
        username = :username,
        userest = :userest,
        usertipo = :usertipo";

    $params = [
        "usercod" => $usercod,
        "useremail" => $useremail,
        "username" => $username,
        "userest" => $userest,
        "usertipo" => $usertipo
    ];

    // Solo agregamos contraseña si se envía una nueva
    if (!empty($userpswd)) {
        $hashedPassword = password_hash($userpswd, PASSWORD_BCRYPT);
        $sqlstr .= ", userpswd = :userpswd";
        $params["userpswd"] = $hashedPassword;
    }

    $sqlstr .= " WHERE usercod = :usercod";

    return self::executeNonQuery($sqlstr, $params);
}




       public static function deleteUser(string $usercod) {
            $sqlstr = "DELETE FROM usuario WHERE usercod = :usercod;";
            return self::executeNonQuery($sqlstr, ["usercod" => $usercod]);
        }

<<<<<<< HEAD
=======
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

>>>>>>> 797238068fa51e7e6566ccf01f64f12e06e6ff5f


    }