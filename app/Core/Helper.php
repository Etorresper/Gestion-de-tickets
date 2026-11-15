<?php
namespace App\Core;

class Helper {
    public static function redireccionar($url) {
        header('Location: ' . APP_URL . '/' . $url);
        exit();
    }

    public static function limpiar($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    public static function formatearFecha($fecha) {
        if (!$fecha) return '';
        return date('d/m/Y H:i', strtotime($fecha));
    }
}
