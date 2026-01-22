const HOST = location.href;
const miModal = new bootstrap.Modal('#miModal', {keyboard:false});
const miToast = document.getElementById("miToast");
const toast = new bootstrap.Toast(miToast, {
    delay: 3000 
});

const cargarLoading = () => {
    const contenido = document.querySelector("#contenido");
    contenido.innerHTML = `<div class="text-center p-5">
    <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
    </div>`;
}

const cargarProductos = async () => {
    try {        
        const response = await fetch(HOST + "/api/productos/");
        const data = await response.json();
    
        return data;
    } catch (error) {
        mostrarMensaje("Error en la Conexión con la API!", "error");
        console.error(error);
    }
}

const renderTablaProductos = (productos) => {
    try {
        if (!Array.isArray(productos)) {
            throw new Error("El parámetro debe ser un Array!");
        }

        const contenido = document.querySelector("#contenido");
        let contenidoHTML;

        if (productos.length == 0) {
            contenidoHTML = `<p class="text-center display-1"><i class="bi bi-recycle"></i></p>
            <h3 class="text-center fw-bold">No se encontraron Productos!</h3>
            <p class="text-center my-5"><button class="btn btn-light btn-sm" title="Agregar" data-bs-toggle="modal" data-bs-target="#miModal">Agregar <i class="bi bi-plus-square"></i></button></p>`;
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
            <th scope="col" class="text-end">
            <button class="btn btn-light btn-sm" title="Agregar" data-bs-toggle="modal" data-bs-target="#miModal" onclick="abrirForm('add');">Agregar <i class="bi bi-plus-square"></i></button>
            </th>
            </tr>
        </thead>
        <tbody>`;

        for (const item of productos) {
            contenidoHTML += `<tr>`;
            contenidoHTML += `<td>${item.id}</td>`;
            contenidoHTML += `<td>${item.nombre}</td>`;
            contenidoHTML += `<td>${item.descripcion}</td>`;
            contenidoHTML += `<td>u$s ${item.precio}</td>`;
            contenidoHTML += `<td class="text-end">
            <button class="btn btn-light btn-sm me-1" title="Editar" onclick="abrirForm('edit', ${item.id});">Editar <i class="bi bi-pencil-square"></i></button>
            <button class="btn btn-light btn-sm" title="Eliminar" onclick="eliminarProducto(${item.id});">Eliminar <i class="bi bi-trash"></i></button>
            </td>`;
            contenidoHTML += `</tr>`;
        }

        contenidoHTML += `</tbody>
        </table>`;
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
        document.querySelector("#btnFormCerrar").click();
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
    miModal.hide();
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

    miModal.show();
}

const limpiarForm = () => {
    document.querySelector("#nombre").value = "";
    document.querySelector("#descripcion").value = "";
    document.querySelector("#precio").value = "";
}

const mostrarMensaje = (mensaje, tipo) => {
    const contenido = document.querySelector("#contenido");
    contenido.innerHTML = `<div class="alert alert-${tipo == "ok" ? "success" : "danger"} text-center" role="alert">${mensaje}</div>`;
}

const mostrarToast = (mensaje, tipo) => {   
    if (tipo == "ok") {
        miToast.classList.remove("bg-danger");
        miToast.classList.add("bg-success");
    } else {
        miToast.classList.remove("bg-success");
        miToast.classList.add("bg-danger");
    }

    const mensajeToast = document.querySelector("#mensajeToast");    
    mensajeToast.innerHTML = mensaje;
    toast.show();
}