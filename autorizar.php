<!--
    Autor: Kimberly Garcia
    Ultimo modificador: Kimberly Garcia
    Fecha de creacion: 13/10/26
    Fecha de modificacion: 16/10/26
    Descripcion: Pantalla para autorizar un prestamo de equipo
-->

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Autorizar préstamo - Data Link</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family
            =Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php include "encabezado.php"; ?>

  <main class="col-izq-ancha">
    <div class="cabecera-pantalla">
      <h1>Autorizar préstamo</h1>
      <p class="subtitulo">Valida al solicitante, selecciona el equipo y define el periodo.</p>
      <p class="nota nota-lateral">
        Se mostrarán todos los equipos o se pueden buscar<br>
        Solo se puede seleccionar un dispositivo
      </p>
    </div>

    <form action="guardar_prestamo.php" method="post">
      <div class="dos-columnas">

        <!-- Columna izquierda: datos del préstamo -->
        <div class="formulario">
          <div class="campo">
            <label for="solicitante">Solicitante</label>
            <div class="select-caja">
              <select id="solicitante" name="solicitante" required>
                <option value="">Selecciona un solicitante</option>
                <!-- Aquí van las opciones desde la base de datos -->
              </select>
            </div>
          </div>

          <div class="campos-fila">
            <div class="campo">
              <label for="fecha_prestamo">Fecha de préstamo</label>
              <input type="date" id="fecha_prestamo" name="fecha_prestamo" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="campo">
              <label for="fecha_devolucion">Fecha de devolución</label>
              <input type="date" id="fecha_devolucion" name="fecha_devolucion" required>
            </div>
          </div>

          <div class="resumen">
            <h2>Resumen</h2>
            <p id="resumen-texto">Selecciona un solicitante y un equipo para ver el resumen.</p>
          </div>
        </div>

        <!-- Columna derecha: lista de equipos -->
        <section class="lista">
          <label class="lista-titulo" for="buscar">Lista de equipos disponibles</label>
          <div class="busqueda">
            <input type="search" id="buscar" name="q" placeholder="Buscar por tipo o marca">
            <button type="button" class="btn">Buscar</button>
          </div>

          <table class="tabla tabla-equipos">
            <thead>
              <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Descripción/marca</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <!-- Aquí van las filas desde la base de datos. Ejemplo de una fila:
              <tr>
                <td>EQ-104</td>
                <td>Laptop</td>
                <td>Portátil 14 pulgadas, HP</td>
                <td><button type="button" class="btn btn-sm">Seleccionar</button></td>
              </tr>
              -->
            </tbody>
          </table>
        </section>

      </div>

      <div class="acciones-centro">
        <button type="submit" class="btn btn-ancho">Autorizar préstamo</button>
      </div>
    </form>
  </main>

</body>
</html>