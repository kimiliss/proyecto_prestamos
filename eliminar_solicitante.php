<?php
/*
    Autor: Kimberly Garcia
    Fecha de creacion: 20/10/26
    Descripcion: Elimina un solicitante (y su historial de prestamos).
                 Contesta en JSON para que mensajes.js muestre el aviso.
*/

require "conexion.php";
header("Content-Type: application/json; charset=utf-8");

$id = (int) ($_POST["id"] ?? 0);

if ($_SERVER["REQUEST_METHOD"] !== "POST" || $id <= 0) {
  echo json_encode(["ok" => false, "mensaje" => "Solicitud no válida."]);
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
    echo json_encode([
      "ok" => false,
      "mensaje" => "No se puede eliminar, el solicitante tiene préstamos activos."
    ]);
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
  echo json_encode(["ok" => true]);

} catch (mysqli_sql_exception $e) {
  $conexion->rollback();
  echo json_encode(["ok" => false, "mensaje" => "Ocurrió un problema al eliminar, intenta de nuevo."]);
}