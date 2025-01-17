<div class="contenedor olvide">
    <?php include_once __DIR__ . '/../templates/icono-modo.php'; ?>
    <?php include_once __DIR__ .'/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Recupera tu acceso a UpTask</p>

        <?php include_once __DIR__ .'/../templates/alertas.php'; ?>

        <form class="formulario" method="POST" action="/olvide" novalidate>
            <div class="campo">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    placeholder="Introduce tu email"
                    name="email"
                />
            </div>
            <input type="submit" class="boton" value="Enviar Instrucciones">
        </form>

        <div class="acciones">
            <a href="/">¿Ya tienes cuenta? Iniciar Sesión</a>
            <a href="/crear">¿Aún no tienes cuenta? Crear cuenta</a>
        </div>
    </div> <!--Fin .contenedor-sm-->
</div>

<?php
    $script = '<script src="/../build/js/app.js"></script>';
?>