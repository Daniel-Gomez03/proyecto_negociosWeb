<?php
namespace Controllers\Maintenance\Users;

use Controllers\PrivateController;
use Dao\Maintenance\Users\Users as UsersDAO;
use Dao\Security\Security;
use Views\Renderer;

use Utilities\Site;
use Utilities\Validators;

const LIST_URL = "index.php?page=Maintenance-Users-Users";

class User extends PrivateController
{
    private array $viewData;
    private array $modes;
    private array $status;
    private array $types;
    private array $passwordStatus;

    public function __construct()
    {
        parent::__construct();
        $this->viewData = [
            "mode" => "",
            "usercod" => 0,
            "useremail" => "",
            "username" => "",
            "userpswd" => "",
            "userfching" => "",
            "userpswdest" => "ACT",
            "userpswdexp" => "",
            "userest" => "ACT",
            "useractcod" => "",
            "userpswdchg" => "",
            "usertipo" => "PBL",
            "modeDsc" => "",
            "selectedUserACT" => "",
            "selectedUserINA" => "",
            "selectedUserBLQ" => "",
            "selectedPswdACT" => "",
            "selectedPswdINA" => "",
            "selectedPswdEXP" => "",
            "selectedPBL" => "",
            "selectedAUD" => "",
            "selectedADM" => "",
            "errors" => [],
            "cancelLabel" => "Cancel",
            "showConfirm" => true,
            "readonly" => "",
            "isChangePassword" => false,
            "current_password" => "",
            "new_password" => "",
            "confirm_password" => ""
        ];

        $this->modes = [
            "INS" => "Nuevo Usuario",
            "UPD" => "Actualizando %s",
            "DEL" => "Eliminando %s",
            "DSP" => "Detalles de %s",
            "CHGPWD" => "Cambiando Contraseña de %s"
        ];

        $this->status = ["ACT", "INA"];
        $this->passwordStatus = ["ACT", "INA", "EXP"];
        $this->types = ["PBL", "AUD", "ADM"];

    }

