<?php
include '../../app/conexionBD.php';
include '../../layouts/sesion.php';

// ===============================
// CAJA ABIERTA DEL USUARIO
// ===============================
$sqlCaja = "
    SELECT *
    FROM caja
    WHERE estado = 'abierta'
      AND id_Usuarios = :id_Usuarios
    ORDER BY id_caja DESC
    LIMIT 1
";

$stmtCaja = $pdo->prepare($sqlCaja);
$stmtCaja->execute([
    'id_Usuarios' => $_SESSION['id_usuario']
]);

$caja = $stmtCaja->fetch(PDO::FETCH_ASSOC);

$id_caja    = $caja['id_caja'] ?? null;
$estadoCaja = $caja ? 'ABIERTA' : 'CERRADA';

// ===============================
// VARIABLES INICIALES
// ===============================
$montoInicial = $montoActual = 0.00;
$ingresos = $egresos = $devoluciones = $prestamos = $gastos = 0.00;
$ingresosTotales = $egresosTotales = $totalSalidas = $saldo = $montoFinal = 0.00;

// ===============================
// CALCULOS SOLO SI HAY CAJA
// ===============================
if ($id_caja) {

    $montoInicial = (float)$caja['monto_apertura'];
    $montoActual  = (float)$caja['monto_actual'];

    $qMovimientos = $pdo->prepare("
        SELECT tipo_movimiento, SUM(monto) AS total
        FROM historial_caja
        WHERE id_caja = :id_caja
        GROUP BY tipo_movimiento
    ");
    $qMovimientos->execute(['id_caja' => $id_caja]);

    while ($row = $qMovimientos->fetch(PDO::FETCH_ASSOC)) {
        $total = (float)$row['total'];

        switch ($row['tipo_movimiento']) {
            case 'ingreso':
                $ingresos += $total;
                break;
            case 'devolucion':
                $devoluciones += $total;
                break;
            case 'prestamo':
                $prestamos += $total;
                break;
            case 'gasto':
                $gastos += $total;
                break;
            case 'egreso':
                $egresos += $total;
                break;
        }
    }

    $ingresosTotales = $ingresos + $devoluciones;
    $egresosTotales  = $egresos + $gastos + $prestamos;
    $totalSalidas    = $egresos + $gastos + $prestamos;
    $saldo           = $ingresosTotales - $egresosTotales;
    $montoFinal      = $montoInicial + $saldo;
}
?>



  <!DOCTYPE html>
  <html lang="es">

  <head>
    <title>Administración de Caja</title>
    <?php include '../../layouts/head.php'; ?>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <style>
      #estadoCajaCard {
        transition: all 0.4s ease-in-out;
      }

      #estadoCajaCard.fade {
        opacity: 0.4;
        transform: scale(0.98);
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
                <h3 class="mb-0">Administración de Caja</h3>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="<?php echo $URL; ?>">Inicio</a></li>
                  <li class="breadcrumb-item active">Caja</li>
                  <li class="breadcrumb-item active">Administración de Caja</li>
                </ol>
              </div>
            </div>
          </div>
        </div>

        <div class="app-content">
          <div class="container-fluid">

            <!-- Tarjeta dinámica -->
            <div class="card shadow-sm border-0 mb-3" id="estadoCajaCard">
              <div class="card-body">
                <h5 class="fw-bold mb-1 text-secondary">
                  <i class="bi bi-cash-stack"></i> Estado de Caja
                </h5>
                <small class="text-muted">Cargando estado actual...</small>
              </div>
            </div>

            <!-- Resumen y gráfico -->
            <div class="row">
              <div class="col-md-6">
                <div class="card card-outline card-primary">
                  <div class="card-header">
                    <h5 class="card-title">Resumen de Movimientos</h5>
                  </div>
                  <div class="card-body">
                    <table class="table table-sm">
                      <tr>
                        <th>MONTO INICIAL</th>
                        <td class="text-end">Q<?php echo number_format($montoInicial, 2); ?></td>
                      </tr>
                      <tr>
                        <th class="text-success">INGRESOS</th>
                        <td class="text-end text-success">Q<?php echo number_format($ingresos, 2); ?></td>
                      </tr>
                      <tr>
                        <th class="text-primary">DEVOLUCIONES</th>
                        <td class="text-end text-primary">Q<?php echo number_format($devoluciones, 2); ?></td>
                      </tr>
                      <tr>
                        <th class="text-warning">PRÉSTAMOS</th>
                        <td class="text-end text-warning">Q<?php echo number_format($prestamos, 2); ?></td>
                      </tr>
                      <tr>
                        <th class="text-danger">GASTOS</th>
                        <td class="text-end text-danger">Q<?php echo number_format($gastos, 2); ?></td>
                      </tr>
                      <tr>
                        <th class="text-danger">EGRESOS</th>
                        <td class="text-end text-danger">Q<?php echo number_format($egresos, 2); ?></td>
                      </tr>
                      <tr class="table-warning">
                        <th>SALIDAS TOTALES</th>
                        <td class="text-end fw-bold text-danger">Q<?php echo number_format($totalSalidas, 2); ?></td>
                      </tr>
                      <tr>
                        <th>TOTAL INGRESOS</th>
                        <td class="text-end fw-bold text-success">Q<?php echo number_format($ingresosTotales, 2); ?></td>
                      </tr>
                      <tr>
                        <th>TOTAL EGRESOS</th>
                        <td class="text-end fw-bold text-danger">Q<?php echo number_format($egresosTotales, 2); ?></td>
                      </tr>
                      <tr>
                        <th>SALDO</th>
                        <td class="text-end fw-bold">Q<?php echo number_format($saldo, 2); ?></td>
                      </tr>
                      <tr class="table-info">
                        <th>MONTO FINAL</th>
                        <td class="text-end fw-bold">Q<?php echo number_format($montoFinal, 2); ?></td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="card card-outline card-secondary">
                  <div class="card-header">
                    <h5 class="card-title">Gráfico de Movimientos</h5>
                  </div>
                  <div class="card-body">
                    <div id="graficoMovimientos" style="width:100%;height:300px;"></div>
                  </div>
                </div>
              </div>

            </div>

          </div>
        </div>
      </main>


    </div>

    <!-- 📊 Gráfico de dona -->
    <script>
      google.charts.load('current', {
        packages: ['corechart']
      });
      google.charts.setOnLoadCallback(() => {
        const data = google.visualization.arrayToDataTable([
          ['Tipo', 'Monto (Q)'],
          ['Monto Inicial', <?php echo  $montoInicial; ?>],
          ['Ingresos', <?php echo $ingresos; ?>],
          ['Devoluciones', <?php echo $devoluciones; ?>],
          ['Préstamos', <?php echo $prestamos; ?>],
          ['Gastos', <?php echo $gastos; ?>],
          ['Egresos', <?php echo $egresos; ?>]
        ]);
        new google.visualization.PieChart(document.getElementById('graficoMovimientos'))
          .draw(data, {
            pieHole: 0.5,
            legend: {
              position: 'bottom'
            },
            chartArea: {
              width: '90%',
              height: '80%'
            }
          });
      });
    </script>

    <!-- 🔁 Estado de caja dinámico sin duplicados -->
    <script>
      async function actualizarEstadoCaja() {
        const card = document.getElementById('estadoCajaCard');
        card.classList.add('fade');
        try {
          const res = await fetch('../../app/controllers/caja/estado_caja.php?ts=' + Date.now()) ;
          const data = await res.json();

          const estado = data.estado?.toUpperCase() === 'ABIERTA' ? 'ABIERTA' : 'CERRADA';
          const color = estado === 'ABIERTA' ? 'text-success' : 'text-danger';

          const contenido = `
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
              <div class="mb-2 mb-md-0">
                <h5 class="fw-bold mb-1 ${color}">
                  <i class="bi bi-cash-stack"></i> Caja ${estado}
                </h5>
                <small class="text-muted">
                  ${estado === 'ABIERTA'
                    ? `<i class="bi bi-person-fill"></i> Usuario: <span class="fw-bold text-dark">${data.usuario}</span><br>
                      <i class="bi bi-calendar-event"></i> Fecha de apertura: <span class="text-secondary">${data.fecha_apertura}</span><br>
                      <i class="bi bi-wallet2"></i> Monto actual: <span class="fw-bold text-success">Q ${data.monto_actual}</span>`
                    : 'No hay ninguna caja abierta actualmente.'}
                </small>
              </div>

              <!-- Dropdowns -->
              <div class="d-flex flex-wrap gap-2">
                <div class="btn-group">
                  <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-gear-fill"></i> Opciones
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalAperturaCaja"><i class="bi bi-box-arrow-in-right text-success"></i> Apertura de Caja</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalEditarMonto"><i class="bi bi-pencil-square text-warning"></i> Editar Monto Inicial</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalCierreCaja"><i class="bi bi-box-arrow-left text-danger"></i> Cierre de Caja</a></li>
                  </ul>
                </div>

                <div class="btn-group">
                  <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-cash-coin"></i> Movimientos Caja
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalDevolucion"><i class="bi bi-arrow-counterclockwise text-success"></i> Devolución</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalPrestamo"><i class="bi bi-cash-stack text-warning"></i> Préstamo</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalGasto"><i class="bi bi-wallet2 text-danger"></i> Gasto</a></li>
                  </ul>
                </div>
              </div>
            </div>
          `;

          card.innerHTML = contenido;
          card.classList.remove('fade');

          // Reactivar dropdowns y modales
          document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => new bootstrap.Dropdown(el));
          document.querySelectorAll('[data-bs-toggle="modal"]').forEach(el => {
            el.addEventListener('click', e => {
              const target = el.getAttribute('data-bs-target');
              const modal = document.querySelector(target);
              if (modal) new bootstrap.Modal(modal).show();
            });
          });

        } catch (e) {
          console.error('Error al actualizar estado de caja:', e);
          card.classList.remove('fade');
        }
      }

      setInterval(actualizarEstadoCaja, 5000);
      actualizarEstadoCaja();
    </script>



    <!-- Modal: Apertura de Caja -->
    <div class="modal fade" id="modalAperturaCaja" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title bi bi-box-arrow-in-right"> Apertura de Caja</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <form action="../../app/controllers/caja/apertura_caja.php" method="POST">
            <div class="modal-body">
              <div class="mb-3">
                <label for="monto_apertura" class="form-label fw-bold">Monto de Apertura (Q):</label>
                <input type="number" name="monto_apertura" id="monto_apertura" class="form-control" required step="0.01" min="0">
              </div>
              <!-- Usuario logueado -->
              <input type="hidden" name="id_Usuarios" value="<?= $_SESSION['id_Usuarios']; ?>">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-success">Abrir Caja</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal: Editar Monto Inicial -->
    <div class="modal fade" id="modalEditarMonto" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title bi bi-pencil-square"> Editar Monto Inicial</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <form action="../../app/controllers/caja/editar_monto.php" method="POST">
            <div class="modal-body">
              <input type="hidden" name="id_caja" value="<?php echo $caja['id_caja']; ?>">

              <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Monto actual:</label>
                <input
                  type="text"
                  class="form-control text-success fw-bold"
                  value="Q<?php echo number_format($caja['monto_apertura'], 2); ?>"
                  readonly>
              </div>

              <div class="mb-3">
                <label for="nuevo_monto" class="form-label fw-bold">Nuevo Monto (Q):</label>
                <input
                  type="number"
                  name="nuevo_monto"
                  id="nuevo_monto"
                  class="form-control"
                  required
                  step="0.01"
                  min="0"
                  placeholder="Ingrese el nuevo monto">
              </div>

              <!-- Usuario logueado -->
              <input type="hidden" name="id_Usuarios" value="<?= $_SESSION['id_Usuarios']; ?>">
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-warning text-dark">Actualizar</button>
            </div>
          </form>
        </div>
      </div>
    </div>


    <!-- ========================= MODAL: PRÉSTAMO ========================= -->
    <div class="modal fade" id="modalPrestamo" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title"><i class="bi bi-cash-stack"></i> Registrar Préstamo</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <form action="../../app/controllers/caja/prestamo.php" method="POST">
            <input type="hidden" name="id_Usuarios" value="<?= $_SESSION['id_Usuarios']; ?>">
            <input type="hidden" name="id_caja" value="<?= $id_caja ?? ''; ?>">

            <div class="modal-body">
              <div class="mb-3">
                <label for="monto_prestamo" class="form-label fw-bold">Monto del préstamo (Q):</label>
                <input type="number" name="monto" id="monto_prestamo" class="form-control" required step="0.01" min="0" placeholder="Ingrese el monto">
              </div>

              <div class="mb-3">
                <label for="descripcion_prestamo" class="form-label fw-bold">Descripción:</label>
                <textarea name="descripcion" id="descripcion_prestamo" class="form-control" rows="3" placeholder="Motivo del préstamo..." required></textarea>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-warning text-dark">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- ========================= MODAL: DEVOLUCIÓN ========================= -->
    <div class="modal fade" id="modalDevolucion" tabindex="-1" aria-labelledby="modalDevolucionLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title bi bi-arrow-counterclockwise" id="modalDevolucionLabel">
              <i class="fas fa-undo-alt"></i> Registrar Devolución
            </h5>
            <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>

          <div class="modal-body">
            <form action="../../app/controllers/caja/devolucion.php" method="POST">

              <!-- Usuario logueado -->
              <input type="hidden" name="id_Usuarios" value="<?= $_SESSION['id_Usuarios']; ?>">

              <!-- Caja actual -->
              <input type="hidden" name="id_caja" value="<?= $id_caja ?? ''; ?>">

              <div class="mb-3">
                <label for="montoDevolucion" class="form-label">Monto</label>
                <input type="number" class="form-control" id="montoDevolucion" name="monto"
                  placeholder="Ingrese el monto devuelto" required step="0.01" min="0">
              </div>

              <div class="mb-3">
                <label for="descripcionDevolucion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcionDevolucion" name="descripcion"
                  rows="3" placeholder="Motivo de la devolución..." required></textarea>
              </div>

              <div class="text-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Guardar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>


    <!-- ========================= MODAL: GASTO ========================= -->
    <div class="modal fade" id="modalGasto" tabindex="-1" aria-labelledby="modalGastoLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="modalGastoLabel"><i class="fas fa-money-bill-wave"></i> Registrar Gasto</h5>
            <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <form action="../../app/controllers/caja/gasto.php" method="POST">
              <!-- Usuario logueado -->
              <input type="hidden" name="id_Usuarios" value="<?= $_SESSION['id_Usuarios']; ?>">

              <!-- Caja actual -->
              <input type="hidden" name="id_caja" value="<?= $id_caja ?? ''; ?>">

              <div class="mb-3">
                <label for="montoGasto" class="form-label">Monto</label>
                <input type="number" class="form-control" id="montoGasto" name="monto" placeholder="Ingrese el monto del gasto" required>
              </div>

              <div class="mb-3">
                <label for="descripcionGasto" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcionGasto" name="descripcion" rows="3" placeholder="Detalle del gasto..." required></textarea>
              </div>

              <div class="text-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Guardar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>


    <!-- Modal: Cierre de Caja -->
    <div class="modal fade" id="modalCierreCaja" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-secondary text-white">
            <h5 class="modal-title bi bi-box-arrow-left"> Cierre de Caja</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <form action="../../app/controllers/caja/cierre_caja.php" method="POST">
            <div class="modal-body">
              <input type="hidden" name="id_caja" value="<?php echo $id_caja; ?>">

              <div class="mb-3">
                <label for="monto_final" class="form-label fw-bold">Monto Contado (Q):</label>
                <input type="number"
                  name="monto_final"
                  id="monto_final"
                  class="form-control"
                  required
                  step="0.01"
                  min="0"
                  value="<?php echo number_format($montoActual, 2, '.', ''); ?>">
              </div>

              <div class="mb-3">
                <label for="observaciones" class="form-label fw-bold">Observaciones (opcional):</label>
                <textarea name="observaciones" id="observaciones" class="form-control" rows="2"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-secondary">Cerrar Caja</button>
            </div>
          </form>
        </div>
      </div>
    </div>


    <script>
      // 🔧 Solución para fondo oscuro persistente al cerrar modales
      document.addEventListener('hidden.bs.modal', function() {
        // Elimina cualquier backdrop que quede visible
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(b => b.remove());

        // Restaura el scroll del body (por si quedó bloqueado)
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
      });
    </script>



    <?php include '../../layouts/footer.php'; ?>
    <?php include '../../layouts/notificaciones.php'; ?>
  </body>

  </html>