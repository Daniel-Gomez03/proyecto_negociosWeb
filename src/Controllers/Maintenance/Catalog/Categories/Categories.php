<?php
namespace Controllers\Maintenance\Catalog\Categories;

use Controllers\PublicController;
use Utilities\Context;
use Utilities\Paging;
use Dao\Maintenance\Catalog\Categories\Categories as DaoCategories;
use Views\Renderer;

class Categories extends PublicController
{
    private $partialName = "";
    private $status = "";
    private $orderBy = "";
    private $orderDescending = false;
    private $pageNumber = 1;
    private $itemsPerPage = 10;
    private $viewData = [];
    private $categories = [];
    private $categoriesCount = 0;
    private $pages = 0;

    public function run(): void
    {
        $this->getParamsFromContext();
        $this->getParams();
        $tmpCategories = DaoCategories::getCategories(
            $this->partialName,
            $this->status,
            $this->orderBy,
            $this->orderDescending,
            $this->pageNumber - 1,
            $this->itemsPerPage
        );
        $this->categories = $tmpCategories["categories"];
        $this->categoriesCount = $tmpCategories["total"];
        $this->pages = $this->categoriesCount > 0 ? ceil($this->categoriesCount / $this->itemsPerPage) : 1;
        if ($this->pageNumber > $this->pages) {
            $this->pageNumber = $this->pages;
        }
        $this->setParamsToContext();
        $this->setParamsToDataView();
        Renderer::render("maintenance/catalog/categories/categories", $this->viewData);
    }

    private function getParams(): void
    {
        $this->partialName = $_GET["partialName"] ?? $this->partialName;
        $this->status = isset($_GET["status"]) && in_array($_GET["status"], ['ACT', 'INA', 'EMP']) ? $_GET["status"] : $this->status;
        if ($this->status === "EMP") {
            $this->status = "";
        }
        $this->orderBy = isset($_GET["orderBy"]) && in_array($_GET["orderBy"], ["categoryId", "categoryName", "clear"]) ? $_GET["orderBy"] : $this->orderBy;
        if ($this->orderBy === "clear") {
            $this->orderBy = "";
        }
        $this->orderDescending = isset($_GET["orderDescending"]) ? boolval($_GET["orderDescending"]) : $this->orderDescending;
        $this->pageNumber = isset($_GET["pageNum"]) ? intval($_GET["pageNum"]) : $this->pageNumber;
        $this->itemsPerPage = isset($_GET["itemsPerPage"]) ? intval($_GET["itemsPerPage"]) : $this->itemsPerPage;
    }

    private function getParamsFromContext(): void
    {
        $this->partialName = Context::getContextByKey("categories_partialName");
        $this->status = Context::getContextByKey("categories_status");
        $this->orderBy = Context::getContextByKey("categories_orderBy");
        $this->orderDescending = boolval(Context::getContextByKey("categories_orderDescending"));
        $this->pageNumber = intval(Context::getContextByKey("categories_page"));
        $this->itemsPerPage = intval(Context::getContextByKey("categories_itemsPerPage"));
        if ($this->pageNumber < 1) $this->pageNumber = 1;
        if ($this->itemsPerPage < 1) $this->itemsPerPage = 10;
    }

    private function setParamsToContext(): void
    {
        Context::setContext("categories_partialName", $this->partialName, true);
        Context::setContext("categories_status", $this->status, true);
        Context::setContext("categories_orderBy", $this->orderBy, true);
        Context::setContext("categories_orderDescending", $this->orderDescending, true);
        Context::setContext("categories_page", $this->pageNumber, true);
        Context::setContext("categories_itemsPerPage", $this->itemsPerPage, true);
    }

    private function setParamsToDataView(): void
    {
        $this->viewData["partialName"] = $this->partialName;
        $this->viewData["status"] = $this->status;
        $this->viewData["orderBy"] = $this->orderBy;
        $this->viewData["orderDescending"] = $this->orderDescending;
        $this->viewData["pageNum"] = $this->pageNumber;
        $this->viewData["itemsPerPage"] = $this->itemsPerPage;
        $this->viewData["categoriesCount"] = $this->categoriesCount;
        $this->viewData["pages"] = $this->pages;
        $this->viewData["categories"] = $this->categories;

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
            $this->categoriesCount,
            $this->itemsPerPage,
            $this->pageNumber,
            "index.php?page=Maintenance_Catalog_Categories_Categories",
            "Maintenance_Catalog_Categories_Categories"
        );
        $this->viewData["pagination"] = $pagination;
    }
}