<?php
namespace Controllers\Checkout;

use Controllers\PublicController;
use Utilities\Site;
use Dao\Cart\Cart;
use Utilities\Security;

class Catalogo extends PublicController
{
    public function run(): void
    {
        Site::addLink("public/css/catalogo.css");

        if ($this->isPostBack()) {
            $this->handleCartActions();
        }

        $productosDisponibles = Cart::getProductosDisponibles();
        $carretillaUsuario = Cart::getAuthCart(Security::getUserId());

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
            "formAction" => "index.php?page=Checkout_Catalogo"
        ];

        \Views\Renderer::render("catalog", $viewData);
    }

    private function handleCartActions(): void
    {
        if (!isset($_POST["productId"])) {
            return;
        }

        $productId = intval($_POST["productId"]);
        $producto = Cart::getProductoDisponible($productId);

        if (!$producto || $producto["productStock"] <= 0) {
            return;
        }

        $precio = $producto["productPrice"];
        $usercod = Security::getUserId();

        Cart::addToAuthCart($productId, $usercod, 1, $precio);

        Site::redirectTo("index.php?page=Checkout_Catalogo");
    }
}