<?php
/*
    Autor: Kimberly Garcia
    Fecha de creacion: 20/10/26
    Descripcion: Conexion a la base de datos datalink
*/

// Para que los errores de MySQL se puedan atrapar con try/catch
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$servidor = "localhost";
$usuario  = "root";
$clave    = "picholino12";
$base     = "datalink";

$conexion = new mysqli($servidor, $usuario, $clave, $base);
$conexion->set_charset("utf8");