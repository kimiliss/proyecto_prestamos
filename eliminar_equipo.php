<!--
    Autor: Kimberly Garcia
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 22/10/26
    Fecha de modificacion: 22/10/26
    Descripcion: Elimina un equipo (y su historial de prestamos)
                 y regresa a equipos.php con el aviso que toca.
-->

<?php

require "conexion.php";

$id = (int) ($_POST["id"] ?? 0);

if ($_SERVER["REQUEST_METHOD"] !== "POST" || $id <= 0) {
  header("Location: equipos.php");
  exit;
}

try {
  // 1) ¿Tiene préstamos activos? (los que todavía no se devuelven)
  $stmt = $conexion->prepare(
    "SELECT COUNT(*) FROM prestamo WHERE id_equipo = ? AND fecha_devolucion IS NULL"
  );
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->bind_result($activos);
  $stmt->fetch();
  $stmt->close();

  if ($activos > 0) {
    header("Location: equipos.php?aviso=activos");
    exit;
  }

  // 2) Se borra su historial y luego el equipo (las dos cosas o ninguna)
  $conexion->begin_transaction();

  $stmt = $conexion->prepare("DELETE FROM prestamo WHERE id_equipo = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();

  $stmt = $conexion->prepare("DELETE FROM equipo WHERE id_equipo = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();

  $conexion->commit();
  header("Location: equipos.php?aviso=eliminado");

} catch (mysqli_sql_exception $e) {
  $conexion->rollback();
  header("Location: equipos.php?aviso=error");
}
exit;