<?php
    namespace Controllers\Maintenance\Users;
 

    use Controllers\PublicController;
    use Views\Renderer;
    use Dao\Maintenance\Users\Users as UsersDao;
    use Utilities\Site;
    use Utilities\Validators;

     class User extends PublicController {

    

        private $viewData = [];
        private $mode = "DSP";
        private $modeDescriptions = [
            "DSP" => "Detalle de usuario %s - %s",
            "INS" => "Nuevo Usuario",
            "UPD" => "Editar usuario %s - %s",
            "DEL" => "Eliminar usuario %s - %s"
        ];
        private $readonly = "";
        private $showCommitBtn = true;


        private $user = [
            "usercod" => 0,
            "useremail" => "",
            "username" => "",
            "userpswd" => "",
            "userfching" => "",
            "userpswdest" => "",
            "userpswdexp" => "",
            "userest" => "ACT",
            "useractcod" => "",
            "userpswdchg" => "",
            "usertipo" => "PBL"
        ];

        
        private $user_xss_token = "";

        public function run(): void {
            try {
                $this->getData();
                if ($this->isPostBack()) {
                    if ($this->validateData()) {
                        $this->handlePostAction();
                    }
                }
                $this->setViewData();
                Renderer::render("maintenance/users/user", $this->viewData);
            } catch (\Exception $ex) {
                Site::redirectToWithMsg(
                    "index.php?page=Maintenance_Users_Users",
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
            $this->user = UsersDao::getUserById(intval($_GET["id"]));
            if (!$this->user) {
                throw new \Exception("No se encontró el Usuario", 1);
            }

      
            $this->user["userpswd"] = "";
        }
    } else {
        throw new \Exception("Formulario cargado en modalidad inválida", 1);
    }
}


        private function validateData() {
            $errors = [];
            $this->user_xss_token = $_POST["user_xss_token"] ?? "";
            $this->user["usercod"] = intval($_POST["usercod"] ?? 0);
            $this->user["useremail"] = trim($_POST["useremail"] ?? "");
            $this->user["username"] = trim($_POST["username"] ?? "");
            $this->user["userpswd"] = trim($_POST["userpswd"] ?? "");
            $this->user["userfching"] = trim($_POST["userfching"] ?? "");
            $this->user["userpswdest"] = trim($_POST["userpswdest"] ?? "");
            $this->user["userpswdexp"] = trim($_POST["userpswdexp"] ?? "");
            $this->user["userest"] = trim($_POST["userest"] ?? "");
            $this->user["useractcod"] = trim($_POST["useractcod"] ?? "");
            $this->user["userpswdchg"] = trim($_POST["userpswdchg"] ?? "");
            $this->user["usertipo"] = trim($_POST["usertipo"] ?? "");

            if (Validators::IsEmpty($this->user["useremail"])) {
                $errors["useremail_error"] = "El correo electrónico es requerido";
            }

            if (Validators::IsEmpty($this->user["username"])) {
                $errors["username_error"] = "El nombre de usuario es requerido";
            }

            if ($this->mode === "INS" && Validators::IsEmpty($this->user["userpswd"])) {
                $errors["userpswd_error"] = "La contraseña es requerida para un nuevo usuario";
            }

            if (!in_array($this->user["userest"], ["ACT", "INA"])) {
                $errors["userest_error"] = "El estado del usuario es inválido";
            }

            if (count($errors) > 0) {
                foreach ($errors as $key => $value) {
                    $this->user[$key] = $value;
                }
                return false;
            }
            return true;
        }

        

        private function setViewData(): void {
            $this->viewData["mode"] = $this->mode;
            $this->viewData["user_xss_token"] = $this->user_xss_token;
            $this->viewData["FormTitle"] = sprintf(
                $this->modeDescriptions[$this->mode],
                $this->user["usercod"],
                $this->user["username"]
            );
            $this->viewData["showCommitBtn"] = $this->showCommitBtn;
            $this->viewData["readonly"] = $this->readonly;

            $userStatusKey = "userest_" . strtolower($this->user["userest"]);
            $this->user[$userStatusKey] = "selected";

            $this->viewData["user"] = $this->user;
        }


        private function handlePostAction(){
            switch($this->mode){
                case "INS":
                    $this->user["userpswd"] = password_hash($this->user["userpswd"], PASSWORD_BCRYPT);
                    $this->user["userpswdexp"] = date('Y-m-d', time() + 7776000); 
                    $this->user["useractcod"] = hash("sha256", $this->user["useremail"] . time()); 
                    $this->user["userpswdchg"] = date('Y-m-d H:i:s'); 
                    $this->user["userpswdest"] = "ACT"; 
                    $result = UsersDao::insertUser(
                        $this->user["useremail"],
                        $this->user["username"],
                        $this->user["userpswd"],
                        $this->user["userest"],
                        $this->user["usertipo"],
                        $this->user["userpswdexp"],
                        $this->user["userpswdest"],
                        $this->user["useractcod"],
                        $this->user["userpswdchg"]

                    );
                    if (!$result) {
                        throw new \Exception("Error al insertar el usuario", 1);
                    }
                    else{
                        Site::redirectToWithMsg("index.php?page=Maintenance_Users_Users", "Usuario creado exitosamente");
                    }
                    
                    break;
                case "UPD":
                    if (empty($this->user["userpswd"])) {
                    throw new \Exception("Debe ingresar una nueva contraseña.");
                    }
                    $this->user["userpswd"] = password_hash($this->user["userpswd"], PASSWORD_BCRYPT);
                    $result = UsersDao::updateUser(
                        $this->user["usercod"],
                        $this->user["useremail"],
                        $this->user["username"],
                        $this->user["userpswd"],
                        $this->user["userest"],
                        $this->user["usertipo"]
                    );
                    if (!$result) {
                        throw new \Exception("Error al actualizar el usuario", 1);
                    } else {
                        Site::redirectToWithMsg("index.php?page=Maintenance_Users_Users", "Usuario actualizado exitosamente");
                    }
                    
                    break;
                case "DEL":
                    $result = UsersDao::deleteUser($this->user["usercod"]);
            if ($result > 0) {
                Site::redirectToWithMsg(
                    "index.php?page=Maintenance_Users_Users",
                    "User eliminado exitosamente"
                );
            }
                    
                    break;
                default:
                    throw new \Exception("Modo inválido", 1);
            }
        }




    }