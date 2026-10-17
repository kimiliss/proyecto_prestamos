<!--
    Autor: Kimberly Garcia 
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 13/10/26
    Fecha de modificacion: 16/10/26
    Descripcion: Pagina de equipos 
-->

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Equipos - Data Link</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family
            =Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php include "encabezado.php"; ?>

  <main>
    <h1>Equipos</h1>
    <p class="subtitulo">Registra dispositivos y consulta el inventario. El ID se asigna
                        automáticamente</p>

    <div class="dos-columnas">

      <!-- Formulario -->
      <form class="formulario" action="guardar_equipo.php" method="post">
        <div class="campo">
          <label for="tipo_equipo">Tipo de equipo</label>
          <input type="text" id="tipo_equipo" name="tipo_equipo" placeholder="Laptop, Desktop, etc."
           required>
        </div>

        <div class="campo">
          <label for="descripcion">Descripción</label>
          <input type="text" id="descripcion" name="descripcion" placeholder="Especificaciones del equipo"
           required>
        </div>

        <div class="campo">
          <label for="observaciones">Observaciones</label>
          <input type="text" id="observaciones" name="observaciones" placeholder="Notas adicionales"
           required>
        </div>

        <div class="acciones-form">
          <button type="submit" class="btn">Guardar</button>
          <button type="reset" class="btn-texto">Limpiar</button>
        </div>
      </form>

      <!-- Lista -->
      <section class="lista">
        <label class="lista-titulo" for="buscar">Lista de equipos</label>
        <form class="busqueda" action="equipos.php" method="get">
          <input type="search" id="buscar" name="q" placeholder="Buscar por tipo o descripción">
          <button type="submit" class="btn">Buscar</button>
        </form>

        <table class="tabla">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tipo de equipo</th>
              <th>Descripción</th>
              <th>Observaciones</th>
              <th>Estado</th>
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