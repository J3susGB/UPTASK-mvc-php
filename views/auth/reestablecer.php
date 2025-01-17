<div class="contenedor reestablecer">
    <?php include_once __DIR__ . '/../templates/icono-modo.php'; ?>
    <?php include_once __DIR__ .'/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Introduce tu nueva contraseña</p>

        <?php include_once __DIR__ .'/../templates/alertas.php'; ?>

        <?php if($mostrar) { ?>

        <form class="formulario" method="POST">
            <div class="campo">
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    placeholder="Introduce tu contraseña"
                    name="password"
                />
            </div>
            <input type="submit" class="boton" value="Guardar constraseña">
        </form>

        <?php } ?>

        <div class="acciones">
            <a href="/crear">¿Aún no tienes cuenta? Crear cuenta</a>
            <a href="/olvide">Olvidé mi contraseña</a>
        </div>
    </div> <!--Fin .contenedor-sm-->
</div>

<?php
    $script = '<script src="/../build/js/app.js"></script>';
?>