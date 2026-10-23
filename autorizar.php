<!--
    Autor: Kimberly Garcia
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 13/10/26
    Fecha de modificacion: 22/10/26
    Descripcion: Pantalla para autorizar un prestamo de equipo
-->

<?php

include "conexion.php";
require "mensajes.php";


// ---- Lo que ya habían llenado (si venimos de Seleccionar o Buscar) ----
$q              = trim($_GET['q'] ?? "");
$id_solicitante = (int) ($_GET['solicitante'] ?? 0);
$fecha_prestamo = $_GET['fecha_prestamo'] ?? date('Y-m-d');
$fecha_limite   = $_GET['fecha_limite'] ?? "";
$observaciones  = $_GET['observaciones'] ?? "";
$id_equipo      = (int) ($_GET['nuevo_equipo'] ?? $_GET['id_equipo'] ?? 0);

// ---- Que el equipo elegido siga disponible ----
if ($id_equipo > 0) {
    $stmt = $conexion->prepare("SELECT COUNT(*) FROM equipo WHERE id_equipo = ? AND estado = 1");
    $stmt->bind_param("i", $id_equipo);
    $stmt->execute();
    $stmt->bind_result($disponible);
    $stmt->fetch();
    $stmt->close();
    if ($disponible == 0) {
        $id_equipo = 0;
    }
}

// ---- Solicitantes para el select ----
$solicitantes = $conexion->query("SELECT id_solicitante, nombre FROM solicitante ORDER BY nombre")
                         ->fetch_all(MYSQLI_ASSOC);

$nombre_sel = "";
foreach ($solicitantes as $s) {
    if ($s['id_solicitante'] == $id_solicitante) {
        $nombre_sel = $s['nombre'];
    }
}

// ---- Equipos disponibles (estado = 1), con busqueda opcional ----
$sql = "SELECT e.id_equipo, t.nombre AS tipo, e.descripcion
        FROM equipo e
        INNER JOIN tipoequipo t ON t.id_tipo = e.id_tipo
        WHERE e.estado = 1";

if ($q !== "") {
    $sql .= " AND (t.nombre LIKE ? OR e.descripcion LIKE ?)";
}
$sql .= " ORDER BY e.id_equipo ASC";

$stmt = $conexion->prepare($sql);
if ($q !== "") {
    $like = "%" . $q . "%";
    $stmt->bind_param("ss", $like, $like);
}
$stmt->execute();
$equipos = $stmt->get_result();

// ---- Texto del resumen ----
$resumen = "Selecciona un solicitante y un equipo para ver el resumen.";

if ($nombre_sel !== "" && $id_equipo > 0) {
    $codigo_sel = "EQ-" . str_pad($id_equipo, 3, "0", STR_PAD_LEFT);
    $resumen = $nombre_sel . " recibirá el equipo " . $codigo_sel;

    $d1 = DateTime::createFromFormat("Y-m-d", $fecha_prestamo);
    $d2 = DateTime::createFromFormat("Y-m-d", $fecha_limite);

    if ($d1 && $d2) {
        $dias = (int) $d1->diff($d2)->format("%r%a");
        if ($dias <= 0) {
            $resumen = "La fecha de devolución debe ser posterior a la fecha de préstamo.";
        } else {
            $resumen .= " por " . $dias . ($dias == 1 ? " día" : " días") . ". 
            El equipo cambiará a estado Prestado.";
        }
    } else {
        $resumen .= ". El equipo cambiará a estado Prestado.";
    }
}

