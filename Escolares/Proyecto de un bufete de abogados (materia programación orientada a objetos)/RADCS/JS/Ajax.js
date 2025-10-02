function enviar_formulario_ajax(e) {
    e.preventDefault();

    let enviar = confirm("Quieres enviar el formulario");
    if (enviar == true) {
        let data = new FormData(this);
        let method = this.getAttribute("method");
        let action = this.getAttribute("action");

        console.log("Datos enviados:", [...data.entries()]);
        console.log("Método:", method);
        console.log("Acción:", action);

        let encabezados = new Headers();
        let config = {
            method: method,
            headers: encabezados,
            mode: 'cors',
            cache: 'no-cache',
            body: data
        };

        fetch(action, config)
            .then(respuesta => respuesta.text())
            .then(respuesta => {
                console.log("Respuesta del servidor:", respuesta);
                let contenedor = document.querySelector(".form-rest");
                if (contenedor) {
                    contenedor.innerHTML = respuesta;
                } else {
                    console.error("No se encontró el contenedor .form-rest");
                }
            })
            .catch(error => {
                console.error("Error en la solicitud:", error);
            });
    }
}
document.querySelectorAll(".FormularioAjax").forEach(form => {
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        console.log("Formulario enviado:", this);
        enviar_formulario_ajax.call(this, e);
    });
});