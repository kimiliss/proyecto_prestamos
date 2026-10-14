<!--
    Autor: Kimberly Garcia 
    Ultimo modificador: yo
    Fecha de creacion: 13/10/26
    Fecha de modificacion: hoy
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
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Source+Sans+3:wght@400;600;700&display=swap"
    rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="encabezado">
    <div class="marca">
      <img src="logotipouca.png" alt="logouca" width="55px" height="60px">
      <span class="marca-nombre">Universidad Continente Americano</span>
    </div>
    <a class="sesion" href="#salir">Administrador • Salir</a>
    </header>
 
  <nav class="nav" aria-label="Principal">
    <ul>
      <li><a href="#inicio" aria-current="page">Inicio</a></li>
      <li><a href="#solicitantes">Solicitantes</a></li>
      <li><a href="#equipos">Equipos</a></li>
      <li><a href="#autorizar">Autorizar préstamo</a></li>
      <li><a href="#devoluciones">Devoluciones</a></li>
      <li><a href="#vencidos">Vencidos</a></li>
      <li><a href="#reportes">Reportes</a></li>
    </ul>
  </nav>
 
  <main id="inicio">
    <h1>Bienvenido a Data Link</h1>
    <p class="subtitulo">Gestiona el préstamo de dispositivos electrónicos desde un solo lugar.</p>
 
    <ul class="modulos">
      <li>
        <a class="modulo" href="#solicitantes">
          <span class="num">01</span>
          <span><h2>Solicitantes</h2><p>Alta y consulta de usuarios</p></span>
        </a>
      </li>
      <li>
        <a class="modulo" href="#equipos">
          <span class="num">02</span>
          <span><h2>Equipos</h2><p>Inventario y disponibilidad</p></span>
        </a>
      </li>
      <li>
        <a class="modulo" href="#autorizar">
          <span class="num">03</span>
          <span><h2>Autorizar préstamo</h2><p>Autorizar una nueva salida</p></span>
        </a>
      </li>
      <li>
        <a class="modulo" href="#devoluciones">
          <span class="num">04</span>
          <span><h2>Devoluciones</h2><p>Registrar equipos recibidos</p></span>
        </a>
      </li>
      <li>
        <a class="modulo" href="#vencidos">
          <span class="num">05</span>
          <span><h2>Vencidos</h2><p>Equipos fuera de fecha</p></span>
        </a>
      </li>
      <li>
        <a class="modulo" href="#reportes">
          <span class="num">06</span>
          <span><h2>Reportes</h2><p>Prestados, disponibles y vencidos</p></span>
        </a>
      </li>
    </ul>
  </main>
    
</body>
</html>