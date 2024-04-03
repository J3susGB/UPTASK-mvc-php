<?php 

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            //Si pasa validación y no hay alertas de error
            if(empty($alertas)) { 
                //Verificar que el usuario exista en la base de datos:
                $usuario = Usuario::where('email', $auth->email);

                if ( !$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta('error', 'El usuario no existe o su cuenta no está confirmada');
                } else {
                    //El Usuario existe:
                    if ( password_verify($_POST['password'], $usuario->password) ) {
                        //Iniciar sesión:
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Redireccionar:
                        header('Location: /dashboard');
                    } else {
                        Usuario::setAlerta('error', 'La contraseña no es correcta');
                    }

                    
                }
            }
        }

        $alertas = Usuario::getAlertas();

        //Render a la vista:
        $router->render('auth/login', [
            'titulo'=> 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout() {
        session_start(); //Inicia sesión para traer a esta vista toda la información del usuario
        $_SESSION = []; //Se reinician los valores
        header('Location: /'); //Al cerrar sesión, redireccionar a página inicial


    }

    public static function crear(Router $router) {
        $alertas = [];
        $usuario = new Usuario;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if(empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario) {
                    Usuario::setAlerta('error', 'El Usuario ya está registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear el password
                    $usuario->hashPassword();

                    // Eliminar password2
                    unset($usuario->password2);

                    // Generar el Token
                    $usuario->crearToken();

                    // Crear un nuevo usuario
                    $resultado =  $usuario->guardar();

                    // Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();
                    

                    if($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }
        }

        // Render a la vista
        $router->render('auth/crear', [
            'titulo' => 'Crea tu cuenta en UpTask', 
            'usuario' => $usuario, 
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router) {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();
            
            //Si alertas está vacío (pasa todas las validaciones del formulario)
            if(empty($alertas)) {
                //Buscar el usuario en la Base de datos
                $usuario = Usuario::where('email', $usuario->email); //Consulta que trae los datos de la persona a la que pertenece el email introducido, si existe en la BD
                
                if ( $usuario && $usuario->confirmado ) { //Sí encuentra al usuario y está confirmado
                    //Generar un nuevo token
                    $usuario->crearToken();

                    //Eliminar variable password2(no existe en la base de datos)
                    unset($usuario->password2);

                    //Actualizar el usuario
                    $usuario->guardar();

                    //Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    //Imprimir la alerta de éxito
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');

                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no ha confirmado su cuenta');
                }
            }
            
        }

        $alertas = Usuario::getAlertas();

        //Render a la vista:
        $router->render('auth/olvide', [
            'titulo'=> 'Olvidé mi contraseña',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router) {
        $token = s($_GET['token']);
        $mostrar = true;
       
        //Si no hay un token, redireccionamos a página principal
        if ( !$token ) header('Location: /');

        //Identificar al usuario con este token
        $usuario = Usuario::where('token', $token); //Almacena los datos del usuario en un un objeto de la Clase usuario

        if (empty($usuario)) { //Si el objeto que se trae está vacío o null, es que no lo encuentra
            Usuario::setAlerta('error', 'Token no válido');
            $mostrar = false;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Añadir el nuevo password
            $usuario->sincronizar($_POST);

            //Validar el password
            $alertas = $usuario->validarPassword();

            if (empty($alertas)) { //Verifica que no hay alertas de error (pasa validación)
                //Hashear el nuevo password:
                $usuario->hashPassword();

                //Eliminar del objeto password2:
                unset($usuario->password2);

                //Eliminar el token:
                $usuario->token = null;

                //Guardar cambios en DB:
                $resultado = $usuario-> guardar();
                
                //Redireccionar:
                if ($resultado) {
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        //Render a la vista:
        $router->render('auth/reestablecer', [
            'titulo'=> 'Reestablecer contraseña',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router) {
       
        //Render a la vista:
        $router->render('auth/mensaje', [
            'titulo'=> 'Cuenta creada con éxito'
        ]);
    }

    public static function confirmar(Router $router) {

        $token = s($_GET['token']);
        
        if (!$token) header('Location: /');

        //Encontrar al usuario con este token:
        $usuario = Usuario::where('token', $token);
        
        if(empty($usuario)) { //Comprueba si el objeto usuario está vacío
            //No se encontró un usuario con ese token
            Usuario::setAlerta('error', 'Token no válido');
        } else {
            //Confirmar la cuenta:
            $usuario->confirmado = 1; //Cambia a 1 el valor de la columna confirmado en DB
            $usuario->token = null; //Cambia el valor de la columna token a nulo, para eliminar el token
            unset($usuario->password2); //Elimina el atributo password2, ya que en la DB no existe
            
            //Guardar en la Base de datos
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta creada y comprobada correctamente');
        }

        $alertas = Usuario::getAlertas();

        //Render a la vista:
        $router->render('auth/confirmar', [
            'titulo'=> 'Confirma tu cuenta',
            'alertas' => $alertas
        ]);
    }
}