<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Comentario {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function add($id_ticket, $id_usuario, $comentario) {
        $sql = "INSERT INTO comentarios_ticket (id_ticket, id_usuario, comentario) 
                VALUES (:id_ticket, :id_usuario, :comentario)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_ticket' => $id_ticket,
            ':id_usuario' => $id_usuario,
            ':comentario' => $comentario
        ]);
    }

    public function getByTicketId($id_ticket) {
        $sql = "SELECT c.*, c.fecha_comentario as fecha_creacion, u.nombre as usuario 
                FROM COMENTARIOS_TICKET c 
                LEFT JOIN USUARIOS u ON c.id_usuario = u.id_usuario 
                WHERE c.id_ticket = :id_ticket 
                ORDER BY c.fecha_comentario ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_ticket' => $id_ticket]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
