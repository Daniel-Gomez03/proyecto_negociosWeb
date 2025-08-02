<?php
namespace Controllers\Maintenance\Roles;

use Controllers\PrivateController;
use Dao\Maintenance\Roles\Roles as RolesDAO;
use Views\Renderer;

use Utilities\Site;
use Utilities\Validators;

const LIST_URL = "index.php?page=Maintenance-Roles-Roles";

class Rol extends PrivateController
{
    private array $viewData;
    private array $modes;
    public function __construct()
    {
        parent::__construct();
        $this->viewData = [
            "mode" => "",
            "rolescod" => "",
            "rolesdsc" => "",
            "rolesest" => "ACT",
            "modeDsc" => "",
            "errors" => [],
            "cancelLabel" => "Cancel",
            "showConfirm" => true,
            "readonly" => "",
            "selectedACT" => "selected",
            "selectedINA" => ""
        ];
        $this->modes = [
            "INS" => "Nuevo Rol",
            "UPD" => "Actualizando %s",
            "DEL" => "Eliminando %s",
            "DSP" => "Detalles del Rol: %s"
        ];
    }

    public function run(): void
    {
        $this->getQueryParamsData();
        if ($this->viewData["mode"] !== "INS") {
            $this->getDataFromDB();
        }
        if ($this->isPostBack()) {
            $this->getBodyData();
            if ($this->validateData()) {
                $this->processData();
            }
        }
        $this->prepareViewData();
        Renderer::render("maintenance/roles/rol", $this->viewData);
    }

    private function throwError(string $message, string $logMessage = "")
    {
        if (!empty($logMessage)) {
            error_log(sprintf("%s - %s", $this->name, $logMessage));
        }
        Site::redirectToWithMsg(LIST_URL, $message);
    }

    private function innerError(string $scope, string $message)
    {
        if (!isset($this->viewData["errors"][$scope])) {
            $this->viewData["errors"][$scope] = [$message];
        } else {
            $this->viewData["errors"][$scope][] = $message;
        }
    }

    private function getQueryParamsData()
    {
        if (!isset($_GET["mode"])) {
            $this->throwError(
                "Algo salió mal, inténtalo de nuevo.",
                "Intento de cargar el controlador sin el parámetro de consulta requerido MODE"
            );
        }
        $this->viewData["mode"] = $_GET["mode"];
        if (!isset($this->modes[$this->viewData["mode"]])) {
            $this->throwError(
                "Algo salió mal, inténtalo de nuevo.",
                "Intento de cargar el controlador con un valor incorrecto en el parámetro de consulta MODE - " . $this->viewData["mode"]
            );
        }
        if ($this->viewData["mode"] !== "INS") {
            if (!isset($_GET["rolescod"])) {
                $this->throwError(
                    "Algo salió mal, inténtalo de nuevo.",
                    "Intento de cargar el controlador sin el parámetro de consulta requerido rolescod"
                );
            }
            $this->viewData["rolescod"] = $_GET["rolescod"];
        }
    }

    private function getDataFromDB()
    {
        $tmpRol = RolesDAO::getRolesById($this->viewData["rolescod"]);
        if ($tmpRol && count($tmpRol) > 0) {
            $this->viewData["rolescod"] = $tmpRol["rolescod"];
            $this->viewData["rolesdsc"] = $tmpRol["rolesdsc"];
            $this->viewData["rolesest"] = $tmpRol["rolesest"];
            
            // Update status selection
            $this->viewData["selectedACT"] = ($tmpRol["rolesest"] == "ACT") ? "selected" : "";
            $this->viewData["selectedINA"] = ($tmpRol["rolesest"] == "INA") ? "selected" : "";
        } else {
            $this->throwError(
                "Algo salió mal, inténtalo de nuevo.",
                "Registro con id " . $this->viewData["rolescod"] . " no encontrado."
            );
        }
    }

