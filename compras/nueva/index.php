<?php
include '../../app/conexionBD.php';
include '../../layouts/sesion.php';
include '../../app/controllers/proveedores/activoProveedores.php';

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

                                    <hr>

                                    <!-- Tipo de cálculo -->
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">Tipo de Cálculo</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipoCalculo" id="calculoNormal" value="normal" checked>
                                            <label class="form-check-label" for="calculoNormal">Compra normal</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipoCalculo" id="calculoDescuento" value="descuento">
                                            <label class="form-check-label" for="calculoDescuento">Compra con descuento</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipoCalculo" id="calculoImpuesto" value="impuesto">
                                            <label class="form-check-label" for="calculoImpuesto">Compra con impuesto (IVA)</label>
                                        </div>
                                    </div>

                                    <div class="mb-3" id="bloqueDescuento" style="display:none;">
                                        <label class="form-label fw-bold">Porcentaje de descuento (%)</label>
                                        <input type="number" id="porcentajeDescuento" class="form-control" min="0" max="100" step="0.01" placeholder="0.00">
                                    </div>

                                    <div class="mb-3" id="bloqueImpuesto" style="display:none;">
                                        <label class="form-label fw-bold">Tipo de impuesto</label>
                                        <select id="tipoImpuesto" class="form-select">
                                            <option value="IVA12" selected>IVA 12%</option>
                                            <option value="OTRO">Otro</option>
                                        </select>
                                    </div>

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
                                                    <strong>Stock actual:</strong> <span id="stockDisponible" class="text-danger"></span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Precio de compra</label>
                                                <input type="number" id="precioCompra" class="form-control mb-2" min="0" step="0.01" placeholder="0.00">

                                                <label class="form-label fw-bold">Cantidad</label>
                                                <input type="number" id="cantidadSeleccionada" class="form-control mb-2" placeholder="Ej.: 1" min="1">

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
                                                <th style="width:5%;">#</th>
                                                <th>Producto</th>
                                                <th style="width:10%;">Cant.</th>
                                                <th style="width:15%;">Precio Unitario</th>
                                                <th style="width:15%;">Sub-Total</th>
                                                <th style="width:7%;">Quitar</th>
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

                // ---------- Estado / constantes ----------
                const IVA_12 = 0.12;
                let productoSeleccionado = null;
                let listaCompra = []; // array de {id,nombre,cantidad,precio,descuento,subtotal}
                let porcentajeDescuentoGlobal = 0;
                let tipoImpuesto = IVA_12;

                // ---------- Inicialización Select2 (busquedaproductos) ----------
                $("#buscarProducto").select2({
                    placeholder: 'Escriba nombre o Escanee el código...',
                    minimumInputLength: 1,
                    ajax: {
                        url: '../../app/controllers/productos/buscar_productosCompra.php',
                        dataType: 'json',
                        delay: 200,
                        data: function(params) {
                            return {
                                term: params.term
                            };
                        },
                        processResults: function(data) {
                            // el controlador debe devolver [{id:..., text:..., stock:..., precio:...}, ...]
                            return {
                                results: data
                            };
                        }
                    }
                });

                // ---------- UI helpers ----------
                function formatQ(v) {
                    return 'Q ' + parseFloat(v || 0).toFixed(2);
                }

                function resetProductoInfo() {
                    productoSeleccionado = null;
                    $("#nombreSeleccionado").text('');
                    $("#stockDisponible").text('');
                    $("#precioCompra").val('');
                    $("#cantidadSeleccionada").val('');
                    $("#infoProducto").slideUp();
                }

                function enableGuardar(flag) {
                    $("#btnGuardarCompra").prop('disabled', !flag);
                }

                // ---------- Cálculos ----------
                function calcularSubtotalItem(item, tipoCalculo) {
                    // item.subtotal es precio * cantidad (base)
                    let base = parseFloat(item.subtotal) || 0;
                    let result = base;

                    if (tipoCalculo === 'descuento' && item.descuento > 0) {
                        result = base - (base * (item.descuento / 100));
                    }
                    if (tipoCalculo === 'impuesto' && tipoImpuesto > 0) {
                        result = base + (base * tipoImpuesto);
                    }
                    return Number(result);
                }

                function actualizarTotales() {
                    let tipoCalculo = $("input[name='tipoCalculo']:checked").val();
                    let subtotal = 0;
                    let descuentoQ = 0;

                    listaCompra.forEach(item => {
                        let base = Number(item.subtotal);
                        if (tipoCalculo === 'descuento' && item.descuento > 0) {
                            descuentoQ += base * (item.descuento / 100);
                        }
                        // sumar el subtotal item ajustado
                        subtotal += calcularSubtotalItem(item, tipoCalculo);
                    });

                    let totalPagar = subtotal;

                    // mostrar
                    $("#subtotalCompra").text(formatQ(subtotal));
                    $("#descuentoCompra").text(formatQ(descuentoQ));
                    $("#totalPagar").text(formatQ(totalPagar));

                    enableGuardar(listaCompra.length > 0);
                }

                // ---------- Renderizado tabla ----------
                function renderFila(item, index, tipoCalculo) {
                    const subtotalItem = calcularSubtotalItem(item, tipoCalculo);
                    return `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td class="text-start ps-3">${escapeHtml(item.nombre)}</td>
                            <td class="text-center">${item.cantidad}</td>
                            <td class="text-center">Q ${Number(item.precio).toFixed(2)}</td>
                            <td class="text-center">Q ${subtotalItem.toFixed(2)}</td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm btnQuitar" data-index="${index}" title="Quitar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }

                function actualizarTabla() {
                    const tipoCalculo = $("input[name='tipoCalculo']:checked").val();
                    $("#tablaCompra").empty();
                    listaCompra.forEach((item, index) => {
                        $("#tablaCompra").append(renderFila(item, index, tipoCalculo));
                    });
                }

                // ---------- Util ----------
                function escapeHtml(unsafe) {
                    if (unsafe === null || unsafe === undefined) return '';
                    return String(unsafe)
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;");
                }

                // ---------- Eventos UI ----------
                $("#buscarProducto").on("select2:select", function(e) {
                    productoSeleccionado = e.params.data;
                    $("#nombreSeleccionado").text(productoSeleccionado.text);
                    $("#stockDisponible").text(productoSeleccionado.stock ?? '-');
                    $("#infoProducto").slideDown();
                    // leave cantidad empty so it's placeholder only, user types it
                    $("#cantidadSeleccionada").val("");
                    // if product has a suggested purchase price from search, prefill optionally
                    if (productoSeleccionado.precio) {
                        $("#precioCompra").val(Number(productoSeleccionado.precio).toFixed(2));
                    }
                    // default discount captured from the global at the time of adding
                });

                // cambiar tipo de cálculo (actualiza UI y totales)
                $("input[name='tipoCalculo']").change(function() {
                    const tipo = $(this).val();
                    $("#bloqueDescuento").hide();
                    $("#bloqueImpuesto").hide();

                    if (tipo === "descuento") $("#bloqueDescuento").show();
                    if (tipo === "impuesto") $("#bloqueImpuesto").show();

                    // Re-render tabla y totales porque los subtotales pueden cambiar por tipo.
                    actualizarTabla();
                    actualizarTotales();
                });

                $("#porcentajeDescuento").on("input", function() {
                    porcentajeDescuentoGlobal = parseFloat($(this).val()) || 0;
                    // NOTE: We store discount per item when adding. Changing global now affects new items only.
                    // If you prefer global discount to apply to all items immediately, uncomment below:
                    // listaCompra.forEach(i => i.descuento = porcentajeDescuentoGlobal);
                    actualizarTotales();
                });

                $("#tipoImpuesto").change(function() {
                    tipoImpuesto = ($(this).val() === "IVA12") ? IVA_12 : 0.0;
                    actualizarTotales();
                });

                // Limpiar detalles (precio/cantidad) del panel Agregar Producto
                $("#btnLimpiarDetalles").click(function() {
                    $("#precioCompra").val('');
                    $("#cantidadSeleccionada").val('');
                });

                // Agregar producto (validaciones y agregado)
                $("#btnAgregar").click(function() {
                    if (!productoSeleccionado) {
                        Swal.fire("Seleccionar producto", "Debe seleccionar un producto antes de agregar.", "warning");
                        return;
                    }
                    const cantidad = parseInt($("#cantidadSeleccionada").val(), 10);
                    const precio = parseFloat($("#precioCompra").val());

                    if (isNaN(cantidad) || cantidad <= 0) {
                        Swal.fire("Cantidad inválida", "Ingrese una cantidad válida (mayor que 0).", "warning");
                        return;
                    }
                    if (isNaN(precio) || precio <= 0) {
                        Swal.fire("Precio inválido", "Ingrese un precio válido (mayor que 0).", "warning");
                        return;
                    }

                    const subtotal = Number((precio * cantidad).toFixed(2));

                    // Guardar descuento en el item según el valor global actual (puedes cambiar comportamiento)
                    const descuentoItem = porcentajeDescuentoGlobal || 0;

                    // Evitar duplicar exactamente el mismo producto en la misma compra:
                    // Si ya existe, sumamos cantidades y recalculamos subtotal (comportamiento común).
                    const existingIndex = listaCompra.findIndex(i => i.id === productoSeleccionado.id);
                    if (existingIndex !== -1) {
                        // actualizar item existente
                        listaCompra[existingIndex].cantidad = Number(listaCompra[existingIndex].cantidad) + cantidad;
                        listaCompra[existingIndex].subtotal = Number((listaCompra[existingIndex].precio * listaCompra[existingIndex].cantidad).toFixed(2));
                        // mantener descuento existente o actualizar si quieres: listaCompra[existingIndex].descuento = descuentoItem;
                    } else {
                        // nuevo item
                        listaCompra.push({
                            id: productoSeleccionado.id,
                            nombre: productoSeleccionado.text,
                            cantidad: cantidad,
                            precio: Number(precio.toFixed(2)),
                            descuento: descuentoItem,
                            subtotal: subtotal
                        });
                    }

                    // Limpieza UI
                    actualizarTabla();
                    actualizarTotales();
                    resetProductoInfo();
                    $("#buscarProducto").val(null).trigger("change");
                });

                // Quitar producto por index
                $(document).on("click", ".btnQuitar", function() {
                    const i = parseInt($(this).data("index"), 10);
                    if (!isNaN(i)) {
                        listaCompra.splice(i, 1);
                        actualizarTabla();
                        actualizarTotales();
                    }
                });

                // Limpiar toda la fila (lista de productos)
                $("#btnLimpiarFila").click(function() {
                    if (listaCompra.length === 0) {
                        Swal.fire("Nada que limpiar", "La lista ya está vacía.", "info");
                        return;
                    }
                    Swal.fire({
                        title: '¿Limpiar todos los productos?',
                        text: "Esto eliminará todos los productos agregados.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, limpiar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            listaCompra = [];
                            actualizarTabla();
                            actualizarTotales();
                            Swal.fire('Lista limpiada', '', 'success');
                        }
                    });
                });

                // Guardar compra (ejemplo: prepara payload y lo envía por AJAX)
                $("#btnGuardarCompra").click(function() {

                    if (listaCompra.length === 0) {
                        Swal.fire("Sin productos", "Debe agregar al menos un producto", "warning");
                        return;
                    }

                    const proveedor = $("#proveedor").val();
                    const fecha = $("#fecha").val();

                    if (!proveedor) {
                        Swal.fire("Proveedor requerido", "Seleccione un proveedor.", "warning");
                        return;
                    }

                    // =====================================================
                    //   🔵 AQUI VA TU CÁLCULO ANTES DE ENVIAR CON FETCH 🔵
                    // =====================================================

                    const subtotalGeneral = parseFloat($("#subtotalCompra").text().replace("Q", "")) || 0;
                    const descuentoGeneral = parseFloat($("#descuentoCompra").text().replace("Q", "")) || 0;
                    const totalGeneral = parseFloat($("#totalPagar").text().replace("Q", "")) || 0;

                    let impuestoGeneral = 0;
                    if ($("input[name='tipoCalculo']:checked").val() === "impuesto") {
                        impuestoGeneral = totalGeneral - subtotalGeneral + descuentoGeneral;
                    }

                    // =====================================================
                    //              🔵 ARMAMOS EL PAYLOAD 🔵
                    // =====================================================

                    const payload = {
                        fecha: fecha,
                        tipoDocumento: $("#tipoDocumento").val(),
                        numeroDocumento: $("#numeroDocumento").val(),
                        id_proveedor: proveedor,
                        tipoCalculo: $("input[name='tipoCalculo']:checked").val(),

                        porcentajeDescuentoGlobal: porcentajeDescuentoGlobal,
                        tipoImpuesto: tipoImpuesto,

                        subtotalGeneral: subtotalGeneral,
                        descuentoGeneral: descuentoGeneral,
                        impuestoGeneral: impuestoGeneral,
                        totalGeneral: totalGeneral,

                        productos: listaCompra
                    };

                    // =====================================================
                    //              🔵 CONFIRMACIÓN Y FETCH 🔵
                    // =====================================================

                    Swal.fire({
                        title: 'Confirmar compra',
                        html: `Se registrarán <b>${listaCompra.length}</b> productos.<br>¿Desea continuar?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, guardar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {

                        if (!result.isConfirmed) return;

                        fetch('<?php echo $URL; ?>app/controllers/compras/save_compra.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(payload),
                                credentials: 'same-origin'
                            })
                            .then(resp => resp.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Guardado', data.message, 'success')
                                        .then(() => window.location.reload());
                                } else {
                                    Swal.fire('Error', data.message, 'error');
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
                            });

                    });

                });


                // inicializar estado
                resetProductoInfo();
                actualizarTotales();
                enableGuardar(false);

            }); // end document ready
        </script>
</body>

</html>