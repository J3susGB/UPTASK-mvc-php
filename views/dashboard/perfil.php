<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <a href="/cambiar-password" class="enlace">Cambiar contraseña</a>

    <form class="formulario" method="POST" action="/perfil" novalidate>
        <div class="campo">
            <label for="nombre">Nombre</label>
            <input 
                type="text"
                name="nombre"
                placeholder="Introduce tu nombre"
                value="<?php echo $usuario->nombre; ?>"
            />
        </div>
        <div class="campo">
            <label for="email">Email</label>
            <input 
                type="email"
                name="email"
                placeholder="Introduce tu email"
                value="<?php echo $usuario->email; ?>"
            />
        </div>
        <input type="submit" value="Guardar cambios">
    </form>
</div>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>