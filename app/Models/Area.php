<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Area {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM AREAS");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
