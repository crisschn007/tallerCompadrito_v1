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
                            <!-- Input con Select2 -->
                            <div class="mb-3">
                                <label for="buscarProducto" class="form-label fw-bold">Buscar o escanear producto</label>
                                <input type="text" id="buscarProducto" class="form-control" placeholder="Escriba nombre o código...">
                            </div>

                            <!-- Tabla de productos -->
                            <div class="table-responsive">
                                <table id="tablaProductos" class="table table-bordered text-center align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th>Stock</th>
                                            <th>Cantidad</th>
                                            <th>Precio</th>
                                            <th>Descuento</th>
                                            <th>SubTotal</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                            <!-- Totales -->
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

                                            <!-- 🔹 Botón para abrir el modal -->
                                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#guardarCotizacion">
                                                <i class="bi bi-save"></i> Guardar Cotización
                                            </button>


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
                                                                        <h6 class="fw-bold text-success mb-3"><i class="bi bi-person-lines-fill"></i> Información del Cliente</h6>
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
                                                                        <h6 class="fw-bold text-success mb-3"><i class="bi bi-cash-coin"></i> Detalles de la Cotización</h6>
                                                                        <div class="row g-3">
                                                                            <div class="col-md-4">
                                                                                <label for="total" class="form-label fw-semibold">Total (Q):</label>
                                                                                <input type="text" name="total" id="total" class="form-control text-end fw-bold bg-light" readonly value="0.00">
                                                                            </div>
                                                                            <!-- 🔽 Campo de Estado (ajustado a select) -->
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
                                                                                <input type="text" name="fecha" id="fecha" class="form-control text-center bg-light"
                                                                                    value="<?= date('Y-m-d H:i:s'); ?>" readonly>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- 👤 Información del usuario -->
                                                                    <div class="text-end text-muted small">
                                                                        <i class="bi bi-person-check"></i> Usuario actual:
                                                                        <span class="fw-semibold text-dark"><?= $_SESSION['nombre_usuario'] ?? '—'; ?></span>
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



                                            <a href="<?php echo $URL; ?>cotizaciones/nueva" class="btn btn-danger" tabindex="-1" role="button">Cancelar</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <?php include '../../layouts/footer.php'; ?>
    </div>

    <!-- Script principal -->
    <script>
        $(document).ready(function() {

            // Inicializar Select2 con AJAX
            $('#buscarProducto').select2({
                placeholder: 'Escriba nombre o código...',
                minimumInputLength: 1,
                ajax: {
                    url: '../../app/controllers/productos/buscar_productos.php',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            // Al seleccionar un producto
            $('#buscarProducto').on('select2:select', function(e) {
                const producto = e.params.data;

                if ($('#fila-' + producto.id).length) {
                    Swal.fire('Atención', 'Este producto ya está en la lista.', 'warning');
                    return;
                }

                const colorStock = producto.stock <= 5 ? 'text-danger fw-bold' : 'text-success';
                const fila = ` <tr id="fila-${producto.id}" data-price="${parseFloat(producto.precio)}" data-stock="${parseInt(producto.stock,10)}">
                    <td>${producto.codigo}</td>
                    <td>${producto.text}</td>
                    <td class="${colorStock}">${producto.stock}</td>
                    <td><input type="number" class="form-control cantidad text-center" value="1" min="1" max="${producto.stock}" step="1" /></td>
                    <td class="precio-text">Q${parseFloat(producto.precio).toFixed(2)}</td>
                    <td>
                    <div class="input-group input-group-sm">
                    <span class="input-group-text">Q</span>
                    <input type="number" class="form-control descuento text-center" value="0" min="0" max="${parseFloat(producto.precio)}" step="0.01" />
                    </div>
                    </td>
                    <td class="subtotal">Q${parseFloat(producto.precio).toFixed(2)}</td>
                    <td><button class="btn btn-danger btn-sm eliminar"><i class="bi bi-trash"></i></button></td>
                    </tr>`;
                $('#tablaProductos tbody').append(fila);
                $('#buscarProducto').val(null).trigger('change.select2');
                calcularTotales();
            });

            // Eliminar producto
            $(document).on('click', '.eliminar', function() {
                $(this).closest('tr').remove();
                calcularTotales();
            });

            // Recalcular subtotal
            $(document).on('input', '.cantidad, .descuento', function() {
                const fila = $(this).closest('tr');
                const cantidadInput = fila.find('.cantidad');
                const cantidad = parseFloat(cantidadInput.val()) || 0;
                const descuento = parseFloat(fila.find('.descuento').val()) || 0;
                const precio = parseFloat(fila.find('td:eq(4)').text().replace('Q', '')) || 0;
                const maxStock = parseFloat(cantidadInput.attr('max')) || 0;

                if (cantidad > maxStock) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stock insuficiente',
                        text: `Solo hay ${maxStock} unidad${maxStock > 1 ? 'es' : ''} disponibles.`,
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#198754'
                    });
                    cantidadInput.val(maxStock);
                }

                const subtotal = ((precio - descuento) * cantidadInput.val()).toFixed(2);
                fila.find('.subtotal').text(`Q${subtotal}`);
                calcularTotales();
            });

            // Calcular totales
            function calcularTotales() {
                let totalArticulos = 0,
                    totalDescuento = 0,
                    totalFinal = 0;

                $('#tablaProductos tbody tr').each(function() {
                    const fila = $(this);
                    const precio = parseFloat(fila.data('price')) || 0;
                    const cantidad = parseInt(fila.find('.cantidad').val(), 10) || 0;
                    const descuento = parseFloat(fila.find('.descuento').val()) || 0;

                    const subtotal = (precio - descuento) * cantidad;
                    totalArticulos += cantidad;
                    totalDescuento += descuento * cantidad;
                    totalFinal += subtotal;
                });

                $('#totalArticulos').text(totalArticulos);
                $('#totalDescuento').text(`Q${totalDescuento.toFixed(2)}`);
                $('#totalFinal').text(`Q${totalFinal.toFixed(2)}`);
                $('#totalGeneral').text(totalFinal.toFixed(2));

                // ✅ Sincroniza el total dentro del modal automáticamente
                $('#total').val(totalFinal.toFixed(2));
            }

        });
    </script>

    <script>
        // ✅ Al enviar el formulario, agregamos los productos en un campo oculto
        $('#formGuardarCotizacion').on('submit', function(e) {
            e.preventDefault();

            const productos = [];
            $('#tablaProductos tbody tr').each(function() {
                const fila = $(this);
                productos.push({
                    id_producto: fila.attr('id').replace('fila-', ''),
                    cantidad: parseInt(fila.find('.cantidad').val(), 10) || 0,
                    precio_unitario: parseFloat(fila.data('price')) || 0
                });
            });

            if (productos.length === 0) {
                Swal.fire('Atención', 'Debe agregar al menos un producto.', 'warning');
                return;
            }

            const formData = new FormData(this);
            formData.append('productos', JSON.stringify(productos));

            $.ajax({
                url: '<?php echo $URL; ?>app/controllers/cotizaciones/generar_Cotizacion.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json', // ✅ jQuery convierte la respuesta automáticamente

                success: function(data) {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cotización guardada',
                            text: data.message,
                            confirmButtonColor: '#198754'
                        }).then(() => {
                            window.location.href = '<?php echo $URL; ?>cotizaciones/nueva';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Ocurrió un problema al guardar la cotización.'
                        });
                    }
                },

                error: function(xhr, status, error) {
                    console.error('Error AJAX:', xhr.responseText);
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                }
            });

        });
    </script>

</body>

</html>