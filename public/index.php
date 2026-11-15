<?php
// public/index.php
session_start();

require_once '../config.php';

// Autocarga de clases básicas basadas en el namespace App\
spl_autoload_register(function ($class_name) {
    // Ejemplo: App\Core\Database -> app/Core/Database.php
    $prefix = 'App\\';
    $base_dir = APP_ROOT . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class_name, $len) !== 0) {
        return;
    }

    $relative_class = substr($class_name, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\Router;

// Inicializar y ejecutar el enrutador
$router = new Router();
$router->run();
