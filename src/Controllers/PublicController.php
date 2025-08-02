<?php

namespace Controllers;

abstract class PublicController implements IController
{
    protected $name = "";

    public function __construct()
    {
        $this->name = get_class($this);
        \Utilities\Nav::setPublicNavContext();
        if (\Utilities\Security::isLogged()) {
            $layoutFile = \Utilities\Context::getContextByKey("PRIVATE_LAYOUT");
            if ($layoutFile !== "") {
                \Utilities\Context::setContext(
                    "layoutFile",
                    $layoutFile
                );
                \Utilities\Nav::setNavContext();
            }
        }
        $this->getCartCounter();
    }
    
    public function toString(): string
    {
        return $this->name;
    }
    
    protected function isPostBack()
    {
        return $_SERVER["REQUEST_METHOD"] == "POST";
    }

    protected function getCartCounter()
    {
        if (\Utilities\Security::isLogged()) {
            $cartItems = \Dao\Cart\Cart::getAuthCart(\Utilities\Security::getUserId());
            \Utilities\Context::setContext("CART_ITEMS", count($cartItems));
        } else {
            $annonCod = \Utilities\Cart\CartFns::getAnnonCartCode();
            $cartItems = \Dao\Cart\Cart::getAnonCart($annonCod);
            \Utilities\Context::setContext("CART_ITEMS", count($cartItems));
        }
    }
}