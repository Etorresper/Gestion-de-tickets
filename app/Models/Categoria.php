<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Categoria {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM CATEGORIAS");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAreaById($id) {
        $stmt = $this->db->prepare("SELECT id_area_responsable FROM CATEGORIAS WHERE id_categoria = :id");
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch();
        return $res ? $res['id_area_responsable'] : null;
    }
}
