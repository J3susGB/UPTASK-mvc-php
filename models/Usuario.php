<?php

namespace Model;

use Model\ActiveRecord;

class Usuario extends ActiveRecord {
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    //Validar el login de usuario
    public function validarLogin() : array {
        if(!$this->email) {
            self::$alertas['error'][] = 'Debes introducir un email';
        }

        if ( !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El email introducido no es válido';
        }

        if(!$this->password) {
            self::$alertas['error'][] = 'Debes introducir una contraseña';
        }

        return self::$alertas;
    }

    // Validación para cuentas nuevas
    public function validarNuevaCuenta() : array {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'Debes introducir un nombre de usuario';
        }
        if(!$this->email) {
            self::$alertas['error'][] = 'Debes introducir un email';
        }
        if(!$this->password) {
            self::$alertas['error'][] = 'Debes introducir una contraseña';
        }
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'La contraseña debe contener al menos 6 caracteres';
        }
        if($this->password !== $this->password2) {
            self::$alertas['error'][] = 'La contraseña no coincide';
        }
        return self::$alertas;
    }

    //Validar un email:
    public function validarEmail() : array {
        //Si no rellena el campo email, muestra alerta de error
        if ( !$this->email) {
            self::$alertas['error'][] = 'Debes introducir un email';
        }

        //Si el texto que introduce no es un email, muestra alerta de error
        if ( !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El email introducido no es válido';
        }

        return self::$alertas;
    }

    //Validar el password
    public function validarPassword() : array {
        if(!$this->password) {
            self::$alertas['error'][] = 'Debes introducir una contraseña';
        }

        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'La contraseña debe contener al menos 6 caracteres';
        }
        
        return self::$alertas;
    }

    public function validar_perfil() : array {
        if( !$this->nombre ) {
            self::$alertas['error'][] = 'Debes introducir un nombre';
        }

        if( !$this->email ) {
            self::$alertas['error'][] = 'Debes introducir un email';
        }

        //Si el texto que introduce no es un email, muestra alerta de error
        if ( !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El email introducido no es válido';
        }

        return self::$alertas;
    }

    public function nuevo_password() : array {
        if( !$this->password_actual ) {
            self::$alertas['error'][] = 'Por favor, introduce la contraseña actual';
        }

        if( !$this->password_nuevo ) {
            self::$alertas['error'][] = 'Por favor, introduce la nueva contraseña';
        } else {
            if( strlen($this->password_nuevo) < 6 ) {
                self::$alertas['error'][] = 'La nueva contraseña tiene que tener al menos 6 caracteres';
            }

            if( $this->password_actual === $this->password_nuevo ) {
                self::$alertas['error'][] = 'Las contraseñas son iguales. Por favor, introduce una contraseña nueva';
            }
        }

        return self::$alertas;
    }

    public function volverInicio() {
        header('Location: /');
        $_SESSION = [];
    }

    //Verifica si el password introducido es igual al password que había registrado (password registrado = password actual)
    public function comprobar_password() : bool {
        return password_verify($this->password_actual, $this->password);
    }
    
    //Hashea el password:
    public function hashPassword() : void {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    //Generar un token:
    public function crearToken() : void {
        $this->token = uniqid();
    }
}