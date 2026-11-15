<?php
namespace App\Controllers;

use App\Models\Usuario;
use App\Models\Role;
use App\Models\Area;
use App\Core\Helper;

class UsuarioControlador {
    private $usuarioModel;
    private $rolModel;
    private $areaModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Administrador') {
            Helper::redireccionar('dashboard');
        }
        $this->usuarioModel = new Usuario();
        $this->rolModel = new Role();
        $this->areaModel = new Area();
    }

    public function index() {
        $usuarios = $this->usuarioModel->getAll();
        require_once '../app/Views/users/index.php';
    }

    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nombre' => Helper::limpiar($_POST['nombre']),
                'email' => Helper::limpiar($_POST['email']),
                'password' => $_POST['password'],
                'id_rol' => $_POST['id_rol'],
                'id_area' => $_POST['id_area']
            ];

            if ($this->usuarioModel->crear($data)) {
                Helper::redireccionar('usuario/index');
            }
        } else {
            $roles = $this->rolModel->getAll();
            $areas = $this->areaModel->getAll();
            require_once '../app/Views/users/form.php';
        }
    }

    public function editar($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nombre' => Helper::limpiar($_POST['nombre']),
                'email' => Helper::limpiar($_POST['email']),
                'password' => $_POST['password'],
                'id_rol' => $_POST['id_rol'],
                'id_area' => $_POST['id_area']
            ];

            if ($this->usuarioModel->actualizar($id, $data)) {
                Helper::redireccionar('usuario/index');
            }
        } else {
            $usuario = $this->usuarioModel->getUsuarioById($id);
            $roles = $this->rolModel->getAll();
            $areas = $this->areaModel->getAll();
            require_once '../app/Views/users/form.php';
        }
    }

    public function cambiar($id) {
        $this->usuarioModel->cambiarEstado($id);
        Helper::redireccionar('usuario/index');
    }

    public function restablecerPassword($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nuevaPassword = trim($_POST['nueva_password'] ?? '');

            if (empty($nuevaPassword) || strlen($nuevaPassword) < 4) {
                $_SESSION['alerta'] = [
                    'tipo' => 'danger',
                    'mensaje' => 'La nueva contraseña debe tener como mínimo 4 caracteres.'
                ];
                Helper::redireccionar('usuario/index');
                return;
            }

            $u = $this->usuarioModel->getUsuarioById($id);
            if (!$u) {
                $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Usuario no encontrado.'];
                Helper::redireccionar('usuario/index');
                return;
            }

            if ($this->usuarioModel->restablecerPassword($id, $nuevaPassword)) {
                $_SESSION['alerta'] = [
                    'tipo' => 'success',
                    'mensaje' => "¡Contraseña restablecida correctamente para <strong>" . htmlspecialchars($u['nombre']) . "</strong>! Nueva clave asignada: <strong class='text-dark bg-white px-2 py-1 rounded border'>" . htmlspecialchars($nuevaPassword) . "</strong> (Compártela con el colaborador)."
                ];
            } else {
                $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Error al actualizar la contraseña en la base de datos.'];
            }
            Helper::redireccionar('usuario/index');
        }
    }

    public function eliminar($id) {
        if ($id == $_SESSION['user_id']) {
            $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'No puedes eliminar tu propio usuario administrador.'];
            Helper::redireccionar('usuario/index');
            return;
        }

        try {
            if ($this->usuarioModel->eliminar($id)) {
                $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => 'Usuario eliminado correctamente.'];
            } else {
                $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Error al eliminar el usuario.'];
            }
        } catch (\PDOException $e) {
            $_SESSION['alerta'] = [
                'tipo' => 'danger',
                'mensaje' => 'No se puede eliminar este usuario porque tiene registros asociados (tickets, comentarios o asignaciones). Te sugerimos cambiar su estado a Inactivo.'
            ];
        }
        Helper::redireccionar('usuario/index');
    }
}
