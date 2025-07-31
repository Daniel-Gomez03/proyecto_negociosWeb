{{if order}}
<div class="container-invoice">
    <div class="invoice-header">
        <h1>Detalles de la Orden</h1>
    </div>

    {{with order}}
    <div class="invoice-section">
        <div class="invoice-row">
            <span class="label">ID de la Orden:</span>
            <span>{{id}}</span>
        </div>
        <div class="invoice-row">
            <span class="label">Fecha de Pago:</span>
            <span>{{update_time}}</span>
        </div>
        <div class="invoice-row">
            <span class="label">Pagado por:</span>
            <span>{{payer_name}} ({{payer_email}})</span>
        </div>
    </div>

    <div class="invoice-section">
        <h3>Artículos</h3>
        <table class="invoice-products">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                {{foreach items}}
                <tr>
                    <td>{{name}}</td>
                    <td>{{quantity}}</td>
                    <td>{{unit_amount}} {{currency_code}}</td>
                    <td>{{subtotal}} {{currency_code}}</td>
                </tr>
                {{endfor items}}
            </tbody>
        </table>
    </div>

    <div class="invoice-totals">
        <div class="invoice-row">
            <span class="label">Subtotal de Productos:</span>
            <span>{{items_subtotal}} {{currency}}</span>
        </div>

        {{if difference}}
        <div class="invoice-row">
            <span class="label">Ajuste/Descuento:</span>
            <span>-{{difference}} {{currency}}</span>
        </div>
        {{endif difference}}

        <div class="invoice-row total-row">
            <span class="label">Monto Total:</span>
            <span>{{amount}} {{currency}}</span>
        </div>

        <div class="invoice-row">
            <span class="label">Comisión PayPal:</span>
            <span>{{paypal_fee}} {{currency}}</span>
        </div>

        <div class="invoice-row">
            <span class="label">Monto Neto Recibido:</span>
            <span>{{net_amount}} {{currency}}</span>
        </div>
    </div>

    {{if shipping}}
    <div class="invoice-section">
        <h3>Información de Envío</h3>
        {{with shipping}}
        <div class="invoice-row">
            <span class="label">Enviar a:</span>
            <span>{{name}}</span>
        </div>
        {{with address}}
        <div class="invoice-row">
            <span class="label">Dirección:</span>
            <div>
                {{line1}}<br>
                {{city}}, {{state}}, {{postal_code}}<br>
                {{country}}
            </div>
        </div>
        {{endwith address}}
        {{endwith shipping}}
    </div>
    {{endif shipping}}

    <div class="invoice-actions">
        <button class="print-button" onclick="window.print()">Imprimir Factura</button>
        <a href="index.php?page=Orders_History" class="return-button">Volver al Historial</a>
    </div>
    {{endwith order}}
</div>
{{endif order}}

{{if error}}
<div class="error-message">
    <h2>¡Ha ocurrido un problema!</h2>
    <p>{{error}}</p>
    <a href="index.php?page=Orders_History" class="return-button">Volver al Historial</a>
</div>
{{endif error}}