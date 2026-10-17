<!--
    Autor: Kimberly Garcia 
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 13/10/26
    Fecha de modificacion: 16/10/26
    Descripcion: Pagina inicial del proyecto
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Data Link</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=
                Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "encabezado.php"; ?>
 
  <main id="inicio">
    <h1>Bienvenido a Data Link</h1>
    <p class="subtitulo">Gestiona el préstamo de dispositivos electrónicos desde un solo lugar.</p>
 
    <ul class="modulos">
      <li>
        <a class="modulo" href="solicitantes.php">
          <span class="num">01</span>
          <span><h2>Solicitantes</h2><p>Alta y consulta de usuarios</p></span>
        </a>
      </li>
      <li>
        <a class="modulo" href="equipos.php">
          <span class="num">02</span>
          <span><h2>Equipos</h2><p>Inventario y disponibilidad</p></span>
        </a>
      </li>
      <li>
        <a class="modulo" href="autorizar.php">
          <span class="num">03</span>
          <span><h2>Autorizar préstamo</h2><p>Autorizar una nueva salida</p></span>
        </a>
      </li>
      <li>
        <a class="modulo" href="devoluciones.php">
          <span class="num">04</span>
          <span><h2>Devoluciones</h2><p>Registrar equipos recibidos</p></span>
        </a>
      </li>
      <li>
        <a class="modulo" href="reportes.php">
          <span class="num">05</span>
          <span><h2>Reportes</h2><p>Prestados, disponibles y vencidos</p></span>
        </a>
      </li>
    </ul>
  </main>
    
</body>
</html>