<?php

namespace Controllers\Maintenance\Funciones;

use Controllers\PrivateController;
use Utilities\Context;
use Utilities\Paging;
use Dao\Maintenance\Funciones\Funciones as DaoFunciones;
use Views\Renderer;

class Funciones extends PrivateController {
    private $partialDescription = "";
    private $status = "";
    private $type = "";
    private $orderBy = "";
    private $orderDescending = false;
    private $pageNumber = 1;
    private $itemsPerPage = 10;
    private $viewData = [];

    public function run(): void {
        $this->getParamsFromContext();
        $this->getParams();
        
        $tmpFunciones = DaoFunciones::getFunciones(
            $this->partialDescription,
            $this->status,
            $this->type,
            $this->orderBy,
            $this->orderDescending,
            $this->pageNumber - 1,
            $this->itemsPerPage
        );

        $funciones = $tmpFunciones["funciones"];
        $funcionesCount = $tmpFunciones["total"];
        $pages = $funcionesCount > 0 ? ceil($funcionesCount / $this->itemsPerPage) : 1;
        
        if ($this->pageNumber > $pages) {
            $this->pageNumber = $pages;
        }

        $this->setParamsToContext();
        $this->setParamsToDataView($funciones, $funcionesCount, $pages);
        
        Renderer::render("maintenance/funciones/funciones", $this->viewData);
    }

    private function getParams(): void {
        $this->partialDescription = $_GET["partialDesc"] ?? $this->partialDescription;
        $this->status = (isset($_GET["status"]) && in_array($_GET["status"], ['ACT', 'INA', 'EMP'])) ? $_GET["status"] : $this->status;
        if ($this->status === "EMP") {
            $this->status = "";
        }
        $this->type = $_GET["type"] ?? $this->type;
        
        $validOrderBy = ["fncod", "fndsc", "fnest", "fntyp"];
        $orderByValue = $_GET["orderBy"] ?? $this->orderBy;
        if ($orderByValue === "clear") {
            $this->orderBy = "";
        } else if (in_array($orderByValue, $validOrderBy)) {
            $this->orderBy = $orderByValue;
        }

        $this->orderDescending = isset($_GET["orderDescending"]) ? boolval($_GET["orderDescending"]) : $this->orderDescending;
        $this->pageNumber = isset($_GET["pageNum"]) ? intval($_GET["pageNum"]) : $this->pageNumber;
        $this->itemsPerPage = isset($_GET["itemsPerPage"]) ? intval($_GET["itemsPerPage"]) : $this->itemsPerPage;
    }

    private function getParamsFromContext(): void {
        $this->partialDescription = Context::getContextByKey("funciones_partialDescription");
        $this->status = Context::getContextByKey("funciones_status");
        $this->type = Context::getContextByKey("funciones_type");
        $this->orderBy = Context::getContextByKey("funciones_orderBy");
        $this->orderDescending = boolval(Context::getContextByKey("funciones_orderDescending"));
        $this->pageNumber = intval(Context::getContextByKey("funciones_page"));
        $this->itemsPerPage = intval(Context::getContextByKey("funciones_itemsPerPage"));
        if ($this->pageNumber < 1) $this->pageNumber = 1;
        if ($this->itemsPerPage < 1) $this->itemsPerPage = 10;
    }

    private function setParamsToContext(): void {
        Context::setContext("funciones_partialDescription", $this->partialDescription, true);
        Context::setContext("funciones_status", $this->status, true);
        Context::setContext("funciones_type", $this->type, true);
        Context::setContext("funciones_orderBy", $this->orderBy, true);
        Context::setContext("funciones_orderDescending", $this->orderDescending, true);
        Context::setContext("funciones_page", $this->pageNumber, true);
        Context::setContext("funciones_itemsPerPage", $this->itemsPerPage, true);
    }

    private function setParamsToDataView(array $funciones, int $funcionesCount, int $pages): void {
        $this->viewData["partialDesc"] = $this->partialDescription;
        $this->viewData["status"] = $this->status;
        $this->viewData["type"] = $this->type;
        $this->viewData["orderBy"] = $this->orderBy;
        $this->viewData["orderDescending"] = $this->orderDescending;
        $this->viewData["pageNum"] = $this->pageNumber;
        $this->viewData["itemsPerPage"] = $this->itemsPerPage;
        $this->viewData["funcionesCount"] = $funcionesCount;
        $this->viewData["pages"] = $pages;
        $this->viewData["funciones"] = $funciones;

        if ($this->orderBy !== "") {
            $orderByKey = "Order" . ucfirst($this->orderBy);
            if ($this->orderDescending) {
                $orderByKey .= "Desc";
            }
            $this->viewData[$orderByKey] = true;
        }

        $statusKey = "status_" . ($this->status === "" ? "EMP" : $this->status);
        $this->viewData[$statusKey] = "selected";
        
        $pagination = Paging::getPagination(
            $funcionesCount,
            $this->itemsPerPage,
            $this->pageNumber,
            "index.php?page=Maintenance_Funciones_Funciones",
            "Maintenance_Funciones_Funciones"
        );
        $this->viewData["pagination"] = $pagination;

        $this->viewData["OrderFncod"] = $this->orderBy === "fncod" && !$this->orderDescending;
        $this->viewData["OrderFncodDesc"] = $this->orderBy === "fncod" && $this->orderDescending;
        $this->viewData["OrderByFncod"] = $this->orderBy !== "fncod";
        
        $this->viewData["OrderFndsc"] = $this->orderBy === "fndsc" && !$this->orderDescending;
        $this->viewData["OrderFndscDesc"] = $this->orderBy === "fndsc" && $this->orderDescending;
        $this->viewData["OrderByFndsc"] = $this->orderBy !== "fndsc";
        
        // Agregado para el tipo de función (fntyp)
        $this->viewData["OrderFntyp"] = $this->orderBy === "fntyp" && !$this->orderDescending;
        $this->viewData["OrderFntypDesc"] = $this->orderBy === "fntyp" && $this->orderDescending;
        $this->viewData["OrderByFntyp"] = $this->orderBy !== "fntyp";
    }
}