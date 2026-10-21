<?php
/*
    Autor: Kimberly Garcia
    Fecha de creacion: 20/10/26
    Descripcion: Elimina un solicitante (y su historial de prestamos)
                 y regresa a solicitantes.php con el aviso que toca.
*/

require "conexion.php";

$id = (int) ($_POST["id"] ?? 0);

if ($_SERVER["REQUEST_METHOD"] !== "POST" || $id <= 0) {
  header("Location: solicitantes.php");
  exit;
}

try {
  // 1) ¿Tiene préstamos activos? (los que todavía no se devuelven)
  $stmt = $conexion->prepare(
    "SELECT COUNT(*) FROM prestamo WHERE id_solicitante = ? AND fecha_devolucion IS NULL"
  );
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->bind_result($activos);
  $stmt->fetch();
  $stmt->close();

  if ($activos > 0) {
    header("Location: solicitantes.php?aviso=activos");
    exit;
  }

  // 2) Se borra su historial y luego el solicitante (las dos cosas o ninguna)
  $conexion->begin_transaction();

  $stmt = $conexion->prepare("DELETE FROM prestamo WHERE id_solicitante = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();

  $stmt = $conexion->prepare("DELETE FROM solicitante WHERE id_solicitante = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();

  $conexion->commit();
  header("Location: solicitantes.php?aviso=eliminado");

} catch (mysqli_sql_exception $e) {
  $conexion->rollback();
  header("Location: solicitantes.php?aviso=error");
}
exit;