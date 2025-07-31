<?php

namespace Controllers;

use \Dao\Maintenance\Catalog\Products\Products as ProductsDao;
use \Dao\Cart\Cart;
use \Views\Renderer as Renderer;
use \Utilities\Site as Site;
use \Utilities\Security;
use \Utilities\Cart\CartFns;

class Index extends PublicController
{
    public function run(): void
    {
        Site::addLink("public/css/products.css");
        
        if ($this->isPostBack()) {
            $this->handleCartActions();
        }
        
        $viewData = [];
        $viewData["productsHighlighted"] = ProductsDao::getFeaturedProducts();
        $viewData["productsNew"] = ProductsDao::getNewProducts();

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