<!--
    Autor: Kimberly Garcia 
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 18/10/26
    Fecha de modificacion: 22/10/26
    Descripcion: Pagina registrar devolucion del proyecto
-->

<?php

include "conexion.php";
require "mensajes.php";

// ---- Prestamos activos (los que todavia no se devuelven), con busqueda opcional ----
$q = isset($_GET['q']) ? trim($_GET['q']) : "";

$sql = "SELECT p.id_prestamo, s.nombre AS solicitante, e.id_equipo, t.nombre AS tipo,
               p.fecha_prestamo, p.fecha_limite
        FROM prestamo p
        INNER JOIN solicitante s ON s.id_solicitante = p.id_solicitante
        INNER JOIN equipo e      ON e.id_equipo      = p.id_equipo
        INNER JOIN tipoequipo t  ON t.id_tipo        = e.id_tipo
        WHERE p.fecha_devolucion IS NULL";

if ($q !== "") {
    $sql .= " AND (s.nombre LIKE ?
                   OR t.nombre LIKE ?
                   OR e.descripcion LIKE ?
                   OR CONCAT('EQ-', LPAD(e.id_equipo, 3, '0')) LIKE ?)";
}
$sql .= " ORDER BY p.fecha_limite ASC";

$stmt = $conexion->prepare($sql);
if ($q !== "") {
    $like = "%" . $q . "%";
    $stmt->bind_param("ssss", $like, $like, $like, $like);
}
$stmt->execute();
$prestamos = $stmt->get_result();

// ---- Aviso que regresa confirmar_devolucion.php ----
$aviso = $_GET['aviso'] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Devoluciones - Data Link</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family
            =Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php include "encabezado.php"; ?>

  <?php
  // ---- Ventanas de mensaje ----
  if ($aviso == "devuelto") {
      ventana_aviso("ok", "Devolución registrada", "El equipo volvió a estar Disponible.", "devoluciones.php");
  } elseif ($aviso == "falta") {
      ventana_aviso("error", "Selecciona un préstamo", "Elige el préstamo que vas a devolver.", "devoluciones.php");
  } elseif ($aviso == "yadevuelto") {
      ventana_aviso("error", "Préstamo no disponible", "Ese préstamo ya fue devuelto.", "devoluciones.php");
  } elseif ($aviso == "error") {
      ventana_aviso("error", "Ocurrió un error", "No se pudo registrar la devolución, intenta de nuevo.", "devoluciones.php");
  }
  ?>

  <main>

    <div class="cabecera-pantalla cabecera-devolucion">
      <h1>Registrar devolución</h1>
      <p class="subtitulo">Localiza el préstamo activo y confirma la recepción del equipo.</p>
      <p class="nota-lateral">Se mostrarán todos los equipos en préstamo o puede buscarlo por
                                 solicitante o equipo</p>
    </div>

    <section class="lista pantalla-una-columna">

      <!-- Búsqueda -->
      <label class="lista-titulo" for="buscar">Buscar por solicitante o equipo</label>
      <form class="busqueda" action="devoluciones.php" method="get">
        <input type="search" id="buscar" name="q" value="<?= htmlspecialchars($q) ?>"
              placeholder="Ana López">
        <button type="submit" class="btn">Buscar</button>
      </form>

      <form action="confirmar_devolucion.php" method="post">
        <table class="tabla tabla-devoluciones">
          <thead>
            <tr>
              <th><span class="solo-lector">Seleccionar</span></th>
              <th>Solicitante</th>
              <th>Equipo</th>
              <th>Salida</th>
              <th>Límite</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($prestamos->num_rows > 0) { ?>
              <?php while ($f = $prestamos->fetch_assoc()) { ?>
                <tr>
                  <td><input type="radio" name="id_prestamo" value="<?= $f['id_prestamo'] ?>" required></td>
                  <td><?= htmlspecialchars($f['solicitante']) ?></td>
                  <td>EQ-<?= str_pad($f['id_equipo'], 3, "0", STR_PAD_LEFT) ?> · <?= 
                          htmlspecialchars($f['tipo']) ?>
                  </td>
                  <td><?= date("d/m/Y", strtotime($f['fecha_prestamo'])) ?></td>
                  <td><?= date("d/m/Y", strtotime($f['fecha_limite'])) ?></td>
                  <td class="estado-ocupado">Ocupado</td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr><td colspan="6">No hay préstamos activos.</td></tr>
            <?php } ?>
          </tbody>
        </table>

        <p class="aviso-ok">Al confirmar, el equipo volverá a estar Disponible.</p>

        <div class="acciones-derecha">
          <button type="submit" class="btn btn-ancho">Confirmar devolución</button>
        </div>
      </form>

    </section>
  </main>

</body>
</html>