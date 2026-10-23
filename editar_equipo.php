<!--
    Autor: Kimberly Garcia
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 22/10/26
    Fecha de modificacion: 22/10/26
    Descripcion: Actualiza un equipo (tipo, descripcion y observaciones)
                 y regresa a equipos.php con el aviso que toca.
-->

<?php

require "conexion.php";

$id            = (int) ($_POST["id"] ?? 0);
$id_tipo       = (int) ($_POST["id_tipo"] ?? 0);
$descripcion   = mb_strtoupper(trim($_POST["descripcion"] ?? ""), "UTF-8");
$observaciones = mb_strtoupper(trim($_POST["observaciones"] ?? ""), "UTF-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST" || $id <= 0) {
  header("Location: equipos.php");
  exit;
}

// Si falta algo, se regresa al mismo equipo en modo edición
if ($id_tipo <= 0 || $descripcion === "" || $observaciones === "") {
  header("Location: equipos.php?editar=" . $id);
  exit;
}

try {
  $stmt = $conexion->prepare(
    "UPDATE equipo SET id_tipo = ?, descripcion = ?, observaciones = ? WHERE id_equipo = ?"
  );
  $stmt->bind_param("issi", $id_tipo, $descripcion, $observaciones, $id);
  $stmt->execute();
  $stmt->close();

  header("Location: equipos.php?aviso=editado");

} catch (mysqli_sql_exception $e) {
  header("Location: equipos.php?aviso=error_editar");
}
exit;