<?php

namespace Controllers\Orders;

use Controllers\PublicController;
use Dao\Orders\Order;
use Utilities\Security;
use Utilities\Site;
use DateTime;
use DateTimeZone;

class Detail extends PublicController
{
    public function run(): void
    {
        Site::addLink("public/css/invoice.css");

        if (!Security::isLogged()) {
            Site::redirectToWithMsg("index.php?page=sec_login", "Debes iniciar sesión para ver tus órdenes.");
            die();
        }

        $viewData = ['order' => null, 'error' => null];
        $userId = Security::getUserId();
        $orderIdFromUrl = $_GET['id'] ?? null;

        if (!$orderIdFromUrl) {
            $viewData['error'] = "No se ha especificado un ID de orden.";
            \Views\Renderer::render("orders/detail", $viewData);
            return;
        }

        // 1. Obtener los datos de la tabla 'orders'
        $dbOrder = Order::getOrderByUserAndId($orderIdFromUrl, $userId);

        if (!$dbOrder) {
            $viewData['error'] = "La orden no fue encontrada o no tienes permiso para verla.";
        } else {
            // 2. Obtener los detalles de la tabla 'order_details'
            $dbOrderDetails = Order::getOrderDetails($dbOrder['orderId']);

            // 3. Decodificar el JSON de PayPal para datos extra
            $paypalData = json_decode($dbOrder['paypal_data'], true);

            // 4. Formatear todos los datos en un solo array para la vista
            $viewData['order'] = $this->formatOrderForView($dbOrder, $dbOrderDetails, $paypalData);
        }

        \Views\Renderer::render("orders/detail", $viewData);
    }

    private function formatOrderForView(array $dbOrder, array $dbOrderDetails, ?array $paypalData): array
    {
        // Fallback por si el JSON estuviera corrupto
        if (is_null($paypalData)) {
            $paypalData = [];
        }

        // Formatear la lista de items desde 'order_details'
        $items = [];
        $itemsSubtotal = 0;
        foreach ($dbOrderDetails as $item) {
            $items[] = [
                'name' => $item['productName'],
                'quantity' => $item['quantity'],
                'unit_amount' => number_format($item['price'], 2),
                'subtotal' => number_format($item['subtotal'], 2),
                'currency_code' => 'USD' // Asumimos USD o la moneda que uses
            ];
            $itemsSubtotal += floatval($item['subtotal']);
        }

        $capture = $paypalData['purchase_units'][0]['payments']['captures'][0] ?? [];
        $date = new DateTime($capture['create_time'] ?? $dbOrder['created_at']);
        $date->setTimezone(new DateTimeZone('America/Tegucigalpa'));

        $meses = ["January" => "enero", "February" => "febrero", "March" => "marzo", "April" => "abril", "May" => "mayo", "June" => "junio", "July" => "julio", "August" => "agosto", "September" => "septiembre", "October" => "octubre", "November" => "noviembre", "December" => "diciembre"];
        $mes = $meses[$date->format("F")];
        $hora = str_replace(["AM", "PM"], ["a. m.", "p. m."], $date->format("h:i A"));

        $totalPaid = floatval($dbOrder['total']);
        $difference = $itemsSubtotal - $totalPaid;

        return [
            'id' => $dbOrder['order_id'],
            'update_time' => $date->format("d") . " de " . $mes . " de " . $date->format("Y") . ", " . $hora,
            'payer_name' => ($paypalData['payer']['name']['given_name'] ?? '') . ' ' . ($paypalData['payer']['name']['surname'] ?? 'Cliente'),
            'payer_email' => $paypalData['payer']['email_address'] ?? 'No disponible',
            'amount' => number_format($totalPaid, 2),
            'currency' => $capture['amount']['currency_code'] ?? 'USD',
            'paypal_fee' => number_format(floatval($capture['seller_receivable_breakdown']['paypal_fee']['value'] ?? 0), 2),
            'net_amount' => number_format(floatval($capture['seller_receivable_breakdown']['net_amount']['value'] ?? 0), 2),
            'items' => $items,
            'items_subtotal' => number_format($itemsSubtotal, 2),
            'difference' => $difference > 0.01 ? number_format($difference, 2) : null,
            'shipping' => $this->getShippingInfoFromDb($paypalData)
        ];
    }

    private function getShippingInfoFromDb(?array $paypalData): array
    {
        if (empty($paypalData) || !isset($paypalData['purchase_units'][0]['shipping'])) {
            return [];
        }
        $shipping = $paypalData['purchase_units'][0]['shipping'];
        return [
            'name' => $shipping['name']['full_name'] ?? 'N/A',
            'address' => [
                'line1' => $shipping['address']['address_line_1'] ?? 'N/A',
                'city' => $shipping['address']['admin_area_2'] ?? 'N/A',
                'state' => $shipping['address']['admin_area_1'] ?? 'N/A',
                'postal_code' => $shipping['address']['postal_code'] ?? 'N/A',
                'country' => $shipping['address']['country_code'] ?? 'N/A'
            ]
        ];
    }
}