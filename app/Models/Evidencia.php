<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Evidencia {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function add($id_ticket, $id_usuario, $fase, $nombre_archivo, $ruta_archivo, $tipo_mime, $descripcion = '', $estado_equipo = null) {
        $sql = "INSERT INTO EVIDENCIAS_TICKET (id_ticket, id_usuario, fase_evidencia, nombre_archivo, ruta_archivo, tipo_mime, descripcion, estado_equipo, fecha_registro) 
                VALUES (:tkt, :usr, :fase, :nom, :ruta, :mime, :desc, :est, GETDATE())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':tkt'  => $id_ticket,
            ':usr'  => $id_usuario,
            ':fase' => $fase, // 'Antes', 'Durante', 'Después'
            ':nom'  => $nombre_archivo,
            ':ruta' => $ruta_archivo,
            ':mime' => $tipo_mime,
            ':desc' => $descripcion,
            ':est'  => $estado_equipo
        ]);
    }

    public function getAll($filtros = []) {
        $sql = "SELECT e.*, u.nombre as usuario, r.nombre_rol, 
                       tk.codigo_ticket, tk.asunto as ticket_asunto, tk.id_tienda,
                       t.nombre_tienda, dis.nombre_distrito
                FROM EVIDENCIAS_TICKET e 
                JOIN USUARIOS u ON e.id_usuario = u.id_usuario 
                LEFT JOIN ROLES r ON u.id_rol = r.id_rol 
                JOIN TICKETS tk ON e.id_ticket = tk.id_ticket
                LEFT JOIN TIENDAS t ON tk.id_tienda = t.id_tienda
                LEFT JOIN DISTRITOS dis ON t.id_distrito = dis.id_distrito
                WHERE 1=1";
        $params = [];
        if (!empty($filtros['fase_evidencia'])) {
            $sql .= " AND e.fase_evidencia = :fase";
            $params[':fase'] = $filtros['fase_evidencia'];
        }
        if (!empty($filtros['id_tienda'])) {
            $sql .= " AND tk.id_tienda = :id_tda";
            $params[':id_tda'] = $filtros['id_tienda'];
        }
        if (!empty($filtros['id_ticket'])) {
            $sql .= " AND e.id_ticket = :id_tkt";
            $params[':id_tkt'] = $filtros['id_ticket'];
        }
        if (!empty($filtros['id_usuario'])) {
            $sql .= " AND e.id_usuario = :id_usr";
            $params[':id_usr'] = $filtros['id_usuario'];
        }
        $sql .= " ORDER BY e.id_evidencia DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByTicketId($id_ticket, $fase = null) {
        $sql = "SELECT e.*, u.nombre as usuario, r.nombre_rol 
                FROM EVIDENCIAS_TICKET e 
                JOIN USUARIOS u ON e.id_usuario = u.id_usuario 
                LEFT JOIN ROLES r ON u.id_rol = r.id_rol 
                WHERE e.id_ticket = :tkt";
        $params = [':tkt' => $id_ticket];

        if (!empty($fase)) {
            $sql .= " AND e.fase_evidencia = :fase";
            $params[':fase'] = $fase;
        }

        $sql .= " ORDER BY e.fecha_registro ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarPorFase($id_ticket) {
        $sql = "SELECT fase_evidencia, COUNT(*) as total 
                FROM EVIDENCIAS_TICKET 
                WHERE id_ticket = :tkt 
                GROUP BY fase_evidencia";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':tkt' => $id_ticket]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totales = ['Antes' => 0, 'Durante' => 0, 'Después' => 0];
        foreach ($rows as $r) {
            $totales[$r['fase_evidencia']] = (int)$r['total'];
        }
        return $totales;
    }
}

