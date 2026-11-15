<?php

namespace App\Controllers;

use App\Models\Usuario;
use App\Core\Helper;

class AutenticacionControlador
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new Usuario();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $email = Helper::limpiar($_POST['email']);
            $password = $_POST['password'];

            $user = $this->userModel->login($email, $password);

            if ($user) {

                // Regenera el ID de sesión por seguridad
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id_usuario'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_role'] = $user['nombre_rol'];
                $_SESSION['user_tienda_id'] = $user['id_tienda'] ?? null;
                $_SESSION['user_tienda_nombre'] = $user['nombre_tienda'] ?? null;
                $_SESSION['user_distrito_nombre'] = $user['nombre_distrito'] ?? null;

                Helper::redireccionar('dashboard');

            } else {

                $error = "Credenciales incorrectas o usuario inactivo.";
                require_once '../app/Views/auth/login.php';

            }

        } else {

            require_once '../app/Views/auth/login.php';

        }
    }

    public function logout()
    {
        // Vaciar todas las variables de sesión
        $_SESSION = [];

        // Eliminar la cookie de sesión si existe
        if (ini_get('session.use_cookies')) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destruir la sesión
        session_destroy();

        // Redirigir al login
        Helper::redireccionar('autenticacion/login');
    }
}