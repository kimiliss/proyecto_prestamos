<?php
/*
    Autor: Kimberly Garcia
    Fecha de creacion: 20/10/26
    Descripcion: Guarda un solicitante nuevo y regresa a solicitantes.php
*/

require "conexion.php";

// Si entran directo al archivo (sin mandar el formulario), los regresamos
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: solicitantes.php");
  exit;
}

$nombre    = trim($_POST["nombre"] ?? "");
$matricula = trim($_POST["matricula"] ?? "");
$carrera   = trim($_POST["area"] ?? "");   // en el formulario se llama "area", en la tabla es 
                                            //"carrera"

// Que no vengan vacíos
if ($nombre === "" || $matricula === "" || $carrera === "") {
  header("Location: solicitantes.php?error=vacio");
  exit;
}

try {
  $stmt = $conexion->prepare("INSERT INTO solicitante (nombre, matricula, carrera)
                             VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $nombre, $matricula, $carrera);
  $stmt->execute();
  header("Location: solicitantes.php?ok=1");
} catch (mysqli_sql_exception $e) {
  // 1062 = matrícula repetida (la columna es UNIQUE)
  if ($e->getCode() == 1062) {
    header("Location: solicitantes.php?error=repetida");
  } else {
    header("Location: solicitantes.php?error=bd");
  }
}
exit;