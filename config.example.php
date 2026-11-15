<?php
// config.example.php — PLANTILLA DE CONFIGURACIÓN
// ================================================
// Copia este archivo como "config.php" y completa tus valores.
// config.php está en .gitignore y NUNCA se sube al repositorio.

// ============================================================================
// CONFIGURACIÓN REGIONAL Y ZONA HORARIA
// ============================================================================
date_default_timezone_set('America/Lima');

// ============================================================================
// CONFIGURACIÓN DE BASE DE DATOS (SQL Server 2019)
// ============================================================================
define('DB_SERVER', getenv('DB_SERVER') ?: 'localhost');
define('DB_PORT',   getenv('DB_PORT')   ?: '1433');
define('DB_USER',   getenv('DB_USER')   ?: '');          // Tu usuario de BD
define('DB_PASS',   getenv('DB_PASS')   ?: '');          // Tu contraseña de BD
define('DB_NAME',   getenv('DB_NAME')   ?: 'sistema_tickets');

// ============================================================================
// CONFIGURACIÓN DE INTELIGENCIA ARTIFICIAL (Gemini API)
// ============================================================================
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: '');  // Tu API Key de Gemini
define('GEMINI_API_URL', getenv('GEMINI_API_URL') ?: 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent');

// ============================================================================
// CONFIGURACIÓN DE MACHINE LEARNING LOCAL (Python + Naive Bayes)
// ============================================================================
define('PYTHON_EXECUTABLE', getenv('PYTHON_EXECUTABLE') ?: 'python');
define('ML_PREDICT_SCRIPT', dirname(__FILE__) . '/ml/predict.py');

// ============================================================================
// URL Y RUTAS DE LA APLICACIÓN
// ============================================================================
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$uri = $_SERVER['REQUEST_URI'] ?? '';

if (strpos($uri, '/PROYECTO%20PARACTICAS/public') !== false || strpos($uri, '/PROYECTO PRACTICAS/public') !== false) {
    define('APP_URL', $protocol . '://' . $host . '/PROYECTO%20PARACTICAS/public');
} elseif (strpos($uri, '/practicas/public') !== false) {
    define('APP_URL', $protocol . '://' . $host . '/practicas/public');
} else {
    define('APP_URL', $protocol . '://' . $host);
}

define('APP_ROOT', dirname(__FILE__));
define('STORAGE_PATH', APP_ROOT . '/storage');
