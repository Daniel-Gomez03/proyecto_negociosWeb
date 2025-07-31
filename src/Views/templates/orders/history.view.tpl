<section class="orders-history-container">
    <h2>Mi Historial de Órdenes</h2>
    <hr>
    {{if hasOrders}}
    <div class="table-responsive">
        <table class="orders-history-table">
            <thead>
                <tr>
                    <th>ID de la Orden</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                {{foreach orders}}
                <tr>
                    <td>{{order_id}}</td>
                    <td>{{formatted_date}}</td>
                    <td>{{formatted_total}}</td>
                    <td>{{status_display}}</td>
                    <td>
                        <a href="index.php?page=Orders_Detail&id={{order_id}}" class="btn-view-details">
                            <i class="fas fa-eye"></i>  Ver Detalles
                        </a>
                    </td>
                </tr>
                {{endfor orders}}
            </tbody>
        </table>
    </div>
    {{endif hasOrders}}

    {{ifnot hasOrders}}
    <div class="alert-no-orders">
        <h4>¡Aún no has comprado nada!</h4>
        <p>Parece que todavía no has realizado ninguna orden. ¿Qué esperas para armar tu próxima pieza de colección?</p>
        <a href="index.php?page=Checkout_Catalogo" class="btn-go-to-catalog">
            <i class="fas fa-store"></i> Ir al Catálogo
        </a>
    </div>
    {{endifnot hasOrders}}
</section>