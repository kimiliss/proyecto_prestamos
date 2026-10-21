<?php
/*
    Autor: Kimberly Garcia
    Fecha de creacion: 20/10/26
    Descripcion: Ventanas de mensaje reutilizables (confirmar y avisar), sin JavaScript.
                 Se usan con:  require "mensajes.php";
*/

/* ---------- 1) Ventana de confirmación ----------
   $titulo         → título de la ventana
   $mensaje        → texto de abajo
   $url_cancelar   → a dónde va el botón Cancelar (normalmente la misma página)
   $url_confirmar  → archivo PHP que recibe el POST cuando dicen "Sí"
   $campos         → datos que viaja el POST, ej. ["id" => 18]
   Ejemplo:
     ventana_confirmar("¿Eliminar equipo?", "Se eliminará EQ-104.", "equipos.php",
                       "eliminar_equipo.php", ["id" => 104]);
*/
function ventana_confirmar($titulo, $mensaje, $url_cancelar, $url_confirmar, $campos = [], $texto_confirmar = "Sí, eliminar") {
?>
  <div class="modal-fondo">
    <div class="modal modal-confirmar" role="alertdialog" aria-modal="true" aria-labelledby="modal-titulo">
      <h2 class="modal-titulo" id="modal-titulo"><?php echo htmlspecialchars($titulo); ?></h2>
      <p class="modal-texto"><?php echo htmlspecialchars($mensaje); ?></p>

      <form class="modal-acciones" action="<?php echo htmlspecialchars($url_confirmar); ?>" method="post">
        <?php foreach ($campos as $nombre => $valor): ?>
          <input type="hidden" name="<?php echo htmlspecialchars($nombre); ?>" value="<?php echo htmlspecialchars($valor); ?>">
        <?php endforeach; ?>
        <a class="btn-texto" href="<?php echo htmlspecialchars($url_cancelar); ?>">Cancelar</a>
        <button type="submit" class="btn"><?php echo htmlspecialchars($texto_confirmar); ?></button>
      </form>
    </div>
  </div>
<?php
}

/* ---------- 2) Ventana de aviso ----------
   $tipo        → "ok" (palomita verde) o "error" (tache rojo)
   $url_aceptar → a dónde va el botón Aceptar (normalmente la misma página, sin avisos)
   Ejemplo:
     ventana_aviso("ok", "Registro eliminado", "El equipo fue eliminado correctamente.", "equipos.php");
*/
function ventana_aviso($tipo, $titulo, $mensaje, $url_aceptar) {
  $tipo  = ($tipo == "error") ? "error" : "ok";
  $icono = ($tipo == "ok") ? "✓" : "✕";
?>
  <div class="modal-fondo">
    <div class="modal modal-aviso" role="alertdialog" aria-modal="true" aria-labelledby="modal-titulo">
      <h2 class="modal-titulo" id="modal-titulo"><?php echo htmlspecialchars($titulo); ?></h2>
      <p class="modal-texto"><?php echo htmlspecialchars($mensaje); ?></p>

      <div class="modal-acciones">
        <span class="modal-icono <?php echo $tipo; ?>" aria-hidden="true"><?php echo $icono; ?></span>
        <a class="btn" href="<?php echo htmlspecialchars($url_aceptar); ?>">Aceptar</a>
      </div>
    </div>
  </div>
<?php
}