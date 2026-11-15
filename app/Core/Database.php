<?php
namespace App\Core;

use PDO;
use PDOException;

/**
 * Clase Database — Singleton de conexión PDO para SQL Server 2019.
 * Utiliza el driver PDO_SQLSRV (Microsoft).
 *
 * Requisitos:
 *  - PHP extension: pdo_sqlsrv  (o sqlsrv)
 *  - Microsoft ODBC Driver 17/18 for SQL Server instalado en el sistema
 */
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        // DSN para SQL Server via PDO_SQLSRV
        // Si hay puerto definido, se anexa ",puerto", de lo contrario conecta directamente (permite Shared Memory/Named Pipes)
        $server = DB_SERVER;
        if (defined('DB_PORT') && !empty(DB_PORT)) {
            $server .= "," . DB_PORT;
        }

        $dsn = "sqlsrv:Server=" . $server
             . ";Database=" . DB_NAME
             . ";TrustServerCertificate=1";          // Permite certificados auto-firmados en entornos locales

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::SQLSRV_ATTR_ENCODING    => PDO::SQLSRV_ENCODING_UTF8,
            // NOTA: PDO_SQLSRV NO soporta ATTR_EMULATE_PREPARES=false (es ignorado o causa error)
        ];

        try {
            if (empty(DB_USER)) {
                // Autenticación de Windows (sin contraseña)
                $this->pdo = new PDO($dsn, null, null, $options);
            } else {
                // Autenticación SQL Server
                $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            }
            $this->pdo->exec("SET DATEFORMAT ymd;");
        } catch (PDOException $e) {
            // Fallback: Si la conexión por puerto TCP falló, intentar conexión directa local
            try {
                $fallbackDsn = "sqlsrv:Server=(local);Database=" . DB_NAME . ";TrustServerCertificate=1";
                if (empty(DB_USER)) {
                    $this->pdo = new PDO($fallbackDsn, null, null, $options);
                } else {
                    $this->pdo = new PDO($fallbackDsn, DB_USER, DB_PASS, $options);
                }
                $this->pdo->exec("SET DATEFORMAT ymd;");
            } catch (PDOException $e2) {
                die("Error de conexión a SQL Server: " . $e->getMessage());
            }
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
