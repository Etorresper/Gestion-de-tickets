<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Tienda {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $sql = "SELECT t.*, d.nombre_distrito 
                FROM TIENDAS t 
                LEFT JOIN DISTRITOS d ON t.id_distrito = d.id_distrito 
                ORDER BY t.nombre_tienda ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT t.*, d.nombre_distrito 
                FROM TIENDAS t 
                LEFT JOIN DISTRITOS d ON t.id_distrito = d.id_distrito 
                WHERE t.id_tienda = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
