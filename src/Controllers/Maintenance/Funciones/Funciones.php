<?php
namespace Controllers\Maintenance\Funciones;

use Controllers\PrivateController;
use Controllers\PublicController;
use Utilities\Context;
use Utilities\Paging;
use Dao\Maintenance\Funciones\Funciones as FuncionesDAO;
use Views\Renderer;

class Funciones extends PublicController
{
    private $partialCode = "";
    private $status = "";
    private $type = "";
    private $orderBy = "";
    private $orderDescending = false;
    private $pageNumber = 1;
    private $itemsPerPage = 10;
    private $viewData = [];
    private $funciones = [];
    private $funcionesCount = 0;
    private $pages = 0;

    public function __construct()
    {
        parent::__construct();
    }
    public function run(): void
    {
        $this->getParamsFromContext();
        $this->getParams();
        $tmpFunciones = FuncionesDAO::getFunciones(
            $this->partialCode,
            $this->status,
            $this->type,
            $this->orderBy,
            $this->orderDescending,
            $this->pageNumber - 1,
            $this->itemsPerPage
        );
        $this->funciones = $tmpFunciones["funciones"];
        $this->funcionesCount = $tmpFunciones["total"];
        $this->pages = $this->funcionesCount > 0 ? ceil($this->funcionesCount / $this->itemsPerPage) : 1;
        if ($this->pageNumber > $this->pages) {
            $this->pageNumber = $this->pages;
        }
        $this->setParamsToContext();
        $this->setParamsToDataView();
        Renderer::render("maintenance/funciones/funciones", $this->viewData);
    }

    private function getParams(): void
    {
        $this->partialCode = $_GET["partialCode"] ?? $this->partialCode;
        $this->status = isset($_GET["status"]) && in_array($_GET["status"], ['ACT', 'INA', 'EMP']) ? $_GET["status"] : $this->status;
        if ($this->status === "EMP") {
            $this->status = "";
        }
        $this->type = isset($_GET["type"]) && in_array($_GET["type"], ['FUN', 'CRT', 'EMP']) ? $_GET["type"] : $this->type;
        if ($this->type === "EMP") {
            $this->type = "";
        }
        $this->orderBy = isset($_GET["orderBy"]) && in_array($_GET["orderBy"], ["fncod", "fndsc", "clear"]) ? $_GET["orderBy"] : $this->orderBy;
        if ($this->orderBy === "clear") {
            $this->orderBy = "";
        }
        $this->orderDescending = isset($_GET["orderDescending"]) ? boolval($_GET["orderDescending"]) : $this->orderDescending;
        $this->pageNumber = isset($_GET["pageNum"]) ? intval($_GET["pageNum"]) : $this->pageNumber;
        $this->itemsPerPage = isset($_GET["itemsPerPage"]) ? intval($_GET["itemsPerPage"]) : $this->itemsPerPage;
    }

    private function getParamsFromContext(): void
    {
        $this->partialCode = Context::getContextByKey("funciones_partialCode");
        $this->status = Context::getContextByKey("funciones_status");
        $this->type = Context::getContextByKey("funciones_type");
        $this->orderBy = Context::getContextByKey("funciones_orderBy");
        $this->orderDescending = boolval(Context::getContextByKey("funciones_orderDescending"));
        $this->pageNumber = intval(Context::getContextByKey("funciones_page"));
        $this->itemsPerPage = intval(Context::getContextByKey("funciones_itemsPerPage"));
        if ($this->pageNumber < 1)
            $this->pageNumber = 1;
        if ($this->itemsPerPage < 1)
            $this->itemsPerPage = 10;
    }

    private function setParamsToContext(): void
    {
        Context::setContext("funciones_partialCode", $this->partialCode, true);
        Context::setContext("funciones_status", $this->status, true);
        Context::setContext("funciones_type", $this->type, true);
        Context::setContext("funciones_orderBy", $this->orderBy, true);
        Context::setContext("funciones_orderDescending", $this->orderDescending, true);
        Context::setContext("funciones_page", $this->pageNumber, true);
        Context::setContext("funciones_itemsPerPage", $this->itemsPerPage, true);
    }

    private function setParamsToDataView(): void
    {
        $this->viewData["partialCode"] = $this->partialCode;
        $this->viewData["status"] = $this->status;
        $this->viewData["type"] = $this->type;
        $this->viewData["orderBy"] = $this->orderBy;
        $this->viewData["orderDescending"] = $this->orderDescending;
        $this->viewData["pageNum"] = $this->pageNumber;
        $this->viewData["itemsPerPage"] = $this->itemsPerPage;
        $this->viewData["funcionesCount"] = $this->funcionesCount;
        $this->viewData["pages"] = $this->pages;
        $this->viewData["funciones"] = $this->funciones;

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

        $typeKey = "type_" . ($this->type === "" ? "EMP" : $this->type);
        $this->viewData[$typeKey] = "selected";

        $pagination = Paging::getPagination(
            $this->funcionesCount,
            $this->itemsPerPage,
            $this->pageNumber,
            "index.php?page=Maintenance_Funciones_Funciones",
            "Maintenance_Funciones_Funciones"
        );
        $this->viewData["pagination"] = $pagination;
    }
}