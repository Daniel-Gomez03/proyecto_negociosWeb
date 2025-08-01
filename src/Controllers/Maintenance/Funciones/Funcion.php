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
    "fncod" => "",
    "fndsc" => "",
    "fnest" => "ACT",
    "fntyp" => ""
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
    $this->funcion["fncod"] = trim($_POST["fncod"] ?? "");
    $this->funcion["fndsc"] = trim($_POST["fndsc"] ?? "");
    $this->funcion["fnest"] = trim($_POST["fnest"] ?? "ACT");
    $this->funcion["fntyp"] = trim($_POST["fntyp"] ?? "");

    if (Validators::IsEmpty($this->funcion["fncod"])) {
        $errors["fncod_error"] = "El código de la función es requerido";
    }

    if (Validators::IsEmpty($this->funcion["fndsc"])) {
        $errors["fndsc_error"] = "La descripción es requerida";
    }

    if (!in_array($this->funcion["fnest"], ["ACT", "INA"])) {
        $errors["fnest_error"] = "El estado de la función es inválido";
    }

    if (count($errors) > 0) {
        foreach ($errors as $key => $value) {
            $this->funcion[$key] = $value;
        }
        return false;
    }
    return true;
}



    private function setViewData(): void {
        $this->viewData["mode"] = $this->mode;
        $this->viewData["funcion_xss_token"] = $this->funcion_xss_token;
      $this->viewData["FormTitle"] = sprintf(
    $this->modeDescriptions[$this->mode],
    $this->funcion["fndsc"] ?? "",
    $this->funcion["fnest"] ?? ""
);
        $this->viewData["showCommitBtn"] = $this->showCommitBtn;
        $this->viewData["readonly"] = $this->readonly;

        $statusKey = "fnest_" . strtolower($this->funcion["fnest"]);
$this->funcion[$statusKey] = "selected";


        $this->viewData["funcion"] = $this->funcion;
    }





   private function handlePostAction(){
    switch($this->mode){
        case "INS":
            $result = FuncionesDao::insertFuncion(
                $this->funcion["fncod"],
                $this->funcion["fndsc"],
                $this->funcion["fnest"],
                $this->funcion["fntyp"]
            );
            if (!$result) {
                throw new \Exception("Error al insertar la función");
            }
            Site::redirectToWithMsg("index.php?page=Maintenance_Funciones_Funciones", "Función creada exitosamente");
            break;

        case "UPD":
            $result = FuncionesDao::updateFuncion(
                $this->funcion["fncod"],
                $this->funcion["fndsc"],
                $this->funcion["fnest"],
                $this->funcion["fntyp"]
            );
            if (!$result) {
                throw new \Exception("Error al actualizar la función");
            }
            Site::redirectToWithMsg("index.php?page=Maintenance_Funciones_Funciones", "Función actualizada exitosamente");
            break;

        case "DEL":
            $result = FuncionesDao::deleteFuncion($this->funcion["fncod"]);
            if ($result > 0) {
                Site::redirectToWithMsg("index.php?page=Maintenance_Funciones_Funciones", "Función eliminada exitosamente");
            } else {
                throw new \Exception("Error al eliminar la función");
            }
            break;

        default:
            throw new \Exception("Modo inválido");
    }
}


    
}
