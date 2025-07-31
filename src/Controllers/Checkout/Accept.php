<?php
namespace Controllers\Checkout;

use Controllers\PublicController;
use Utilities\Site;
use Utilities\PayPal\PayPalRestApi;
use Utilities\Context;
use Utilities\Security;
use Dao\Cart\Cart;
use Dao\Orders\Order;
use DateTime;
use DateTimeZone;

class Accept extends PublicController
{
    public function run(): void
    {
        Site::addLink("public/css/invoice.css");

        $dataview = [
            'order' => null,
            'orderjson' => null,
            'error' => null
        ];

        $token = $_GET["token"] ?? "";
        $session_token = $_SESSION["orderid"] ?? "";

        try {
            if ($token === "" || $token !== $session_token) {
                throw new \Exception("Token de orden inválido o sesión expirada");
            }

            $paypal = new PayPalRestApi(
                Context::getContextByKey("PAYPAL_CLIENT_ID"),
                Context::getContextByKey("PAYPAL_CLIENT_SECRET")
            );

            // 1. Capturar la orden en PayPal
            $result = $paypal->captureOrder($session_token);

            $dataview['orderjson'] = json_encode($result, JSON_PRETTY_PRINT);

            if (!$result || !isset($result->status) || $result->status !== "COMPLETED") {
                throw new \Exception("El pago no fue completado correctamente");
            }

            // 2. Preparar datos para la base de datos
            $orderId = $result->id ?? $session_token;
            $userId = Security::isLogged() ? Security::getUserId() : null;
            $total = $this->getOrderTotal($result);
            $status = $result->status;
            $paypalData = json_decode(json_encode($result), true);

            // 3. Guardar en base de datos
            $internalOrderId = Order::insertOrder(
                $orderId,
                $userId,
                $total,
                $status,
                $paypalData
            );

            if (!$internalOrderId) {
                throw new \Exception("Error al guardar la orden en la base de datos");
            }

            // 4. Obtener items del carrito
            $cartItems = $this->getCartItems();

            // 5. Preparar datos para la vista ANTES de limpiar el carrito
            $dataview['order'] = $this->formatOrderData($result, $cartItems);

            // 6. Preparar y guardar detalles de la orden
            $orderDetails = $this->prepareOrderDetails($cartItems);
            $this->updateStock($internalOrderId, $orderDetails);

            // 7. Limpiar carrito (ahora que ya tenemos los datos)
            $this->clearCart();

        } catch (\Exception $e) {
            $dataview['error'] = $e->getMessage();
        }

        \Views\Renderer::render("paypal/accept", $dataview);
    }

    private function updateStock($internalOrderId, $orderDetails): void
    {
        foreach ($orderDetails as $item) {
            $stockUpdated = Cart::actualizarStock(
                $item['productId'],
                $item['quantity']
            );

            if (!$stockUpdated) {
                throw new \Exception("Error al actualizar stock para producto: " . $item['productId']);
            }

            Order::insertOrderDetail($internalOrderId, $item);
        }
    }

    private function getOrderTotal($paypalResult): float
    {
        if (isset($paypalResult->purchase_units[0]->payments->captures[0]->amount->value)) {
            return (float) $paypalResult->purchase_units[0]->payments->captures[0]->amount->value;
        }
        return 0.0;
    }

    private function getCartItems(): array
    {
        if (Security::isLogged()) {
            $cartItems = Cart::getAuthCart(Security::getUserId());
        } else {
            $anonCod = \Utilities\Cart\CartFns::getAnnonCartCode();
            $cartItems = Cart::getAnonCart($anonCod);
        }
        return $cartItems;
    }

    private function prepareOrderDetails(array $cartItems): array
    {
        $details = [];
        foreach ($cartItems as $item) {
            $details[] = [
                "productId" => $item['productId'],
                "quantity" => $item['crrctd'],
                "price" => $item['productPrice']
            ];
        }
        return $details;
    }

    private function clearCart(): void
    {
        if (Security::isLogged()) {
            Cart::clearAuthCart(Security::getUserId());
        } else {
            $anonCod = \Utilities\Cart\CartFns::getAnnonCartCode();
            Cart::clearAnonCart($anonCod);
        }
        unset($_SESSION["orderid"]);
    }

