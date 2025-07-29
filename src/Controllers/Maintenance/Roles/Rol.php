<?php
    namespace Controllers\Maintenance\Roles;

    use Controllers\PublicController;
    use Views\Renderer;
    use Dao\Maintenance\Roles\Roles as RolesDao;
    use Utilities\Site;
    use Utilities\Validators;

    class Rol extends PublicController {
        private $viewData = [];
        private $mode = "DSP";
        private $modeDescriptions = [
            "DSP" => "Detalle del rol %s - %s",
            "INS" => "Nuevo Rol",
            "UPD" => "Editar rol %s - %s",
            "DEL" => "Eliminar rol %s - %s"
        ];
        private $readonly = "";
        private $showCommitBtn = true;
        private $role = [
            "rolescod" => "",
            "rolesdsc" => "",
            "rolesest" => "ACT"
        ];
        private $role_xss_token = "";

        public function run(): void {
            try {
                $this->getData();
                if ($this->isPostBack()) {
                    if ($this->validateData()) {
                        $this->handlePostAction();
                    }
                }
                $this->setViewData();
                Renderer::render("maintenance/roles/rol", $this->viewData);
            } catch (\Exception $ex) {
                Site::redirectToWithMsg(
                    "index.php?page=Maintenance_Roles_Roles",
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
                    $this->role = RolesDao::getRoleById($_GET["id"]);
                    if (!$this->role) {
                        throw new \Exception("No se encontró el Rol", 1);
                    }
                }
            } else {
                throw new \Exception("Formulario cargado en modalidad inválida", 1);
            }
        }

        private function validateData() {
            $errors = [];
            $this->role_xss_token = $_POST["role_xss_token"] ?? "";
            $this->role["rolescod"] = trim($_POST["rolescod"] ?? "");
            $this->role["rolesdsc"] = trim($_POST["rolesdsc"] ?? "");
            $this->role["rolesest"] = trim($_POST["rolesest"] ?? "ACT");

            if (Validators::IsEmpty($this->role["rolescod"])) {
                $errors["rolescod_error"] = "El código del rol es requerido";
            }

            if (Validators::IsEmpty($this->role["rolesdsc"])) {
                $errors["rolesdsc_error"] = "La descripción es requerida";
            }

            if (!in_array($this->role["rolesest"], ["ACT", "INA"])) {
                $errors["rolesest_error"] = "El estado del rol es inválido";
            }

            if (count($errors) > 0) {
                foreach ($errors as $key => $value) {
                    $this->role[$key] = $value;
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
            $result = RolesDao::insertRole(
                $this->role["rolescod"],
                $this->role["rolesdsc"],
                $this->role["rolesest"]
            );
            if ($result > 0) {
                Site::redirectToWithMsg(
                    "index.php?page=Maintenance_Roles_Roles",
                    "Rol creado exitosamente"
                );
            }
        }

        private function handleUpdate() {
            $result = RolesDao::updateRole(
                $this->role["rolescod"],
                $this->role["rolesdsc"],
                $this->role["rolesest"]
            );
            if ($result > 0) {
                Site::redirectToWithMsg(
                    "index.php?page=Maintenance_Roles_Roles",
                    "Rol actualizado exitosamente"
                );
            }
        }

        private function handleDelete() {
            $result = RolesDao::deleteRole($this->role["rolescod"]);
            if ($result > 0) {
                Site::redirectToWithMsg(
                    "index.php?page=Maintenance_Roles_Roles",
                    "Rol eliminado exitosamente"
                );
            }
        }

        private function setViewData(): void {
            $this->viewData["mode"] = $this->mode;
            $this->viewData["role_xss_token"] = $this->role_xss_token;
            $this->viewData["FormTitle"] = sprintf(
                $this->modeDescriptions[$this->mode],
                $this->role["rolescod"],
                $this->role["rolesdsc"]
            );
            $this->viewData["showCommitBtn"] = $this->showCommitBtn;
            $this->viewData["readonly"] = $this->readonly;

            $statusKey = "rolesest_" . strtolower($this->role["rolesest"]);
            $this->role[$statusKey] = "selected";

            $this->viewData["role"] = $this->role;
        }
    }