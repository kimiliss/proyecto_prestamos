<!--
    Autor: Kimberly Garcia 
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 20/10/26
    Fecha de modificacion: 08/11/26
    Descripcion: Pagina solicitantes del proyecto
-->

<?php

require "conexion.php";

// Si entran directo al archivo (sin mandar el formulario), los regresamos
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: solicitantes.php");
  exit;
}

// Todo se guarda en mayúsculas (mb_strtoupper respeta acentos y la Ñ)
// En el formulario el campo se llama "area", en la tabla es "carrera"
$nombre    = mb_strtoupper(trim($_POST["nombre"] ?? ""), "UTF-8");
$matricula = mb_strtoupper(trim($_POST["matricula"] ?? ""), "UTF-8");
$carrera   = mb_strtoupper(trim($_POST["area"] ?? ""), "UTF-8");

// Si no escribieron matrícula se guarda NULL (así no choca con el UNIQUE)
if ($matricula === "") {
  $matricula = null;
}

// Nombre y área son obligatorios (la matrícula ya no)
if ($nombre === "" || $carrera === "") {
  header("Location: solicitantes.php?error=vacio");
  exit;
}

// El nombre solo puede llevar letras y espacios
if (!preg_match("/^[\p{L} ]+$/u", $nombre)) {
  header("Location: solicitantes.php?error=nombre");
  exit;
}

// La matrícula, si viene, máximo 8 caracteres
if ($matricula !== null && mb_strlen($matricula, "UTF-8") > 8) {
  header("Location: solicitantes.php?error=matricula");
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