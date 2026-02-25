<?php
include '../../app/conexionBD.php';
include '../../layouts/sesion.php';
include '../../app/controllers/clientes/listadoClientes.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Nueva Cotización</title>
    <?php include '../../layouts/head.php'; ?>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <?php include '../../layouts/navAside.php'; ?>

        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid">

                    <div class="card card-outline card-success">
                        <div class="card-header bg-dark text-success text-center fw-bold fs-4">
                            GTQ <span id="totalGeneral">0.00</span>
                        </div>

                        <div class="card-body">

                            <!-- 🔍 Buscar producto -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Buscar o escanear producto</label>
                                <input type="text" id="buscarProducto" class="form-control">
                            </div>

                            <!-- 📦 Tabla -->
                            <div class="table-responsive">
                                <table id="tablaProductos" class="table table-bordered text-center align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th>Stock</th>
                                            <th>Cantidad</th>
                                            <th>Precio Venta</th>
                                            <th>Precio Mayorista</th>
                                            <th>Subtotal Venta</th>
                                            <th>Subtotal Mayorista</th>
                                            <th>Acción</th>

                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                            <!-- 📊 Totales -->
                            <div class="mt-3 d-flex justify-content-end">
                                <table class="table table-bordered w-50 text-center">
                                    <tr>
                                        <th>Artículos</th>
                                        <th>Descuento</th>
                                        <th>Total</th>
                                        <th>Acciones</th>
                                    </tr>
                                    <tr>
                                        <td id="totalArticulos">0</td>
                                        <td id="totalDescuento">Q0.00</td>
                                        <td id="totalFinal">Q0.00</td>
                                        <td>
                                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#guardarCotizacion">
                                                <i class="bi bi-save"></i> Guardar Cotización
                                            </button>

                                            <a class="btn btn-danger" href="<?php echo $URL;?>/cotizaciones/nueva" role="button">Cancelar</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- 🔹 Modal Guardar Cotización -->
                            <div class="modal fade" id="guardarCotizacion" tabindex="-1" aria-labelledby="guardarCotizacionLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-success shadow-lg">
                                        <form id="formGuardarCotizacion" method="POST" action="../../app/controllers/cotizaciones/generar_Cotizacion.php">

                                            <!-- 🟢 Encabezado -->
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title fw-bold" id="guardarCotizacionLabel">
                                                    <i class="bi bi-file-earmark-text"></i> Guardar Cotización
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                            </div>

                                            <!-- 📄 Cuerpo del modal -->
                                            <div class="modal-body">
                                                <div class="container-fluid">

                                                    <!-- 🧾 Información del cliente -->
                                                    <div class="mb-3 pb-2 border-bottom">
                                                        <h6 class="fw-bold text-success mb-3">
                                                            <i class="bi bi-person-lines-fill"></i> Información del Cliente
                                                        </h6>
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label for="id_cliente" class="form-label fw-semibold">Cliente:</label>
                                                                <select name="id_cliente" id="id_cliente" class="form-select" required>
                                                                    <option value="">Seleccione un cliente</option>
                                                                    <?php foreach ($clientes_datos as $cliente): ?>
                                                                        <option value="<?= $cliente['id_cliente']; ?>">
                                                                            <?= htmlspecialchars($cliente['nombre_y_apellido']); ?> - <?= htmlspecialchars($cliente['telefono']); ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="condicion_pago" class="form-label fw-semibold">Condición de Pago:</label>
                                                                <select name="condicion_pago" id="condicion_pago" class="form-select" required>
                                                                    <option value="Contado">Contado</option>
                                                                    <option value="Crédito de 15 Días">Crédito de 15 Días</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- 💵 Información de la cotización -->
                                                    <div class="mb-3 pb-2 border-bottom">
                                                        <h6 class="fw-bold text-success mb-3">
                                                            <i class="bi bi-cash-coin"></i> Detalles de la Cotización
                                                        </h6>

                                                        <div class="row g-3">
                                                            <div class="col-md-4">
                                                                <label for="total" class="form-label fw-semibold">Total (Q):</label>
                                                                <input type="text" name="total" id="total"
                                                                    class="form-control text-end fw-bold bg-light"
                                                                    readonly value="0.00">
                                                            </div>

                                                            <div class="col-md-4">
                                                                <label for="estado" class="form-label fw-semibold">Estado:</label>
                                                                <select name="estado" id="estado" class="form-select" required>
                                                                    <option value="Pendiente" selected>Pendiente</option>
                                                                    <option value="Aceptada">Aceptada</option>
                                                                    <option value="Rechazada">Rechazada</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <label for="fecha" class="form-label fw-semibold">Fecha:</label>
                                                                <input type="text" name="fecha" id="fecha"
                                                                    class="form-control text-center bg-light"
                                                                    value="<?= date('Y-m-d H:i:s'); ?>" readonly>
                                                            </div>
                                                        </div>

                                                        <!-- 🔘 Tipo de Precio -->
                                                        <div class="row mt-3">
                                                            <div class="col-md-12">
                                                                <label class="form-label fw-semibold">Tipo de Precio:</label>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="tipo_precio" id="precioNormal"
                                                                        value="Normal" checked>
                                                                    <label class="form-check-label" for="precioNormal">
                                                                        Precio de Venta
                                                                    </label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="tipo_precio" id="precioMayorista"
                                                                        value="Mayorista">
                                                                    <label class="form-check-label" for="precioMayorista">
                                                                        Precio Mayorista
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- 👤 Información del usuario -->
                                                    <div class="text-end text-muted small">
                                                        <i class="bi bi-person-check"></i> Usuario actual:
                                                        <span class="fw-semibold text-dark">
                                                            <?= $_SESSION['nombre'] ?? '—'; ?>
                                                        </span>
                                                    </div>

                                                    <!-- Campo oculto -->
                                                    <input type="hidden" name="id_usuario" value="<?= $_SESSION['id_usuario']; ?>">
                                                </div>
                                            </div>

                                            <!-- 🔘 Footer -->
                                            <div class="modal-footer d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="bi bi-x-circle"></i> Cancelar
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-check-circle"></i> Guardar Cotización
                                                </button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>

                </div>
            </div>
        </main>

        <?php include '../../layouts/footer.php'; ?>
    </div>

    <script>
        $(function() {

            $('#buscarProducto').select2({
                placeholder: 'Buscar producto',
                minimumInputLength: 1,
                ajax: {
                    url: '../../app/controllers/productos/buscar_productos.php',
                    dataType: 'json',
                    delay: 250,
                    data: p => ({
                        term: p.term
                    }),
                    processResults: d => ({
                        results: d
                    })
                }
            });

            $('#buscarProducto').on('select2:select', function(e) {
                const p = e.params.data;
                if ($('#fila-' + p.id).length) {
                    Swal.fire('Atención', 'Producto ya agregado', 'warning');
                    return;
                }

                const stockClase = p.stock <= 5 ? 'text-danger fw-bold' : 'text-success';

                $('#tablaProductos tbody').append(`
                        <tr id="fila-${p.id}"
                            data-precio-normal="${p.precio}"
                            data-precio-mayorista="${p.precio_mayorista}"
                            data-stock="${p.stock}">
                            <td>${p.codigo}</td>
                         <td>${p.text}</td>
                            <td class="${stockClase}">${p.stock}</td>
                        <td>
                                <input type="number" class="form-control cantidad text-center"
                                      value="1" min="1" max="${p.stock}">
                            </td>
                            <td>Q${parseFloat(p.precio).toFixed(2)}</td>
                        <td>Q${parseFloat(p.precio_mayorista).toFixed(2)}</td>
                            <td class="subtotal-venta">Q0.00</td>
                            <td class="subtotal-mayorista">Q0.00</td>
                            <td>
                               <button class="btn btn-danger btn-sm eliminar">
                                   <i class="bi bi-trash"></i>
                               </button>
                         </td>
                        </tr>
                `);



                $('#buscarProducto').val(null).trigger('change');
                calcular();
            });

            //Ajustar automaticamente la cantidad si excede el stock
            $(document).on('input', '.cantidad', function() {

                const fila = $(this).closest('tr');
                const stock = parseInt(fila.data('stock'));
                let cantidad = parseInt($(this).val());

                if (cantidad > stock) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stock insuficiente',
                        text: `Solo hay ${stock} unidad${stock > 1 ? 'es' : ''} disponibles.`,
                        confirmButtonColor: '#198754'
                    });

                    $(this).val(stock);
                    cantidad = stock;
                }

                if (cantidad < 1 || isNaN(cantidad)) {
                    $(this).val(1);
                }

                calcular();
            });



            $(document).on('click', '.eliminar', function() {
                $(this).closest('tr').remove();
                calcular();
            });
            $(document).on('change', 'input[name="tipo_precio"]', calcular);

            function calcular() {
                let total = 0,
                    items = 0;

                const tipo = $('input[name="tipo_precio"]:checked').val();

                $('#tablaProductos tbody tr').each(function() {
                    const fila = $(this);
                    const cant = parseInt(fila.find('.cantidad').val());

                    const precioVenta = parseFloat(fila.data('precio-normal'));
                    const precioMayorista = parseFloat(fila.data('precio-mayorista'));

                    const subVenta = cant * precioVenta;
                    const subMayorista = cant * precioMayorista;

                    fila.find('.subtotal-venta').text(`Q${subVenta.toFixed(2)}`);
                    fila.find('.subtotal-mayorista').text(`Q${subMayorista.toFixed(2)}`);

                    if (tipo === 'Mayorista') {
                        total += subMayorista;
                    } else {
                        total += subVenta;
                    }

                    items += cant;
                });

                // UX: resaltar subtotal activo
                $('.subtotal-venta, .subtotal-mayorista')
                    .removeClass('fw-bold text-success');

                if (tipo === 'Mayorista') {
                    $('.subtotal-mayorista').addClass('fw-bold text-success');
                } else {
                    $('.subtotal-venta').addClass('fw-bold text-success');
                }

                $('#totalArticulos').text(items);
                $('#totalFinal').text(`Q${total.toFixed(2)}`);
                $('#totalGeneral').text(total.toFixed(2));
                $('#total').val(total.toFixed(2));
            }


        });
    </script>

    <script>
