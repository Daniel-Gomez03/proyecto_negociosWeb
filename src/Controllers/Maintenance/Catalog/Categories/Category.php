<?php
namespace Controllers\Maintenance\Catalog\Categories;

use Controllers\PrivateController;
use Views\Renderer;
use Dao\Maintenance\Catalog\Categories\Categories as DaoCategories;
use Utilities\Site;
use Utilities\Validators;

const LIST_URL = "index.php?page=Maintenance_Catalog_Categories_Categories";

class Category extends PrivateController
{
    private array $viewData;
    private array $modes;
    private array $status;

    public function __construct()
    {
 

        $this->viewData = [
            "mode" => "",
            "categoryId" => 0,
            "categoryName" => "",
            "categoryDescription" => "",
            "categoryStatus" => "ACT",
            "formTitle" => "",
            "readonly" => "",
            "showCommitBtn" => true,
            "category_xss_token" => "",
            "errors" => []
        ];

        $this->modes = [
            "DSP" => "Detalle de %s %s",
            "INS" => "Nueva Categoría",
            "UPD" => "Editar %s %s",
            "DEL" => "Eliminar %s %s"
        ];

        $this->status = ["ACT", "INA"];
    }

    public function run(): void
    {
        try {
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
            Renderer::render("maintenance/catalog/categories/category", $this->viewData);
        } catch (\Exception $ex) {
            Site::redirectToWithMsg(LIST_URL, $ex->getMessage());
        }
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
                "Algo salió mal, intenta de nuevo.",
                "Attempt to load controller without the required query parameters MODE"
            );
        }

        $this->viewData["mode"] = $_GET["mode"];

        if (!isset($this->modes[$this->viewData["mode"]])) {
            $this->throwError(
                "Formulario cargado en modalidad invalida",
                "Attempt to load controller with wrong value on query parameter MODE - " . $this->viewData["mode"]
            );
        }

        if ($this->viewData["mode"] !== "INS") {
            if (!isset($_GET["categoryId"])) {
                $this->throwError(
                    "ID de categoría no proporcionado",
                    "Attempt to load controller without the required query parameters CATEGORYID"
                );
            }

            if (!is_numeric($_GET["categoryId"])) {
                $this->throwError(
                    "ID de categoría inválido",
                    "Attempt to load controller with wrong value on query parameter CATEGORYID - " . $_GET["categoryId"]
                );
            }

            $this->viewData["categoryId"] = intval($_GET["categoryId"]);
        }
    }

    private function getDataFromDB()
    {
        $categoryData = DaoCategories::getCategoryById($this->viewData["categoryId"]);

        if ($categoryData && count($categoryData) > 0) {
            $this->viewData["categoryName"] = $categoryData["categoryName"];
            $this->viewData["categoryDescription"] = $categoryData["categoryDescription"];
            $this->viewData["categoryStatus"] = $categoryData["categoryStatus"];
            
        } else {
            $this->throwError(
                "No se encontró la Categoría con ID: " . $this->viewData["categoryId"],
                "Record for categoryId " . $this->viewData["categoryId"] . " not found."
            );
        }
    }

    private function getBodyData()
    {
        if ($this->viewData["mode"] !== "INS" && !isset($_POST["categoryId"])) {
            $this->throwError(
                "Algo salió mal, intenta de nuevo.",
                "Trying to post without parameter CATEGORYID on body"
            );
        }

        if (!isset($_POST["token"])) {
            $this->throwError(
                "Algo salió mal, intenta de nuevo.",
                "Trying to post without parameter TOKEN on body"
            );
        }

        if (
            $this->viewData["mode"] !== "INS" &&
            intval($_POST["categoryId"]) !== $this->viewData["categoryId"]
        ) {
            $this->throwError(
                "Algo salió mal, intenta de nuevo.",
                "Inconsistent CATEGORYID value. Expected: " . $this->viewData["categoryId"] . ", Received: " . $_POST["categoryId"]
            );
        }

        if ($_POST["token"] !== $_SESSION[$this->name . "-xsstoken"]) {
            $this->throwError(
                "Algo salió mal, intenta de nuevo.",
                "Invalid XSSToken. Expected: " . $_SESSION[$this->name . "-xsstoken"] . ", Received: " . $_POST["token"]
            );
        }

        $this->viewData["category_xss_token"] = $_POST["token"];

        if ($this->viewData["mode"] !== "INS") {
            $this->viewData["categoryId"] = intval($_POST["categoryId"]);
        }

        if ($this->viewData["mode"] !== "DEL" && $this->viewData["mode"] !== "DSP") {
            $this->viewData["categoryName"] = $_POST["categoryName"] ?? "";
            $this->viewData["categoryDescription"] = $_POST["categoryDescription"] ?? "";
            $this->viewData["categoryStatus"] = $_POST["categoryStatus"] ?? "ACT";
        }
    }

    private function validateData(): bool
    {
        if (Validators::IsEmpty($this->viewData["categoryName"])) {
            $this->innerError("categoryName", "El nombre de la categoría es requerido");
        }

        if (Validators::IsEmpty($this->viewData["categoryDescription"])) {
            $this->innerError("categoryDescription", "La descripción de la categoría es requerida");
        }

        if (!in_array($this->viewData["categoryStatus"], $this->status)) {
            $this->innerError("categoryStatus", "El estado de la categoría es inválido");
        }

        return !(count($this->viewData["errors"]) > 0);
    }

    private function processData()
    {
        $mode = $this->viewData["mode"];
        switch ($mode) {
            case "INS":
                if (
                    DaoCategories::insertCategory(
                        $this->viewData["categoryName"],
                        $this->viewData["categoryDescription"],
                        $this->viewData["categoryStatus"]
                    ) > 0
                ) {
                    Site::redirectToWithMsg(LIST_URL, "Categoría creada exitosamente");
                } else {
                    $this->innerError("global", "Algo salió mal al guardar la nueva categoría.");
                }
                break;
            case "UPD":
                if (
                    DaoCategories::updateCategory(
                        $this->viewData["categoryId"],
                        $this->viewData["categoryName"],
                        $this->viewData["categoryDescription"],
                        $this->viewData["categoryStatus"]
                    ) > 0
                ) {
                    Site::redirectToWithMsg(LIST_URL, "Categoría actualizada exitosamente");
                } else {
                    $this->innerError("global", "Algo salió mal al actualizar la categoría.");
                }
                break;
            case "DEL":
                if (
                    DaoCategories::deleteCategory($this->viewData["categoryId"]) > 0
                ) {
                    Site::redirectToWithMsg(LIST_URL, "Categoría eliminada exitosamente");
                } else {
                    $this->innerError("global", "Algo salió mal al eliminar la categoría.");
                }
                break;
        }
    }

    private function prepareViewData()
    {
        $this->viewData["formTitle"] = sprintf(
            $this->modes[$this->viewData["mode"]],
            $this->viewData["categoryId"],
            $this->viewData["categoryName"]
        );

        $this->viewData["showCommitBtn"] = $this->viewData["mode"] !== "DSP";
        $this->viewData["readonly"] = ($this->viewData["mode"] === "DEL" || $this->viewData["mode"] === "DSP") ? "readonly" : "";

        $categoryStatusKey = "categoryStatus_" . strtolower($this->viewData["categoryStatus"]);
        $this->viewData[$categoryStatusKey] = "selected";

        if (count($this->viewData["errors"]) > 0) {
            foreach ($this->viewData["errors"] as $scope => $errorsArray) {
                $this->viewData[$scope . "_error"] = implode(", ", $errorsArray);
            }
        }

        $this->viewData["timestamp"] = time();
        $this->viewData["category_xss_token"] = hash("sha256", json_encode($this->viewData));
        $_SESSION[$this->name . "-xsstoken"] = $this->viewData["category_xss_token"];

        $this->viewData["category"] = [
            "categoryId" => $this->viewData["categoryId"],
            "categoryName" => $this->viewData["categoryName"],
            "categoryDescription" => $this->viewData["categoryDescription"],
            "categoryStatus" => $this->viewData["categoryStatus"],
            "mode" => $this->viewData["mode"],
            "category_xss_token" => $this->viewData["category_xss_token"],
            $categoryStatusKey => "selected"
        ];

        foreach ($this->viewData as $key => $value) {
            if (strpos($key, "_error") !== false) {
                $this->viewData["category"][$key] = $value;
            }
        }
    }
}