    public function run(): void
    {
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
        Renderer::render("maintenance/users/user", $this->viewData);
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
                "Algo salió mal, intente de nuevo.",
                "Intento de cargar el controlador sin el parámetro de consulta requerido MODE"
            );
        }
        $this->viewData["mode"] = $_GET["mode"];
        if (!isset($this->modes[$this->viewData["mode"]])) {
            $this->throwError(
                "Algo salió mal, intente de nuevo.",
                "Intento de cargar el controlador con un valor incorrecto en el parámetro de consulta MODE - " . $this->viewData["mode"]
            );
        }
        if ($this->viewData["mode"] !== "INS") {
            if (!isset($_GET["usercod"])) {
                $this->throwError(
                    "Algo salió mal, intente de nuevo.",
                    "Intento de cargar el controlador sin el parámetro de consulta requerido USERCOD"
                );
            }
            if (!is_numeric($_GET["usercod"])) {
                $this->throwError(
                    "Algo salió mal, intente de nuevo.",
                    "Intento de cargar el controlador con un valor incorrecto en el parámetro de consulta USERCOD - " . $_GET["usercod"]
                );
            }
            $this->viewData["usercod"] = intval($_GET["usercod"]);
        }

        $this->viewData["isChangePassword"] = $this->viewData["mode"] === "CHGPWD";
    }

    private function getDataFromDB()
    {
        $tmpUser = UsersDAO::getUsuarioById(
            $this->viewData["usercod"]
        );
        if ($tmpUser && count($tmpUser) > 0) {
            $this->viewData["useremail"] = $tmpUser["useremail"];
            $this->viewData["username"] = $tmpUser["username"];
            $this->viewData["userfching"] = $tmpUser["userfching"];
            $this->viewData["userpswdest"] = $tmpUser["userpswdest"];
            $this->viewData["userpswdexp"] = $tmpUser["userpswdexp"];
            $this->viewData["userest"] = $tmpUser["userest"];
            $this->viewData["useractcod"] = $tmpUser["useractcod"];
            $this->viewData["userpswdchg"] = $tmpUser["userpswdchg"];
            $this->viewData["usertipo"] = $tmpUser["usertipo"];
        } else {
            $this->throwError(
                "Algo salió mal, inténtalo de nuevo.",
                "Registro con id " . $this->viewData["usercod"] . " no encontrado."
            );
        }
    }

    private function getBodyData()
    {
        if ($this->viewData["mode"] !== "INS" && !isset($_POST["usercod"])) {
            $this->throwError(
                "Algo salió mal, inténtalo de nuevo.",
                "Intento de publicar sin el parámetro USERCOD en el cuerpo"
            );
        }

        if (!isset($_POST["xsrtoken"])) {
            $this->throwError(
                "Algo salió mal, inténtalo de nuevo.",
                "Intento de publicar sin el parámetro XSRTOKEN en el cuerpo"
            );
        }

        if ($_POST["xsrtoken"] !== $_SESSION[$this->name . "-xsrtoken"]) {
            $this->throwError(
                "Algo salió mal, inténtalo de nuevo.",
                "Intento de publicar con un parámetro XSRToken inconsistente"
            );
        }

        if ($this->viewData["mode"] === "CHGPWD") {
            $this->viewData["current_password"] = $_POST["current_password"] ?? "";
            $this->viewData["new_password"] = $_POST["new_password"] ?? "";
            $this->viewData["confirm_password"] = $_POST["confirm_password"] ?? "";
        } else {
            $this->viewData["useremail"] = $_POST["useremail"] ?? "";
            $this->viewData["username"] = $_POST["username"] ?? "";

            if ($this->viewData["mode"] === "INS") {
                $this->viewData["userpswd"] = $_POST["userpswd"] ?? "";
            }

            if ($this->viewData["mode"] !== "DEL" && $this->viewData["mode"] !== "DSP") {
                $this->viewData["userpswdest"] = $_POST["userpswdest"] ?? "ACT";
                $this->viewData["userpswdexp"] = $_POST["userpswdexp"] ?? "";
                $this->viewData["userest"] = $_POST["userest"] ?? "ACT";
                $this->viewData["usertipo"] = $_POST["usertipo"] ?? "PBL";
            }
        }

        if ($this->viewData["mode"] !== "INS") {
            $this->viewData["usercod"] = intval($_POST["usercod"]);
        }
    }

    private function validateData(): bool
    {
        if ($this->viewData["mode"] === "CHGPWD") {
            return $this->validatePasswordChange();
        }

        if (Validators::IsEmpty($this->viewData["useremail"])) {
            $this->innerError("useremail", "El email es requerido.");
        }
        if (!Validators::IsValidEmail($this->viewData["useremail"])) {
            $this->innerError("useremail", "El email no es válido.");
        }
        if (Validators::IsEmpty($this->viewData["username"])) {
            $this->innerError("username", "El nombre de usuario es requerido.");
        }
        if (strlen($this->viewData["username"]) > 80) {
            $this->innerError("username", "El nombre de usuario es demasiado largo. Máximo permitido 80 caracteres.");
        }

        if ($this->viewData["mode"] === "INS") {
            if (Validators::IsEmpty($this->viewData["userpswd"])) {
                $this->innerError("userpswd", "La contraseña es requerida.");
            }
            if (!Validators::IsValidPassword($this->viewData["userpswd"])) {
                $this->innerError("userpswd", "La contraseña debe tener al menos 8 caracteres, 1 número, 1 mayúscula y 1 carácter especial.");
            }
        }

        if (!in_array($this->viewData["userest"], $this->status)) {
            $this->innerError("userest", "Valor de estado de usuario no válido.");
        }
        if (!in_array($this->viewData["userpswdest"], $this->passwordStatus)) {
            $this->innerError("userpswdest", "Valor de estado de contraseña no válido.");
        }
        if (!in_array($this->viewData["usertipo"], $this->types)) {
            $this->innerError("usertipo", "Valor de tipo de usuario no válido.");
        }

        if (
            $this->viewData["mode"] === "INS" ||
            ($this->viewData["mode"] === "UPD" && $this->hasEmailChanged())
        ) {
            $existingUser = UsersDAO::getUsuarioByEmail($this->viewData["useremail"]);
            if ($existingUser) {
                $this->innerError("useremail", "Este email ya está registrado.");
            }
        }

        return !(count($this->viewData["errors"]) > 0);
    }

    private function validatePasswordChange(): bool
    {
        if (Validators::IsEmpty($this->viewData["current_password"])) {
            $this->innerError("current_password", "La contraseña actual es requerida.");
        }
        if (Validators::IsEmpty($this->viewData["new_password"])) {
            $this->innerError("new_password", "La nueva contraseña es requerida.");
        }
        if (!Validators::IsValidPassword($this->viewData["new_password"])) {
            $this->innerError("new_password", "La nueva contraseña debe tener al menos 8 caracteres, 1 número, 1 mayúscula y 1 carácter especial.");
        }
        if ($this->viewData["new_password"] !== $this->viewData["confirm_password"]) {
            $this->innerError("confirm_password", "La confirmación de la contraseña no coincide.");
        }

        $currentUser = UsersDAO::getUsuarioById($this->viewData["usercod"]);
        if (!$currentUser) {
            $this->innerError("global", "Usuario no encontrado.");
            return false;
        }

        if (!Security::verifyPassword($this->viewData["current_password"], $currentUser["userpswd"])) {
            $this->innerError("current_password", "La contraseña actual es incorrecta.");
        }

        return !(count($this->viewData["errors"]) > 0);
    }

    private function hasEmailChanged(): bool
    {
        if ($this->viewData["mode"] !== "UPD") {
            return false;
        }

        $currentUser = UsersDAO::getUsuarioById($this->viewData["usercod"]);
        return $currentUser["useremail"] !== $this->viewData["useremail"];
    }

    private function processData()
    {
        $mode = $this->viewData["mode"];
        switch ($mode) {
            case "INS":
                try {
                    $passwordExpiry = date('Y-m-d H:i:s', strtotime('+3 months'));
                    $result = UsersDAO::newUsuario(
                        $this->viewData["useremail"],
                        $this->viewData["username"],
                        $this->viewData["userpswd"],
                        $this->viewData["userpswdest"],
                        $passwordExpiry,
                        $this->viewData["userest"],
                        hash("sha256", $this->viewData["useremail"] . time()),
                        $this->viewData["usertipo"]
                    );
                    if ($result > 0) {
                        Site::redirectToWithMsg(LIST_URL, "Usuario creado exitosamente");
                    } else {
                        $this->innerError("global", "Algo salió mal al guardar el nuevo usuario.");
                    }
                } catch (\Exception $e) {
                    $this->innerError("global", $e->getMessage());
                }
                break;

            case "UPD":
                if (
                    UsersDAO::updateUsuario(
                        $this->viewData["usercod"],
                        $this->viewData["useremail"],
                        $this->viewData["username"],
                        $this->viewData["userpswdest"],
                        $this->viewData["userpswdexp"],
                        $this->viewData["userest"],
                        $this->viewData["usertipo"]
                    ) > 0
                ) {
                    Site::redirectToWithMsg(LIST_URL, "Usuario actualizado exitosamente");
                } else {
                    $this->innerError("global", "Algo salió mal al actualizar el usuario.");
                }
                break;

            case "CHGPWD":
                $newHashedPassword = Security::hashPassword($this->viewData["new_password"]);
                $passwordExpiry = date('Y-m-d H:i:s', strtotime('+3 months'));

                if (
                    UsersDAO::updateUsuarioPassword(
                        $this->viewData["usercod"],
                        $newHashedPassword,
                        $passwordExpiry
                    ) > 0
                ) {
                    Site::redirectToWithMsg(LIST_URL, "La contraseña fue cambiada exitosamente");
                } else {
                    $this->innerError("global", "Algo salió mal al cambiar la contraseña.");
                }
                break;

            case "DEL":
                if (
                    UsersDAO::deleteUsuario(
                        $this->viewData["usercod"]
                    ) > 0
                ) {
                    Site::redirectToWithMsg(LIST_URL, "Usuario eliminado exitosamente");
                } else {
                    $this->innerError("global", "Algo salió mal al eliminar el usuario.");
                }
                break;
        }
    }

    private function prepareViewData()
    {
        $this->viewData["modeDsc"] = sprintf(
            $this->modes[$this->viewData["mode"]],
            $this->viewData["username"]
        );

        $this->viewData['selectedUser' . $this->viewData["userest"]] = "selected";
        $this->viewData['selectedPswd' . $this->viewData["userpswdest"]] = "selected";
        $this->viewData['selected' . $this->viewData["usertipo"]] = "selected";

        if (count($this->viewData["errors"]) > 0) {
            foreach ($this->viewData["errors"] as $scope => $errorsArray) {
                $this->viewData["errors_" . $scope] = $errorsArray;
            }
        }

        if ($this->viewData["mode"] === "DSP") {
            $this->viewData["cancelLabel"] = "Back";
            $this->viewData["showConfirm"] = false;
        }

        if ($this->viewData["mode"] === "DSP" || $this->viewData["mode"] === "DEL") {
            $this->viewData["readonly"] = "readonly";
        }

        $this->viewData["timestamp"] = time();
        $this->viewData["xsrtoken"] = hash("sha256", json_encode($this->viewData));
        $_SESSION[$this->name . "-xsrtoken"] = $this->viewData["xsrtoken"];
    }
}