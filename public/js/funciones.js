const HOST = location.protocol + "//" + location.host;
const modalFormulario = new bootstrap.Modal('#modalFormulario', {keyboard:false});
const modalConfirmacion = new bootstrap.Modal('#modalConfirmacion', {keyboard:false});
const toastNotificacion = document.getElementById("toastNotificacion");
const toast = new bootstrap.Toast(toastNotificacion, {delay: 3000});

const cargarLoading = () => {
    const contenido = document.querySelector("#contenido");
    contenido.innerHTML = `<div class="text-center p-5">
    <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
    </div>`;
}

const cargarProductos = async () => {
    try {        
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const page = urlParams.get('page') ? urlParams.get('page') : 1;
        const limit = urlParams.get('limit') ? urlParams.get('limit') : 10;
        const response = await fetch(HOST + "/api/productos/?page=" + page + "&limit=" + limit);
        const data = await response.json();
    
        return data;
    } catch (error) {
        mostrarMensaje("Error en la Conexión con la API!", "error");
        console.error(error);
    }
}

const renderTablaProductos = (productos) => {
    try {        
        if (!Array.isArray(productos.data)) {
            throw new Error("El parámetro debe ser un Array!");
        }

        const contenido = document.querySelector("#contenido");
        let contenidoHTML;

        if (productos.data.length == 0) {
            contenidoHTML = `<p class="text-center display-1"><i class="bi bi-recycle"></i></p>
            <h3 class="text-center fw-bold">No se encontraron Productos!</h3>
            <p class="text-center my-5"><button class="btn btn-light btn-sm" title="Agregar" data-bs-toggle="modal" data-bs-target="#modalFormulario">Agregar <i class="bi bi-plus-square"></i></button></p>`;
            contenido.innerHTML = contenidoHTML;

            return false;
        }

        contenidoHTML = `<table class="table">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Nombre</th>
            <th scope="col">Descripción</th>
            <th scope="col">Precio</th>
            <th scope="col">Precio USD</th>
            <th scope="col" class="text-end">
            <button class="btn btn-light btn-sm" title="Agregar" data-bs-toggle="modal" data-bs-target="#modalFormulario" onclick="abrirForm('add');">Agregar <i class="bi bi-plus-square"></i></button>
            </th>
            </tr>
        </thead>
        <tbody>`;

        for (const item of productos.data) {
            contenidoHTML += `<tr>`;
            contenidoHTML += `<td>${item.id}</td>`;
            contenidoHTML += `<td>${item.nombre}</td>`;
            contenidoHTML += `<td>${item.descripcion}</td>`;
            contenidoHTML += `<td>$ ${item.precio}</td>`;
            contenidoHTML += `<td>u$s ${item.precio_usd}</td>`;
            contenidoHTML += `<td class="text-end">
            <button class="btn btn-light btn-sm me-1" title="Editar" onclick="abrirForm('edit', ${item.id});">Editar <i class="bi bi-pencil-square"></i></button>
            <button class="btn btn-light btn-sm" title="Eliminar" onclick="confirmarEliminacionProducto(${item.id});">Eliminar <i class="bi bi-trash"></i></button>
            </td>`;
            contenidoHTML += `</tr>`;
        }

        contenidoHTML += `</tbody>
        </table>
        <div class="my-5 text-center">
            <div class="btn-group" role="group">
                <a href="/?page=1" class="btn btn-light"><i class="bi bi-chevron-double-left"></i></a>
                <a ${productos.pagination.prevPage ? `href="/?page=${productos.pagination.prevPage}"` : "disabled='disabled'"} class="btn btn-light"><i class="bi bi-chevron-left"></i></a>
                <button type="button" class="btn btn-light">${productos.pagination.currentPage}</button>
                <a ${productos.pagination.nextPage ? `href="/?page=${productos.pagination.nextPage}"` : "disabled='disabled'"} class="btn btn-light"><i class="bi bi-chevron-right"></i></a>
                <a href="/?page=${productos.pagination.totalPages}" class="btn btn-light"><i class="bi bi-chevron-double-right"></i></a>
            </div>
        </div>`;
        contenido.innerHTML = contenidoHTML;      
    } catch (error) {
        mostrarMensaje("Error en Renderizar la Tabla!", "error");
        console.error(error);
    }
}

const renderProductos = async () => {
    try {
        cargarLoading();
        const productos = await cargarProductos();        
        renderTablaProductos(productos);
    } catch (error) {
        mostrarMensaje("Error en el Renderizado de Productos!");
        console.error(error);
    }
}

