<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Historial {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function add($id_ticket, $id_usuario, $accion, $detalle) {
        $sql = "INSERT INTO HISTORIAL_TICKETS (id_ticket, id_usuario, accion, detalle) 
                VALUES (:id_ticket, :id_usuario, :accion, :detalle)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_ticket' => $id_ticket,
            ':id_usuario' => $id_usuario,
            ':accion' => $accion,
            ':detalle' => $detalle
        ]);
    }

    public function getByTicketId($id_ticket) {
        $sql = "SELECT h.*, u.nombre as usuario 
                FROM HISTORIAL_TICKETS h 
                LEFT JOIN USUARIOS u ON h.id_usuario = u.id_usuario 
                WHERE h.id_ticket = :id_ticket 
                ORDER BY h.fecha_accion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_ticket' => $id_ticket]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
