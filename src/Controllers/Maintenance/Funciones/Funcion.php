<?php
namespace Controllers\Maintenance\Funciones;

use Controllers\PublicController;
use Views\Renderer;
use Dao\Maintenance\Funciones\Funciones as FuncionesDao;
use Utilities\Site;
use Utilities\Validators;

class Funcion extends PublicController {
    private $viewData = [];
    private $mode = "DSP";
    private $modeDescriptions = [
        "DSP" => "Detalle de la función %s - %s",
        "INS" => "Nueva Función",
        "UPD" => "Editar función %s - %s",
        "DEL" => "Eliminar función %s - %s"
    ];
    private $readonly = "";
    private $showCommitBtn = true;
    private $funcion = [
        "funcod" => "",
        "funcdsc" => "",
        "funcest" => "ACT"
    ];
    private $funcion_xss_token = "";

    public function run(): void {
        try {
            $this->getData();
            if ($this->isPostBack()) {
                if ($this->validateData()) {
                    $this->handlePostAction();
                }
            }
            $this->setViewData();
            Renderer::render("maintenance/funciones/funcion", $this->viewData);
        } catch (\Exception $ex) {
            Site::redirectToWithMsg(
                "index.php?page=Maintenance_Funciones_Funciones",
                $ex->getMessage()
            );
        }
    }

    private function getData() {
        $this->mode = $_GET["mode"] ?? "NOF";
        if (isset($this->modeDescriptions[$this->mode])) {
            $this->readonly = $this->mode === "DEL" ? "readonly" : "";
            $this->showCommitBtn = $this->mode !== "DSP";
            if ($this->mode !== "INS") {
                $this->funcion = FuncionesDao::getFuncionById($_GET["id"]);
                if (!$this->funcion) {
                    throw new \Exception("No se encontró la función", 1);
                }
            }
        } else {
            throw new \Exception("Formulario cargado en modalidad inválida", 1);
        }
    }

    private function validateData() {
        $errors = [];
        $this->funcion_xss_token = $_POST["funcion_xss_token"] ?? "";
        $this->funcion["funcod"] = trim($_POST["funcod"] ?? "");
        $this->funcion["funcdsc"] = trim($_POST["funcdsc"] ?? "");
        $this->funcion["funcest"] = trim($_POST["funcest"] ?? "ACT");

        if (Validators::IsEmpty($this->funcion["funcod"])) {
            $errors["funcod_error"] = "El código de la función es requerido";
        }

        if (Validators::IsEmpty($this->funcion["funcdsc"])) {
            $errors["funcdsc_error"] = "La descripción es requerida";
        }

        if (!in_array($this->funcion["funcest"], ["ACT", "INA"])) {
            $errors["funcest_error"] = "El estado de la función es inválido";
        }

        if (count($errors) > 0) {
            foreach ($errors as $key => $value) {
                $this->funcion[$key] = $value;
            }
            return false;
        }
        return true;
    }

    private function handlePostAction() {
        switch ($this->mode) {
            case "INS":
                $this->handleInsert();
                break;
            case "UPD":
                $this->handleUpdate();
                break;
            case "DEL":
                $this->handleDelete();
                break;
            default:
                throw new \Exception("Modo inválido", 1);
        }
    }

    private function handleInsert() {
        $result = FuncionesDao::insertFuncion(
            $this->funcion["funcod"],
            $this->funcion["funcdsc"],
            $this->funcion["funcest"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Maintenance_Funciones_Funciones",
                "Función creada exitosamente"
            );
        }
    }

    private function handleUpdate() {
        $result = FuncionesDao::updateFuncion(
            $this->funcion["funcod"],
            $this->funcion["funcdsc"],
            $this->funcion["funcest"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Maintenance_Funciones_Funciones",
                "Función actualizada exitosamente"
            );
        }
    }

    private function handleDelete() {
        $result = FuncionesDao::deleteFuncion($this->funcion["funcod"]);
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Maintenance_Funciones_Funciones",
                "Función eliminada exitosamente"
            );
        }
    }

    private function setViewData(): void {
        $this->viewData["mode"] = $this->mode;
        $this->viewData["funcion_xss_token"] = $this->funcion_xss_token;
        $this->viewData["FormTitle"] = sprintf(
            $this->modeDescriptions[$this->mode],
            $this->funcion["funcod"],
            $this->funcion["funcdsc"]
        );
        $this->viewData["showCommitBtn"] = $this->showCommitBtn;
        $this->viewData["readonly"] = $this->readonly;

        $statusKey = "funcest_" . strtolower($this->funcion["funcest"]);
        $this->funcion[$statusKey] = "selected";

        $this->viewData["funcion"] = $this->funcion;
    }
}
