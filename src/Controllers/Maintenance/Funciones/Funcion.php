<?php
namespace Controllers\Maintenance\Funciones;

use Controllers\PrivateController;
use Views\Renderer;
use Dao\Maintenance\Funciones\Funciones as FuncionesDAO;
use Utilities\Site;
use Utilities\Validators;

const LIST_URL = "index.php?page=Maintenance-Funciones-Funciones";

class Funcion extends PrivateController {
    private array $viewData;
    private array $modes;
    public function __construct()
    {
        parent::__construct();
        $this->viewData = [
            "mode" => "",
            "fncod" => "",
            "fndsc" => "",
            "fnest" => "ACT",
            "fntyp" => "CTR",
            "modeDsc" => "",
            "errors" => [],
            "cancelLabel" => "Cancel",
            "showConfirm" => true,
            "readonly" => "",
            "selectedACT" => "selected",
            "selectedINA" => "",
            "selectedCTR" => "selected",
            "selectedFNC" => "",
            "selectedMNU" => ""
        ];
        $this->modes = [
            "INS" => "Nueva Función",
            "UPD" => "Actualizando %s",
            "DEL" => "Eliminando %s",
            "DSP" => "Detalles de la Función: %s"
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
        Renderer::render("maintenance/funciones/funcion", $this->viewData);
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
                "Algo salió mal, intente de nuevo.",
                "Intento de cargar el controlador sin el parámetro de consulta requerido MODE"
            );
        }
        $this->viewData["mode"] = $_GET["mode"];
        if (!isset($this->modes[$this->viewData["mode"]])) {
            $this->throwError(
                "Algo salió mal, intente de nuevo.",
                "Intento de cargar el controlador con un valor incorrecto en el parámetro de consulta MODE - " . $this->viewData["mode"]
            );
        }
        if ($this->viewData["mode"] !== "INS") {
            if (!isset($_GET["fncod"])) {
                $this->throwError(
                    "Algo salió mal, intente de nuevo.",
                    "Intento de cargar el controlador sin el parámetro de consulta requerido fncod"
                );
            }
            $this->viewData["fncod"] = $_GET["fncod"];
        }
    }

    private function getDataFromDB()
    {
        $tmpFeature = FuncionesDAO::getFuncionById($this->viewData["fncod"]);
        if ($tmpFeature && count($tmpFeature) > 0) {
            $this->viewData["fncod"] = $tmpFeature["fncod"];
            $this->viewData["fndsc"] = $tmpFeature["fndsc"];
            $this->viewData["fnest"] = $tmpFeature["fnest"];
            $this->viewData["fntyp"] = $tmpFeature["fntyp"];
            
            // Update selections
            $this->viewData["selectedACT"] = ($tmpFeature["fnest"] == "ACT") ? "selected" : "";
            $this->viewData["selectedINA"] = ($tmpFeature["fnest"] == "INA") ? "selected" : "";
            
            $this->viewData["selectedCTR"] = ($tmpFeature["fntyp"] == "CTR") ? "selected" : "";
            $this->viewData["selectedFNC"] = ($tmpFeature["fntyp"] == "FNC") ? "selected" : "";
            $this->viewData["selectedMNU"] = ($tmpFeature["fntyp"] == "MNU") ? "selected" : "";
        } else {
            $this->throwError(
                "Something went wrong, try again.",
                "Record with id " . $this->viewData["fncod"] . " not found."
            );
        }
    }

    private function getBodyData()
    {
        if ($this->viewData["mode"] !== "INS") {
            if (!isset($_POST["fncod"])) {
                $this->throwError(
                    "Algo salió mal, intente de nuevo.",
                    "Intento de publicar sin el parámetro fncod en el cuerpo"
                );
            }
            if ($_POST["fncod"] !== $this->viewData["fncod"]) {
                $this->throwError(
                    "Algo salió mal, intente de nuevo.",
                    "Intento de publicar con un valor inconsistente en el parámetro fncod - esperado: " . $this->viewData["fncod"] . " recibido: " . $_POST["fncod"]
                );
            }
        }

        if (!isset($_POST["fndsc"])) {
            $this->throwError(
                "Algo salió mal, intente de nuevo.",
                "Intento de publicar sin el parámetro fndsc en el cuerpo"
            );
        }

        if (!isset($_POST["fnest"])) {
            $this->throwError(
                "Algo salió mal, intente de nuevo.",
                "Intento de publicar sin el parámetro fnest en el cuerpo"
            );
        }

        if (!isset($_POST["fntyp"])) {
            $this->throwError(
                "Algo salió mal, intente de nuevo.",
                "Intento de publicar sin el parámetro fntyp en el cuerpo"
            );
        }

        if (!isset($_POST["xsrtoken"])) {
            $this->throwError(
                "Algo salió mal, intente de nuevo.",
                "Intento de publicar sin el parámetro xsrtoken en el cuerpo"
            );
        }

        if ($_POST["xsrtoken"] !== $_SESSION[$this->name . "-xsrtoken"]) {
            $this->throwError(
                "Algo salió mal, intente de nuevo.",
                "Intento de publicar con un valor inconsistente en el parámetro XSRToken - esperado: " . $_SESSION[$this->name . "-xsrtoken"] . " recibido: " . $_POST["xsrtoken"]
            );
        }

        if ($this->viewData["mode"] === "INS") {
            $this->viewData["fncod"] = $_POST["fncod"];
        }
        $this->viewData["fndsc"] = $_POST["fndsc"];
        $this->viewData["fnest"] = $_POST["fnest"];
        $this->viewData["fntyp"] = $_POST["fntyp"];
    }

    private function validateData(): bool
    {
        if (Validators::IsEmpty($this->viewData["fncod"])) {
            $this->innerError("fncod", "El código de la función es obligatorio.");
        } elseif (strlen($this->viewData["fncod"]) > 255) {
            $this->innerError("fncod", "El código de la función es demasiado largo. Se permiten un máximo de 255 caracteres.");
        }

        if (Validators::IsEmpty($this->viewData["fndsc"])) {
            $this->innerError("fndsc", "La descripción de la función es obligatoria.");
        } elseif (strlen($this->viewData["fndsc"]) > 255) {
            $this->innerError("fndsc", "La descripción de la función es demasiado larga. Se permiten un máximo de 255 caracteres.");
        }

        if (!in_array($this->viewData["fnest"], ["ACT", "INA"])) {
            $this->innerError("fnest", "Valor de estado de función no válido.");
        }

        if (!in_array($this->viewData["fntyp"], ["CTR", "FNC", "MNU"])) {
            $this->innerError("fntyp", "Valor de tipo de función no válido.");
        }

        return !(count($this->viewData["errors"]) > 0);
    }

    private function processData()
    {
        $mode = $this->viewData["mode"];
        switch ($mode) {
            case "INS":
                if (
                    FuncionesDAO::newFuncion(
                        $this->viewData["fncod"],
                        $this->viewData["fndsc"],
                        $this->viewData["fnest"],
                        $this->viewData["fntyp"]
                    )
                ) {
                    Site::redirectToWithMsg(LIST_URL, "Función creada con éxito");
                } else {
                    $this->innerError("global", "Algo salió mal al guardar la nueva función.");
                }
                break;
            case "UPD":
                if (
                    FuncionesDAO::updateFuncion(
                        $this->viewData["fncod"],
                        $this->viewData["fndsc"],
                        $this->viewData["fnest"],
                        $this->viewData["fntyp"]
                    )
                ) {
                    Site::redirectToWithMsg(LIST_URL, "Función actualizada con éxito");
                } else {
                    $this->innerError("global", "Algo salió mal al actualizar la función.");
                }
                break;
            case "DEL":
                if (
                    FuncionesDAO::deleteFuncion(
                        $this->viewData["fncod"]
                    )
                ) {
                    Site::redirectToWithMsg(LIST_URL, "Función eliminada con éxito");
                } else {
                    $this->innerError("global", "Algo salió mal al eliminar la función.");
                }
                break;
        }
    }

    private function prepareViewData()
    {
        $this->viewData["modeDsc"] = sprintf(
            $this->modes[$this->viewData["mode"]],
            $this->viewData["fncod"]
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
