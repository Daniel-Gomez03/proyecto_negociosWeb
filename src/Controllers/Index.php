<?php
namespace Controllers;

use \Dao\Cart\Cart;
use \Views\Renderer as Renderer;
use \Utilities\Site as Site;
use \Utilities\Security;
use \Utilities\Cart\CartFns;

class Index extends PublicController
{
    public function run(): void
    {

        if ($this->isPostBack()) {
            $this->handleCartActions();
        }

        $viewData = [];

        if (Security::isLogged()) {
            $viewData["catalogUrl"] = "index.php?page=Checkout_Catalogo";
        } else {
            $viewData["catalogUrl"] = "index.php?page=Catalogo";
        }

        $carretillaUsuario = [];
        if (Security::isLogged()) {
            $carretillaUsuario = Cart::getAuthCart(Security::getUserId());
        } else {
            $cartAnonCod = CartFns::getAnnonCartCode();
            $carretillaUsuario = Cart::getAnonCart($cartAnonCod);
        }

        $carretillaAssoc = [];
        foreach ($carretillaUsuario as $item) {
            $carretillaAssoc[$item["productId"]] = $item;
        }

        $mapFunction = function ($producto) use ($carretillaAssoc) {
            $producto['enCarretilla'] = isset($carretillaAssoc[$producto['productId']]);
            return $producto;
        };

        $viewData["productsHighlighted"] = array_map($mapFunction, Cart::getFeaturedProducts());
        $viewData["productsNew"] = array_map($mapFunction, Cart::getNewProducts());

        $viewData["formAction"] = "index.php?page=index";
        
        Renderer::render("index", $viewData);
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

        Site::redirectTo("index.php?page=index");
    }

    private function addToAuthCart(int $productId, float $price): void
    {
        $usercod = Security::getUserId();
        Cart::addToAuthCart(
            $productId,
            $usercod,
            1,
            $price
        );
    }

    private function addToAnonCart(int $productId, float $price): void
    {
        $cartAnonCod = CartFns::getAnnonCartCode();
        Cart::addToAnonCart(
            $productId,
            $cartAnonCod,
            1,
            $price
        );
    }
}