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
            $sqlstr = "SELECT fncod, fndsc, fnest, fntyp,
                            CASE fnest
                                WHEN 'ACT' THEN 'Activo'
                                WHEN 'INA' THEN 'Inactivo'
                                ELSE 'Sin Asignar'
                            END AS fnestDsc
                        FROM funciones";
            $sqlstrCount = "SELECT COUNT(*) AS count FROM funciones";
            $conditions = [];
            $params = [];

            if ($partialDescription !== "") {
                $conditions[] = "fndsc LIKE :partialDescription";
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
                $sqlstr .= " WHERE " . implode(" AND ", $conditions);
                $sqlstrCount .= " WHERE " . implode(" AND ", $conditions);
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
            if ($page > $pagesCount - 1) {
                $page = $pagesCount - 1;
            }

            $sqlstr .= " LIMIT " . $page * $itemsPerPage . ", " . $itemsPerPage;
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
