<?php
include '../../app/conexionBD.php';
include '../../layouts/sesion.php';
include '../../app/controllers/proveedores/activoProveedores.php';

// Verificar caja abierta  (NO TOCAR)

$sqlCaja = "SELECT * FROM caja 
            WHERE id_Usuarios = :id_usuario 
            AND estado = 'abierta' 
            LIMIT 1";

$stmtCaja = $pdo->prepare($sqlCaja);
$stmtCaja->bindParam(':id_usuario', $_SESSION['id_usuario'], PDO::PARAM_INT);
$stmtCaja->execute();
$caja = $stmtCaja->fetch(PDO::FETCH_ASSOC);

if (!$caja) {
    $_SESSION['titulo'] = 'Caja cerrada';
    $_SESSION['mensaje'] = 'Debe abrir una caja antes de registrar una compra.';
    $_SESSION['icono'] = 'warning';
    header('Location: ' . $URL . 'caja/administrar');
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Nueva Compra</title>
    <?php include '../../layouts/head.php'; ?>

    <!-- Custom small CSS to tune AdminLTE to your mockup (Option B) -->
    <style>
        /* Slightly bolder card headers and clearer spacing like your mockup */
        .card-header {
            border-bottom: 2px solid rgba(0, 0, 0, .08);
            font-weight: 600;
        }

        /* Emphasize the product info box */
        .producto-info {
            border: 2px dashed rgba(0, 0, 0, 0.08);
            padding: 12px;
            border-radius: 6px;
            background: #f8f9fa;
        }

        /* Buttons spacing & sizes like the mockup */
        #btnGuardarCompra {
            min-width: 140px;
        }

        #btnLimpiarFila {
            min-width: 120px;
        }

        .table thead th {
            vertical-align: middle;
            text-align: center;
        }

        .table tbody td {
            vertical-align: middle;
            text-align: center;
        }

        /* Right column totals card narrower and aligned visually */
        .totals-card td:first-child {
            text-align: right;
            padding-right: 10px;
        }

        .totals-card td:last-child {
            text-align: right;
            padding-left: 10px;
            font-weight: 700;
        }

        /* Small responsive adjustments */
        @media (max-width: 768px) {
            .producto-info {
                font-size: 14px;
            }

            #btnAgregar {
                margin-top: .5rem;
            }
        }
    </style>

