<!--
    Autor: Kimberly Garcia 
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 13/10/26
    Fecha de modificacion: 16/10/26
    Descripcion: Este archivo contiene el encabezado y la barra de navegación del proyecto.
-->

<?php
/*
    Este archivo contiene el encabezado y la barra de navegación del proyecto.
    Detecta en que pagina se encuentra el usuario y resalta el enlace correspondiente en la barra de navegación.
*/
$paginaActual = basename($_SERVER['PHP_SELF']);

$menu = [
    'index.php'        => 'Inicio',
    'solicitantes.php' => 'Solicitantes',
    'equipos.php'      => 'Equipos',
    'autorizar.php'    => 'Autorizar préstamo',
    'devoluciones.php' => 'Devoluciones',
    'reportes.php'     => 'Reportes',
];
?>
<header class="encabezado">
  <div class="marca">
    <img src="logotipouca.png" alt="logouca" width="55" height="60">
    <span class="marca-nombre">Universidad Continente Americano</span>
  </div>
  <a class="sesion" href="salir.php">Administrador • Salir</a>
</header>

<nav class="nav" aria-label="Principal">
  <ul>
    <?php foreach ($menu as $archivo => $texto): ?>
      <li>
        <a href="<?= $archivo ?>"<?= $archivo === $paginaActual ? ' aria-current="page"' : '' ?>><?= $texto ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>