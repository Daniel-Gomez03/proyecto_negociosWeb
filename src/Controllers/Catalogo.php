<?php

namespace Controllers;

use Dao\Cart\Cart;
use Utilities\Site;
use Utilities\Cart\CartFns;
use Utilities\Security;

class Catalogo extends PublicController
{
    public function run(): void
    {
        if ($this->isPostBack()) {
            $this->handleCartActions();
        }

        $productosDisponibles = Cart::getProductosDisponibles();
        $carretillaUsuario = [];
        
        if (Security::isLogged()) {
            $carretillaUsuario = Cart::getAuthCart(Security::getUserId());
        } else {
            $cartAnonCod = CartFns::getAnnonCartCode();
            $carretillaUsuario = Cart::getAnonCart($cartAnonCod);
        }

        $carretillaAssoc = array();
        foreach ($carretillaUsuario as $item) {
            $carretillaAssoc[$item["productId"]] = $item;
        }

        $viewDataProductos = array_map(function ($producto) use ($carretillaAssoc) {
            $producto['enCarretilla'] = isset($carretillaAssoc[$producto['productId']]);
            return $producto;
        }, $productosDisponibles);
        
        $viewData = [
            "page_title" => "Catálogo de Productos",
            "products" => $viewDataProductos,
            "formAction" => "index.php?page=Catalogo"
        ];

        \Views\Renderer::render("catalog", $viewData);
    }

    private function handleCartActions(): void
    {
        if (!isset($_POST["productId"])) {
            return;
        }

        $productId = intval($_POST["productId"]);
        $product = Cart::getProductoDisponible($productId);

        if (!$product || $product["productStock"] <= 0) {
            return;
        }

        $price = $product["productPrice"];

        if (Security::isLogged()) {
            $this->addToAuthCart($productId, $price);
        } else {
            $this->addToAnonCart($productId, $price);
        }

        Site::redirectTo("index.php?page=Catalogo");
    }

    private function addToAuthCart(int $productId, float $price): void
    {
        $usercod = Security::getUserId();
        Cart::addToAuthCart($productId, $usercod, 1, $price);
    }

    private function addToAnonCart(int $productId, float $price): void
    {
        $cartAnonCod = CartFns::getAnnonCartCode();
        Cart::addToAnonCart($productId, $cartAnonCod, 1, $price);
    }
}