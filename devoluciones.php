<!--
    Autor: Kimberly Garcia 
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 18/10/26
    Fecha de modificacion: 18/10/26
    Descripcion: Pagina registrar devolucion del proyecto
-->

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Devoluciones - Data Link</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family
            =Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php include "encabezado.php"; ?>

  <main>

    <div class="cabecera-pantalla cabecera-devolucion">
      <h1>Registrar devolución</h1>
      <p class="subtitulo">Localiza el préstamo activo y confirma la recepción del equipo.</p>
      <p class="nota-lateral">Se mostrarán todos los equipos en préstamo o puede buscarlo por
                                 solicitante o equipo</p>
    </div>

    <section class="lista pantalla-una-columna">

      <!-- Búsqueda -->
      <label class="lista-titulo" for="buscar">Buscar por solicitante o equipo</label>
      <form class="busqueda" action="devoluciones.php" method="get">
        <input type="search" id="buscar" name="q" placeholder="Ana López">
        <button type="submit" class="btn">Buscar</button>
      </form>

      <form action="confirmar_devolucion.php" method="post">
        <table class="tabla tabla-devoluciones">
          <thead>
            <tr>
              <th><span class="solo-lector">Seleccionar</span></th>
              <th>Solicitante</th>
              <th>Equipo</th>
              <th>Salida</th>
              <th>Límite</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            <!-- Aquí van las filas desde la base de datos, por ejemplo:
            <tr>
              <td><input type="radio" name="id_prestamo" value="1" required></td>
              <td>Ana López</td>
              <td>EQ-104 · Laptop</td>
              <td>12/06/2026</td>
              <td>19/06/2026</td>
              <td class="estado-ocupado">Ocupado</td>
            </tr>
            -->
          </tbody>
        </table>

        <p class="aviso-ok">Al confirmar, el equipo volverá a estar Disponible.</p>

        <div class="acciones-derecha">
          <button type="submit" class="btn btn-ancho">Confirmar devolución</button>
        </div>
      </form>

    </section>
  </main>

</body>
</html>