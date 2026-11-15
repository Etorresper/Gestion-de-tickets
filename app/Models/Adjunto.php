<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Adjunto {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($id_ticket, $nombre, $ruta, $mime) {
        $sql = "INSERT INTO ADJUNTOS (id_ticket, nombre_archivo, ruta_archivo, tipo_mime) 
                VALUES (:id_ticket, :nombre, :ruta, :mime)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_ticket' => $id_ticket,
            ':nombre' => $nombre,
            ':ruta' => $ruta,
            ':mime' => $mime
        ]);
    }

    public function getByTicketId($id_ticket) {
        $sql = "SELECT * FROM ADJUNTOS WHERE id_ticket = :id_ticket";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_ticket' => $id_ticket]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
