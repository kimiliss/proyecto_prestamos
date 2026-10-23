<!--
    Autor: Kimberly Garcia
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 22/10/26
    Fecha de modificacion: 22/10/26
    Descripcion: Confirma la devolucion de un prestamo, poniendo la fecha de devolucion
                 y cambiando el equipo a estado disponible (las dos cosas o ninguna)
                 y regresa a devoluciones.php con el aviso que toca.
-->

<?php

require "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: devoluciones.php");
  exit;
}

$id_prestamo = (int) ($_POST["id_prestamo"] ?? 0);

if ($id_prestamo <= 0) {
  header("Location: devoluciones.php?aviso=falta");
  exit;
}

try {
  $conexion->begin_transaction();

  // 1) Se busca el equipo de ese préstamo, solo si todavía no lo devuelven
  $stmt = $conexion->prepare(
    "SELECT id_equipo FROM prestamo WHERE id_prestamo = ? AND fecha_devolucion IS NULL"
  );
  $stmt->bind_param("i", $id_prestamo);
  $stmt->execute();
  $stmt->bind_result($id_equipo);
  $existe = $stmt->fetch();
  $stmt->close();

  if (!$existe) {
    $conexion->rollback();
    header("Location: devoluciones.php?aviso=yadevuelto");
    exit;
  }

  // 2) Se pone la fecha de devolución (hoy)
  $stmt = $conexion->prepare("UPDATE prestamo SET fecha_devolucion = NOW() WHERE id_prestamo = ?");
  $stmt->bind_param("i", $id_prestamo);
  $stmt->execute();
  $stmt->close();

  // 3) El equipo vuelve a estar disponible
  $stmt = $conexion->prepare("UPDATE equipo SET estado = 1 WHERE id_equipo = ?");
  $stmt->bind_param("i", $id_equipo);
  $stmt->execute();
  $stmt->close();

  $conexion->commit();
  header("Location: devoluciones.php?aviso=devuelto");

} catch (mysqli_sql_exception $e) {
  $conexion->rollback();
  header("Location: devoluciones.php?aviso=error");
}
exit;