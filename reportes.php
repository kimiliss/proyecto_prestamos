<!--
    Autor: Kimberly Garcia 
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 18/10/26
    Fecha de modificacion: 23/10/26
    Descripcion: Pagina reportes del proyecto
-->

<?php
  include "conexion.php"; // ajusta al nombre de tu archivo de conexión

  // Filtro activo
  $filtros = ["prestados", "disponibles", "vencidos"];
  $estado  = $_GET["estado"] ?? "prestados";
  if (!in_array($estado, $filtros)) { $estado = "prestados"; }

  // Conteos de cada filtro
  // Prestados: préstamos sin devolución
  $r = $conexion->query("SELECT COUNT(*) AS n FROM prestamo WHERE fecha_devolucion IS NULL");
  $total_prestados = $r->fetch_assoc()["n"];

  // Vencidos: préstamos sin devolución con fecha límite ya pasada
  $r = $conexion->query("SELECT COUNT(*) AS n FROM prestamo
                         WHERE fecha_devolucion IS NULL AND fecha_limite < CURDATE()");
  $total_vencidos = $r->fetch_assoc()["n"];

  // Disponibles: equipos que no están en un préstamo activo
  $r = $conexion->query("SELECT COUNT(*) AS n FROM equipo e
                         WHERE NOT EXISTS (SELECT 1 FROM prestamo p
                                           WHERE p.id_equipo = e.id_equipo
                                           AND p.fecha_devolucion IS NULL)");
  $total_disponibles = $r->fetch_assoc()["n"];

  // Lista de prestados (solo se consulta si ese filtro está activo)
  $prestados = null;
  if ($estado == "prestados") {
    $prestados = $conexion->query(
      "SELECT e.id_equipo, t.nombre AS tipo, s.nombre AS solicitante,
              DATE_FORMAT(p.fecha_prestamo, '%d/%m/%Y %H:%i') AS prestamo,
              DATE_FORMAT(p.fecha_limite,   '%d/%m/%Y') AS limite
       FROM prestamo p
       JOIN equipo e       ON e.id_equipo = p.id_equipo
       JOIN tipoequipo t   ON t.id_tipo = e.id_tipo
       JOIN solicitante s  ON s.id_solicitante = p.id_solicitante
       WHERE p.fecha_devolucion IS NULL
       ORDER BY p.fecha_limite ASC"
    );
  }

  // Lista de disponibles (equipos que no están en un préstamo activo)
  $disponibles = null;
  if ($estado == "disponibles") {
    $disponibles = $conexion->query(
      "SELECT e.id_equipo, t.nombre AS tipo, e.descripcion, e.estado
       FROM equipo e
       JOIN tipoequipo t ON t.id_tipo = e.id_tipo
       WHERE NOT EXISTS (SELECT 1 FROM prestamo p
                         WHERE p.id_equipo = e.id_equipo
                         AND p.fecha_devolucion IS NULL)
       ORDER BY e.id_equipo ASC"
    );
  }

  // Lista de vencidos (préstamos sin devolución con fecha límite pasada)
  $vencidos = null;
  if ($estado == "vencidos") {
    $vencidos = $conexion->query(
      "SELECT e.id_equipo, t.nombre AS tipo, s.nombre AS solicitante,
              DATE_FORMAT(p.fecha_limite, '%d/%m/%Y') AS limite,
              DATEDIFF(CURDATE(), p.fecha_limite) AS retraso
       FROM prestamo p
       JOIN equipo e       ON e.id_equipo = p.id_equipo
       JOIN tipoequipo t   ON t.id_tipo = e.id_tipo
       JOIN solicitante s  ON s.id_solicitante = p.id_solicitante
       WHERE p.fecha_devolucion IS NULL AND p.fecha_limite < CURDATE()
       ORDER BY retraso DESC"
    );
  }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reportes - Data Link</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family
            =Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php include "encabezado.php"; ?>

  <main>
    <h1>Reportes</h1>
    <p class="subtitulo">Consulta el estado actual del inventario</p>

    <!-- Filtros -->
    <div class="filtros-fila">
      <nav class="filtros" aria-label="Filtrar reporte">
        <a class="filtro" href="reportes.php?estado=prestados"
           <?php if ($estado == "prestados") echo 'aria-current="true"'; ?>>
          Prestados · <?php echo $total_prestados; ?>
        </a>
        <a class="filtro" href="reportes.php?estado=disponibles"
           <?php if ($estado == "disponibles") echo 'aria-current="true"'; ?>>
          Disponibles · <?php echo $total_disponibles; ?>
        </a>
        <a class="filtro" href="reportes.php?estado=vencidos"
           <?php if ($estado == "vencidos") echo 'aria-current="true"'; ?>>
          Vencidos · <?php echo $total_vencidos; ?>
        </a>
      </nav>
    </div>

    <section class="lista">

      <?php if ($estado == "prestados"): ?>
      <!-- ===== Tabla: PRESTADOS ===== -->
      <table class="tabla tabla-reportes">
        <thead>
          <tr>
            <th>Equipo</th>
            <th>Solicitante</th>
            <th>Préstamo</th>
            <th>Fecha límite</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$prestados || $prestados->num_rows == 0): ?>
            <tr><td colspan="5">No hay equipos prestados por el momento.</td></tr>
          <?php else: ?>
            <?php while ($f = $prestados->fetch_assoc()): ?>
            <tr>
              <td>EQ-<?php echo str_pad($f["id_equipo"], 3, "0", STR_PAD_LEFT); ?> · 
                     <?php echo htmlspecialchars($f["tipo"]); ?></td>
              <td><?php echo htmlspecialchars($f["solicitante"]); ?></td>
              <td><?php echo $f["prestamo"]; ?></td>
              <td><?php echo $f["limite"]; ?></td>
              <td class="estado-ocupado">Ocupado</td>
            </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>

      <?php elseif ($estado == "disponibles"): ?>
      <!-- ===== Tabla: DISPONIBLES ===== -->
      <table class="tabla tabla-disponibles">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Descripción/marca</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$disponibles || $disponibles->num_rows == 0): ?>
            <tr><td colspan="4">No hay equipos disponibles por el momento.</td></tr>
          <?php else: ?>
            <?php while ($f = $disponibles->fetch_assoc()): ?>
            <tr>
              <td>EQ-<?php echo str_pad($f["id_equipo"], 3, "0", STR_PAD_LEFT); ?></td>
              <td><?php echo htmlspecialchars($f["tipo"]); ?></td>
              <td><?php echo htmlspecialchars($f["descripcion"]); ?></td>
              <?php if ($f["estado"] == 1): ?>
                <td class="estado-disponible">Disponible</td>
              <?php else: ?>
                <td class="estado-ocupado">No disponible</td>
              <?php endif; ?>
            </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>

      <?php else: ?>
      <!-- ===== Tabla: VENCIDOS ===== -->
      <table class="tabla tabla-vencidos">
        <thead>
          <tr>
            <th>Solicitante</th>
            <th>Equipo</th>
            <th>Fecha límite</th>
            <th>Retraso</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$vencidos || $vencidos->num_rows == 0): ?>
            <tr><td colspan="4">No hay préstamos vencidos por el momento.</td></tr>
          <?php else: ?>
            <?php while ($f = $vencidos->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($f["solicitante"]); ?></td>
              <td>EQ-<?php echo str_pad($f["id_equipo"], 3, "0", STR_PAD_LEFT); ?> ·
                     <?php echo htmlspecialchars($f["tipo"]); ?></td>
              <td><?php echo $f["limite"]; ?></td>
              <td class="estado-ocupado"><?php echo $f["retraso"]; ?> 
                                         <?php echo $f["retraso"] == 1 ? "día" : "días"; ?>
              </td>
            </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
      <?php endif; ?>

    </section>
  </main>

</body>
</html>