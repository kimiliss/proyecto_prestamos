<!--
    Autor: Kimberly Garcia 
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 13/10/26
    Fecha de modificacion: 21/10/26
    Descripcion: Guarda un equipo nuevo y regresa a equipos.php
-->

<?php

include "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: equipos.php");
    exit;
}

try {
    $id_tipo       = (int) $_POST['id_tipo'];
    // Todo se guarda en MAYUSCULAS (mb_ para que tambien funcione con acentos y ñ)
    $descripcion   = mb_strtoupper(trim($_POST['descripcion']), 'UTF-8');
    $observaciones = mb_strtoupper(trim($_POST['observaciones']), 'UTF-8');
    $estado        = 1; // 1 = disponible

    $stmt = $conexion->prepare(
        "INSERT INTO equipo (id_tipo, descripcion, observaciones, estado) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("issi", $id_tipo, $descripcion, $observaciones, $estado);
    $stmt->execute();

    header("Location: equipos.php");
    exit;

} catch (Throwable $e) {
    // Si algo falla, mostramos el motivo en vez de dejar la pantalla en blanco
    echo "<h3>No se pudo guardar el equipo</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><a href='equipos.php'>Volver a equipos</a></p>";
}