    private function getBodyData()
    {
        if ($this->viewData["mode"] !== "INS") {
            if (!isset($_POST["rolescod"])) {
                $this->throwError(
                    "Algo salió mal, inténtalo de nuevo.",
                    "Intento de enviar sin el parámetro rolescod en el cuerpo"
                );
            }
            if ($_POST["rolescod"] !== $this->viewData["rolescod"]) {
                $this->throwError(
                    "Algo salió mal, inténtalo de nuevo.",
                    "Intento de enviar con un valor inconsistente en el parámetro rolescod - esperado: " . $this->viewData["rolescod"] . " recibido: " . $_POST["rolescod"]
                );
            }
        }

        if (!isset($_POST["rolesdsc"])) {
            $this->throwError(
                "Algo salió mal, inténtalo de nuevo.",
                "Intento de enviar sin el parámetro rolesdsc en el cuerpo"
            );
        }

        if (!isset($_POST["rolesest"])) {
            $this->throwError(
                "Algo salió mal, inténtalo de nuevo.",
                "Intento de enviar sin el parámetro rolesest en el cuerpo"
            );
        }

        if (!isset($_POST["xsrtoken"])) {
            $this->throwError(
                "Algo salió mal, inténtalo de nuevo.",
                "Intento de enviar sin el parámetro xsrtoken en el cuerpo"
            );
        }

        if ($_POST["xsrtoken"] !== $_SESSION[$this->name . "-xsrtoken"]) {
            $this->throwError(
                "Algo salió mal, inténtalo de nuevo.",
                "Intento de enviar con un valor inconsistente en el parámetro xsrtoken - esperado: " . $_SESSION[$this->name . "-xsrtoken"] . " recibido: " . $_POST["xsrtoken"]
            );
        }

        if ($this->viewData["mode"] === "INS") {
            $this->viewData["rolescod"] = $_POST["rolescod"];
        }
        $this->viewData["rolesdsc"] = $_POST["rolesdsc"];
        $this->viewData["rolesest"] = $_POST["rolesest"];
    }

    private function validateData(): bool
    {
        if (Validators::IsEmpty($this->viewData["rolescod"])) {
            $this->innerError("rolescod", "El código de rol es obligatorio.");
        } elseif (strlen($this->viewData["rolescod"]) > 128) {
            $this->innerError("rolescod", "El código de rol es demasiado largo. Se permiten un máximo de 128 caracteres.");
        }

        if (Validators::IsEmpty($this->viewData["rolesdsc"])) {
            $this->innerError("rolesdsc", "La descripción del rol es obligatoria.");
        } elseif (strlen($this->viewData["rolesdsc"]) > 45) {
            $this->innerError("rolesdsc", "La descripción es demasiado larga. Se permiten un máximo de 45 caracteres.");
        }

        if (!in_array($this->viewData["rolesest"], ["ACT", "INA"])) {
            $this->innerError("rolesest", "Valor de estado de rol inválido.");
        }

        return !(count($this->viewData["errors"]) > 0);
    }

    private function processData()
    {
        $mode = $this->viewData["mode"];
        switch ($mode) {
            case "INS":
                if (
                    RolesDAO::newRole(
                        $this->viewData["rolescod"],
                        $this->viewData["rolesdsc"],
                        $this->viewData["rolesest"]
                    )
                ) {
                    Site::redirectToWithMsg(LIST_URL, "Rol creado exitosamente");
                } else {
                    $this->innerError("global", "Algo salió mal al guardar el nuevo rol.");
                }
                break;
            case "UPD":
                if (
                    RolesDAO::updateRole(
                        $this->viewData["rolescod"],
                        $this->viewData["rolesdsc"],
                        $this->viewData["rolesest"]
                    )
                ) {
                    Site::redirectToWithMsg(LIST_URL, "Rol actualizado exitosamente");
                } else {
                    $this->innerError("global", "Algo salió mal al actualizar el rol.");
                }
                break;
            case "DEL":
                if (
                    RolesDAO::deleteRole(
                        $this->viewData["rolescod"]
                    )
                ) {
                    Site::redirectToWithMsg(LIST_URL, "Rol eliminado exitosamente");
                } else {
                    $this->innerError("global", "Algo salió mal al eliminar el rol.");
                }
                break;
        }
    }

    private function prepareViewData()
    {
        $this->viewData["modeDsc"] = sprintf(
            $this->modes[$this->viewData["mode"]],
            $this->viewData["rolescod"]
        );

        if (count($this->viewData["errors"]) > 0) {
            foreach ($this->viewData["errors"] as $scope => $errorsArray) {
                $this->viewData["errors_" . $scope] = $errorsArray;
            }
        }

        if ($this->viewData["mode"] === "DSP") {
            $this->viewData["cancelLabel"] = "Back";
            $this->viewData["showConfirm"] = false;
        }

        if ($this->viewData["mode"] === "DSP" || $this->viewData["mode"] === "DEL") {
            $this->viewData["readonly"] = "readonly";
        }

        $this->viewData["timestamp"] = time();
        $this->viewData["xsrtoken"] = hash("sha256", json_encode($this->viewData));
        $_SESSION[$this->name . "-xsrtoken"] = $this->viewData["xsrtoken"];
    }
}