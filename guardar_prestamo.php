<!--
    Autor: Kimberly Garcia
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 22/10/26
    Fecha de modificacion: 22/10/26
    Descripcion: Guarda un prestamo y cambia el equipo a estado Prestado
                 (las dos cosas o ninguna) y regresa a autorizar.php con el aviso que toca.
-->


<?php

require "conexion.php";

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: autorizar.php");
  exit;
}

$id_solicitante = (int) ($_POST["solicitante"] ?? 0);
$id_equipo      = (int) ($_POST["id_equipo"] ?? 0);
$fecha_prestamo = $_POST["fecha_prestamo"] ?? "";
$fecha_limite   = $_POST["fecha_limite"] ?? "";
$observaciones  = trim($_POST["observaciones"] ?? "");

// Quien autoriza el préstamo (ajusta el nombre de la variable de sesión si el tuyo es otro)
$id_usuario = isset($_SESSION["id_usuario"]) ? (int) $_SESSION["id_usuario"] : null;

// 1) Que no falte el equipo ni el solicitante
if ($id_equipo <= 0) {
  header("Location: autorizar.php?aviso=falta_equipo");
  exit;
}
if ($id_solicitante <= 0) {
  header("Location: autorizar.php");
  exit;
}

// 2) Fechas válidas y devolución después del préstamo
$d1 = DateTime::createFromFormat("Y-m-d", $fecha_prestamo);
$d2 = DateTime::createFromFormat("Y-m-d", $fecha_limite);
if (!$d1 || !$d2 || $d2 <= $d1) {
  header("Location: autorizar.php?aviso=fechas");
  exit;
}

try {
  $conexion->begin_transaction();

  // 3) Se marca el equipo como prestado, pero solo si seguía disponible
  $stmt = $conexion->prepare("UPDATE equipo SET estado = 0 WHERE id_equipo = ? AND estado = 1");
  $stmt->bind_param("i", $id_equipo);
  $stmt->execute();
  $actualizados = $stmt->affected_rows;
  $stmt->close();

  if ($actualizados === 0) {
    $conexion->rollback();
    header("Location: autorizar.php?aviso=ocupado");
    exit;
  }

  // 4) Se registra el préstamo (fecha_devolucion se queda vacía hasta que lo devuelvan)
  $stmt = $conexion->prepare(
    "INSERT INTO prestamo (id_solicitante, id_equipo, id_usuario, fecha_prestamo, fecha_limite, observaciones)
     VALUES (?, ?, ?, ?, ?, ?)"
  );
  $stmt->bind_param("iiisss", $id_solicitante, $id_equipo, $id_usuario, $fecha_prestamo, $fecha_limite, $observaciones);
  $stmt->execute();
  $stmt->close();

  $conexion->commit();
  header("Location: autorizar.php?aviso=guardado");

} catch (mysqli_sql_exception $e) {
  $conexion->rollback();
  header("Location: autorizar.php?aviso=error");
}
exit;