<?php
require_once '../config.php';
require_once '../app/Core/Database.php';

try {
    $db = \App\Core\Database::getInstance()->getConnection();
    
    // Ver los tickets
    $stmt = $db->query("SELECT id_ticket, codigo_ticket, asunto, id_estado, id_categoria, escalar_no_resuelto FROM TICKETS");
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Tickets en BD:</h3><pre>";
    print_r($tickets);
    echo "</pre>";

    // Ver si existe el estado 5 en ESTADOS
    $stmt2 = $db->query("SELECT * FROM ESTADOS");
    $estados = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>Estados en BD:</h3><pre>";
    print_r($estados);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
