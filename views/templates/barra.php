<div class="barra-mobile">
    <h1>UpTask</h1>
    
    <div class="menu">
        <img id="mobile-menu" src="build/img/menu.svg" alt="imagen menu">
    </div>
</div>
<?php include_once __DIR__ . '/icono-modo.php'; ?>


<div class="barra">
    <p>Hola <span><?php echo $_SESSION['nombre']; ?></span></p>

    <a href="/logout" class="cerrar-sesion">Cerrar Sesión</a>
</div>