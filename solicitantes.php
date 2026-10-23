<!--
    Autor: Kimberly Garcia 
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 13/10/26
    Fecha de modificacion: 22/10/26
    Descripcion: Pagina solicitantes del proyecto
-->

<?php
  require "conexion.php";
  require "mensajes.php";

  // Texto de la búsqueda (si no escribieron nada queda vacío)
  $q = trim($_GET["q"] ?? "");

  // Traemos los solicitantes, ordenados por ID
  if ($q !== "") {
    $stmt = $conexion->prepare(
      "SELECT id_solicitante, nombre, matricula, carrera
       FROM solicitante
       WHERE nombre LIKE ? OR matricula LIKE ?
       ORDER BY id_solicitante"
    );
    $like = "%" . $q . "%";
    $stmt->bind_param("ss", $like, $like);
  } else {
    $stmt = $conexion->prepare(
      "SELECT id_solicitante, nombre, matricula, carrera
       FROM solicitante
       ORDER BY id_solicitante"
    );
  }
  $stmt->execute();
  $resultado = $stmt->get_result();

  // Si viene ?editar=ID, traemos ese solicitante para llenar el formulario
  $editar = (int) ($_GET["editar"] ?? 0);
  $datos  = null;
  if ($editar > 0) {
    $stmt_ed = $conexion->prepare(
      "SELECT id_solicitante, nombre, matricula, carrera FROM solicitante WHERE id_solicitante = ?"
    );
    $stmt_ed->bind_param("i", $editar);
    $stmt_ed->execute();
    $datos = $stmt_ed->get_result()->fetch_assoc();   // queda en null si no existe
  }
  $modo_editar = ($datos !== null);

  // Si viene ?eliminar=ID, buscamos el nombre para la ventana de confirmación
  $eliminar     = (int) ($_GET["eliminar"] ?? 0);
  $a_eliminar   = null;
  if ($eliminar > 0) {
    $stmt_el = $conexion->prepare("SELECT id_solicitante, nombre FROM solicitante WHERE
                                  id_solicitante = ?");
    $stmt_el->bind_param("i", $eliminar);
    $stmt_el->execute();
    $a_eliminar = $stmt_el->get_result()->fetch_assoc();   // null si no existe
  }

  // Aviso después de eliminar (lo manda eliminar_solicitante.php)
  $aviso = $_GET["aviso"] ?? "";

  // Mensajes que manda guardar_solicitante.php
  $ok    = $_GET["ok"] ?? "";
  $error = $_GET["error"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Solicitantes - Data Link</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family
            =Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php include "encabezado.php"; ?>

  <main>
    <h1>Solicitantes</h1>
    <p class="subtitulo">Registra y administra a las personas autorizadas para solicitar equipos.</p>

    <!-- Mensajes de guardado -->
    <?php if ($ok == "1"): ?>
      <p class="aviso-ok aviso-corto">Solicitante guardado correctamente.</p>
    <?php elseif ($ok == "2"): ?>
      <p class="aviso-ok aviso-corto">Cambios guardados correctamente.</p>
    <?php elseif ($error == "vacio"): ?>
      <p class="aviso-error aviso-corto">Llena todos los campos antes de guardar.</p>
    <?php elseif ($error == "repetida"): ?>
      <p class="aviso-error aviso-corto">Esa matrícula ya está registrada.</p>
    <?php elseif ($error == "bd"): ?>
      <p class="aviso-error aviso-corto">No se pudo guardar, intenta de nuevo.</p>
    <?php endif; ?>

    <div class="dos-columnas">

      <!-- Formulario (sirve para registrar y para editar) -->
      <form class="formulario" action="<?php echo $modo_editar ? 'editar_solicitante.php' :
            'guardar_solicitante.php'; ?>" method="post">
        <input type="hidden" name="id" value="<?php echo $modo_editar ? $datos["id_solicitante"] : ""; ?>">

        <div class="campo">
          <label for="nombre">Nombre completo</label>
          <input type="text" id="nombre" name="nombre" placeholder="Ana López García" maxlength="70"
                 value="<?php echo $modo_editar ? htmlspecialchars($datos["nombre"]) : ""; ?>" required>
        </div>

        <div class="campo">
          <label for="matricula">Matrícula</label>
          <input type="text" id="matricula" name="matricula" placeholder="24241568" maxlength="30"
                 value="<?php echo $modo_editar ? htmlspecialchars($datos["matricula"]) : ""; ?>"
                 required>
        </div>

        <div class="campo">
          <label for="area">Área / carrera</label>
          <input type="text" id="area" name="area" placeholder="Sistemas" maxlength="100"
                 value="<?php echo $modo_editar ? htmlspecialchars($datos["carrera"]) : ""; ?>" 
                 required>
        </div>

        <div class="acciones-form">
          <button type="submit" class="btn"><?php echo $modo_editar ? "Guardar cambios" : "Guardar"; ?>
          </button>
          <?php if ($modo_editar): ?>
            <a class="btn-texto" href="solicitantes.php">Cancelar</a>
          <?php else: ?>
            <button type="reset" class="btn-texto">Limpiar</button>
          <?php endif; ?>
        </div>
      </form>

      <!-- Lista -->
      <section class="lista">
        <label class="lista-titulo" for="buscar">Lista de solicitantes</label>
        <form class="busqueda" action="solicitantes.php" method="get">
          <input type="search" id="buscar" name="q" placeholder="Buscar por nombre o matrícula"
                 value="<?php echo htmlspecialchars($q); ?>">
          <button type="submit" class="btn">Buscar</button>
        </form>

        <table class="tabla">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Matrícula</th>
              <th>Área</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($resultado->num_rows == 0): ?>
              <tr>
                <td colspan="5">No hay solicitantes para mostrar.</td>
              </tr>
            <?php else: ?>
              <?php while ($fila = $resultado->fetch_assoc()): ?>
              <tr>
                <td><?php echo sprintf("S-%03d", $fila["id_solicitante"]); ?></td>
                <td><?php echo htmlspecialchars($fila["nombre"]); ?></td>
                <td><?php echo htmlspecialchars($fila["matricula"]); ?></td>
                <td><?php echo htmlspecialchars($fila["carrera"]); ?></td>
                <td>
                  <a href="solicitantes.php?editar=<?php echo $fila["id_solicitante"]; ?>">Editar</a>
                  <a href="solicitantes.php?eliminar=<?php echo $fila["id_solicitante"]; ?>">Eliminar</a>
                </td>
              </tr>
              <?php endwhile; ?>
            <?php endif; ?>
          </tbody>
        </table>

        <p class="nota">El orden se mostrará dependiendo de su ID</p>
      </section>

    </div>
  </main>

  <!-- Ventanas de mensaje -->
  <?php if ($a_eliminar !== null): ?>
    <?php ventana_confirmar(
      "¿Eliminar solicitante?",
      "Se eliminará a " . $a_eliminar["nombre"] . " y todo su historial de préstamos. ¿Deseas continuar?",
      "solicitantes.php",
      "eliminar_solicitante.php",
      ["id" => $a_eliminar["id_solicitante"]]
    ); ?>
  <?php elseif ($aviso == "eliminado"): ?>
    <?php ventana_aviso("ok", "Registro eliminado", "El solicitante fue eliminado correctamente.",
                         "solicitantes.php"); ?>
  <?php elseif ($aviso == "activos"): ?>
    <?php ventana_aviso("error", "No se puede eliminar", "No se puede eliminar, 
                        el solicitante tiene préstamos activos.", "solicitantes.php"); ?>
  <?php elseif ($aviso == "error"): ?>
    <?php ventana_aviso("error", "No se puede eliminar", "Ocurrió un problema, intenta de nuevo.",
                        "solicitantes.php"); ?>
  <?php endif; ?>

</body>
</html>