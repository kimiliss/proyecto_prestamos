/*
    Autor: Kimberly Garcia
    Fecha de creacion: 20/10/26
    Descripcion: Ventanas de mensaje reutilizables (confirmar y avisar).
                 Se usan en cualquier página con <script src="mensajes.js" defer></script>
*/

/* ---------- Ayuda para crear elementos con texto seguro ---------- */
function crearElemento(etiqueta, clase, texto) {
  var e = document.createElement(etiqueta);
  if (clase) { e.className = clase; }
  if (texto) { e.textContent = texto; }
  return e;
}

function crearDialogo(clase) {
  var d = document.createElement("dialog");
  d.className = "modal " + clase;
  document.body.appendChild(d);
  return d;
}

/* ---------- 1) Ventana de confirmación ----------
   Regresa una promesa: true si le dan "Sí", false si cancelan.
   Ejemplo:
     mostrarConfirmacion({ titulo: "¿Eliminar equipo?", mensaje: "..." })
       .then(function (acepto) { if (acepto) { ... } });
*/
function mostrarConfirmacion(opciones) {
  return new Promise(function (resolver) {
    var d = crearDialogo("modal-confirmar");
    d.appendChild(crearElemento("h2", "modal-titulo", opciones.titulo || "¿Estás seguro?"));
    d.appendChild(crearElemento("p", "modal-texto", opciones.mensaje || ""));

    var acciones = crearElemento("div", "modal-acciones");
    var cancelar = crearElemento("button", "btn-texto", opciones.textoCancelar || "Cancelar");
    var aceptar  = crearElemento("button", "btn", opciones.textoConfirmar || "Sí, eliminar");
    cancelar.type = "button";
    aceptar.type  = "button";
    acciones.appendChild(cancelar);
    acciones.appendChild(aceptar);
    d.appendChild(acciones);

    function cerrar(respuesta) {
      d.close();
      d.remove();
      resolver(respuesta);
    }

    cancelar.addEventListener("click", function () { cerrar(false); });
    aceptar.addEventListener("click",  function () { cerrar(true); });
    // Con la tecla Esc cuenta como cancelar
    d.addEventListener("cancel", function (e) { e.preventDefault(); cerrar(false); });

    d.showModal();
    cancelar.focus();
  });
}

/* ---------- 2) Ventana de aviso ----------
   tipo: "ok" (palomita verde) o "error" (tache rojo)
   Regresa una promesa que se cumple cuando presionan "Aceptar".
   Ejemplo:
     mostrarAviso({ tipo: "ok", titulo: "Registro eliminado", mensaje: "..." });
*/
function mostrarAviso(opciones) {
  return new Promise(function (resolver) {
    var tipo = opciones.tipo === "error" ? "error" : "ok";
    var d = crearDialogo("modal-aviso");
    d.appendChild(crearElemento("h2", "modal-titulo", opciones.titulo || ""));
    d.appendChild(crearElemento("p", "modal-texto", opciones.mensaje || ""));

    var acciones = crearElemento("div", "modal-acciones");
    var icono = crearElemento("span", "modal-icono " + tipo, tipo === "ok" ? "✓" : "✕");
    icono.setAttribute("aria-hidden", "true");
    var aceptar = crearElemento("button", "btn", "Aceptar");
    aceptar.type = "button";
    acciones.appendChild(icono);
    acciones.appendChild(aceptar);
    d.appendChild(acciones);

    function cerrar() {
      d.close();
      d.remove();
      resolver();
    }

    aceptar.addEventListener("click", cerrar);
    d.addEventListener("cancel", function (e) { e.preventDefault(); cerrar(); });

    d.showModal();
    aceptar.focus();
  });
}

/* ---------- 3) Eliminar un registro (junta las dos de arriba) ----------
   Pregunta, manda el id al archivo PHP y muestra si se pudo o no.
   El PHP tiene que contestar en JSON:
     { "ok": true }
     { "ok": false, "mensaje": "Texto del problema" }
*/
async function eliminarRegistro(opciones) {
  var confirmado = await mostrarConfirmacion({
    titulo: opciones.titulo,
    mensaje: opciones.mensaje
  });
  if (!confirmado) { return; }

  try {
    var datos = new URLSearchParams();
    datos.append("id", opciones.id);

    var respuesta = await fetch(opciones.url, { method: "POST", body: datos });
    var resultado = await respuesta.json();

    if (resultado.ok) {
      await mostrarAviso({
        tipo: "ok",
        titulo: opciones.tituloOk || "Registro eliminado",
        mensaje: opciones.mensajeOk || "El registro fue eliminado correctamente."
      });
      window.location.reload();
    } else {
      await mostrarAviso({
        tipo: "error",
        titulo: "No se puede eliminar",
        mensaje: resultado.mensaje || "Ocurrió un problema, intenta de nuevo."
      });
    }
  } catch (error) {
    await mostrarAviso({
      tipo: "error",
      titulo: "No se puede eliminar",
      mensaje: "Ocurrió un problema, intenta de nuevo."
    });
  }
}

/* ---------- Cualquier enlace con data-eliminar dispara el proceso ----------
   <a href="#" data-eliminar
      data-url="eliminar_algo.php"
      data-id="5"
      data-titulo="¿Eliminar algo?"
      data-mensaje="Se eliminará esto. ¿Deseas continuar?"
      data-ok-titulo="Registro eliminado"          (opcional)
      data-ok-mensaje="Se eliminó correctamente."  (opcional)
   >Eliminar</a>
*/
document.addEventListener("click", function (e) {
  var enlace = e.target.closest("[data-eliminar]");
  if (!enlace) { return; }
  e.preventDefault();
  eliminarRegistro({
    url: enlace.dataset.url,
    id: enlace.dataset.id,
    titulo: enlace.dataset.titulo,
    mensaje: enlace.dataset.mensaje,
    tituloOk: enlace.dataset.okTitulo,
    mensajeOk: enlace.dataset.okMensaje
  });
});