// ---- Aviso que regresa guardar_prestamo.php ----
$aviso = $_GET['aviso'] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Autorizar préstamo - Data Link</title>

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
  if ($aviso == "guardado") {
      ventana_aviso("ok", "Préstamo autorizado", "El equipo cambió a estado Prestado.", "autorizar.php");
  } elseif ($aviso == "falta_equipo") {
      ventana_aviso("error", "Selecciona un equipo", "Elige un equipo de la lista para autorizar
                    el préstamo.", "autorizar.php");
  } elseif ($aviso == "fechas") {
      ventana_aviso("error", "Revisa las fechas", "La fecha de devolución debe ser posterior 
                    a la fecha de préstamo.", "autorizar.php");
  } elseif ($aviso == "ocupado") {
      ventana_aviso("error", "Equipo no disponible", "Ese equipo ya está prestado, elige otro.",
                    "autorizar.php");
  } elseif ($aviso == "error") {
      ventana_aviso("error", "Ocurrió un error", "No se pudo autorizar el préstamo, intenta de nuevo.",
                    "autorizar.php");
  }
  ?>

  <main class="col-izq-ancha">
    <div class="cabecera-pantalla">
      <h1>Autorizar préstamo</h1>
      <p class="subtitulo">Valida al solicitante, selecciona el equipo y define el periodo.</p>
      <p class="nota nota-lateral">
        Se mostrarán todos los equipos o se pueden buscar<br>
        Solo se puede seleccionar un dispositivo
      </p>
    </div>

    <!-- Por defecto el formulario va por GET a esta misma página;
         solo el botón "Autorizar préstamo" lo manda por POST a guardar_prestamo.php -->
    <form action="autorizar.php" method="get">
      <!-- Aquí se guarda el equipo que ya eligieron -->
      <input type="hidden" name="id_equipo" value="<?= $id_equipo ?>">

      <div class="dos-columnas">

        <!-- Columna izquierda: datos del préstamo -->
        <div class="formulario">
          <div class="campo">
            <label for="solicitante">Solicitante</label>
            <div class="select-caja">
              <select id="solicitante" name="solicitante" required>
                <option value="">Selecciona un solicitante</option>
                <?php foreach ($solicitantes as $s) { ?>
                  <option value="<?= $s['id_solicitante'] ?>"
                    <?= $s['id_solicitante'] == $id_solicitante ? "selected" : "" ?>>
                    <?= htmlspecialchars($s['nombre']) ?>
                  </option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="campos-fila">
            <div class="campo">
              <label for="fecha_prestamo">Fecha de préstamo</label>
              <input type="date" id="fecha_prestamo" name="fecha_prestamo"
                     value="<?= htmlspecialchars($fecha_prestamo) ?>" required>
            </div>
            <div class="campo">
              <label for="fecha_devolucion">Fecha de devolución</label>
              <input type="date" id="fecha_devolucion" name="fecha_limite"
                     value="<?= htmlspecialchars($fecha_limite) ?>"
                     min="<?= htmlspecialchars($fecha_prestamo) ?>" required>
            </div>
          </div>

          <div class="campo" style="margin: 18px 0 0;">
            <label for="observaciones">Observaciones</label>
            <input type="text" id="observaciones" name="observaciones" maxlength="200"
                   value="<?= htmlspecialchars($observaciones) ?>"
                   placeholder="Notas adicionales">
          </div>

          <div class="resumen">
            <h2>Resumen</h2>
            <p id="resumen-texto"><?= htmlspecialchars($resumen) ?></p>
          </div>
        </div>

        <!-- Columna derecha: lista de equipos -->
        <section class="lista">
          <label class="lista-titulo" for="buscar">Lista de equipos disponibles</label>
          <div class="busqueda">
            <input type="search" id="buscar" name="q" value="<?= htmlspecialchars($q) ?>"
                   placeholder="Buscar por tipo o marca">
            <button type="submit" class="btn" formaction="autorizar.php" formmethod="get" 
                    formnovalidate>Buscar</button>
          </div>

          <table class="tabla tabla-equipos">
            <thead>
              <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Descripción/marca</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if ($equipos->num_rows > 0) { ?>
                <?php while ($f = $equipos->fetch_assoc()) { ?>
                  <tr>
                    <td>EQ-<?= str_pad($f['id_equipo'], 3, "0", STR_PAD_LEFT) ?></td>
                    <td><?= htmlspecialchars($f['tipo']) ?></td>
                    <td><?= htmlspecialchars($f['descripcion']) ?></td>
                    <td>
                      <button type="submit" name="nuevo_equipo" value="<?= $f['id_equipo'] ?>"
                              formaction="autorizar.php" formmethod="get" formnovalidate
                              class="btn btn-sm <?= $f['id_equipo'] == $id_equipo ?
                              'btn-claro' : '' ?>">Seleccionar
                      </button>
                    </td>
                  </tr>
                <?php } ?>
              <?php } else { ?>
                <tr><td colspan="4">No hay equipos disponibles.</td></tr>
              <?php } ?>
            </tbody>
          </table>
        </section>

      </div>

      <div class="acciones-centro">
        <button type="submit" class="btn btn-ancho" formaction="guardar_prestamo.php"
                formmethod="post">Autorizar préstamo</button>
      </div>
    </form>
  </main>

</body>
</html>