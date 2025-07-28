<?php
namespace Controllers\Maintenance\Catalog\Products;

use Controllers\PublicController;
use Views\Renderer;
use Dao\Maintenance\Catalog\Products\Products as ProductsDao;
use Dao\Maintenance\Catalog\Brands\Brands as BrandsDao;
use Dao\Maintenance\Catalog\Categories\Categories as CategoriesDao;
use Utilities\Site;
use Utilities\Validators;

const LIST_URL = "index.php?page=Maintenance_Catalog_Products_Products";

class Product extends PublicController
{
    private array $viewData;
    private array $modes;
    private array $status;
    private array $productTypes;

    public function __construct()
    {
        $this->viewData = [
            "mode" => "",
            "productId" => 0,
            "productName" => "",
            "productPrice" => 0,
            "productStock" => 0,
            "productBrandId" => 0,
            "productCategoryId" => 0,
            "productDescription" => "",
            "productImgUrl" => "",
            "productStatus" => "ACT",
            "productType" => "",
            // Campos específicos para cada tipo
            "gunplaGrade" => "",
            "gunplaScale" => "",
            "gunplaPremiumBandai" => false,
            "gunplaGundamBase" => false,
            "legoLine" => "",
            "legoSetNumber" => "",
            "legoPieceCount" => 0,
            "blokeesVersion" => "",
            "blokeesSize" => "",
            // Datos adicionales
            "brands" => [],
            "categories" => [],
            "formTitle" => "",
            "readonly" => "",
            "showCommitBtn" => true,
            "product_xss_token" => "",
            "errors" => []
        ];

        $this->modes = [
            "DSP" => "Detalle de %s %s",
            "INS" => "Nuevo Producto",
            "UPD" => "Editar %s %s",
            "DEL" => "Eliminar %s %s"
        ];

        $this->status = ["ACT", "INA"];
        $this->productTypes = ["gunpla", "lego", "blokees"];
    }

    public function run(): void
    {
        try {
            $this->loadBrandsAndCategories();
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
            Renderer::render("maintenance/catalog/products/product", $this->viewData);
        } catch (\Exception $ex) {
            Site::redirectToWithMsg(LIST_URL, $ex->getMessage());
        }
    }

