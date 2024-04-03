<div class="contenedor login" id="login">
    <?php include_once __DIR__ . '/../templates/icono-modo.php'; ?>
    <?php include_once __DIR__ .'/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Iniciar Sesión</p>

        <?php include_once __DIR__ .'/../templates/alertas.php'; ?>
        
        <form class="formulario" method="POST" action="/" novalidate>
            <div class="campo">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    placeholder="Introduce tu email"
                    name="email"
                />
            </div>
            <div class="campo">
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    placeholder="Introduce tu contraseña"
                    name="password"
                />
            </div>
            <input type="submit" class="boton" value="Iniciar Sesión">
        </form>

        <div class="acciones">
            <a href="/crear">¿Aún no tienes cuenta? Crear cuenta</a>
            <a href="/olvide">Olvidé mi contraseña</a>
        </div>
    </div> <!--Fin .contenedor-sm-->
</div>

<?php
    $script = '<script src="/../build/js/app.js"></script>';
?>