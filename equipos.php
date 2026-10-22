<!--
    Autor: Kimberly Garcia 
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 13/10/26
    Fecha de modificacion: 21/10/26
    Descripcion: Pagina de equipos 
-->

<?php

include "conexion.php"; 

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

  <main>
    <h1>Equipos</h1>
    <p class="subtitulo">Registra dispositivos y consulta el inventario. El ID se asigna automáticamente</p>

    <div class="dos-columnas">

      <!-- Formulario -->
      <form class="formulario" action="guardar_equipo.php" method="post">
        <div class="campo">
          <label for="id_tipo">Tipo de equipo</label>
          <select id="id_tipo" name="id_tipo" required>
            <option value="">Selecciona un tipo</option>
            <?php while ($t = $tipos->fetch_assoc()) { ?>
              <option value="<?= $t['id_tipo'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="campo">
          <label for="descripcion">Descripción</label>
          <input type="text" id="descripcion" name="descripcion" maxlength="150"
                 placeholder="Especificaciones del equipo" required>
        </div>

        <div class="campo">
          <label for="observaciones">Observaciones</label>
          <input type="text" id="observaciones" name="observaciones" maxlength="200"
                 placeholder="Notas adicionales" required>
        </div>

        <div class="acciones-form">
          <button type="submit" class="btn">Guardar</button>
          <button type="reset" class="btn-texto">Limpiar</button>
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
                  <td><?= $f['id_equipo'] ?></td>
                  <td><?= htmlspecialchars($f['tipo']) ?></td>
                  <td><?= htmlspecialchars($f['descripcion']) ?></td>
                  <td><?= htmlspecialchars($f['observaciones']) ?></td>
                  <td><?= $f['estado'] == 1 ? "Disponible" : "Prestado" ?></td>
                  <td><!-- Aquí van Editar / Eliminar --></td>
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
</html>