    private function loadBrandsAndCategories()
    {
        $this->viewData["brands"] = BrandsDao::getBrands();
        $this->viewData["categories"] = CategoriesDao::getCategories();
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
            if (!isset($_GET["productId"])) {
                $this->throwError(
                    "ID de producto no proporcionado",
                    "Attempt to load controller without the required query parameters PRODUCTID"
                );
            }

            if (!is_numeric($_GET["productId"])) {
                $this->throwError(
                    "ID de producto inválido",
                    "Attempt to load controller with wrong value on query parameter PRODUCTID - " . $_GET["productId"]
                );
            }

            $this->viewData["productId"] = intval($_GET["productId"]);
        }
    }

    private function getDataFromDB()
    {
        $productData = ProductsDao::getProductById($this->viewData["productId"]);

        if ($productData && count($productData) > 0) {
            // Datos básicos del producto
            $this->viewData["productName"] = $productData["productName"];
            $this->viewData["productPrice"] = $productData["productPrice"];
            $this->viewData["productStock"] = $productData["productStock"];
            $this->viewData["productBrandId"] = $productData["productBrandId"];
            $this->viewData["productCategoryId"] = $productData["productCategoryId"];
            $this->viewData["productDescription"] = $productData["productDescription"];
            $this->viewData["productImgUrl"] = $productData["productImgUrl"];
            $this->viewData["productStatus"] = $productData["productStatus"];
            $this->viewData["productType"] = $productData["productType"] ?? "";

            // Datos específicos según el tipo de producto
            if ($productData["productType"] === "gunpla") {
                $this->viewData["gunplaGrade"] = $productData["gunplaGrade"];
                $this->viewData["gunplaScale"] = $productData["gunplaScale"];
                $this->viewData["gunplaPremiumBandai"] = (bool) $productData["gunplaPremiumBandai"];
                $this->viewData["gunplaGundamBase"] = (bool) $productData["gunplaGundamBase"];
            } elseif ($productData["productType"] === "lego") {
                $this->viewData["legoLine"] = $productData["legoLine"];
                $this->viewData["legoSetNumber"] = $productData["legoSetNumber"];
                $this->viewData["legoPieceCount"] = $productData["legoPieceCount"];
            } elseif ($productData["productType"] === "blokees") {
                $this->viewData["blokeesVersion"] = $productData["blokeesVersion"];
                $this->viewData["blokeesSize"] = $productData["blokeesSize"];
            }
        } else {
            $this->throwError(
                "No se encontró el Producto con ID: " . $this->viewData["productId"],
                "Record for productId " . $this->viewData["productId"] . " not found."
            );
        }
    }

    private function getBodyData()
    {
        // Validación del productId (requerido en todos los modos excepto INS)
        if ($this->viewData["mode"] !== "INS" && !isset($_POST["productId"])) {
            $this->throwError(
                "Algo salió mal, intenta de nuevo.",
                "Trying to post without parameter PRODUCTID on body"
            );
        }

        // Validación del token XSS (siempre requerido)
        if (!isset($_POST["token"])) {
            $this->throwError(
                "Algo salió mal, intenta de nuevo.",
                "Trying to post without parameter TOKEN on body"
            );
        }

        // Validación de consistencia del productId (excepto en INS)
        if (
            $this->viewData["mode"] !== "INS" &&
            intval($_POST["productId"]) !== $this->viewData["productId"]
        ) {
            $this->throwError(
                "Algo salió mal, intenta de nuevo.",
                "Inconsistent PRODUCTID value. Expected: " . $this->viewData["productId"] . ", Received: " . $_POST["productId"]
            );
        }

        // Validación del token
        if ($_POST["token"] !== $_SESSION[$this->name . "-xsstoken"]) {
            $this->throwError(
                "Algo salió mal, intenta de nuevo.",
                "Invalid XSSToken. Expected: " . $_SESSION[$this->name . "-xsstoken"] . ", Received: " . $_POST["token"]
            );
        }

        // Asignación básica para todos los modos
        $this->viewData["product_xss_token"] = $_POST["token"];

        // Solo asignar productId si no es INS
        if ($this->viewData["mode"] !== "INS") {
            $this->viewData["productId"] = intval($_POST["productId"]);
        }

        // Validación y asignación de campos específicos (no requeridos en DEL/DSP)
        if ($this->viewData["mode"] !== "DEL" && $this->viewData["mode"] !== "DSP") {
            // Datos básicos
            $this->viewData["productName"] = $_POST["productName"] ?? "";
            $this->viewData["productPrice"] = floatval($_POST["productPrice"] ?? 0);
            $this->viewData["productStock"] = intval($_POST["productStock"] ?? 0);
            $this->viewData["productBrandId"] = intval($_POST["productBrandId"] ?? 0);
            $this->viewData["productCategoryId"] = intval($_POST["productCategoryId"] ?? 0);
            $this->viewData["productDescription"] = $_POST["productDescription"] ?? "";
            $this->viewData["productImgUrl"] = $_POST["productImgUrl"] ?? "";
            $this->viewData["productStatus"] = $_POST["productStatus"] ?? "ACT";
            $this->viewData["productType"] = $_POST["productType"] ?? "";

            // Datos específicos según el tipo de producto
            if ($this->viewData["productType"] === "gunpla") {
                $this->viewData["gunplaGrade"] = $_POST["gunplaGrade"] ?? "";
                $this->viewData["gunplaScale"] = $_POST["gunplaScale"] ?? "";
                $this->viewData["gunplaPremiumBandai"] = isset($_POST["gunplaPremiumBandai"]);
                $this->viewData["gunplaGundamBase"] = isset($_POST["gunplaGundamBase"]);
            } elseif ($this->viewData["productType"] === "lego") {
                $this->viewData["legoLine"] = $_POST["legoLine"] ?? "";
                $this->viewData["legoSetNumber"] = $_POST["legoSetNumber"] ?? "";
                $this->viewData["legoPieceCount"] = intval($_POST["legoPieceCount"] ?? 0);
            } elseif ($this->viewData["productType"] === "blokees") {
                $this->viewData["blokeesVersion"] = $_POST["blokeesVersion"] ?? "";
                $this->viewData["blokeesSize"] = $_POST["blokeesSize"] ?? "";
            }
        }
    }

    private function validateData(): bool
    {
        if (Validators::IsEmpty($this->viewData["productName"])) {
            $this->innerError("productName", "El nombre del producto es requerido");
        }

        if ($this->viewData["productPrice"] <= 0) {
            $this->innerError("productPrice", "El precio del producto es requerido y debe ser un valor mayor a cero");
        }

        if ($this->viewData["productStock"] < 0) {
            $this->innerError("productStock", "El stock no puede ser negativo");
        }

        if ($this->viewData["productBrandId"] <= 0) {
            $this->innerError("productBrandId", "Debe seleccionar una marca");
        }

        if ($this->viewData["productCategoryId"] <= 0) {
            $this->innerError("productCategoryId", "Debe seleccionar una categoría");
        }

        if (Validators::IsEmpty($this->viewData["productDescription"])) {
            $this->innerError("productDescription", "La descripción del producto es requerida");
        }

        if (Validators::IsEmpty($this->viewData["productImgUrl"])) {
            $this->innerError("productImgUrl", "La imagen del producto es requerida");
        }

        if (!in_array($this->viewData["productStatus"], $this->status)) {
            $this->innerError("productStatus", "El estado del producto es invalido");
        }

        
        if (!in_array($this->viewData["productType"], $this->productTypes)) {
            $this->innerError("productType", "Debe seleccionar un tipo de producto válido");
        }
        // Validaciones específicas por tipo de producto
        if ($this->viewData["productType"] === "gunpla") {
            if (Validators::IsEmpty($this->viewData["gunplaGrade"])) {
                $this->innerError("gunplaGrade", "El grado del Gunpla es requerido");
            }
            if (Validators::IsEmpty($this->viewData["gunplaScale"])) {
                $this->innerError("gunplaScale", "La escala del Gunpla es requerida");
            }
        } elseif ($this->viewData["productType"] === "lego") {
            if (Validators::IsEmpty($this->viewData["legoLine"])) {
                $this->innerError("legoLine", "La línea de LEGO es requerida");
            }
            if (Validators::IsEmpty($this->viewData["legoSetNumber"])) {
                $this->innerError("legoSetNumber", "El número de set de LEGO es requerido");
            }
            if ($this->viewData["legoPieceCount"] <= 0) {
                $this->innerError("legoPieceCount", "El número de piezas debe ser mayor a cero");
            }
        } elseif ($this->viewData["productType"] === "blokees") {
            if (Validators::IsEmpty($this->viewData["blokeesVersion"])) {
                $this->innerError("blokeesVersion", "La versión de Blokees es requerida");
            }
            if (Validators::IsEmpty($this->viewData["blokeesSize"])) {
                $this->innerError("blokeesSize", "El tamaño de Blokees es requerido");
            }
        }


        return !(count($this->viewData["errors"]) > 0);
    }

    private function processData()
    {
        $mode = $this->viewData["mode"];
        switch ($mode) {
            case "INS":
                $productId = ProductsDao::insertProduct(
                    $this->viewData["productName"],
                    $this->viewData["productPrice"],
                    $this->viewData["productStock"],
                    $this->viewData["productBrandId"],
                    $this->viewData["productCategoryId"],
                    $this->viewData["productDescription"],
                    $this->viewData["productImgUrl"],
                    $this->viewData["productStatus"]
                );

                if ($productId > 0) {
                    // Insertar detalles específicos según el tipo
                    $this->insertProductSpecifics($productId);
                    Site::redirectToWithMsg(LIST_URL, "Producto creado exitosamente");
                } else {
                    $this->innerError("global", "Algo salió mal al guardar el nuevo producto.");
                }
                break;
            case "UPD":
                $result = ProductsDao::updateProduct(
                    $this->viewData["productId"],
                    $this->viewData["productName"],
                    $this->viewData["productPrice"],
                    $this->viewData["productStock"],
                    $this->viewData["productBrandId"],
                    $this->viewData["productCategoryId"],
                    $this->viewData["productDescription"],
                    $this->viewData["productImgUrl"],
                    $this->viewData["productStatus"]
                );

                if ($result > 0) {
                    // Actualizar detalles específicos según el tipo
                    $this->updateProductSpecifics();
                    Site::redirectToWithMsg(LIST_URL, "Producto actualizado exitosamente");
                } else {
                    $this->innerError("global", "Algo salió mal al actualizar el producto.");
                }
                break;
            case "DEL":
                if (ProductsDao::deleteProduct($this->viewData["productId"]) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Producto eliminado exitosamente");
                } else {
                    $this->innerError("global", "Algo salió mal al eliminar el producto.");
                }
                break;
        }
    }

    private function insertProductSpecifics(int $productId)
    {
        switch ($this->viewData["productType"]) {
            case "gunpla":
                ProductsDao::insertGunplaProduct(
                    $productId,
                    $this->viewData["gunplaGrade"],
                    $this->viewData["gunplaScale"],
                    $this->viewData["gunplaPremiumBandai"],
                    $this->viewData["gunplaGundamBase"]
                );
                break;
            case "lego":
                ProductsDao::insertLegoProduct(
                    $productId,
                    $this->viewData["legoLine"],
                    $this->viewData["legoSetNumber"],
                    $this->viewData["legoPieceCount"]
                );
                break;
            case "blokees":
                ProductsDao::insertBlokeesProduct(
                    $productId,
                    $this->viewData["blokeesVersion"],
                    $this->viewData["blokeesSize"]
                );
                break;
        }
    }

    private function updateProductSpecifics()
    {
        switch ($this->viewData["productType"]) {
            case "gunpla":
                ProductsDao::updateGunplaProduct(
                    $this->viewData["productId"],
                    $this->viewData["gunplaGrade"],
                    $this->viewData["gunplaScale"],
                    $this->viewData["gunplaPremiumBandai"],
                    $this->viewData["gunplaGundamBase"]
                );
                break;
            case "lego":
                ProductsDao::updateLegoProduct(
                    $this->viewData["productId"],
                    $this->viewData["legoLine"],
                    $this->viewData["legoSetNumber"],
                    $this->viewData["legoPieceCount"]
                );
                break;
            case "blokees":
                ProductsDao::updateBlokeesProduct(
                    $this->viewData["productId"],
                    $this->viewData["blokeesVersion"],
                    $this->viewData["blokeesSize"]
                );
                break;
        }
    }

    private function prepareViewData()
    {
        $this->viewData["formTitle"] = sprintf(
            $this->modes[$this->viewData["mode"]],
            $this->viewData["productId"],
            $this->viewData["productName"]
        );

        $this->viewData["showCommitBtn"] = $this->viewData["mode"] !== "DSP";
        $this->viewData["readonly"] = ($this->viewData["mode"] === "DEL" || $this->viewData["mode"] === "DSP") ? "readonly" : "";

        // Para los selects
        $productStatusKey = "productStatus_" . strtolower($this->viewData["productStatus"]);
        $this->viewData[$productStatusKey] = "selected";

        // Cargar marcas y categorías
        $this->viewData["brands"] = BrandsDao::getActiveBrands();
        $this->viewData["categories"] = CategoriesDao::getActiveCategories();

        // AGREGAR: Marcar la marca y categoría seleccionadas
        if (is_array($this->viewData["brands"])) {
            foreach ($this->viewData["brands"] as &$brand) {
                $brand["selected"] = ($brand["value"] == $this->viewData["productBrandId"]) ? "selected" : "";
            }
        }

        if (is_array($this->viewData["categories"])) {
            foreach ($this->viewData["categories"] as &$category) {
                $category["selected"] = ($category["value"] == $this->viewData["productCategoryId"]) ? "selected" : "";
            }
        }

        // Manejo de errores
        if (count($this->viewData["errors"]) > 0) {
            foreach ($this->viewData["errors"] as $scope => $errorsArray) {
                $this->viewData[$scope . "_error"] = implode(", ", $errorsArray);
            }
        }

        // Generar token XSS
        $this->viewData["timestamp"] = time();
        $this->viewData["product_xss_token"] = hash("sha256", json_encode($this->viewData));
        $_SESSION[$this->name . "-xsstoken"] = $this->viewData["product_xss_token"];

        // Preparar datos para la vista
        $this->viewData["product"] = [
            "productId" => $this->viewData["productId"],
            "productName" => $this->viewData["productName"],
            "productPrice" => $this->viewData["productPrice"],
            "productStock" => $this->viewData["productStock"],
            "productBrandId" => $this->viewData["productBrandId"],
            "productCategoryId" => $this->viewData["productCategoryId"],
            "productDescription" => $this->viewData["productDescription"],
            "productImgUrl" => $this->viewData["productImgUrl"],
            "productStatus" => $this->viewData["productStatus"],
            "productType" => $this->viewData["productType"],
            // Campos específicos
            "gunplaGrade" => $this->viewData["gunplaGrade"],
            "gunplaScale" => $this->viewData["gunplaScale"],
            "gunplaPremiumBandai" => $this->viewData["gunplaPremiumBandai"],
            "gunplaGundamBase" => $this->viewData["gunplaGundamBase"],
            "legoLine" => $this->viewData["legoLine"],
            "legoSetNumber" => $this->viewData["legoSetNumber"],
            "legoPieceCount" => $this->viewData["legoPieceCount"],
            "blokeesVersion" => $this->viewData["blokeesVersion"],
            "blokeesSize" => $this->viewData["blokeesSize"],
            // Otros
            "mode" => $this->viewData["mode"],
            "product_xss_token" => $this->viewData["product_xss_token"],
            $productStatusKey => "selected"
        ];

        // Agregar errores al array product si existen
        foreach ($this->viewData as $key => $value) {
            if (strpos($key, "_error") !== false) {
                $this->viewData["product"][$key] = $value;
            }
        }

        // Agregar listas de marcas y categorías al producto
        $this->viewData["product"]["brands"] = $this->viewData["brands"];
        $this->viewData["product"]["categories"] = $this->viewData["categories"];
    }
}