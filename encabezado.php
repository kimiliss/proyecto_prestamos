<!--
    Autor: Kimberly Garcia 
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 13/10/26
    Fecha de modificacion: 13/11/26
    Descripcion: Este archivo contiene el encabezado y la barra de navegación del proyecto.
-->

<?php

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
</header>

<nav class="nav" aria-label="Principal">
  <ul>
    <?php foreach ($menu as $archivo => $texto): ?>
      <li>
        <a href="<?= $archivo ?>"<?= $archivo === $paginaActual ? ' aria-current="page"' : '' ?>>
                 <?= $texto ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>