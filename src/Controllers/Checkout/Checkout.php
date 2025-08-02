<?php
namespace Controllers\Checkout;

use Controllers\PrivateController;
use Controllers\PublicController;
use Dao\Cart\Cart;
use Utilities\Security;
use Utilities\Site;

class Checkout extends PrivateController
{
     public function __construct()
    {
        parent::__construct();
    }
    public function run(): void
    {
        
        $viewData = array();
        $usercod = Security::getUserId();
        $carretilla = Cart::getAuthCart($usercod) ?? []; 

        if ($this->isPostBack()) {
            $processPayment = true;
            
            if (isset($_POST["removeOne"]) || isset($_POST["addOne"])) {
                $productId = intval($_POST["productId"]);
                $productoDisp = Cart::getProductoDisponible($productId);
                
                if ($productoDisp) {
                    $amount = isset($_POST["removeOne"]) ? -1 : 1;
                    
                    if ($amount == 1 && $productoDisp["productStock"] - $amount >= 0) {
                        Cart::addToAuthCart(
                            $productId,
                            $usercod,
                            $amount,
                            $productoDisp["productPrice"]
                        );
                    } elseif ($amount == -1) {
                        Cart::addToAuthCart(
                            $productId,
                            $usercod,
                            $amount,
                            $productoDisp["productPrice"]
                        );
                    }
                    
                    $carretilla = Cart::getAuthCart($usercod) ?? [];
                    $processPayment = false;
                }
            }

            // Procesamiento de pago
            if ($processPayment && !empty($carretilla)) {
                $PayPalOrder = new \Utilities\Paypal\PayPalOrder(
                    "test" . (time() - 10000000),
                    "http://localhost:8080/proyecto_negociosWeb/index.php?page=Checkout_Error",
                    "http://localhost:8080/proyecto_negociosWeb/index.php?page=Checkout_Accept"
                );

                foreach ($carretilla as $producto) {
                    $PayPalOrder->addItem(
                        $producto["productName"],
                        $producto["productDescription"],
                        $producto["productId"],
                        $producto["crrprc"],
                        0,
                        $producto["crrctd"],
                        "DIGITAL_GOODS"
                    );
                }

                $PayPalRestApi = new \Utilities\PayPal\PayPalRestApi(
                    \Utilities\Context::getContextByKey("PAYPAL_CLIENT_ID"),
                    \Utilities\Context::getContextByKey("PAYPAL_CLIENT_SECRET")
                );
                
                $response = $PayPalRestApi->getAccessToken();
                if ($response) {
                    $response = $PayPalRestApi->createOrder($PayPalOrder);
                    
                    if ($response && isset($response->id)) {
                        $_SESSION["orderid"] = $response->id;
                        
                        if (isset($response->links)) {
                            foreach ($response->links as $link) {
                                if ($link->rel == "approve") {
                                    Site::redirectTo($link->href);
                                    return;
                                }
                            }
                        }
                    }
                }
                
                Site::redirectTo("index.php?page=Checkout_Error");
                return;
            }
        }

        // Preparar datos para la vista
        $finalCarretilla = [];
        $total = 0;
        
        foreach ($carretilla as $index => $prod) {
            $prod["row"] = $index + 1;
            $subtotal = $prod["crrprc"] * $prod["crrctd"];
            $prod["subtotal"] = number_format($subtotal, 2);
            $total += $subtotal;
            $prod["crrprc"] = number_format($prod["crrprc"], 2);
            $finalCarretilla[] = $prod;
        }

        $viewData["carretilla"] = $finalCarretilla;
        $viewData["total"] = number_format($total, 2);
        $viewData["hasItems"] = !empty($finalCarretilla);
        
        \Views\Renderer::render("paypal/checkout", $viewData);
    }
}
