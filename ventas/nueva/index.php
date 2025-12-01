<?php
include '../../app/conexionBD.php';
include '../../layouts/sesion.php';
include '../../app/controllers/clientes/listadoClientes.php';

// Verificar caja abierta
$sqlCaja = "SELECT * FROM Caja WHERE estado = 'abierta' AND id_usuario = :id_usuario LIMIT 1";
$stmtCaja = $pdo->prepare($sqlCaja);
$stmtCaja->bindParam(':id_usuario', $_SESSION['id_usuario'], PDO::PARAM_INT);
$stmtCaja->execute();
$caja = $stmtCaja->fetch(PDO::FETCH_ASSOC);

if (!$caja) {
    $_SESSION['titulo'] = 'Caja cerrada';
    $_SESSION['mensaje'] = 'Debe abrir una caja antes de registrar una venta.';
    $_SESSION['icono'] = 'warning';
    header('Location: ' . $URL . 'caja/administrar');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Nueva Venta</title>
    <?php include '../../layouts/head.php'; ?>

    <style>
        #tablaProductos input.form-control {
            min-width: 60px;
            max-width: 120px;
            width: 100%;
            font-size: 0.9rem;
        }

        #tablaProductos td,
        #tablaProductos th {
            white-space: nowrap;
            vertical-align: middle;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 767.98px) {

            #tablaProductos th,
            #tablaProductos td {
                padding: .35rem;
                font-size: .8rem;
            }

            .acciones-venta {
                flex-direction: column;
                gap: .5rem;
            }

            .acciones-venta .btn {
                width: 100%;
            }
        }
    </style>

