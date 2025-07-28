<?php

namespace Controllers\Maintenance\Catalog\Products;

use Controllers\PublicController;
use Views\Renderer;
use Dao\Maintenance\Catalog\Products as ProductsDao;
use Utilities\Site;
use Utilities\Validators;

class Product extends PublicController
{
  private $viewData = [];
  private $mode = "DSP";
  private $modeDescriptions = [
    "DSP" => "Detalle de %s %s",
    "INS" => "Nuevo Producto",
    "UPD" => "Editar %s %s",
    "DEL" => "Eliminar %s %s"
  ];



  private $readonly = "";
  private $showCommitBtn = true;
  private $product = [
  "productId" => 0,
  "productName" => "",
  "productPrice" => 0.00,
  "productStock" => 0,
  "productBrandId" => 0,
  "productCategoryId" => 0,
  "productDescription" => "",
  "productImgUrl" => "",
  "productStatus" => "ACT"
];


  private $product_xss_token = "";

  public function run(): void
  {
    try {
      $this->getData();
      if ($this->isPostBack()) {
        if ($this->validateData()) {
          $this->handlePostAction();
        }
      }
      $this->setViewData();


      //esto tiene que ver con el view
      Renderer::render("products/product", $this->viewData);
    } catch (\Exception $ex) {
      Site::redirectToWithMsg(
        "index.php?page=Maintenance_Catalog_Products_Product",
        $ex->getMessage()
      );
    }
  }

  private function getData()
  {
    $this->mode = $_GET["mode"] ?? "NOF";
    if (isset($this->modeDescriptions[$this->mode])) {
      $this->readonly = $this->mode === "DEL" ? "readonly" : "";
      $this->showCommitBtn = $this->mode !== "DSP";
      if ($this->mode !== "INS") {
    if (!isset($_GET["productId"])) {
        throw new \Exception("Falta el ID del producto", 1);
    }

    $this->product = ProductsDao::getProductById(intval($_GET["productId"]));
    if (!$this->product) {
        throw new \Exception("No se encontró el Producto", 1);
    }

}}
  }




 private function validateData()
{
  $this->product_xss_token = $_POST["product_xss_token"] ?? "";
  $this->product["productId"] = intval($_POST["productId"] ?? "");

  if ($this->mode !== "DEL") {
    $errors = [];

    $this->product["productName"] = strval($_POST["productName"] ?? "");
    $this->product["productPrice"] = floatval($_POST["productPrice"] ?? "");
    $this->product["productStock"] = intval($_POST["productStock"] ?? 0);
    $this->product["productBrandId"] = intval($_POST["productBrandId"] ?? 0);
    $this->product["productCategoryId"] = intval($_POST["productCategoryId"] ?? 0);
    $this->product["productDescription"] = strval($_POST["productDescription"] ?? "");
    $this->product["productImgUrl"] = strval($_POST["productImgUrl"] ?? "");
    $this->product["productStatus"] = strval($_POST["productStatus"] ?? "");

    // Validaciones
    if (Validators::IsEmpty($this->product["productName"])) {
      $errors["productName_error"] = "El nombre del producto es requerido";
    }

    if (Validators::IsEmpty($this->product["productPrice"]) || $this->product["productPrice"] <= 0) {
      $errors["productPrice_error"] = "El precio debe ser mayor a cero";
    }

    if ($this->product["productStock"] < 0) {
      $errors["productStock_error"] = "El stock no puede ser negativo";
    }

    if ($this->product["productBrandId"] <= 0) {
      $errors["productBrandId_error"] = "Debe seleccionar una marca válida";
    }

    if ($this->product["productCategoryId"] <= 0) {
      $errors["productCategoryId_error"] = "Debe seleccionar una categoría válida";
    }

    if (Validators::IsEmpty($this->product["productDescription"])) {
      $errors["productDescription_error"] = "La descripción es requerida";
    }

    if (Validators::IsEmpty($this->product["productImgUrl"])) {
      $errors["productImgUrl_error"] = "Debe ingresar la URL de una imagen";
    }

    if (!in_array($this->product["productStatus"], ["ACT", "INA"])) {
      $errors["productStatus_error"] = "Estado inválido";
    }

    if (count($errors) > 0) {
      foreach ($errors as $key => $value) {
        $this->product[$key] = $value;
      }
      return false;
    }
  }

  return true;
}



  private function handlePostAction()
  {
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
        throw new \Exception("Modo invalido", 1);
        break;
    }
  }




  private function handleInsert()
{
  $result = ProductsDao::insertProduct(
    $this->product["productName"],
    $this->product["productPrice"],
    $this->product["productStock"],
    $this->product["productBrandId"],
    $this->product["productCategoryId"],
    $this->product["productDescription"],
    $this->product["productImgUrl"],
    $this->product["productStatus"]
  );
  //tengo que ver esto aun
  if ($result > 0) {
    Site::redirectToWithMsg(
      "index.php?page=Maintenance_Catalog_Products_Product",
      "Producto creado exitosamente"
    );
  }
}


  private function handleUpdate()
  {
    $result = ProductsDao::updateProduct(
    $this->product["productId"],
    $this->product["productName"],
    $this->product["productPrice"],
    $this->product["productStock"],
    $this->product["productBrandId"],
    $this->product["productCategoryId"],
    $this->product["productDescription"],
    $this->product["productImgUrl"],
    $this->product["productStatus"]
    );

    //tengo que ver esto aun
    if ($result > 0) {
      Site::redirectToWithMsg(
        "index.php?page=Maintenance_Catalog_Products_Product",
        "Producto actualizado exitosamente"
      );
    }
  }

  private function handleDelete()
  {
    $result = ProductsDao::deleteProduct($this->product["productId"]);
    //tengo que ver esto aun
    if ($result > 0) {
      Site::redirectToWithMsg(
        "index.php?page=Maintenance_Catalog_Products_Product",
        "Producto Eliminado exitosamente"
      );
    }
  }

  private function setViewData(): void
  {
    $this->viewData["mode"] = $this->mode;
    $this->viewData["product_xss_token"] = $this->product_xss_token;
    $this->viewData["FormTitle"] = sprintf(
      $this->modeDescriptions[$this->mode],
      $this->product["productId"],
      $this->product["productName"]
    );
    $this->viewData["showCommitBtn"] = $this->showCommitBtn;
    $this->viewData["readonly"] = $this->readonly;

    $productStatusKey = "productStatus_" . strtolower($this->product["productStatus"]);
    $this->product[$productStatusKey] = "selected";

     // Para marcar la marca seleccionada en el select
    $this->product["selectedBrand_" . $this->product["productBrandId"]] = "selected";

    // Para marcar la categoría seleccionada en el select
    $this->product["selectedCategory_" . $this->product["productCategoryId"]] = "selected";

    $this->viewData["product"] = $this->product;
  }
}
?>