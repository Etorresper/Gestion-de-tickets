<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Role {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM ROLES");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
