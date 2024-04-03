//Agrego función IIFI para proteger el archivo y que solo se pueda usar su funcionalidad en el 
(function() {

    obtenerTareas();
    let tareas = [];
    let filtradas = [];

    //Botón para mostrar el modal de agregar tarea:
    const nuevaTareaBtn = document.querySelector('#agregar-tarea');
    nuevaTareaBtn.addEventListener('click', function() {
        mostrarFormulario();
    });

    //Filtros de búsqueda:
    const filtros = document.querySelectorAll('#filtros input[type="radio"');
    filtros.forEach( radio => {
        radio.addEventListener('input', filtrarTareas);
    });

    function filtrarTareas(e) {
        const filtro = e.target.value;

        if( filtro !== '' ) {
           filtradas = tareas.filter(tarea => tarea.estado === filtro);
        } else {
            filtradas = [];
        }

        mostrarTareas();
    }

    async function obtenerTareas() {
        try {
            const id = obtenerProyecto();
            const url = `api/tareas?id=${id}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            
            tareas = resultado.tareas;
            mostrarTareas();

        } catch (error) {
            console.log(error);
        }
    }

    function mostrarTareas() {
        limpiarTareas();
        totalPendientes();
        totalCompletas();

        const arrayTareas = filtradas.length ? filtradas : tareas;

        if( arrayTareas.length === 0 ) {
            const contenedorTareas = document.querySelector('#listado-tareas');
            const textoNoTareas = document.createElement('LI');
            textoNoTareas.textContent = 'No existen tareas';
            textoNoTareas.classList.add('no-tareas');

            contenedorTareas.appendChild(textoNoTareas);
            return;
        }

        const estados = {
            0: 'Pendiente',
            1: 'Completa'
        };

        arrayTareas.forEach(tarea => {
            const contenedorTarea = document.createElement('LI');
            contenedorTarea.dataset.tareaId = tarea.id;
            contenedorTarea.classList.add('tarea');

            const nombreTarea = document.createElement('P');
            nombreTarea.textContent = tarea.nombre;
            nombreTarea.ondblclick = function() {
                mostrarFormulario(true, {...tarea});
            };

            const opcionesDiv = document.createElement('DIV');
            opcionesDiv.classList.add('opciones');

            //Botones:
            const btnEstadoTarea = document.createElement('BUTTON');
            btnEstadoTarea.classList.add('estado-tarea');
            btnEstadoTarea.classList.add(`${estados[tarea.estado].toLowerCase()}`);
            btnEstadoTarea.textContent = estados[tarea.estado];
            btnEstadoTarea.dataset.estadoTarea = tarea.estado;
            btnEstadoTarea.ondblclick = function() {
                cambiarEstadoTarea({...tarea});
            }

            const btnEliminarTarea = document.createElement('BUTTON');
            btnEliminarTarea.classList.add('eliminar-tarea');
            btnEliminarTarea.dataset.idTarea = tarea.id;
            btnEliminarTarea.textContent = 'Eliminar';
            btnEliminarTarea.ondblclick = function() {
                confirmarEliminarTarea({...tarea});
            }

            opcionesDiv.appendChild(btnEstadoTarea);
            opcionesDiv.appendChild(btnEliminarTarea);

            contenedorTarea.appendChild(nombreTarea);
            contenedorTarea.appendChild(opcionesDiv);

            const listadoTareas = document.querySelector('#listado-tareas');
            listadoTareas.appendChild(contenedorTarea);
        });
    }

    function totalPendientes() {
        const totalPendientes = tareas.filter(tarea => tarea.estado === "0");
        const pendientesRadio = document.querySelector('#pendientes');

        if( totalPendientes.length === 0 ) {
            pendientesRadio.disabled = true;

        } else {
            pendientesRadio.disabled = false;
        }
    }

    function totalCompletas() {
        const totalCompletas = tareas.filter(tarea => tarea.estado === "1");
        const completasRadio = document.querySelector('#completadas');

        if( totalCompletas.length === 0 ) {
            completasRadio.disabled = true;

        } else {
            completasRadio.disabled = false;
        }
    }

    function mostrarFormulario(editar=false, tarea={}) {
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML = `
            <form class="formulario nueva-tarea">
                <legend>${editar ? 'Editar Tarea' :' Añade una nueva tarea'}</legend>
                <div class="campo">
                    <label>Tarea</label>
                    <input
                        type="text"
                        name="tarea"
                        placeholder="${tarea.nombre ? 'Añade el nuevo nombre de la tarea' : 'Añadir nueva tarea'}"
                        id="tarea"
                        value="${tarea.nombre ? tarea.nombre : ''}"
                    />
                </div>
                <div class="opciones">
                    <input 
                        type="submit" 
                        class="submit-nueva-tarea" 
                        value="${tarea.nombre ? 'Guardar cambios' : 'Añadir'}"
                    />
                    <button 
                        type="button" class="cerrar-modal">Cancelar</button>
                </div>
            </form>
        
        `;

        setTimeout(() => {
            const formulario = document.querySelector('.formulario');
            formulario.classList.add('animar');
        }, 0);

        //Identificar si el elemento que se pulsa en el modal es el botón cancelar, que tiene la clase cerrar-modal (delegation)
        modal.addEventListener('click', function(e) {
            e.preventDefault(); //Previene por defecto el comportamiento de enviar del botón submit del modal

            if( e.target.classList.contains('cerrar-modal') ) {
                const formulario = document.querySelector('.formulario');
                formulario.classList.add('cerrar');
                setTimeout(() => {
                    modal.remove(); //Elimina el modal y así se cierra. Dentro de un time out para que se cierre con animación
                }, 600);
            }

            if( e.target.classList.contains('submit-nueva-tarea') ) {
                const nombreTarea = document.querySelector('#tarea').value.trim();

                if (nombreTarea === '') {
                    //Como el input no está relleno, mostrar una alerta de error
                    mostrarAlerta('Debes dar un nombre a tu tarea', 'error', document.querySelector('.formulario legend'));

                    return; //Return para que no siga ejecutando
                }

                if(editar) {
                    tarea.nombre = nombreTarea;
                    actualizarTarea(tarea);
                } else {
                    agregarTarea(nombreTarea);
                }
            }
        });

        document.querySelector('.dashboard').appendChild(modal);
    }

    //Muestra un mensaje en la interfaz:
    function mostrarAlerta(mensaje, tipo, referencia) {
        //Prevenir la creación de múltiples alertas:
        const alertaPrevia = document.querySelector('.alerta');
        if(alertaPrevia) {
            alertaPrevia.remove();
        }

        const alerta = document.createElement('DIV');
        alerta.classList.add('alerta', tipo);
        alerta.textContent = mensaje;
        
        //Como referencia hace referencia al legend, insertará la alerta entre el legend y su padre, que es el formulario, es decir, justo antes del legend
        //referencia.parentElement.insertBefore(alerta, referencia);

        //Para hacer lo mismo, pero que lo imprima justo después del legend, hay que hacerlo así:
        referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling);

        //Eliminar la alerta después de 5 segundos:
        setTimeout(() => {
            alerta.remove();
        }, 3000);
    }

    //Consultar el servidor para añadir una nueva tarea al proyecto actual
    async function agregarTarea(tarea) {
        //Construir la petición:
        const datos = new FormData();
        datos.append('nombre', tarea);
        datos.append('proyectoId', obtenerProyecto());

        try {
            const url = `${location.origin}/api/tareas`;
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();
            console.log(resultado);

            mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.formulario legend'));

            //Si la alerta es correcta, porque se ha introducido la tarea, quitar el modal
            if (resultado.tipo === 'exito') {
                const modal = document.querySelector('.modal');

                setTimeout(() => {
                    modal.remove();
                }, 2000);

                //Agregar el objeto de tareas al global de tareas:
                const tareaObj = {
                    id: String(resultado.id),
                    nombre: tarea,
                    estado: "0",
                    proyectoId: resultado.proyectoId
                }
                tareas = [...tareas, tareaObj];
                mostrarTareas();
            }

        } catch (error) {
            console.log(error);
        }
    }

    function cambiarEstadoTarea(tarea) {
        const nuevoEstado = tarea.estado === "1" ? "0" : "1";
        tarea.estado = nuevoEstado;
        actualizarTarea(tarea);
    }

    async function actualizarTarea(tarea) {
        const { estado, id, nombre, proyectoId } = tarea;
        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('proyectoId', obtenerProyecto());

        //Iterar en un objeto formData, la única forma que existe:
        // for(let valor of datos.values()) {
        //     console.log(valor);
        // }

        try {
            const url = `${location.origin}/api/tareas/actualizar`;
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();
            
            if( resultado.respuesta.tipo === 'exito' ) {
                // mostrarAlerta(
                //     resultado.respuesta.mensaje, 
                //     resultado.respuesta.tipo, 
                //     document.querySelector('.contenedor-nueva-tarea')
                // );

                Swal.fire(
                    '¡Actualizado!',
                    resultado.respuesta.mensaje,
                    'success'
                );

                const modal = document.querySelector('.modal');
                if(modal) {
                    modal.remove();
                }
                

                tareas = tareas.map(tareaMemoria => {
                    if( tareaMemoria.id === id ) {
                        tareaMemoria.estado = estado;
                        tareaMemoria.nombre = nombre;
                    } 
                    return tareaMemoria;
                });
                
                mostrarTareas();
            }

        } catch (error) {
            console.log(error);
        }
    }

    function confirmarEliminarTarea(tarea) {
        Swal.fire({
            title: `¿Quieres eliminar la tarea:\n"${tarea.nombre}"?`,
            showCancelButton: true,
            confirmButtonText: "Si",
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
              eliminarTarea(tarea);
            }
        });
    }

    async function eliminarTarea(tarea) {
        const { estado, id, nombre } = tarea;
        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('proyectoId', obtenerProyecto());

        try {
            const url = `${location.origin}/api/tareas/eliminar`;
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();
            if( resultado.resultado ) {
                // mostrarAlerta(
                //     resultado.mensaje, 
                //     resultado.tipo, 
                //     document.querySelector('.contenedor-nueva-tarea')
                // );

                Swal.fire('¡Eliminada!', resultado.mensaje, 'success');

                tareas = tareas.filter( tareaMemoria => tareaMemoria.id !== tarea.id );
                mostrarTareas();
            }

        } catch (error) {
            console.log(error);
        }
    }

    //Para traer los datos de la url del proyecto, que la tenemos en la url de la página:
    function obtenerProyecto() {
        const proyectoParams = new URLSearchParams(window.location.search);
        const proyecto = Object.fromEntries(proyectoParams.entries());
        return proyecto.id;
    }

    function limpiarTareas() {
        const listadoTareas = document.querySelector('#listado-tareas');
        
        while(listadoTareas.firstChild) {
            listadoTareas.removeChild(listadoTareas.firstChild);
        }
    }

})();