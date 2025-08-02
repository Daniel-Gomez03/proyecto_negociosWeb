<?php

namespace Controllers\Sec;

use Dao\Cart\Cart;
use Utilities\Cart\CartFns;

class Login extends \Controllers\PublicController
{
    private $txtEmail = "";
    private $txtPswd = "";
    private $errorEmail = "";
    private $errorPswd = "";
    private $generalError = "";
    private $hasError = false;

    public function run() :void
    {
        if ($this->isPostBack()) {
            $this->txtEmail = $_POST["txtEmail"];
            $this->txtPswd = $_POST["txtPswd"];

            if (!\Utilities\Validators::IsValidEmail($this->txtEmail)) {
                $this->errorEmail = "¡Correo no tiene el formato adecuado!";
                $this->hasError = true;
            }

            if (\Utilities\Validators::IsEmpty($this->txtPswd)) {
                $this->errorPswd = "¡Debe ingresar una contraseña!";
                $this->hasError = true;
            }

            if (!$this->hasError) {
                if ($dbUser = \Dao\Security\Security::getUsuarioByEmail($this->txtEmail)) {
                    if ($dbUser["userest"] != \Dao\Security\Estados::ACTIVO) {
                        $this->generalError = "¡Credenciales son incorrectas!";
                        $this->hasError = true;
                        error_log(
                            sprintf(
                                "ERROR: %d %s tiene cuenta con estado %s",
                                $dbUser["usercod"],
                                $dbUser["useremail"],
                                $dbUser["userest"]
                            )
                        );
                    }

                    if (!\Dao\Security\Security::verifyPassword($this->txtPswd, $dbUser["userpswd"])) {
                        $this->generalError = "¡Credenciales son incorrectas!";
                        $this->hasError = true;
                        error_log(
                            sprintf(
                                "ERROR: %d %s contraseña incorrecta",
                                $dbUser["usercod"],
                                $dbUser["useremail"]
                            )
                        );
                    }

                    if (!$this->hasError) {
                        // 1. Verificamos el rol del usuario para decidir qué layout usar.
                        if (\Dao\Security\Security::isUsuarioInRol($dbUser['usercod'], 'ADM')) {
                            \Utilities\Context::setContext('layoutFile', 'privatelayout.view.tpl');
                        } else {
                            \Utilities\Context::setContext('layoutFile', 'privatelayout_cliente.view.tpl');
                        }

                        // 2. Invalidamos la caché de navegación. Esto es útil para el Admin,
                        // para que su menú siempre se recalcule con los permisos actuales.
                        \Utilities\Nav::invalidateNavData();

                        // 3. Establecemos la sesión del usuario.
                        \Utilities\Security::login(
                            $dbUser["usercod"],
                            $dbUser["username"],
                            $dbUser["useremail"]
                        );
                        
                        // 4. Movemos los artículos del carrito anónimo al carrito del usuario.
                        $anonCod = CartFns::getAnnonCartCode();
                        Cart::moveAnonToAuth($anonCod, $dbUser["usercod"]);
                        
                        // 5. Redirigimos al usuario a la página correspondiente.
                        if (\Utilities\Context::getContextByKey("redirto") !== "") {
                            \Utilities\Site::redirectTo(
                                \Utilities\Context::getContextByKey("redirto")
                            );
                        } else {
                            \Utilities\Site::redirectTo("index.php");
                        }
                    }
                } else {
                    error_log(
                        sprintf(
                            "ERROR: %s trato de ingresar",
                            $this->txtEmail
                        )
                    );
                    $this->generalError = "¡Credenciales son incorrectas!";
                }
            }
        }

        $dataView = get_object_vars($this);
        \Views\Renderer::render("security/login", $dataView);
    }
}
?>