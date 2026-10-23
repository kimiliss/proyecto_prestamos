<!--
    Autor: Kimberly Garcia 
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 18/10/26
    Fecha de modificacion: 22/10/26
    Descripcion: Pagina reportes del proyecto
-->

<?php
  // Filtro activo
  $filtros = ["prestados", "disponibles", "vencidos"];
  $estado  = $_GET["estado"] ?? "prestados";
  if (!in_array($estado, $filtros)) { $estado = "prestados"; }

  // Conteos de cada filtro
  $total_prestados   = 0;
  $total_disponibles = 0;
  $total_vencidos    = 0;
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

    <!-- Filtros + botón Exportar -->
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
      </table>
      <?php endif; ?>

    </section>
  </main>

</body>
</html>