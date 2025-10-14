<?php
include '../app/conexionBD.php';
include '../layouts/sesion.php';
include '../app/controllers/productos/listado_productos.php'; // Consulta de productos

// Cargar categorías activas
if (file_exists('../app/controllers/categorias/activoCate.php')) {
  require_once '../app/controllers/categorias/activoCate.php';
} else {
  die("<div class='alert alert-danger'>Archivo de categorías no encontrado</div>");
}

// Librería de código de barras
require_once '../vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <title>Productos</title>
  <?php include '../layouts/head.php'; ?>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
  <div class="app-wrapper">
    <?php include '../layouts/navAside.php'; ?>

    <main class="app-main">
      <div class="app-content-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-6">
              <h3 class="mb-0">Productos</h3>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="<?= $URL ?>">Inicio</a></li>
                <li class="breadcrumb-item active">Productos</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <div class="app-content">
        <div class="container-fluid">

          <div class="card card-outline card-secondary">
            <div class="card-header d-flex justify-content-between align-items-center">
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarProducto">
                <i class="bi bi-cart-plus"></i> Agregar Producto
              </button>
            </div>

            <div class="card-body">
              <div class="table-responsive overflow-auto">
                <table class="table table-bordered table-hover align-middle text-center w-100" id="Productos">
                  <thead class="table-dark">
                    <tr>
                      <th>#</th>
                      <th>Código</th>
                      <th>Imagen</th>
                      <th>Nombre</th>
                      <th>Precio</th>
                      <th>Stock</th>
                      <th>Categoría</th>
                      <th>Código de Barras</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($producto_datos)): ?>
                      <?php $i = 1; ?>
                      <?php foreach ($producto_datos as $p): ?>
                        <tr>
                          <td><?= $i++; ?></td>
                          <td><?= htmlspecialchars($p['codigo_barras']); ?></td>

                          <td>
                            <?php if (!empty($p['imagen']) && file_exists("../img/productos/" . $p['imagen'])): ?>
                              <img src="../img/productos/<?= htmlspecialchars($p['imagen']); ?>" width="70" height="70" class="img-thumbnail" alt="Imagen del producto">
                            <?php else: ?>
                              <img src="https://via.placeholder.com/70x70?text=Sin+imagen" class="img-thumbnail" alt="producto no existente">
                            <?php endif; ?>
                          </td>


                          <td><?= htmlspecialchars($p['nombre_producto']); ?></td>
                          <td>Q<?= number_format($p['precio'], 2); ?></td>
                          <td><?= htmlspecialchars($p['stock']); ?></td>
                          <td><?= htmlspecialchars($p['categoria'] ?? 'Sin categoría'); ?></td>
                          <td>
                            <?php if (!empty($p['codigo_barras'])): ?>
                              <?php
                              $generator = new BarcodeGeneratorPNG();
                              $barcodeFile = "../img/barcodes/barcode_{$p['id_producto']}.png";
                              if (!file_exists($barcodeFile)) {
                                $barcodeData = $generator->getBarcode($p['codigo_barras'], $generator::TYPE_CODE_128);
                                file_put_contents($barcodeFile, $barcodeData);
                              }
                              ?>
                              <img src="<?= $barcodeFile ?>" width="120" height="40" alt="codigo de barras del producto">
                              <p class="small text-muted mb-0"><?= htmlspecialchars($p['codigo_barras']); ?></p>
                            <?php else: ?>
                              <span class="text-muted">No asignado</span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <div class="btn-group">
                              <button class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">Acciones</button>
                              <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                  <a class="dropdown-item text-success" href="#" data-bs-toggle="modal" data-bs-target="#verProducto<?= $p['id_producto']; ?>">
                                    <i class="bi bi-eye"></i> Ver
                                  </a>
                                </li>
                                <li>
                                  <hr class="dropdown-divider">
                                </li>
                                <li>
                                  <a class="dropdown-item text-primary" href="#" data-bs-toggle="modal" data-bs-target="#editarProducto<?= $p['id_producto']; ?>">
                                    <i class="bi bi-pencil-square"></i> Editar
                                  </a>
                                </li>
                                <li>
                                  <hr class="dropdown-divider">
                                </li>
                                <li>
                                  <button class="dropdown-item text-danger btn-eliminar" data-id="<?= $p['id_producto']; ?>">
                                    <i class="bi bi-trash"></i> Eliminar
                                  </button>
                                </li>
                              </ul>
                            </div>
                          </td>
                        </tr>

                        <!-- 🔹 Modal Ver Producto -->
                        <div class="modal fade" id="verProducto<?= $p['id_producto']; ?>" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                              <div class="modal-header bg-success text-white">
                                <h5 class="modal-title"><i class="bi bi-eye"></i> Detalle del Producto</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                              </div>
                              <div class="modal-body">
                                <div class="row g-4 align-items-center">
                                  <div class="col-md-4 text-center">
                                    <img src="../img/productos/<?= $p['imagen'] ?: 'default.png'; ?>"
                                      class="img-fluid rounded shadow-sm"
                                      style="max-height: 250px;" alt="imagen del producto">
                                  </div>
                                  <div class="col-md-8">
                                    <h4 class="fw-bold"><?= htmlspecialchars($p['nombre_producto']); ?></h4>
                                    <p><strong>Descripción:</strong> <?= htmlspecialchars($p['descripcion']); ?></p>
                                    <p><strong>Categoría:</strong> <?= htmlspecialchars($p['categoria']); ?></p>
                                    <p><strong>Stock:</strong> <?= htmlspecialchars($p['stock']); ?></p>
                                    <p><strong>Precio:</strong> Q<?= number_format($p['precio'], 2); ?></p>
                                    <p><strong>Código de barras:</strong></p>
                                    <?php if (!empty($p['codigo_barras'])): ?>
                                      <div class="text-center mb-2">
                                        <img id="barcodeImg<?= $p['id_producto']; ?>"
                                          src="https://barcode.tec-it.com/barcode.ashx?data=<?= urlencode($p['codigo_barras']); ?>&code=Code128"
                                          alt="Código de barras" style="width:200px; height:auto;">
                                        <p class="small text-muted mt-1"><?= htmlspecialchars($p['codigo_barras']); ?></p>

                                        <!-- 🔹 Botón de descarga con Picqer -->
                                        <button type="button" class="btn btn-outline-primary btn-sm mt-2"
                                          onclick="window.open('../app/controllers/productos/descargar_codigo.php?descargar_codigo=1&codigo=<?= urlencode($p['codigo_barras']); ?>', '_blank')">
                                          <i class="bi bi-download"></i> Descargar
                                        </button>

                                      </div>
                                    <?php else: ?>
                                      <span class="text-muted">No asignado</span>
                                    <?php endif; ?>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>


                        <!-- 🔹 Modal Editar Producto -->
                        <div class="modal fade" id="editarProducto<?= $p['id_producto']; ?>" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content shadow-lg border-0 rounded-3 overflow-hidden">

                              <!-- 🔹 Encabezado -->
                              <div class="modal-header text-white" style="background: linear-gradient(135deg, #0d6efd, #0a58ca); box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                                <h5 class="modal-title fw-bold d-flex align-items-center">
                                  <i class="bi bi-pencil-square me-2 fs-5"></i> Editar Producto
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                              </div>

                              <form action="../app/controllers/productos/editar_producto.php" method="POST" enctype="multipart/form-data">
                                <div class="modal-body bg-light">
                                  <input type="hidden" name="id_producto" value="<?= $p['id_producto']; ?>">
                                  <div class="container-fluid">
                                    <div class="row g-3">

                                      <!-- 🔹 Columna Izquierda -->
                                      <div class="col-lg-8">
                                        <div class="row g-3">

                                          <!-- Nombre -->
                                          <div class="col-md-12">
                                            <label class="form-label fw-semibold"><i class="bi bi-tag"></i> Nombre del producto</label>
                                            <input type="text" name="nombre_producto" class="form-control"
                                              value="<?= htmlspecialchars($p['nombre_producto']); ?>" required>
                                          </div>

                                          <!-- Código de barras -->
                                          <div class="col-md-12">
                                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                                              <span><i class="bi bi-upc"></i> Código de barras</span>
                                              <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="actualizarCodigoBarras('<?= $p['id_producto']; ?>')">
                                                <i class="bi bi-arrow-repeat"></i> Actualizar vista
                                              </button>
                                            </label>
                                            <input type="text" name="codigo_barras" id="codigo_barras_<?= $p['id_producto']; ?>"
                                              class="form-control" value="<?= htmlspecialchars($p['codigo_barras']); ?>">
                                          </div>

                                          <!-- Descripción -->
                                          <div class="col-md-12">
                                            <label class="form-label fw-semibold"><i class="bi bi-card-text"></i> Descripción</label>
                                            <textarea name="descripcion" class="form-control" rows="2"><?= htmlspecialchars($p['descripcion']); ?></textarea>
                                          </div>

                                          <!-- Stock, Precio, Categoría -->
                                          <div class="col-md-4">
                                            <label class="form-label fw-semibold"><i class="bi bi-boxes"></i> Stock</label>
                                            <input type="number" name="stock" class="form-control"
                                              value="<?= htmlspecialchars($p['stock']); ?>" required>
                                          </div>

                                          <div class="col-md-4">
                                            <label class="form-label fw-semibold"><i class="bi bi-cash-stack"></i> Precio</label>
                                            <div class="input-group">
                                              <span class="input-group-text text-primary fw-bold">Q</span>
                                              <input type="number" name="precio" class="form-control text-primary fw-semibold" step="0.01"
                                                value="<?= htmlspecialchars($p['precio']); ?>" required>
                                            </div>
                                          </div>

                                          <div class="col-md-4">
                                            <label class="form-label fw-semibold"><i class="bi bi-collection"></i> Categoría</label>
                                            <select name="id_categoria" class="form-select" required>
                                              <?php foreach ($categoria_Activos as $categoria): ?>
                                                <option value="<?= $categoria['id_categoria']; ?>"
                                                  <?= $categoria['id_categoria'] == $p['id_categoria'] ? 'selected' : ''; ?>>
                                                  <?= htmlspecialchars($categoria['nombre']); ?>
                                                </option>
                                              <?php endforeach; ?>
                                            </select>
                                          </div>

                                        </div>
                                      </div>

                                      <!-- 🔹 Columna Derecha: Vista previa -->
                                      <div class="col-lg-4">
                                        <div class="border rounded-3 p-3 bg-white text-center shadow-sm">

                                          <!-- Código de barras -->
                                          <h6 class="fw-bold text-primary mb-2"><i class="bi bi-upc-scan"></i> Vista previa del código</h6>
                                          <svg id="previewBarcode_<?= $p['id_producto']; ?>"
                                            class="border rounded-2 bg-light p-2 mb-3"
                                            style="max-width: 250px; <?= $p['codigo_barras'] ? '' : 'display: none;' ?>"></svg>
                                          <p id="barcodeText_<?= $p['id_producto']; ?>"
                                            class="text-muted small mb-3" <?= $p['codigo_barras'] ? 'style="display:none;"' : ''; ?>>
                                            Aún no se ha generado código
                                          </p>

                                          <hr>

                                          <!-- Imagen -->
                                          <h6 class="fw-bold text-primary mb-2"><i class="bi bi-image"></i> Vista previa de imagen</h6>
                                          <input type="file" name="imagen" id="inputImagen_<?= $p['id_producto']; ?>"
                                            class="form-control mb-3" accept="image/*"
                                            onchange="vistaPreviaEditar(event, '<?= $p['id_producto']; ?>')">
                                          <img id="previewImagen_<?= $p['id_producto']; ?>"
                                            src="../img/productos/<?= $p['imagen'] ?: 'default.png'; ?>"
                                            alt="Vista previa"
                                            class="img-fluid rounded-3 shadow-sm mb-2"
                                            style="max-width: 200px;">

                                        </div>
                                      </div>

                                    </div>
                                  </div>
                                </div>

                                <!-- 🔹 Footer -->
                                <div class="modal-footer bg-light border-top">
                                  <button type="button" class="btn btn-outline-danger px-4" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                  </button>
                                  <button type="submit" class="btn btn-outline-primary px-4 shadow-sm">
                                    <i class="bi bi-check-circle"></i> Guardar Cambios
                                  </button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>

                        <!-- 🔹 Script de vista previa -->
                        <script>
                          // 🔹 Vista previa imagen
                          function vistaPreviaEditar(event, id) {
                            const img = document.getElementById(`previewImagen_${id}`);
                            if (event.target.files && event.target.files[0]) {
                              img.src = URL.createObjectURL(event.target.files[0]);
                              img.style.display = 'block';
                            }
                          }

                          // 🔹 Actualizar vista de código de barras
                          function actualizarCodigoBarras(id) {
                            const input = document.getElementById(`codigo_barras_${id}`);
                            const svg = document.getElementById(`previewBarcode_${id}`);
                            const text = document.getElementById(`barcodeText_${id}`);
                            const valor = input.value.trim();
                            if (valor.length > 0) {
                              JsBarcode(svg, valor, {
                                format: "CODE128",
                                displayValue: true,
                                fontSize: 14,
                                height: 60
                              });
                              svg.style.display = 'block';
                              text.style.display = 'none';
                            } else {
                              svg.style.display = 'none';
                              text.style.display = 'block';
                            }
                          }

                          // 🔹 Inicializar vista al abrir el modal (para mostrar el código existente)
                          document.addEventListener("DOMContentLoaded", () => {
                            const id = "<?= $p['id_producto']; ?>";
                            const valor = "<?= $p['codigo_barras']; ?>";
                            if (valor.trim().length > 0) {
                              actualizarCodigoBarras(id);
                            }
                          });
                        </script>


                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="9" class="text-muted">No hay productos registrados</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>

          </div>

        </div>
      </div>
    </main>

    <script>
      //uso de sweetalert2
      document.addEventListener('DOMContentLoaded', function() {
        // Selecciona todos los botones con la clase .btn-eliminar
        const botonesEliminar = document.querySelectorAll('.btn-eliminar');

        botonesEliminar.forEach(boton => {
          boton.addEventListener('click', function() {
            const idProducto = this.getAttribute('data-id');

            Swal.fire({
              title: "¿Estás seguro?",
              text: "¡No podrás revertir esto!",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "Sí, eliminar",
              cancelButtonText: "Cancelar"
            }).then((result) => {
              if (result.isConfirmed) {
                // Redirige al controlador PHP para eliminar el producto
                window.location.href = `../app/controllers/productos/delete_producto.php?id=${idProducto}`;
              }
            });
          });
        });
      });
    </script>

    <?php include '../layouts/footer.php'; ?>


    <script>
      $(document).ready(function() {
        $('#Productos').DataTable({
          lengthMenu: [
            [5, 10, 25, 50, 100],
            [5, 10, 25, 50, 100] // Textos que se muestran en el menú
          ],
          language: {
            processing: "Procesando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ productos registrados",
            infoEmpty: "Mostrando 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros en total)",
            loadingRecords: "Cargando...",
            zeroRecords: "No se encontraron registros coincidentes",
            emptyTable: "No hay datos disponibles en la tabla",
            paginate: {
              first: "Primero",
              previous: "Anterior",
              next: "Siguiente",
              last: "Último"
            },
            aria: {
              sortAscending: ": activar para ordenar la columna ascendente",
              sortDescending: ": activar para ordenar la columna descendente"
            }
          }
        });
      });
    </script>



    <style>
      .dataTables_wrapper .btn {
        border-radius: 8px;
        margin-right: 4px;
      }

      .dataTables_filter input {
        border-radius: 8px;
      }

      .dataTables_length select {
        border-radius: 8px;
      }
    </style>



    <?php include '../layouts/notificaciones.php'; ?>
  </div>

  <!-- 🔹 Modal Agregar Producto -->
  <div class="modal fade" id="modalAgregarProducto" tabindex="-1" aria-labelledby="modalAgregarProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content shadow-lg border-0 rounded-3 overflow-hidden">

        <!-- 🔹 Encabezado Mejorado -->
        <div class="modal-header text-white"
          style="background: linear-gradient(135deg, #198754, #157347);
                  box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
          <h5 class="modal-title fw-bold d-flex align-items-center" id="modalAgregarProductoLabel">
            <i class="bi bi-box-seam me-2 fs-5"></i> Nuevo Producto
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <form action="../app/controllers/productos/add_productos.php" method="POST" enctype="multipart/form-data">
          <div class="modal-body bg-light">
            <div class="container-fluid">
              <div class="row g-3">

                <!-- 🔹 Columna Izquierda: Datos principales -->
                <div class="col-lg-8">
                  <div class="row g-3">

                    <!-- Nombre -->
                    <div class="col-md-12">
                      <label class="form-label fw-semibold"><i class="bi bi-tag"></i> Nombre del producto</label>
                      <input type="text" name="nombre_producto" class="form-control" placeholder="Ej: Alcohol en gel 500ml" required>
                    </div>

                    <!-- Código de barras -->
                    <div class="col-md-12">
                      <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-upc"></i> Código de barras</span>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="generarCodigoBarras()">
                          <i class="bi bi-arrow-repeat"></i> Generar
                        </button>
                      </label>
                      <input type="text" name="codigo_barras" id="codigo_barras" class="form-control" placeholder="Ingrese o genere uno">
                    </div>

                    <!-- Descripción -->
                    <div class="col-md-12">
                      <label class="form-label fw-semibold"><i class="bi bi-card-text"></i> Descripción</label>
                      <textarea name="descripcion" class="form-control" rows="2" placeholder="Agregue una breve descripción..."></textarea>
                    </div>

                    <!-- Stock, Precio, Categoría -->
                    <div class="col-md-4">
                      <label class="form-label fw-semibold"><i class="bi bi-boxes"></i> Stock</label>
                      <input type="number" name="stock" class="form-control" placeholder="Cantidad disponible" min="1" required>
                    </div>

                    <div class="col-md-4">
                      <label class="form-label fw-semibold"><i class="bi bi-cash-stack"></i> Precio</label>
                      <div class="input-group">
                        <span class="input-group-text text-success fw-bold">Q</span>
                        <input type="number" name="precio" class="form-control text-success fw-semibold" step="0.01" placeholder="0.00" required>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <label class="form-label fw-semibold"><i class="bi bi-collection"></i> Categoría</label>
                      <select name="id_categoria" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($categoria_Activos as $categoria): ?>
                          <option value="<?= $categoria['id_categoria']; ?>">
                            <?= htmlspecialchars($categoria['nombre']); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                  </div>
                </div>

                <!-- 🔹 Columna Derecha: Vista previa -->
                <div class="col-lg-4">
                  <div class="border rounded-3 p-3 bg-white text-center shadow-sm">

                    <!-- Código de barras -->
                    <h6 class="fw-bold text-success mb-2"><i class="bi bi-upc-scan"></i> Vista previa del código</h6>
                    <svg id="previewBarcode" class="border rounded-2 bg-light p-2 mb-3"
                      style="max-width: 250px; display: none;"></svg>
                    <p id="barcodeText" class="text-muted small mb-3">Aún no se ha generado código</p>

                    <hr>

                    <!-- Imagen -->
                    <h6 class="fw-bold text-success mb-2"><i class="bi bi-image"></i> Vista previa de imagen</h6>
                    <input type="file" name="imagen" id="inputImagen" class="form-control mb-3" accept="image/*" onchange="vistaPrevia(event)">
                    <img id="previewImagen" src="" alt="Vista previa"
                      class="img-fluid rounded-3 shadow-sm mb-2" style="max-width: 200px; display: none;">
                    <p id="previewText" class="text-muted small">Aún no se ha seleccionado imagen</p>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <!-- 🔹 Footer -->
          <div class="modal-footer bg-light border-top">
            <button type="button" class="btn btn-outline-danger px-4" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Cancelar</button>
            <button type="submit" class="btn btn-outline-success px-4 shadow-sm"><i class="bi bi-check-circle"></i> Guardar Cambios</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // 🔹 Vista previa de imagen
    function vistaPrevia(event) {
      const img = document.getElementById('previewImagen');
      const text = document.getElementById('previewText');
      if (event.target.files && event.target.files[0]) {
        img.src = URL.createObjectURL(event.target.files[0]);
        img.style.display = 'block';
        text.style.display = 'none';
      } else {
        img.style.display = 'none';
        text.style.display = 'block';
      }
    }

    // 🔹 Generar código de barras aleatorio (con letras incluidas)
    function generarCodigoBarras() {
      const codigoInput = document.getElementById('codigo_barras');
      const letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      const numeros = Math.floor(100000 + Math.random() * 900000);
      const letra = letras.charAt(Math.floor(Math.random() * letras.length));
      const nuevoCodigo = `${letra}${numeros}GT`; // ejemplo: A123456GT
      codigoInput.value = nuevoCodigo;
      mostrarVistaPreviaCodigo(nuevoCodigo);
    }

    // 🔹 Mostrar código de barras dinámicamente
    const codigoInput = document.getElementById('codigo_barras');
    codigoInput.addEventListener('input', () => {
      const valor = codigoInput.value.trim();
      if (valor) mostrarVistaPreviaCodigo(valor);
    });

    function mostrarVistaPreviaCodigo(valor) {
      const svg = document.getElementById('previewBarcode');
      const text = document.getElementById('barcodeText');
      if (valor.length > 0) {
        JsBarcode(svg, valor, {
          format: "CODE128",
          displayValue: true,
          fontSize: 14,
          height: 60
        });
        svg.style.display = 'block';
        text.style.display = 'none';
      } else {
        svg.style.display = 'none';
        text.style.display = 'block';
      }
    }
  </script>






  <!-- 🔹 Librería JsBarcode -->
  <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

</body>

</html>