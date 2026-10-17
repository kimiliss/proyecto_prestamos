<!--
    Autor: Kimberly Garcia 
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 13/10/26
    Fecha de modificacion: 16/10/26
    Descripcion: Pagina solicitantes del proyecto
-->

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Solicitantes - Data Link</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family
            =Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php include "encabezado.php"; ?>

  <main>
    <h1>Solicitantes</h1>
    <p class="subtitulo">Registra y administra a las personas autorizadas para solicitar equipos.</p>

    <div class="dos-columnas">

      <!-- Formulario -->
      <form class="formulario" action="guardar_solicitante.php" method="post">
        <div class="campo">
          <label for="nombre">Nombre completo</label>
          <input type="text" id="nombre" name="nombre" placeholder="Ana López García" required>
        </div>

        <div class="campo">
          <label for="matricula">Matrícula</label>
          <input type="text" id="matricula" name="matricula" placeholder="24241568" required>
        </div>

        <div class="campo">
          <label for="area">Área / carrera</label>
          <input type="text" id="area" name="area" placeholder="Sistemas" required>
        </div>

        <div class="acciones-form">
          <button type="submit" class="btn">Guardar</button>
          <button type="reset" class="btn-texto">Limpiar</button>
        </div>
      </form>

      <!-- Lista -->
      <section class="lista">
        <label class="lista-titulo" for="buscar">Lista de solicitantes</label>
        <form class="busqueda" action="solicitantes.php" method="get">
          <input type="search" id="buscar" name="q" placeholder="Buscar por nombre o matrícula">
          <button type="submit" class="btn">Buscar</button>
        </form>

        <table class="tabla">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Matrícula</th>
              <th>Área</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <!-- Aquí van las filas desde la base de datos -->
          </tbody>
        </table>

        <p class="nota">El orden se mostrará dependiendo de su ID</p>
      </section>

    </div>
  </main>

</body>
</html>