    private function getPayerName($paypalResult): string
    {
        if (isset($paypalResult->payer->name)) {
            return trim(
                ($paypalResult->payer->name->given_name ?? '') . ' ' .
                ($paypalResult->payer->name->surname ?? '')
            );
        }
        return 'Cliente no identificado';
    }

    private function getPaypalFee($paypalResult): string
    {
        $capture = $paypalResult->purchase_units[0]->payments->captures[0];
        if (isset($capture->seller_receivable_breakdown->paypal_fee->value)) {
            return number_format((float) $capture->seller_receivable_breakdown->paypal_fee->value, 2);
        }
        return '0.00';
    }

    private function getNetAmount($paypalResult): string
    {
        $capture = $paypalResult->purchase_units[0]->payments->captures[0];
        if (isset($capture->seller_receivable_breakdown->net_amount->value)) {
            return number_format((float) $capture->seller_receivable_breakdown->net_amount->value, 2);
        }
        return number_format($this->getOrderTotal($paypalResult), 2);
    }

    private function getShippingInfo($paypalResult): array
    {
        if (!isset($paypalResult->purchase_units[0]->shipping)) {
            return [];
        }

        $shipping = $paypalResult->purchase_units[0]->shipping;
        return [
            'name' => $shipping->name->full_name ?? 'N/A',
            'address' => [
                'line1' => $shipping->address->address_line_1 ?? 'N/A',
                'city' => $shipping->address->admin_area_2 ?? 'N/A',
                'state' => $shipping->address->admin_area_1 ?? 'N/A',
                'postal_code' => $shipping->address->postal_code ?? 'N/A',
                'country' => $shipping->address->country_code ?? 'N/A'
            ]
        ];
    }

    private function calculateItemsSubtotal(array $items): float
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += (float) str_replace(',', '', $item['subtotal']);
        }
        return $subtotal;
    }

    private function formatOrderItems(array $cartItems): array
    {
        $items = [];
        foreach ($cartItems as $item) {
            $unitAmount = (float) ($item['productPrice'] ?? 0);
            $quantity = (int) ($item['crrctd'] ?? 1);
            $subtotal = $unitAmount * $quantity;

            $items[] = [
                'name' => $item['productName'] ?? 'Producto sin nombre',
                'quantity' => $quantity,
                'unit_amount' => number_format($unitAmount, 2),
                'subtotal' => number_format($subtotal, 2),
                'currency_code' => 'USD'
            ];
        }
        return $items;
    }
    private function formatOrderData($paypalResult, array $cartItems): array
    {
        $capture = $paypalResult->purchase_units[0]->payments->captures[0];
        $date = new DateTime($capture->create_time);
        $date->setTimezone(new DateTimeZone('America/Tegucigalpa'));

        $meses = [
            "January" => "enero",
            "February" => "febrero",
            "March" => "marzo",
            "April" => "abril",
            "May" => "mayo",
            "June" => "junio",
            "July" => "julio",
            "August" => "agosto",
            "September" => "septiembre",
            "October" => "octubre",
            "November" => "noviembre",
            "December" => "diciembre"
        ];

        $mes = $meses[$date->format("F")];
        $hora = str_replace(["AM", "PM"], ["a. m.", "p. m."], $date->format("h:i A"));

        $formattedItems = $this->formatOrderItems($cartItems);
        $itemsSubtotal = $this->calculateItemsSubtotal($formattedItems);
        $totalPaid = $this->getOrderTotal($paypalResult);
        $difference = $itemsSubtotal - $totalPaid;

        return [
            'id' => $paypalResult->id ?? 'N/A',
            'update_time' => $date->format("d") . " de " . $mes . " de " . $date->format("Y") . ", " . $hora,
            'payer_name' => $this->getPayerName($paypalResult),
            'payer_email' => $paypalResult->payer->email_address ?? 'N/A',
            'amount' => number_format($this->getOrderTotal($paypalResult), 2),
            'currency' => $capture->amount->currency_code ?? 'USD',
            'paypal_fee' => $this->getPaypalFee($paypalResult),
            'net_amount' => $this->getNetAmount($paypalResult),
            'items' => $formattedItems,
            'items_subtotal' => number_format($itemsSubtotal, 2),
            'difference' => $difference > 0 ? number_format($difference, 2) : null,
            'shipping' => $this->getShippingInfo($paypalResult)
        ];
    }
}