$(function () {

    /* ===============================
       ENVÍO DE PRODUCTOS AL SUBMIT
       =============================== */
    $('#formGuardarCotizacion').on('submit', function (e) {

        // Eliminar productos previos (si reenvían)
        $('input[name="productos"]').remove();

        const productos = [];
        const tipoPrecio = $('input[name="tipo_precio"]:checked').val();

        $('#tablaProductos tbody tr').each(function () {
            const fila = $(this);
            const idProducto = fila.attr('id').replace('fila-', '');
            const cantidad = parseInt(fila.find('.cantidad').val());

            const precioUnitario = (tipoPrecio === 'Mayorista')
                ? parseFloat(fila.data('precio-mayorista'))
                : parseFloat(fila.data('precio-normal'));

            productos.push({
                id_producto: idProducto,
                cantidad: cantidad,
                precio_unitario: precioUnitario
            });
        });

        // Validar que haya productos
        if (productos.length === 0) {
            e.preventDefault();
            Swal.fire('Atención', 'Debe agregar al menos un producto', 'warning');
            return false;
        }

        // Crear input hidden con JSON
        $('<input>', {
            type: 'hidden',
            name: 'productos',
            value: JSON.stringify(productos)
        }).appendTo(this);

    });

});
</script>

<?php include '../../layouts/notificaciones.php';?>
</body>

</html>