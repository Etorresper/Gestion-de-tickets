<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Distrito {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM DISTRITOS ORDER BY nombre_distrito ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
