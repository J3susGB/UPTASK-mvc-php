<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <a href="/perfil" class="enlace">Volver a editar perfil</a>

    <form class="formulario" method="POST" action="/cambiar-password">
        <div class="campo">
            <label for="password_actual">Contraseña actual</label>
            <input 
                type="password"
                name="password_actual"
                placeholder="Introduce tu contraseña actual"
            />
        </div>
        <div class="campo">
            <label for="password_nuevo">Nueva contraseña</label>
            <input 
                type="password"
                name="password_nuevo"
                placeholder="Introduce tu nueva contraseña"
            />
        </div>
        <input type="submit" value="Guardar cambios">
    </form>
</div>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>