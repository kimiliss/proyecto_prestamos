<!--
    Autor: Kimberly Garcia 
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 13/10/26
    Fecha de modificacion: 22/10/26
    Descripcion: Pagina de equipos 
-->

<?php

include "conexion.php"; 
require "mensajes.php";

// ---- Tipos de equipo para el select del formulario ----
$tipos = $conexion->query("SELECT id_tipo, nombre FROM tipoequipo ORDER BY nombre = 'OTRO', nombre");

// ---- Lista de equipos (con busqueda opcional) ----
$q = isset($_GET['q']) ? trim($_GET['q']) : "";

$sql = "SELECT e.id_equipo, t.nombre AS tipo, e.descripcion, e.observaciones, e.estado
        FROM equipo e
        INNER JOIN tipoequipo t ON t.id_tipo = e.id_tipo";

if ($q !== "") {
    $sql .= " WHERE t.nombre LIKE ? OR e.descripcion LIKE ?";
}
$sql .= " ORDER BY e.id_equipo ASC";

$stmt = $conexion->prepare($sql);
if ($q !== "") {
    $like = "%" . $q . "%";
    $stmt->bind_param("ss", $like, $like);
}
$stmt->execute();
$equipos = $stmt->get_result();

// ---- Si le dieron a "Eliminar", buscamos el equipo para la ventana de confirmación ----
$equipo_a_eliminar = null;
$id_eliminar = (int) ($_GET['eliminar'] ?? 0);
if ($id_eliminar > 0) {
    $stmt2 = $conexion->prepare("SELECT id_equipo, descripcion FROM equipo WHERE id_equipo = ?");
    $stmt2->bind_param("i", $id_eliminar);
    $stmt2->execute();
    $equipo_a_eliminar = $stmt2->get_result()->fetch_assoc();
    $stmt2->close();
}

// ---- Si le dieron a "Editar", buscamos el equipo para llenar el formulario ----
$equipo_a_editar = null;
$id_editar = (int) ($_GET['editar'] ?? 0);
if ($id_editar > 0) {
    $stmt3 = $conexion->prepare("SELECT id_equipo, id_tipo, descripcion, observaciones FROM equipo WHERE id_equipo = ?");
    $stmt3->bind_param("i", $id_editar);
    $stmt3->execute();
    $equipo_a_editar = $stmt3->get_result()->fetch_assoc();
    $stmt3->close();
}

// ---- Aviso que regresa eliminar_equipo.php / editar_equipo.php ----
$aviso = $_GET['aviso'] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Equipos - Data Link</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php include "encabezado.php"; ?>

  <?php
  // ---- Ventanas de mensaje ----
  if ($equipo_a_eliminar) {
      ventana_confirmar(
          "¿Eliminar equipo?",
          "Se eliminará EQ-" . str_pad($equipo_a_eliminar['id_equipo'], 3, "0", STR_PAD_LEFT) . " del inventario.",
          "equipos.php",
          "eliminar_equipo.php",
          ["id" => $equipo_a_eliminar['id_equipo']],
          "Eliminar"
      );
  }

  if ($aviso == "eliminado") {
      ventana_aviso("ok", "Equipo eliminado", "El inventario se actualizó correctamente.", "equipos.php");
  } elseif ($aviso == "activos") {
      ventana_aviso("error", "No se puede eliminar", "No se puede eliminar, el equipo está en estatus de préstamo", "equipos.php");
  } elseif ($aviso == "editado") {
      ventana_aviso("ok", "Cambios guardados", "El equipo fue actualizado correctamente.", "equipos.php");
  } elseif ($aviso == "error_editar") {
      ventana_aviso("error", "Ocurrió un error", "No se pudo actualizar el equipo, intenta de nuevo.", "equipos.php");
  } elseif ($aviso == "error") {
      ventana_aviso("error", "Ocurrió un error", "No se pudo eliminar el equipo, intenta de nuevo.", "equipos.php");
  }
  ?>

  <main>
    <h1>Equipos</h1>
    <p class="subtitulo">Registra dispositivos y consulta el inventario. El ID se asigna automáticamente</p>

    <div class="dos-columnas">

      <!-- Formulario -->
      <form class="formulario" action="<?= $equipo_a_editar ? 'editar_equipo.php' : 'guardar_equipo.php' ?>" method="post">
        <?php if ($equipo_a_editar) { ?>
          <input type="hidden" name="id" value="<?= $equipo_a_editar['id_equipo'] ?>">
        <?php } ?>

        <div class="campo">
          <label for="id_tipo">Tipo de equipo</label>
          <select id="id_tipo" name="id_tipo" required>
            <option value="">Selecciona un tipo</option>
            <?php while ($t = $tipos->fetch_assoc()) { ?>
              <option value="<?= $t['id_tipo'] ?>"
                <?= ($equipo_a_editar && $equipo_a_editar['id_tipo'] == $t['id_tipo']) ? "selected" : "" ?>>
                <?= htmlspecialchars($t['nombre']) ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="campo">
          <label for="descripcion">Descripción</label>
          <input type="text" id="descripcion" name="descripcion" maxlength="150"
                 value="<?= $equipo_a_editar ? htmlspecialchars($equipo_a_editar['descripcion']) : '' ?>"
                 placeholder="Especificaciones del equipo" required>
        </div>

        <div class="campo">
          <label for="observaciones">Observaciones</label>
          <input type="text" id="observaciones" name="observaciones" maxlength="200"
                 value="<?= $equipo_a_editar ? htmlspecialchars($equipo_a_editar['observaciones']) : '' ?>"
                 placeholder="Notas adicionales" required>
        </div>

        <div class="acciones-form">
          <?php if ($equipo_a_editar) { ?>
            <button type="submit" class="btn">Actualizar</button>
            <a class="btn-texto" href="equipos.php">Cancelar</a>
          <?php } else { ?>
            <button type="submit" class="btn">Guardar</button>
            <button type="reset" class="btn-texto">Limpiar</button>
          <?php } ?>
        </div>
      </form>

      <!-- Lista -->
      <section class="lista">
        <label class="lista-titulo" for="buscar">Lista de equipos</label>
        <form class="busqueda" action="equipos.php" method="get">
          <input type="search" id="buscar" name="q" value="<?= htmlspecialchars($q) ?>"
                 placeholder="Buscar por tipo o descripción">
          <button type="submit" class="btn">Buscar</button>
        </form>

        <table class="tabla">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tipo de equipo</th>
              <th>Descripción</th>
              <th>Observaciones</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($equipos->num_rows > 0) { ?>
              <?php while ($f = $equipos->fetch_assoc()) { ?>
                <tr>
                  <td>EQ-<?= str_pad($f['id_equipo'], 3, "0", STR_PAD_LEFT) ?></td>
                  <td><?= htmlspecialchars($f['tipo']) ?></td>
                  <td><?= htmlspecialchars($f['descripcion']) ?></td>
                  <td><?= htmlspecialchars($f['observaciones']) ?></td>
                  <td><?= $f['estado'] == 1 ? "Disponible" : "Prestado" ?></td>
                  <td>
                    <a href="equipos.php?editar=<?= $f['id_equipo'] ?>">Editar</a>
                    <a href="equipos.php?eliminar=<?= $f['id_equipo'] ?>">Eliminar</a>
                  </td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr><td colspan="6">No hay equipos registrados.</td></tr>
            <?php } ?>
          </tbody>
        </table>

        <p class="nota">El orden se mostrará dependiendo de su ID</p>
      </section>

    </div>
  </main>

</body>
</html>