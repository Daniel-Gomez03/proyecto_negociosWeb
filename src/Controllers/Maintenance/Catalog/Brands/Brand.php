<?php
namespace Controllers\Maintenance\Catalog\Brands;

use Controllers\PrivateController;
use Views\Renderer;
use Dao\Maintenance\Catalog\Brands\Brands as DaoBrands;
use Utilities\Site;
use Utilities\Validators;

const LIST_URL = "index.php?page=Maintenance_Catalog_Brands_Brands";

class Brand extends PrivateController
{
    private array $viewData;
    private array $modes;
    private array $status;

    public function __construct()
    {
        $this->viewData = [
            "mode" => "",
            "brandId" => 0,
            "brandName" => "",
            "brandDescription" => "",
            "brandStatus" => "ACT",
            "formTitle" => "",
            "readonly" => "",
            "showCommitBtn" => true,
            "brand_xss_token" => "",
            "errors" => []
        ];

        $this->modes = [
            "DSP" => "Detalle de %s %s",
            "INS" => "Nueva Marca",
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
            Renderer::render("maintenance/catalog/brands/brand", $this->viewData);
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
            if (!isset($_GET["brandId"])) {
                $this->throwError(
                    "ID de marca no proporcionado",
                    "Attempt to load controller without the required query parameters BRANDID"
                );
            }

            if (!is_numeric($_GET["brandId"])) {
                $this->throwError(
                    "ID de marca inválido",
                    "Attempt to load controller with wrong value on query parameter BRANDID - " . $_GET["brandId"]
                );
            }

            $this->viewData["brandId"] = intval($_GET["brandId"]);
        }
    }

    private function getDataFromDB()
    {
        $brandData = DaoBrands::getBrandById($this->viewData["brandId"]);

        if ($brandData && count($brandData) > 0) {
            $this->viewData["brandName"] = $brandData["brandName"];
            $this->viewData["brandDescription"] = $brandData["brandDescription"];
            $this->viewData["brandStatus"] = $brandData["brandStatus"];
        } else {
            $this->throwError(
                "No se encontró la Marca con ID: " . $this->viewData["brandId"],
                "Record for brandId " . $this->viewData["brandId"] . " not found."
            );
        }
    }

    private function getBodyData()
    {
        if ($this->viewData["mode"] !== "INS" && !isset($_POST["brandId"])) {
            $this->throwError(
                "Algo salió mal, intenta de nuevo.",
                "Trying to post without parameter BRANDID on body"
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
            intval($_POST["brandId"]) !== $this->viewData["brandId"]
        ) {
            $this->throwError(
                "Algo salió mal, intenta de nuevo.",
                "Inconsistent BRANDID value. Expected: " . $this->viewData["brandId"] . ", Received: " . $_POST["brandId"]
            );
        }

        if ($_POST["token"] !== $_SESSION[$this->name . "-xsstoken"]) {
            $this->throwError(
                "Algo salió mal, intenta de nuevo.",
                "Invalid XSSToken. Expected: " . $_SESSION[$this->name . "-xsstoken"] . ", Received: " . $_POST["token"]
            );
        }

        $this->viewData["brand_xss_token"] = $_POST["token"];

        if ($this->viewData["mode"] !== "INS") {
            $this->viewData["brandId"] = intval($_POST["brandId"]);
        }

        if ($this->viewData["mode"] !== "DEL" && $this->viewData["mode"] !== "DSP") {
            $this->viewData["brandName"] = $_POST["brandName"] ?? "";
            $this->viewData["brandDescription"] = $_POST["brandDescription"] ?? "";
            $this->viewData["brandStatus"] = $_POST["brandStatus"] ?? "ACT";
        }
    }

    private function validateData(): bool
    {
        if (Validators::IsEmpty($this->viewData["brandName"])) {
            $this->innerError("brandName", "El nombre de la marca es requerido");
        }

        if (Validators::IsEmpty($this->viewData["brandDescription"])) {
            $this->innerError("brandDescription", "La descripción de la marca es requerida");
        }

        if (!in_array($this->viewData["brandStatus"], $this->status)) {
            $this->innerError("brandStatus", "El estado de la marca es inválido");
        }

        return !(count($this->viewData["errors"]) > 0);
    }

    private function processData()
    {
        $mode = $this->viewData["mode"];
        switch ($mode) {
            case "INS":
                if (
                    DaoBrands::insertBrand(
                        $this->viewData["brandName"],
                        $this->viewData["brandDescription"],
                        $this->viewData["brandStatus"]
                    ) > 0
                ) {
                    Site::redirectToWithMsg(LIST_URL, "Marca creada exitosamente");
                } else {
                    $this->innerError("global", "Algo salió mal al guardar la nueva marca.");
                }
                break;
            case "UPD":
                if (
                    DaoBrands::updateBrand(
                        $this->viewData["brandId"],
                        $this->viewData["brandName"],
                        $this->viewData["brandDescription"],
                        $this->viewData["brandStatus"]
                    ) > 0
                ) {
                    Site::redirectToWithMsg(LIST_URL, "Marca actualizada exitosamente");
                } else {
                    $this->innerError("global", "Algo salió mal al actualizar la marca.");
                }
                break;
            case "DEL":
                if (
                    DaoBrands::deleteBrand($this->viewData["brandId"]) > 0
                ) {
                    Site::redirectToWithMsg(LIST_URL, "Marca eliminada exitosamente");
                } else {
                    $this->innerError("global", "Algo salió mal al eliminar la marca.");
                }
                break;
        }
    }

    private function prepareViewData()
    {
        $this->viewData["formTitle"] = sprintf(
            $this->modes[$this->viewData["mode"]],
            $this->viewData["brandId"],
            $this->viewData["brandName"]
        );

        $this->viewData["showCommitBtn"] = $this->viewData["mode"] !== "DSP";
        $this->viewData["readonly"] = ($this->viewData["mode"] === "DEL" || $this->viewData["mode"] === "DSP") ? "readonly" : "";

        $brandStatusKey = "brandStatus_" . strtolower($this->viewData["brandStatus"]);
        $this->viewData[$brandStatusKey] = "selected";

        if (count($this->viewData["errors"]) > 0) {
            foreach ($this->viewData["errors"] as $scope => $errorsArray) {
                $this->viewData[$scope . "_error"] = implode(", ", $errorsArray);
            }
        }

        $this->viewData["timestamp"] = time();
        $this->viewData["brand_xss_token"] = hash("sha256", json_encode($this->viewData));
        $_SESSION[$this->name . "-xsstoken"] = $this->viewData["brand_xss_token"];

        $this->viewData["brand"] = [
            "brandId" => $this->viewData["brandId"],
            "brandName" => $this->viewData["brandName"],
            "brandDescription" => $this->viewData["brandDescription"],
            "brandStatus" => $this->viewData["brandStatus"],
            "mode" => $this->viewData["mode"],
            "brand_xss_token" => $this->viewData["brand_xss_token"],
            $brandStatusKey => "selected"
        ];

        foreach ($this->viewData as $key => $value) {
            if (strpos($key, "_error") !== false) {
                $this->viewData["brand"][$key] = $value;
            }
        }
    }
}