</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <?php include '../../layouts/navAside.php'; ?>

        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid">

                    <div class="card card-outline card-success shadow-lg">
                        <div class="card-header bg-dark text-success text-center fw-bold fs-4">
                            GTQ <span id="totalGeneral">0.00</span>
                        </div>

                        <div class="card-body">

                            <!-- Buscar producto -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Buscar o escanear producto</label>
                                <input type="text" id="buscarProducto" class="form-control" placeholder="Escriba nombre o código...">
                            </div>

                            <!-- Tabla productos -->
                            <div class="table-responsive">
                                <table id="tablaProductos" class="table table-bordered table-sm text-center align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th>Stock</th>
                                            <th>Cantidad</th>
                                            <th>Precio</th>
                                            <th>Descuento</th>
                                            <th>Subtotal</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                            <!-- Totales -->
                            <div class="mt-3 d-flex justify-content-end">
                                <table class="table table-bordered text-center">
                                    <tr class="table-success">
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
                                            <div class="d-flex acciones-venta justify-content-center">
                                                <button type="button" id="btnGuardarVenta" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#guardarVentaModal" disabled>
                                                    <i class="bi bi-save"></i> Guardar
                                                </button>

                                                <button type="button" class="btn btn-warning" id="btnLimpiarLista">
                                                    <i class="bi bi-trash3"></i> Limpiar
                                                </button>

                                                <button type="button" class="btn btn-danger" id="btnCancelarVenta">
                                                    <i class="bi bi-x-circle"></i> Cancelar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Modal Guardar Venta -->
                            <div class="modal fade" id="guardarVentaModal" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-success shadow-lg">

                                        <form id="formGuardarVenta" method="POST" action="../../app/controllers/ventas/generar_Ventas.php">

                                            <!-- Header -->
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title fw-bold">
                                                    <i class="bi bi-cart-check"></i> Guardar Venta
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="container-fluid">

                                                    <!-- Cliente -->
                                                    <div class="mb-3 pb-2 border-bottom">
                                                        <h6 class="fw-bold text-success">
                                                            <i class="bi bi-person-lines-fill"></i> Datos del Cliente
                                                        </h6>

                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold">Cliente:</label>
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
                                                                <label class="form-label fw-semibold">Condición de Pago:</label>
                                                                <select name="condicion_pago" id="condicion_pago" class="form-select" required>
                                                                    <option value="Contado">Contado</option>
                                                                    <option value="Crédito de 15 Días">Crédito de 15 Días</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Totales y Pago -->
                                                    <div class="row g-3">

                                                        <!-- Total a Pagar -->
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Total (Q):</label>
                                                            <input type="text" name="total" id="total" class="form-control text-end fw-bold bg-light" readonly>
                                                        </div>

                                                        <!-- Efectivo Recibido -->
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Efectivo Recibido (Q):</label>
                                                            <input type="number" name="efectivo_recibido" id="efectivo_recibido"
                                                                class="form-control text-end fw-bold" step="0.01" min="0" required>
                                                        </div>

                                                        <!-- Cambio -->
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Cambio (Q):</label>
                                                            <input type="text" name="cambio" id="cambio"
                                                                class="form-control text-end fw-bold bg-light" readonly>
                                                        </div>
                                                    </div>

                                                    <br>

                                                    <!-- Fecha + Usuario -->
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Fecha:</label>
                                                            <input type="text" class="form-control bg-light text-center" readonly
                                                                value="<?= date('Y-m-d H:i:s'); ?>" name="fecha">
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Usuario:</label>
                                                            <input class="form-control bg-light" readonly value="<?= $_SESSION['nombre']; ?>">
                                                        </div>
                                                    </div>

                                                    <!-- Hidden Inputs -->
                                                    <input type="hidden" name="id_usuario" value="<?= $_SESSION['id_usuario']; ?>">
                                                    <input type="hidden" name="id_caja" value="<?= $caja['id_caja']; ?>">
                                                    <input type="hidden" name="productos" id="productos_oculto">

                                                </div>
                                            </div>

                                            <div class="modal-footer d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="bi bi-x-circle"></i> Cancelar
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-check-circle"></i> Confirmar Venta
                                                </button>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>


                        </div><!-- card-body -->
                    </div><!-- card -->

                </div>
            </div>
        </main>

        <?php include '../../layouts/footer.php'; ?>
        <?php include '../../layouts/notificaciones.php'; ?>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const totalInput = document.getElementById("total");
            const efectivoInput = document.getElementById("efectivo_recibido");
            const cambioInput = document.getElementById("cambio");

            function calcularCambio() {
                let total = parseFloat(totalInput.value) || 0;
                let recibido = parseFloat(efectivoInput.value) || 0;

                let cambio = recibido - total;
                cambioInput.value = cambio.toFixed(2);
            }

            efectivoInput.addEventListener("input", calcularCambio);
        });
    </script>


    <!-- Script -->
    <script>
        $(document).ready(function() {

            // ======================= BUSCAR PRODUCTOS AJAX =======================
            $('#buscarProducto').select2({
                placeholder: 'Escriba nombre o código...',
                minimumInputLength: 1,
                ajax: {
                    url: '../../app/controllers/productos/buscar_productos.php',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        term: params.term
                    }),
                    processResults: data => ({
                        results: data
                    }),
                    cache: true
                },
                width: '100%'
            });

            // ======================= FUNCIONES BASE =======================
            function actualizarEstadoBotones() {
                $('#btnGuardarVenta').prop('disabled',
                    $('#tablaProductos tbody tr').length === 0
                );
            }

            function calcularTotales() {
                let totalArt = 0,
                    totalDesc = 0,
                    totalFinal = 0;

                $('#tablaProductos tbody tr').each(function() {
                    const f = $(this);
                    const precio = parseFloat(f.data('price')) || 0;
                    const cantidad = parseInt(f.find('.cantidad').val()) || 0;
                    const desc = parseFloat(f.find('.descuento').val()) || 0;

                    const subtotal = (precio - desc) * cantidad;

                    totalArt += cantidad;
                    totalDesc += desc * cantidad;
                    totalFinal += subtotal;

                    f.find('.subtotal').text('Q' + subtotal.toFixed(2));
                });

                $('#totalArticulos').text(totalArt);
                $('#totalDescuento').text('Q' + totalDesc.toFixed(2));
                $('#totalFinal').text('Q' + totalFinal.toFixed(2));
                $('#totalGeneral').text(totalFinal.toFixed(2));
                $('#total').val(totalFinal.toFixed(2));

                actualizarEstadoBotones();
            }

            // ======================= AGREGAR PRODUCTOS =======================
            $('#buscarProducto').on('select2:select', function(e) {
                const p = e.params.data;

                if ($('#fila-' + p.id).length) {
                    Swal.fire('Atención', 'Este producto ya está en la lista.', 'warning');
                    return;
                }

                const colorStock = p.stock <= 5 ? 'text-danger fw-bold' : 'text-success';

                const fila = `
            <tr id="fila-${p.id}" data-price="${p.precio}" data-stock="${p.stock}">
                <td>${p.codigo}</td>
                <td class="text-start">${p.text}</td>
                <td class="${colorStock}">${p.stock}</td>
                <td><input type="number" class="form-control cantidad text-center" value="1" min="1" max="${p.stock}"></td>
                <td>Q${parseFloat(p.precio).toFixed(2)}</td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Q</span>
                        <input type="number" class="form-control descuento" value="0" min="0" max="${p.precio}" step="0.01">
                    </div>
                </td>
                <td class="subtotal">Q${parseFloat(p.precio).toFixed(2)}</td>
                <td><button type="button" class="btn btn-danger btn-sm eliminar"><i class="bi bi-trash"></i></button></td>
            </tr>
        `;

                $('#tablaProductos tbody').append(fila);
                $('#buscarProducto').val(null).trigger('change');

                calcularTotales();
            });

            // ======================= ELIMINAR PRODUCTO =======================
            $(document).on('click', '.eliminar', function() {
                $(this).closest('tr').remove();
                calcularTotales();
            });

            // ======================= CAMBIOS EN CANTIDAD / DESCUENTO =======================
            $(document).on('input change', '.cantidad, .descuento', function() {
                calcularTotales();
            });

            // ======================= LIMPIAR LISTA =======================
            $('#btnLimpiarLista').on('click', function() {
                if ($('#tablaProductos tbody tr').length === 0) {
                    Swal.fire('Atención', 'No hay productos.', 'info');
                    return;
                }

                Swal.fire({
                    title: '¿Limpiar lista?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí'
                }).then(r => {
                    if (r.isConfirmed) {
                        $('#tablaProductos tbody').empty();
                        calcularTotales();
                    }
                });
            });

            // ======================= CANCELAR =======================
            $('#btnCancelarVenta').on('click', function() {
                Swal.fire({
                    title: '¿Cancelar venta?',
                    icon: 'question',
                    showCancelButton: true
                }).then(r => {
                    if (r.isConfirmed) {
                        window.location.href = '<?= $URL; ?>ventas/nueva';
                    }
                });
            });

            // ======================= ENVIAR FORMULARIO =======================
            $('#formGuardarVenta').on('submit', function(e) {
                const productos = [];

                $('#tablaProductos tbody tr').each(function() {
                    const f = $(this);
                    productos.push({
                        id_producto: f.attr('id').replace('fila-', ''),
                        cantidad: parseInt(f.find('.cantidad').val()) || 0,
                        precio_unitario: parseFloat(f.data('price')) || 0,
                        descuento: parseFloat(f.find('.descuento').val()) || 0
                    });
                });

                if (productos.length === 0) {
                    e.preventDefault();
                    Swal.fire('Debe agregar productos.', '', 'warning');
                    return;
                }

                $('#productos_oculto').val(JSON.stringify(productos));
            });

            calcularTotales();
        });
    </script>

</body>

</html>