</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <?php include '../../layouts/navAside.php'; ?>

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Nueva Compra</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo $URL ?>">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Compras</li>
                                <li class="breadcrumb-item active" aria-current="page">Nueva Compra</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">

                    <div class="row g-3">

                        <!-- ================ Left column: Datos de la Compra (card) ================ -->
                        <div class="col-lg-4 col-md-5">
                            <div class="card shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">Datos de la Compra</h5>
                                </div>
                                <div class="card-body">

                                    <div class="mb-3">
                                        <label for="buscarProducto" class="form-label fw-bold">Buscar o escanear producto</label>
                                        <select id="buscarProducto" class="form-select" style="width: 100%;">
                                            <option value="">Escriba nombre o código...</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Proveedor</label>
                                        <select id="proveedor" class="form-select">
                                            <option value="" selected disabled> --Seleccione proveedor --</option>
                                            <?php if (!empty($proveedores_activos)): ?>
                                                <?php foreach ($proveedores_activos as $proveedores): ?>
                                                    <option value="<?= htmlspecialchars($proveedores['id_proveedor']) ?>">
                                                        <?= htmlspecialchars($proveedores['nombre_empresa']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="" disabled>No hay Proveedores activos</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Fecha</label>
                                        <input type="date" id="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tipo Documento</label>
                                        <select id="tipoDocumento" class="form-select">
                                            <option value="FACTURA">Factura</option>
                                            <option value="TICKET">Ticket</option>
                                            <option value="RECIBO">Recibo</option>
                                            <option value="OTRO">Otro</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">No. Documento</label>
                                        <input type="text" id="numeroDocumento" class="form-control" placeholder="000123">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tipo de Compra</label><br>

                                        <input type="radio" name="tipoCompra" value="normal" checked> Compra Normal<br>
                                        <input type="radio" name="tipoCompra" value="descuento"> Compra con descuento
                                    </div>

                                    <div class="mb-3" id="bloqueDescuento" style="display:none;">
                                        <label class="form-label fw-bold">Descuento (%)</label>
                                        <input type="number" id="porcentajeDescuento" class="form-control" min="0" max="100" value="0">
                                    </div>

                                    <hr>



                                </div>
                            </div>
                        </div>

                        <!-- ================ Right column: Agregar Producto + Tabla + Totales ================ -->
                        <div class="col-lg-8 col-md-7">

                            <!-- Card: Agregar Producto -->
                            <div class="card shadow-sm">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="card-title mb-0">Agregar Producto</h5>
                                </div>
                                <div class="card-body">

                                    <div id="infoProducto" style="display:none;">
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <div class="producto-info">
                                                    <strong>Producto:</strong> <span id="nombreSeleccionado"></span><br>
                                                    <strong>Código:</strong> <span id="codigoProducto"></span><br>
                                                    <strong>Stock actual:</strong> <span id="stockDisponible" class="text-danger"></span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Precio de compra</label>
                                                <input type="number" id="precioCompra" class="form-control mb-2" min="0" step="0.01" readonly>

                                                <label class="form-label fw-bold">Cantidad</label>
                                                <input type="number" id="cantidadSeleccionada" class="form-control mb-2" placeholder="Ej.: 1" min="1">

                                                <label class="form-label fw-bold">Precio de venta</label>
                                                <input type="number" id="precioVenta" class="form-control mb-2" min="0" step="0.01">

                                                <label class="form-label fw-bold">Precio mayorista</label>
                                                <input type="number" id="precioMayorista" class="form-control mb-2" min="0" step="0.01">

                                                <div class="d-flex gap-2 mt-2">
                                                    <button class="btn btn-success flex-grow-1" id="btnAgregar">Agregar a la compra</button>
                                                    <button class="btn btn-outline-warning flex-grow-1" id="btnLimpiarDetalles">Limpiar Detalles</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Card: Productos Agregados (tabla) -->
                            <div class="card mt-3 shadow-sm">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="card-title mb-0">Productos Agregados</h5>
                                </div>
                                <div class="card-body table-responsive p-2">
                                    <table class="table table-bordered table-striped align-middle">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>#</th>
                                                <th>Producto</th>
                                                <th>Cant.</th>
                                                <th>Precio Compra</th>
                                                <th>Precio Venta</th>
                                                <th>Precio Mayorista</th>
                                                <th>Sub-Total</th>
                                                <th>Quitar</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tablaCompra"></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Card: Totales + Acciones -->
                            <div class="card mt-3 shadow-sm">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless totals-card">
                                                <tbody>
                                                    <tr>
                                                        <td>Subtotal:</td>
                                                        <td id="subtotalCompra">Q 0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Descuento:</td>
                                                        <td id="descuentoCompra">Q 0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total a Pagar:</td>
                                                        <td id="totalPagar">Q 0.00</td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <div class="d-flex justify-content-end gap-2 mt-3">
                                                <a href="<?php echo $URL; ?>compras/nueva" class="btn btn-danger">Cancelar</a>
                                                <button id="btnLimpiarFila" class="btn btn-warning">Limpiar Fila</button>
                                                <button id="btnGuardarCompra" class="btn btn-success">Guardar Compra</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div> <!-- end right column -->

                    </div> <!-- end row -->

                </div> <!-- container-fluid -->
            </div> <!-- app-content -->
        </main>

        <?php include '../../layouts/footer.php'; ?>

        <!-- ========================= JavaScript limpio y organizado ========================= -->
        <script>
            $(function() {

                let productoSeleccionado = null;
                let listaCompra = [];

                let tipoCompra = "normal";
                let porcentajeDescuento = 0;

                function formatQ(v) {
                    return 'Q ' + parseFloat(v || 0).toFixed(2);
                }

                function resetProductoInfo() {
                    productoSeleccionado = null;
                    $("#nombreSeleccionado").text('');
                    $("#stockDisponible").text('');
                    $("#precioCompra").val('');
                    $("#precioVenta").val('');
                    $("#precioMayorista").val('');
                    $("#cantidadSeleccionada").val('');
                    $("#infoProducto").slideUp();
                    $("#codigoProducto").text('');
                }

                function enableGuardar(flag) {
                    $("#btnGuardarCompra").prop('disabled', !flag);
                }

                function actualizarTotales() {
                    let subtotal = 0;

                    listaCompra.forEach(item => {
                        subtotal += item.precio * item.cantidad;
                    });

                    let descuentoQ = 0;

                    if (tipoCompra === "descuento") {
                        descuentoQ = subtotal * (porcentajeDescuento / 100);
                    }

                    let total = subtotal - descuentoQ;

                    $("#subtotalCompra").text(formatQ(subtotal));
                    $("#descuentoCompra").text(formatQ(descuentoQ));
                    $("#totalPagar").text(formatQ(total));

                    enableGuardar(listaCompra.length > 0);
                }



                function renderFila(item, index) {
                    return `
            <tr>
                <td>${index + 1}</td>
                <td>${item.nombre}</td>
                <td>${item.cantidad}</td>
                <td>Q ${item.precio.toFixed(2)}</td>
                <td>Q ${item.precio_venta.toFixed(2)}</td>
                <td>Q ${item.precio_mayorista.toFixed(2)}</td>
                <td>Q ${(item.precio * item.cantidad).toFixed(2)}</td>
                <td>
                    <button class="btn btn-danger btn-sm btnQuitar" data-index="${index}">
                        🗑
                    </button>
                </td>
            </tr>
        `;
                }

                function actualizarTabla() {
                    $("#tablaCompra").empty();
                    listaCompra.forEach((item, index) => {
                        $("#tablaCompra").append(renderFila(item, index));
                    });
                }

                // SELECT2
                $("#buscarProducto").select2({
                    placeholder: 'Escriba nombre o código...',
                    minimumInputLength: 1,
                    ajax: {
                        url: '../../app/controllers/productos/buscar_productosCompra.php',
                        dataType: 'json',
                        delay: 200,
                        data: params => ({
                            term: params.term
                        }),
                        processResults: data => ({
                            results: data
                        })
                    }
                });

                //EVENTO INPUT DESCUENTO
                $("#porcentajeDescuento").on("input", function() {
                    porcentajeDescuento = parseFloat($(this).val()) || 0;
                    actualizarTotales();
                });

                //EVENTO CHECKBOX
                $("input[name='tipoCompra']").change(function() {
                    tipoCompra = $(this).val();

                    if (tipoCompra === "descuento") {
                        $("#bloqueDescuento").show();
                    } else {
                        $("#bloqueDescuento").hide();
                        porcentajeDescuento = 0;
                        $("#porcentajeDescuento").val(0);
                    }

                    actualizarTotales();
                });

                // SELECCIONAR PRODUCTO
                $("#buscarProducto").on("select2:select", function(e) {
                    productoSeleccionado = e.params.data;

                    $("#nombreSeleccionado").text(productoSeleccionado.text);
                    $("#codigoProducto").text(productoSeleccionado.codigo ?? '-');
                    $("#stockDisponible").text(productoSeleccionado.stock ?? '-');

                    $("#infoProducto").slideDown();

                    $("#cantidadSeleccionada").val("");

                    $("#precioCompra").val(
                        productoSeleccionado.precio ?
                        Number(productoSeleccionado.precio).toFixed(2) :
                        ''
                    );

                    $("#precioVenta").val(
                        productoSeleccionado.precio_venta ?
                        Number(productoSeleccionado.precio_venta).toFixed(2) :
                        ''
                    );

                    $("#precioMayorista").val(
                        productoSeleccionado.precio_mayorista ?
                        Number(productoSeleccionado.precio_mayorista).toFixed(2) :
                        ''
                    );
                });

                // LIMPIAR DETALLES
                $("#btnLimpiarDetalles").click(function() {
                    $("#precioCompra").val('');
                    $("#cantidadSeleccionada").val('');
                    $("#precioVenta").val('');
                    $("#precioMayorista").val('');
                });

                // AGREGAR PRODUCTO
                $("#btnAgregar").click(function() {

                    if (!productoSeleccionado) {
                        Swal.fire("Seleccionar producto", "Debe seleccionar un producto.", "warning");
                        return;
                    }

                    const cantidad = parseInt($("#cantidadSeleccionada").val());
                    const precio = parseFloat($("#precioCompra").val());
                    const precioVenta = parseFloat($("#precioVenta").val());
                    const precioMayorista = parseFloat($("#precioMayorista").val());

                    if (isNaN(cantidad) || cantidad <= 0) {
                        Swal.fire("Cantidad inválida", "Ingrese una cantidad válida.", "warning");
                        return;
                    }

                    if (!precio || precio <= 0) {
                        Swal.fire("Precio inválido", "Ingrese precio de compra válido.", "warning");
                        return;
                    }

                    if (!precioVenta || precioVenta <= 0) {
                        Swal.fire("Precio venta inválido", "Ingrese precio de venta válido.", "warning");
                        return;
                    }

                    if (!precioMayorista || precioMayorista <= 0) {
                        Swal.fire("Precio mayorista inválido", "Ingrese precio mayorista válido.", "warning");
                        return;
                    }
                    const index = listaCompra.findIndex(p => p.id == productoSeleccionado.id);

                    if (index !== -1) {
                        listaCompra[index].cantidad += cantidad;
                    } else {
                        listaCompra.push({
                            id: productoSeleccionado.id,
                            nombre: productoSeleccionado.text,
                            cantidad,
                            precio,
                            precio_venta: precioVenta,
                            precio_mayorista: precioMayorista
                        });
                    }

                    actualizarTabla();
                    actualizarTotales();
                    resetProductoInfo();
                    $("#buscarProducto").val(null).trigger("change");
                });

                // QUITAR PRODUCTO
                $(document).on("click", ".btnQuitar", function() {
                    const i = $(this).data("index");
                    listaCompra.splice(i, 1);
                    actualizarTabla();
                    actualizarTotales();
                });

                // LIMPIAR TODO
                $("#btnLimpiarFila").click(function() {
                    if (listaCompra.length === 0) return;

                    Swal.fire({
                        title: '¿Limpiar productos?',
                        icon: 'warning',
                        showCancelButton: true
                    }).then(r => {
                        if (r.isConfirmed) {
                            listaCompra = [];
                            actualizarTabla();
                            actualizarTotales();
                        }
                    });
                });

                // GUARDAR COMPRA
                $("#btnGuardarCompra").click(function() {

                    if (listaCompra.length === 0) {
                        Swal.fire("Sin productos", "Agregue productos", "warning");
                        return;
                    }

                    const proveedor = $("#proveedor").val();
                    if (!proveedor) {
                        Swal.fire("Proveedor requerido", "Seleccione proveedor", "warning");
                        return;
                    }

                    let subtotal = listaCompra.reduce((acc, i) => acc + (i.precio * i.cantidad), 0);

                    let descuentoQ = (tipoCompra === "descuento") ?
                        subtotal * (porcentajeDescuento / 100) :
                        0;

                    let total = subtotal - descuentoQ;

                    const payload = {
                        fecha: $("#fecha").val(),
                        tipoDocumento: $("#tipoDocumento").val(),
                        numeroDocumento: $("#numeroDocumento").val(),
                        id_proveedor: proveedor,

                        tipoCompra: tipoCompra,
                        porcentaje_descuento: porcentajeDescuento,

                        subtotal: subtotal,
                        descuento: descuentoQ,
                        totalGeneral: total,

                        productos: listaCompra
                    };

                    Swal.fire({
                        title: 'Confirmar compra',
                        showCancelButton: true
                    }).then(r => {

                        if (!r.isConfirmed) return;

                        fetch('<?php echo $URL; ?>app/controllers/compras/save_compra.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(payload)
                            })
                            .then(r => r.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Guardado', data.message, 'success')
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire('Error', data.message, 'error');
                                }
                            })
                            .catch(error => {
                                console.error("Error en fetch:", error);
                                Swal.fire('Error', 'Ocurrió un error inesperado', 'error');
                            });

                    });
                });

                resetProductoInfo();
                actualizarTotales();
                enableGuardar(false);

            });
        </script>
</body>

</html>