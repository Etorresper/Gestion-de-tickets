<?php
namespace App\Core;

class Router {
    protected $controller = 'DashboardControlador';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // Determinar controlador objetivo de la URL
        $targetController = isset($url[0]) ? ucwords($url[0]) . 'Controlador' : $this->controller;
        
        // Control de sesión básico: Si no hay sesión y no estamos en el login
        if (!isset($_SESSION['user_id']) && $targetController !== 'AutenticacionControlador') {
            $this->controller = 'AutenticacionControlador';
            $this->method = 'login';
        } else {
            if (isset($url[0]) && file_exists('../app/Controllers/' . ucwords($url[0]) . 'Controlador.php')) {
                $this->controller = ucwords($url[0]) . 'Controlador';
                unset($url[0]);
            }
        }

        $controllerClass = '\\App\\Controllers\\' . $this->controller;
        if (class_exists($controllerClass)) {
            $this->controller = new $controllerClass;
        } else {
            die("Error 404: Controller no encontrado -> " . $controllerClass);
        }

        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        $this->params = $url ? array_values($url) : [];
    }

    public function run() {
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
