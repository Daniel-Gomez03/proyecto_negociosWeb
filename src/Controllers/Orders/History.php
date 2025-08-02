<?php

namespace Controllers\Orders;

use Controllers\PrivateController;
use Controllers\PublicController;
use Utilities\Security;
use Utilities\Site;
use Dao\Orders\Order;
use DateTime;
use DateTimeZone;

class History extends PrivateController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function run(): void
    {
        // 1. Verificar si el usuario ha iniciado sesión
        if (!Security::isLogged()) {
            Site::redirectTo("index.php?page=sec_login&returnto=index.php?page=Orders_History");
            die();
        }
        
        $viewData = [];
        $userId = Security::getUserId();
        
        // 2. Obtener las órdenes del usuario desde la base de datos
        $orders = Order::getOrdersByUserId($userId);
        
        // 3. Formatear los datos para la vista (fechas, moneda, etc.)
        $formattedOrders = [];
        foreach ($orders as $order) {
            // Formatear la fecha para que sea más legible
            $date = new DateTime($order["created_at"], new DateTimeZone('UTC'));
            $date->setTimezone(new DateTimeZone('America/Tegucigalpa')); // Ajusta a tu zona horaria
            $order["formatted_date"] = $date->format('d/m/Y h:i A');

            // Formatear el total como moneda
            $order["formatted_total"] = number_format($order["total"], 2);

            // Traducir el estado si es necesario
            $order["status_display"] = $order["status"] === 'COMPLETED' ? 'Completado' : 'Pendiente';
            
            $formattedOrders[] = $order;
        }

        $viewData["orders"] = $formattedOrders;
        $viewData["hasOrders"] = count($formattedOrders) > 0;
        
        
        \Views\Renderer::render("orders/history", $viewData);
    }
}