<!--
    Autor: Kimberly Garcia
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 22/10/26
    Fecha de modificacion: 22/10/26
    Descripcion: Actualiza un solicitante (nombre, matricula y area/carrera)
                 y regresa a solicitantes.php con el aviso que toca.
-->

<?php

require "conexion.php";

$id        = (int) ($_POST["id"] ?? 0);
$nombre    = trim($_POST["nombre"] ?? "");
$matricula = trim($_POST["matricula"] ?? "");
$carrera   = trim($_POST["area"] ?? "");

if ($_SERVER["REQUEST_METHOD"] !== "POST" || $id <= 0) {
  header("Location: solicitantes.php");
  exit;
}

// Si falta algo, se regresa al mismo solicitante en modo edición
if ($nombre === "" || $matricula === "" || $carrera === "") {
  header("Location: solicitantes.php?editar=" . $id . "&error=vacio");
  exit;
}

try {
  // 1) ¿Esa matrícula ya la tiene otro solicitante?
  $stmt = $conexion->prepare(
    "SELECT COUNT(*) FROM solicitante WHERE matricula = ? AND id_solicitante <> ?"
  );
  $stmt->bind_param("si", $matricula, $id);
  $stmt->execute();
  $stmt->bind_result($repetidas);
  $stmt->fetch();
  $stmt->close();

  if ($repetidas > 0) {
    header("Location: solicitantes.php?editar=" . $id . "&error=repetida");
    exit;
  }

  // 2) Se actualizan los datos
  $stmt = $conexion->prepare(
    "UPDATE solicitante SET nombre = ?, matricula = ?, carrera = ? WHERE id_solicitante = ?"
  );
  $stmt->bind_param("sssi", $nombre, $matricula, $carrera, $id);
  $stmt->execute();
  $stmt->close();

  header("Location: solicitantes.php?ok=2");

} catch (mysqli_sql_exception $e) {
  header("Location: solicitantes.php?editar=" . $id . "&error=bd");
}
exit;