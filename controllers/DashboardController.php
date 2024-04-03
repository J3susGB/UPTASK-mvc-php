<?php

namespace Controllers;

use Model\Proyecto;
use Model\Usuario;
use MVC\Router;

class DashboardController {
    public static function index(Router $router) {
        session_start();
        isAuth(); //Para comprobar si el usuario está autenticado

        $id = $_SESSION['id'];

        $proyectos = Proyecto::belongsTo('propietarioId', $id);

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto(Router $router) {
        session_start();
        isAuth(); //Para comprobar si el usuario está autenticado
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = new Proyecto($_POST);
            
            //Validación
            $alertas = $proyecto->validarProyecto();

            if (empty($alertas)) {
                //Generar url única:
                $hash = md5(uniqid());
                $proyecto->url = $hash;

                //Almacenar el creador del proyecto:
                $proyecto->propietarioId = $_SESSION['id'];

                //Guardar el proyecto:
                $proyecto->guardar();

                // Redireccionar:
                header('Location: /proyecto?id=' . $proyecto->url);
            }
        }

        $router->render('dashboard/crear-proyecto', [
            'alertas' => $alertas,
            'titulo' => 'Crear Proyecto'
        ]);

    }

    public static function proyecto(Router $router) {
        session_start();
        isAuth(); //Para comprobar si el usuario está autenticado

        //Revisar que la persona que visita el proyecto es quien lo creó:
        $token = $_GET['id'];
        if ( !$token ) header('Location: /dashboard');

        $proyecto = Proyecto::where('url', $token);
        if ( $proyecto->propietarioId !== $_SESSION['id'] ) {
            header('Location: /dashboard');
        }

        $router->render('dashboard/proyecto', [
        
            'titulo' => $proyecto->proyecto
        ]);
    }

    public static function perfil(Router $router) {
        session_start();
        isAuth();
        $alertas = [];

        $usuario = Usuario::find($_SESSION['id']);

        if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validar_perfil();

            if( empty($alertas) ) {
                //Comprobar que el usuario no intenta poner un correo ya existente en la base de datos:
                $existeUsuario = Usuario::where('email', $usuario->email);

                if( $existeUsuario && $existeUsuario->id !== $usuario->id ) {
                    //Mostrar mensaje de error:
                    Usuario::setAlerta('error', 'El email pertenece a una cuenta existente');

                } else {
                    //Guardar el registro:
                    $usuario->guardar();
                    Usuario::setAlerta('exito', 'Cambios guardados correctamente');
                    $_SESSION['nombre'] = $usuario->nombre; //Asignar el nombre nuevo a la barra
                }         
            }
        }

        $alertas = $usuario->getAlertas();

        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' => $alertas  
        ]);
    }

    public static function cambiar_password(Router $router) {
        session_start();
        isAuth();
        $alertas = [];

        if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $usuario = Usuario::find($_SESSION['id']);
            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password();

            if( empty($alertas) ) {
                $resultado = $usuario->comprobar_password();

                if($resultado) {
                    //Asignar el nuevo password a la variable de la columna de password:
                    $usuario->password = $usuario->password_nuevo;

                    //Eliminar propiedades no necesarias:
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);

                    //Hashear el nuevo password:
                    $usuario-> hashPassword();
                    
                    //Asignar el nuevo password:
                    $resultado = $usuario-> guardar();

                    if( $resultado ) {
                        Usuario::setAlerta('exito', 'Contraseña actualizada correctamente. Por favor, vuelve a iniciar sesión');
                        $alertas = $usuario->getAlertas();
                    }

                } else {
                    Usuario::setAlerta('error', 'La contraseña introducida no es correcta');
                    $alertas = $usuario->getAlertas();
                }
            }
        }
        
        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Contraseña',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }
}