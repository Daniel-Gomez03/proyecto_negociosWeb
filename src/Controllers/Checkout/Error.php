<?php

namespace Controllers\Checkout;

use Controllers\PrivateController;
use Controllers\PublicController;

class Error extends PrivateController
{
     public function __construct()
    {
        parent::__construct();
    }
    public function run(): void
    {

        $viewData = [
            'title' => 'Error en el Pago',
            'message' => 'Ha ocurrido un error al procesar su pago.',
            'details' => 'El pago fue cancelado o no se pudo completar. Por favor, intente nuevamente.'
        ];

        if (isset($_SESSION["orderid"])) {
            unset($_SESSION["orderid"]);
        }

        \Views\Renderer::render("paypal/error", $viewData);
    }
}