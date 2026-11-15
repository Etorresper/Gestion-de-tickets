<?php
// Script de auto-migración ejecutado a través del servidor web PHP
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

header('Content-Type: text/html; charset=utf-8');

try {
    $db = Database::getInstance()->getConnection();
    echo "<h2>Conectado con éxito a SQL Server (" . htmlspecialchars(DB_NAME) . ")</h2>";

    $queries = [
        "IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TICKETS' AND COLUMN_NAME = 'equipo_nombre')
         BEGIN
             ALTER TABLE dbo.TICKETS ADD equipo_nombre NVARCHAR(100) NULL;
             PRINT 'Columna equipo_nombre agregada.';
         END",
        "IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TICKETS' AND COLUMN_NAME = 'equipo_modelo')
         BEGIN
             ALTER TABLE dbo.TICKETS ADD equipo_modelo NVARCHAR(100) NULL;
             PRINT 'Columna equipo_modelo agregada.';
         END",
        "IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TICKETS' AND COLUMN_NAME = 'equipo_serie')
         BEGIN
             ALTER TABLE dbo.TICKETS ADD equipo_serie NVARCHAR(100) NULL;
             PRINT 'Columna equipo_serie agregada.';
         END",
        "IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TICKETS' AND COLUMN_NAME = 'desde_cuando_falla')
         BEGIN
             ALTER TABLE dbo.TICKETS ADD desde_cuando_falla NVARCHAR(100) NULL;
             PRINT 'Columna desde_cuando_falla agregada.';
         END",
        "IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TICKETS' AND COLUMN_NAME = 'estado_final_equipo')
         BEGIN
             ALTER TABLE dbo.TICKETS ADD estado_final_equipo NVARCHAR(50) NULL;
             PRINT 'Columna estado_final_equipo agregada.';
         END"
    ];

    foreach ($queries as $sql) {
        $db->exec($sql);
    }

    echo "<p style='color: green; font-size: 1.2rem; font-weight: bold;'>¡Migración completada con éxito! Las columnas 'equipo_nombre', 'equipo_modelo', 'equipo_serie', 'desde_cuando_falla' y 'estado_final_equipo' ya existen en la tabla TICKETS.</p>";

    // Mostrar columnas actuales
    $stmt = $db->query("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TICKETS'");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h4>Columnas actuales en la tabla TICKETS:</h4><ul>";
    foreach ($cols as $col) {
        echo "<li><strong>" . htmlspecialchars($col['COLUMN_NAME']) . "</strong> (" . htmlspecialchars($col['DATA_TYPE']) . ")</li>";
    }
    echo "</ul>";

} catch (\Exception $e) {
    echo "<p style='color: red;'>Error ejecutando migración: " . htmlspecialchars($e->getMessage()) . "</p>";
}