const agregarProducto = async () => {    
    try {
        const nombre = document.querySelector("#nombre").value;
        const descripcion = document.querySelector("#descripcion").value;
        const precio = document.querySelector("#precio").value;
        const producto = {nombre, descripcion, precio};        
    
        const response = await fetch(HOST + "/api/productos", {
            method:"POST",
            headers:{'Content-type':'application/json; charset=UTF-8'},
            body:JSON.stringify(producto)
        });
        const resultado = await response.json();
        mostrarToast(resultado, "ok");
        document.querySelector("#btnFormularioCerrar").click();
        renderProductos();
    } catch (error) {
        mostrarToast("Error en la carga del Producto!", "error");
        console.error(error);   
    }
}

const buscarProducto = async (id) => {
    try {
        if (!Number.isInteger(id)) {
            throw new Error("El ID debe ser un valor Numérico!");
        }

        const response = await fetch(HOST + "/api/productos/" + id);
        const resultado = await response.json();
        
        return resultado;
    } catch (error) {
        mostrarToast("Error en la búsqueda del Producto!", "error");
        console.error(error);   
    }
}

const editarProducto = async (id) => {
    try {
        if (!Number.isInteger(id)) {
            throw new Error("El ID debe ser un valor Numérico!");
        }

        const nombre = document.querySelector("#nombre").value;
        const descripcion = document.querySelector("#descripcion").value;
        const precio = document.querySelector("#precio").value;
        const producto = {nombre, descripcion, precio};
    
        const response = await fetch(HOST + "/api/productos/" + id, {
            method:"PUT",
            headers:{'Content-type':'application/json; charset=UTF-8'},
            body:JSON.stringify(producto)
        });
        const resultado = await response.json();
        mostrarToast(resultado, "ok");
        renderProductos();
    } catch (error) {
        mostrarToast("Error en la edición del Producto!", "error");
        console.error(error);   
    }

    limpiarForm();
    modalFormulario.hide();
}

const eliminarProducto = async (id) => {
    try {        
        if (!Number.isInteger(id)) {
            throw new Error("El ID debe ser un valor Numérico!");
        }
    
        const response = await fetch(HOST + "/api/productos/" + id, {
            method:"DELETE"
        })
        const resultado = await response.json();
        mostrarToast(resultado, "ok");
        renderProductos();
    } catch (error) {
        mostrarToast("Error en la eliminación del Producto!", "error");
        console.error(error);   
    }

    modalConfirmacion.hide();
}

const abrirForm = async (action, id=0) => {
    const btnFormEnviar = document.querySelector("#btnFormEnviar");
    const modalLabel = document.querySelector("#modalLabel");
    modalLabel.innerHTML = action == "add" ? "Agregar Producto" : "Editar Producto";

    if (action == "edit") {
        const producto = await buscarProducto(id);
        document.querySelector("#nombre").value = producto.nombre;
        document.querySelector("#descripcion").value = producto.descripcion;
        document.querySelector("#precio").value = producto.precio;
    }

    btnFormEnviar.onclick = () => {
        action == "add" ? agregarProducto() : editarProducto(id);
    }

    modalFormulario.show();
}

const limpiarForm = () => {
    document.querySelector("#nombre").value = "";
    document.querySelector("#descripcion").value = "";
    document.querySelector("#precio").value = "";
}

const confirmarEliminacionProducto = (id) => {
    const mensajeConfirmacion = document.querySelector("#mensajeConfirmacion");
    const btnConfirmacionAceptar = document.querySelector("#btnConfirmacionAceptar");
    mensajeConfirmacion.innerHTML = "Está seguro que desea eliminar el Producto #" + id + "?";
    btnConfirmacionAceptar.onclick = () => {
        eliminarProducto(id);
    }

    modalConfirmacion.show();
}

const mostrarMensaje = (mensaje, tipo) => {
    const contenido = document.querySelector("#contenido");
    contenido.innerHTML = `<div class="alert alert-${tipo == "ok" ? "success" : "danger"} text-center" role="alert">${mensaje}</div>`;
}

const mostrarToast = (mensaje, tipo) => {   
    if (tipo == "ok") {
        toastNotificacion.classList.remove("bg-danger");
        toastNotificacion.classList.add("bg-success");
    } else {
        toastNotificacion.classList.remove("bg-success");
        toastNotificacion.classList.add("bg-danger");
    }

    const mensajeToast = document.querySelector("#mensajeToast");    
    mensajeToast.innerHTML = mensaje;
    toast.show();
}