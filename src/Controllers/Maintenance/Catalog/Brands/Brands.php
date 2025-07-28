<?php
namespace Controllers\Maintenance\Catalog\Brands;

use Controllers\PublicController;
use Utilities\Context;
use Utilities\Paging;
use Dao\Maintenance\Catalog\Brands\Brands as DaoBrands;
use Views\Renderer;

class Brands extends PublicController
{
    private $partialName = "";
    private $status = "";
    private $orderBy = "";
    private $orderDescending = false;
    private $pageNumber = 1;
    private $itemsPerPage = 10;
    private $viewData = [];
    private $brands = [];
    private $brandsCount = 0;
    private $pages = 0;

    public function run(): void
    {
        $this->getParamsFromContext();
        $this->getParams();
        $tmpBrands = DaoBrands::getBrands(
            $this->partialName,
            $this->status,
            $this->orderBy,
            $this->orderDescending,
            $this->pageNumber - 1,
            $this->itemsPerPage
        );
        $this->brands = $tmpBrands["brands"];
        $this->brandsCount = $tmpBrands["total"];
        $this->pages = $this->brandsCount > 0 ? ceil($this->brandsCount / $this->itemsPerPage) : 1;
        if ($this->pageNumber > $this->pages) {
            $this->pageNumber = $this->pages;
        }
        $this->setParamsToContext();
        $this->setParamsToDataView();
        Renderer::render("maintenance/catalog/brands/brands", $this->viewData);
    }

    private function getParams(): void
    {
        $this->partialName = $_GET["partialName"] ?? $this->partialName;
        $this->status = isset($_GET["status"]) && in_array($_GET["status"], ['ACT', 'INA', 'EMP']) ? $_GET["status"] : $this->status;
        if ($this->status === "EMP") {
            $this->status = "";
        }
        $this->orderBy = isset($_GET["orderBy"]) && in_array($_GET["orderBy"], ["brandId", "brandName", "clear"]) ? $_GET["orderBy"] : $this->orderBy;
        if ($this->orderBy === "clear") {
            $this->orderBy = "";
        }
        $this->orderDescending = isset($_GET["orderDescending"]) ? boolval($_GET["orderDescending"]) : $this->orderDescending;
        $this->pageNumber = isset($_GET["pageNum"]) ? intval($_GET["pageNum"]) : $this->pageNumber;
        $this->itemsPerPage = isset($_GET["itemsPerPage"]) ? intval($_GET["itemsPerPage"]) : $this->itemsPerPage;
    }

    private function getParamsFromContext(): void
    {
        $this->partialName = Context::getContextByKey("brands_partialName");
        $this->status = Context::getContextByKey("brands_status");
        $this->orderBy = Context::getContextByKey("brands_orderBy");
        $this->orderDescending = boolval(Context::getContextByKey("brands_orderDescending"));
        $this->pageNumber = intval(Context::getContextByKey("brands_page"));
        $this->itemsPerPage = intval(Context::getContextByKey("brands_itemsPerPage"));
        if ($this->pageNumber < 1) $this->pageNumber = 1;
        if ($this->itemsPerPage < 1) $this->itemsPerPage = 10;
    }

    private function setParamsToContext(): void
    {
        Context::setContext("brands_partialName", $this->partialName, true);
        Context::setContext("brands_status", $this->status, true);
        Context::setContext("brands_orderBy", $this->orderBy, true);
        Context::setContext("brands_orderDescending", $this->orderDescending, true);
        Context::setContext("brands_page", $this->pageNumber, true);
        Context::setContext("brands_itemsPerPage", $this->itemsPerPage, true);
    }

    private function setParamsToDataView(): void
    {
        $this->viewData["partialName"] = $this->partialName;
        $this->viewData["status"] = $this->status;
        $this->viewData["orderBy"] = $this->orderBy;
        $this->viewData["orderDescending"] = $this->orderDescending;
        $this->viewData["pageNum"] = $this->pageNumber;
        $this->viewData["itemsPerPage"] = $this->itemsPerPage;
        $this->viewData["brandsCount"] = $this->brandsCount;
        $this->viewData["pages"] = $this->pages;
        $this->viewData["brands"] = $this->brands;

        if ($this->orderBy !== "") {
            $orderByKey = "Order" . ucfirst($this->orderBy);
            $orderByKeyNoOrder = "OrderBy" . ucfirst($this->orderBy);
            $this->viewData[$orderByKeyNoOrder] = true;
            if ($this->orderDescending) {
                $orderByKey .= "Desc";
            }
            $this->viewData[$orderByKey] = true;
        }

        $statusKey = "status_" . ($this->status === "" ? "EMP" : $this->status);
        $this->viewData[$statusKey] = "selected";

        $pagination = Paging::getPagination(
            $this->brandsCount,
            $this->itemsPerPage,
            $this->pageNumber,
            "index.php?page=Maintenance_Catalog_Brands_Brands",
            "Maintenance_Catalog_Brands_Brands"
        );
        $this->viewData["pagination"] = $pagination;
    }
}