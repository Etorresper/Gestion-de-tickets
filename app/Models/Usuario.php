<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Usuario {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT u.*, r.nombre_rol, t.nombre_tienda, d.nombre_distrito 
                                    FROM USUARIOS u 
                                    LEFT JOIN ROLES r ON u.id_rol = r.id_rol 
                                    LEFT JOIN TIENDAS t ON u.id_tienda = t.id_tienda 
                                    LEFT JOIN DISTRITOS d ON t.id_distrito = d.id_distrito 
                                    WHERE u.email = :email AND u.activo = 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return false;
    }

    public function getUsuarioById($id) {
        $stmt = $this->db->prepare("SELECT * FROM USUARIOS WHERE id_usuario = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function getAll() {
        $sql = "SELECT u.*, r.nombre_rol, a.nombre_area 
                FROM USUARIOS u 
                LEFT JOIN ROLES r ON u.id_rol = r.id_rol 
                LEFT JOIN AREAS a ON u.id_area = a.id_area 
                ORDER BY u.nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($data) {
        $sql = "INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo) 
                VALUES (:nombre, :email, :password, :id_rol, :id_area, 1)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':email' => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':id_rol' => $data['id_rol'],
            ':id_area' => $data['id_area']
        ]);
    }

    public function actualizar($id, $data) {
        $sql = "UPDATE USUARIOS SET nombre = :nombre, email = :email, id_rol = :id_rol, id_area = :id_area";
        $params = [
            ':nombre' => $data['nombre'],
            ':email' => $data['email'],
            ':id_rol' => $data['id_rol'],
            ':id_area' => $data['id_area'],
            ':id' => $id
        ];

        if (!empty($data['password'])) {
            $sql .= ", password_hash = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function cambiarEstado($id) {
        $stmt = $this->db->prepare("UPDATE USUARIOS SET activo = CASE WHEN activo = 1 THEN 0 ELSE 1 END WHERE id_usuario = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function restablecerPassword($id, $nuevaPassword) {
        $stmt = $this->db->prepare("UPDATE USUARIOS SET password_hash = :hash WHERE id_usuario = :id");
        return $stmt->execute([
            ':hash' => password_hash($nuevaPassword, PASSWORD_DEFAULT),
            ':id'   => $id
        ]);
    }
    
    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM USUARIOS WHERE id_usuario = :id");
        return $stmt->execute([':id' => $id]);
    }
    
    /**
     * Obtiene técnicos de Soporte de un área específica, excluyendo administradores.
     */
    public function getTecnicosPorArea($id_area) {
        $stmt = $this->db->prepare(
            "SELECT u.* FROM USUARIOS u 
             INNER JOIN ROLES r ON u.id_rol = r.id_rol 
             WHERE u.id_area = :id_area 
               AND r.nombre_rol = 'Soporte' 
               AND u.id_rol != 1
               AND u.activo = 1"
        );
        $stmt->bindParam(':id_area', $id_area, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los técnicos de Soporte del sistema, excluyendo administradores.
     * Se usa como fallback si no hay técnicos en el área específica.
     */
    public function getTecnicosGlobales() {
        $stmt = $this->db->prepare(
            "SELECT u.* FROM USUARIOS u 
             INNER JOIN ROLES r ON u.id_rol = r.id_rol 
             WHERE r.nombre_rol = 'Soporte' 
               AND u.id_rol != 1
               AND u.activo = 1 
             ORDER BY u.id_usuario ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el técnico asignado por defecto según la prioridad especificada.
     * Reglas:
     * - Crítica / Alta -> Henri Gonzales (ID 4)
     * - Media -> Jofre Melgarejo (ID 3)
     * - Baja -> Edwin Torres (ID 2)
     */
    public function getTecnicoPorPrioridad($prioridad) {
        $prioridad = strtolower(trim($prioridad));
        
        $nombreBuscar = '';
        $fallbackId = null;
        if ($prioridad === 'crítica' || $prioridad === 'critica' || $prioridad === 'alta') {
            $nombreBuscar = 'Henri Gonzales';
            $fallbackId = 4;
        } elseif ($prioridad === 'media') {
            $nombreBuscar = 'Jofre Melgarejo';
            $fallbackId = 3;
        } elseif ($prioridad === 'baja') {
            $nombreBuscar = 'Edwin Torres';
            $fallbackId = 2;
        }
        
        if (!empty($nombreBuscar)) {
            $stmt = $this->db->prepare("SELECT TOP 1 * FROM USUARIOS WHERE (nombre LIKE :nombre OR id_usuario = :fallbackId) AND activo = 1");
            $stmt->execute([':nombre' => '%' . $nombreBuscar . '%', ':fallbackId' => $fallbackId]);
            $tecnico = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($tecnico) {
                return $tecnico;
            }
        }
        
        return null;
    }
}
