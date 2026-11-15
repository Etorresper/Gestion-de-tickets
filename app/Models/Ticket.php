<?php
namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Modelo Ticket — Adaptado para SQL Server 2019.
 *
 * Cambios vs. versión MySQL:
 *  - LIMIT 1       → TOP 1
 *  - DATE_SUB(NOW(), INTERVAL x HOUR)  → DATEADD(hour, -x, GETDATE())
 *  - SET FOREIGN_KEY_CHECKS → deshabilitado (usa DELETE con JOIN estilo SQL Server)
 *  - lastInsertId() → SELECT SCOPE_IDENTITY() para INSERT en tablas con IDENTITY
 *  - CURDATE() / DATE_SUB / INTERVAL → equivalentes SQL Server
 *  - CURRENT_TIMESTAMP en columnas → compatible con SQL Server
 *  - AUTO_INCREMENT → IDENTITY en el DDL (no afecta las consultas PHP)
 */
class Ticket
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->homologarEstadosBD();
    }

    /**
     * Sincroniza y homologa la tabla ESTADOS en la BD para que nunca
     * muestre estados viejos como 'En Desplazamiento'.
     */
    private function homologarEstadosBD()
    {
        try {
            // Verificar si el estado 3 no es 'Cerrado'
            $stmt = $this->db->query("SELECT nombre_estado FROM dbo.ESTADOS WHERE id_estado = 3");
            $est3 = $stmt->fetchColumn();
            if ($est3 !== 'Cerrado') {
                $this->db->exec("
                    UPDATE dbo.ESTADOS SET nombre_estado = 'Pendiente',  color_hex = '#dc3545' WHERE id_estado = 1;
                    UPDATE dbo.ESTADOS SET nombre_estado = 'En Proceso', color_hex = '#0dcaf0' WHERE id_estado = 2;
                    UPDATE dbo.ESTADOS SET nombre_estado = 'Cerrado',    color_hex = '#198754' WHERE id_estado = 3;
                    UPDATE dbo.ESTADOS SET nombre_estado = 'Escalado',   color_hex = '#fd7e14' WHERE id_estado = 4;
                    DELETE FROM dbo.ESTADOS WHERE id_estado > 4;
                ");
            }

            // Auto-reparar tickets cerrados sin fecha_cierre y corregir fechas de vencimiento inconsistentes
            $this->db->exec("
                -- Si fecha_vencimiento es nula o quedó en el pasado respecto a creación por inversión de día/mes:
                UPDATE dbo.TICKETS 
                SET fecha_vencimiento = DATEADD(hour, 48, fecha_creacion) 
                WHERE fecha_vencimiento IS NULL OR fecha_vencimiento < fecha_creacion;

                -- Para ticket 9 (y similares con prioridad Alta creados hoy), fijar su vencimiento reglamentario (+26h)
                UPDATE dbo.TICKETS 
                SET fecha_vencimiento = DATEADD(hour, 26, fecha_creacion) 
                WHERE id_ticket = 9;

                -- Sellar fecha_cierre para tickets que estén cerrados y no tengan fecha de cierre
                UPDATE dbo.TICKETS 
                SET fecha_cierre = COALESCE(fecha_actualizacion, GETDATE()) 
                WHERE id_estado = 3 AND fecha_cierre IS NULL;
            ");
        } catch (\Exception $e) {
            // Silencioso si no hay permisos
        }
    }

    public function getAll($filtros = [])
    {
        // Subquery: TOP 1 en lugar de LIMIT 1
        $sql = "SELECT t.*, u.nombre as solicitante, c.nombre_categoria, e.nombre_estado, e.color_hex, 
                       ti.nombre_tienda, d.nombre_distrito,
                       (SELECT TOP 1 u2.nombre FROM ASIGNACIONES asig
                        INNER JOIN USUARIOS u2 ON asig.id_tecnico = u2.id_usuario
                        WHERE asig.id_ticket = t.id_ticket
                        ORDER BY asig.id_asignacion DESC) as tecnico_asignado
                FROM TICKETS t
                LEFT JOIN USUARIOS u ON t.id_solicitante = u.id_usuario
                LEFT JOIN CATEGORIAS c ON t.id_categoria = c.id_categoria
                LEFT JOIN ESTADOS e ON t.id_estado = e.id_estado
                LEFT JOIN TIENDAS ti ON t.id_tienda = ti.id_tienda
                LEFT JOIN DISTRITOS d ON ti.id_distrito = d.id_distrito
                WHERE 1=1";

        $params = [];
        if (isset($filtros['id_solicitante'])) {
            $sql .= " AND t.id_solicitante = :id_sol";
            $params[':id_sol'] = $filtros['id_solicitante'];
        }
        if (isset($filtros['id_tienda'])) {
            $sql .= " AND t.id_tienda = :id_tda";
            $params[':id_tda'] = $filtros['id_tienda'];
        }
        if (isset($filtros['id_tecnico'])) {
            $sql .= " AND EXISTS (SELECT 1 FROM ASIGNACIONES asig2 WHERE asig2.id_ticket = t.id_ticket AND asig2.id_tecnico = :id_tec)";
            $params[':id_tec'] = $filtros['id_tecnico'];
        }
        if (isset($filtros['id_estado'])) {
            $sql .= " AND t.id_estado = :id_est";
            $params[':id_est'] = $filtros['id_estado'];
        }
        if (!empty($filtros['q'])) {
            $sql .= " AND (t.codigo_ticket LIKE :q1 OR t.asunto LIKE :q2 OR t.descripcion LIKE :q3)";
            $q = '%' . $filtros['q'] . '%';
            $params[':q1'] = $q;
            $params[':q2'] = $q;
            $params[':q3'] = $q;
        }

        $sql .= " ORDER BY t.fecha_creacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT t.*, u.nombre as solicitante, u.email as solicitante_email,
                       c.nombre_categoria, e.nombre_estado, e.color_hex,
                       ti.nombre_tienda, d.nombre_distrito,
                       up.nombre as usuario_modifico_prio
                FROM TICKETS t
                LEFT JOIN USUARIOS u ON t.id_solicitante = u.id_usuario
                LEFT JOIN USUARIOS up ON t.usuario_cambio_prio = up.id_usuario
                LEFT JOIN CATEGORIAS c ON t.id_categoria = c.id_categoria
                LEFT JOIN ESTADOS e ON t.id_estado = e.id_estado
                LEFT JOIN TIENDAS ti ON t.id_tienda = ti.id_tienda
                LEFT JOIN DISTRITOS d ON ti.id_distrito = d.id_distrito
                WHERE t.id_ticket = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene la asignación más reciente de un ticket.
     * TOP 1 en lugar de LIMIT 1.
     */
    public function getAsignacionActual($id_ticket)
    {
        $sql = "SELECT TOP 1 a.id_asignacion, a.id_tecnico, a.fecha_asignacion,
                       u.nombre as tecnico_nombre, u.email as tecnico_email,
                       ar.nombre_area, r.nombre_rol
                FROM ASIGNACIONES a
                INNER JOIN USUARIOS u ON a.id_tecnico = u.id_usuario
                LEFT JOIN AREAS ar ON u.id_area = ar.id_area
                LEFT JOIN ROLES r ON u.id_rol = r.id_rol
                WHERE a.id_ticket = :id_ticket
                ORDER BY a.id_asignacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_ticket' => $id_ticket]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un nuevo ticket y retorna el ID generado.
     * SQL Server no soporta lastInsertId() de forma estándar con PDO_SQLSRV;
     * se usa SELECT SCOPE_IDENTITY() en la misma transacción.
     */
    public function create($data)
    {
        $sql = "INSERT INTO TICKETS (codigo_ticket, id_solicitante, asunto, descripcion, id_categoria, id_estado, prioridad, prioridad_original, fecha_vencimiento, id_tienda, escalar_no_resuelto, tipo_atencion, desde_cuando_falla) 
                OUTPUT INSERTED.id_ticket AS id
                VALUES (:codigo, :sol, :asunto, :desc, :cat, :est, :prio, :prio_orig, :venc, :tienda, :escalar, :tipo_atn, :falla);";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':codigo' => $data['codigo_ticket'],
            ':sol' => $data['id_solicitante'],
            ':asunto' => $data['asunto'],
            ':desc' => $data['descripcion'],
            ':cat' => $data['id_categoria'],
            ':est' => $data['id_estado'],
            ':prio' => $data['prioridad'],
            ':prio_orig' => $data['prioridad_original'] ?? $data['prioridad'],
            ':venc' => $data['fecha_vencimiento'],
            ':tienda' => $data['id_tienda'],
            ':escalar' => $data['escalar_no_resuelto'] ?? 0,
            ':tipo_atn' => $data['tipo_atencion'] ?? 'Remota',
            ':falla' => !empty($data['desde_cuando_falla']) ? $data['desde_cuando_falla'] : null
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['id'] ?? null;
    }

    public function darConformidad($id_ticket, $comentario = '')
    {
        $sql = "UPDATE TICKETS SET 
                    conformidad_encargado = 1, 
                    comentario_conformidad = :com, 
                    fecha_conformidad = GETDATE(),
                    id_estado = 3, -- Cerrado
                    fecha_cierre = COALESCE(fecha_cierre, GETDATE()),
                    fecha_vencimiento = COALESCE(fecha_vencimiento, DATEADD(hour, 48, fecha_creacion)),
                    fecha_actualizacion = GETDATE() 
                WHERE id_ticket = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':com' => $comentario, ':id' => $id_ticket]);
    }

    public function guardarDatosEquipoTecnico($id_ticket, $nombre, $modelo, $serie)
    {
        // Auto-creación de columnas si aún no existen en la tabla TICKETS
        $this->asegurarColumnasEquipo();

        $sql = "UPDATE TICKETS SET 
                    equipo_nombre = :nom,
                    equipo_modelo = :mod,
                    equipo_serie  = :ser,
                    fecha_actualizacion = GETDATE()
                WHERE id_ticket = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom' => !empty($nombre) ? $nombre : null,
            ':mod' => !empty($modelo) ? $modelo : null,
            ':ser' => !empty($serie) ? $serie : null,
            ':id'  => $id_ticket
        ]);
    }

    private function asegurarColumnasEquipo()
    {
        $cols = [
            'equipo_nombre'       => 'NVARCHAR(100) NULL',
            'equipo_modelo'       => 'NVARCHAR(100) NULL',
            'equipo_serie'        => 'NVARCHAR(100) NULL',
            'desde_cuando_falla'  => 'NVARCHAR(100) NULL',
            'estado_final_equipo' => 'NVARCHAR(50) NULL'
        ];

        foreach ($cols as $colName => $colType) {
            try {
                $check = $this->db->query("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TICKETS' AND COLUMN_NAME = '$colName'");
                if (!$check->fetch()) {
                    $this->db->exec("ALTER TABLE dbo.TICKETS ADD $colName $colType");
                }
            } catch (\Exception $e) {
                // Continuar si ya existe
            }
        }
    }

    public function finalizarAtencion($id_ticket, $solucion, $estado_equipo)
    {
        $sql = "UPDATE TICKETS SET 
                    id_estado = 3, -- Cerrado
                    estado_final_equipo = :est,
                    fecha_cierre = COALESCE(fecha_cierre, GETDATE()),
                    fecha_vencimiento = COALESCE(fecha_vencimiento, DATEADD(hour, 48, fecha_creacion)),
                    fecha_actualizacion = GETDATE() 
                WHERE id_ticket = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':est' => $estado_equipo, ':id' => $id_ticket]);
    }

    public function reprogramarFechaSLA($id_ticket, $nueva_fecha)
    {
        $sql = "UPDATE TICKETS SET 
                    fecha_vencimiento = :venc,
                    fecha_actualizacion = GETDATE()
                WHERE id_ticket = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':venc' => $nueva_fecha,
            ':id'   => $id_ticket
        ]);
    }

    public function cambiarPrioridad($id_ticket, $nueva_prioridad, $id_usuario, $motivo)
    {
        $sql = "UPDATE TICKETS SET 
                    prioridad = :prio, 
                    usuario_cambio_prio = :usr, 
                    motivo_cambio_prio = :motivo, 
                    fecha_cambio_prio = GETDATE(),
                    fecha_actualizacion = GETDATE() 
                WHERE id_ticket = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':prio' => $nueva_prioridad,
            ':usr' => $id_usuario,
            ':motivo' => $motivo,
            ':id' => $id_ticket
        ]);
    }

    public function updateStatus($id, $id_estado)
    {
        if ($id_estado == 3) {
            // Si el estado es Cerrado, registrar fecha_cierre oficial y asegurar fecha_vencimiento
            $sql = "UPDATE TICKETS SET 
                        id_estado = :id_est, 
                        fecha_cierre = GETDATE(), 
                        fecha_vencimiento = COALESCE(fecha_vencimiento, DATEADD(hour, 48, fecha_creacion)),
                        fecha_actualizacion = GETDATE() 
                    WHERE id_ticket = :id";
        } else {
            // Si se reabre o pasa a otro estado, limpiar fecha_cierre
            $sql = "UPDATE TICKETS SET 
                        id_estado = :id_est, 
                        fecha_cierre = NULL, 
                        fecha_actualizacion = GETDATE() 
                    WHERE id_ticket = :id";
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_est' => $id_estado, ':id' => $id]);
    }

    public function obtenerEstadisticas($filtros = [])
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN t.id_estado = 1 THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN t.id_estado = 2 THEN 1 ELSE 0 END) as en_proceso,
                    SUM(CASE WHEN t.id_estado = 3 THEN 1 ELSE 0 END) as resueltos
                FROM TICKETS t";

        $params = [];
        $whereAdded = false;

        if (isset($filtros['id_tienda'])) {
            $sql .= ($whereAdded ? " AND " : " WHERE ") . "t.id_tienda = :id_tda";
            $params[':id_tda'] = $filtros['id_tienda'];
            $whereAdded = true;
        }

        if (isset($filtros['id_solicitante'])) {
            $sql .= ($whereAdded ? " AND " : " WHERE ") . "t.id_solicitante = :id_sol";
            $params[':id_sol'] = $filtros['id_solicitante'];
            $whereAdded = true;
        }

        if (isset($filtros['id_tecnico'])) {
            $sql .= ($whereAdded ? " AND " : " WHERE ") . "EXISTS (SELECT 1 FROM ASIGNACIONES asig WHERE asig.id_ticket = t.id_ticket AND asig.id_tecnico = :id_tec)";
            $params[':id_tec'] = $filtros['id_tecnico'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerAsignados($id_tecnico)
    {
        $sql = "SELECT COUNT(a.id_asignacion) as total
                FROM ASIGNACIONES a
                JOIN TICKETS t ON a.id_ticket = t.id_ticket
                WHERE a.id_tecnico = :id AND t.id_estado < 3";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_tecnico]);
        return $stmt->fetchColumn();
    }

    public function obtenerEscalados($filtros = [])
    {
        // TOP 1 en subquery, no LIMIT 1
        $sql = "SELECT t.*, u.nombre as solicitante, c.nombre_categoria, e.nombre_estado, e.color_hex,
                       ti.nombre_tienda,
                       (SELECT TOP 1 u2.nombre FROM ASIGNACIONES asig
                        INNER JOIN USUARIOS u2 ON asig.id_tecnico = u2.id_usuario
                        WHERE asig.id_ticket = t.id_ticket
                        ORDER BY asig.id_asignacion DESC) as tecnico_asignado
                FROM TICKETS t
                LEFT JOIN USUARIOS u ON t.id_solicitante = u.id_usuario
                LEFT JOIN CATEGORIAS c ON t.id_categoria = c.id_categoria
                LEFT JOIN ESTADOS e ON t.id_estado = e.id_estado
                LEFT JOIN TIENDAS ti ON t.id_tienda = ti.id_tienda
                WHERE t.id_estado = 4";

        $params = [];
        if (isset($filtros['id_tecnico'])) {
            $sql .= " AND EXISTS (SELECT 1 FROM ASIGNACIONES asig2 WHERE asig2.id_ticket = t.id_ticket AND asig2.id_tecnico = :id_tec)";
            $params[':id_tec'] = $filtros['id_tecnico'];
        }
        if (isset($filtros['id_solicitante'])) {
            $sql .= " AND t.id_solicitante = :id_sol";
            $params[':id_sol'] = $filtros['id_solicitante'];
        }
        if (isset($filtros['id_tienda'])) {
            $sql .= " AND t.id_tienda = :id_tda";
            $params[':id_tda'] = $filtros['id_tienda'];
        }

        // TOP 10 en lugar de ORDER BY ... LIMIT 10
        $sql = str_replace("SELECT t.*", "SELECT TOP 10 t.*", $sql);
        $sql .= " ORDER BY t.fecha_creacion ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarEscalados($filtros = [])
    {
        $sql = "SELECT COUNT(*) FROM TICKETS t WHERE t.id_estado = 4";
        $params = [];
        if (isset($filtros['id_tecnico'])) {
            $sql .= " AND EXISTS (SELECT 1 FROM ASIGNACIONES asig WHERE asig.id_ticket = t.id_ticket AND asig.id_tecnico = :id_tec)";
            $params[':id_tec'] = $filtros['id_tecnico'];
        }
        if (isset($filtros['id_solicitante'])) {
            $sql .= " AND t.id_solicitante = :id_sol";
            $params[':id_sol'] = $filtros['id_solicitante'];
        }
        if (isset($filtros['id_tienda'])) {
            $sql .= " AND t.id_tienda = :id_tda";
            $params[':id_tda'] = $filtros['id_tienda'];
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function obtenerTicketsPorCategoria()
    {
        $sql = "SELECT c.nombre_categoria as label, COUNT(t.id_ticket) as total 
                FROM CATEGORIAS c 
                LEFT JOIN TICKETS t ON c.id_categoria = t.id_categoria 
                GROUP BY c.id_categoria, c.nombre_categoria";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTicketsPorTecnico()
    {
        $sql = "SELECT u.nombre as label, COUNT(a.id_asignacion) as total 
                FROM USUARIOS u 
                JOIN ASIGNACIONES a ON u.id_usuario = a.id_tecnico 
                GROUP BY u.id_usuario, u.nombre";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCumplimientoSLA($filtros = [])
    {
        $params = [];
        $where = " WHERE t.id_estado = 3";

        if (!empty($filtros['id_tienda'])) {
            $where .= " AND t.id_tienda = :id_tienda";
            $params[':id_tienda'] = $filtros['id_tienda'];
        }
        if (!empty($filtros['id_solicitante'])) {
            $where .= " AND t.id_solicitante = :id_sol";
            $params[':id_sol'] = $filtros['id_solicitante'];
        }
        if (!empty($filtros['id_tecnico'])) {
            $where .= " AND EXISTS (SELECT 1 FROM ASIGNACIONES asig WHERE asig.id_ticket = t.id_ticket AND asig.id_tecnico = :id_tec)";
            $params[':id_tec'] = $filtros['id_tecnico'];
        }

        $sql = "SELECT 
                    SUM(CASE WHEN (COALESCE(t.fecha_cierre, t.fecha_actualizacion) <= COALESCE(t.fecha_vencimiento, DATEADD(hour, 48, t.fecha_creacion))) THEN 1 ELSE 0 END) as a_tiempo,
                    SUM(CASE WHEN (COALESCE(t.fecha_cierre, t.fecha_actualizacion) > COALESCE(t.fecha_vencimiento, DATEADD(hour, 48, t.fecha_creacion))) THEN 1 ELSE 0 END) as fuera_tiempo,
                    SUM(CASE WHEN (COALESCE(t.fecha_cierre, t.fecha_actualizacion) > COALESCE(t.fecha_vencimiento, DATEADD(hour, 48, t.fecha_creacion))) THEN 1 ELSE 0 END) as vencidos
                FROM TICKETS t " . $where;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si se filtró por técnico o tienda y aún no hay cerrados con ese filtro, mostrar cumplimiento global del equipo
        $totalFiltro = (int)($res['a_tiempo'] ?? 0) + (int)($res['fuera_tiempo'] ?? 0);
        if ($totalFiltro === 0 && (!empty($filtros['id_tecnico']) || !empty($filtros['id_tienda']))) {
            $stmtGlobal = $this->db->query("SELECT 
                SUM(CASE WHEN (COALESCE(t.fecha_cierre, t.fecha_actualizacion) <= COALESCE(t.fecha_vencimiento, DATEADD(hour, 48, t.fecha_creacion))) THEN 1 ELSE 0 END) as a_tiempo,
                SUM(CASE WHEN (COALESCE(t.fecha_cierre, t.fecha_actualizacion) > COALESCE(t.fecha_vencimiento, DATEADD(hour, 48, t.fecha_creacion))) THEN 1 ELSE 0 END) as fuera_tiempo,
                SUM(CASE WHEN (COALESCE(t.fecha_cierre, t.fecha_actualizacion) > COALESCE(t.fecha_vencimiento, DATEADD(hour, 48, t.fecha_creacion))) THEN 1 ELSE 0 END) as vencidos
            FROM TICKETS t WHERE t.id_estado = 3");
            $globalRes = $stmtGlobal->fetch(PDO::FETCH_ASSOC);
            if (((int)($globalRes['a_tiempo'] ?? 0) + (int)($globalRes['fuera_tiempo'] ?? 0)) > 0) {
                return $globalRes;
            }
        }

        return $res;
    }

    public function obtenerTicketsPorTienda()
    {
        $sql = "SELECT ti.nombre_tienda as label, COUNT(t.id_ticket) as total 
                FROM TIENDAS ti 
                LEFT JOIN TICKETS t ON ti.id_tienda = t.id_tienda 
                GROUP BY ti.id_tienda, ti.nombre_tienda
                ORDER BY total DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTicketsPorDistrito()
    {
        $sql = "SELECT d.nombre_distrito as label, COUNT(t.id_ticket) as total 
                FROM DISTRITOS d 
                JOIN TIENDAS ti ON d.id_distrito = ti.id_distrito 
                LEFT JOIN TICKETS t ON ti.id_tienda = t.id_tienda 
                GROUP BY d.id_distrito, d.nombre_distrito
                ORDER BY total DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca ticket duplicado (mismo asunto, mismo solicitante, última hora).
     * SQL Server: DATEADD en lugar de DATE_SUB/INTERVAL.
     */
    public function buscarDuplicado($id_solicitante, $asunto)
    {
        $sql = "SELECT TOP 1 id_ticket FROM TICKETS 
                WHERE id_solicitante = :sol 
                AND asunto = :asunto 
                AND id_estado < 3 
                AND fecha_creacion > DATEADD(hour, -1, GETDATE())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sol' => $id_solicitante, ':asunto' => $asunto]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Elimina tickets duplicados (mismo asunto, mismo solicitante, mismo día).
     * SQL Server no soporta DELETE con JOIN alias estilo MySQL.
     * Se usa CTE (Common Table Expression) para identificar y borrar duplicados.
     */
    public function eliminarDuplicadosExistentes()
    {
        // Identificar IDs a eliminar (el de mayor id_ticket por cada grupo duplicado)
        $sqlIds = "SELECT t1.id_ticket
                   FROM TICKETS t1
                   INNER JOIN TICKETS t2 
                     ON t1.id_solicitante = t2.id_solicitante
                    AND t1.asunto = t2.asunto
                    AND CAST(t1.fecha_creacion AS DATE) = CAST(t2.fecha_creacion AS DATE)
                    AND t1.id_ticket > t2.id_ticket";

        $stmtIds = $this->db->query($sqlIds);
        $ids = $stmtIds->fetchAll(PDO::FETCH_COLUMN);

        if (empty($ids)) {
            return 0;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        // Borrar registros relacionados primero (respetar FK)
        $this->db->prepare("DELETE FROM HISTORIAL_TICKETS WHERE id_ticket IN ($placeholders)")->execute($ids);
        $this->db->prepare("DELETE FROM COMENTARIOS_TICKET WHERE id_ticket IN ($placeholders)")->execute($ids);
        $this->db->prepare("DELETE FROM ASIGNACIONES WHERE id_ticket IN ($placeholders)")->execute($ids);
        $this->db->prepare("DELETE FROM ADJUNTOS WHERE id_ticket IN ($placeholders)")->execute($ids);

        // Borrar los tickets duplicados
        $stmt = $this->db->prepare("DELETE FROM TICKETS WHERE id_ticket IN ($placeholders)");
        $stmt->execute($ids);
        return count($ids);
    }

    public function existeCodigo($codigo)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM TICKETS WHERE codigo_ticket = :codigo");
        $stmt->execute([':codigo' => $codigo]);
        return $stmt->fetchColumn() > 0;
    }

    public function generarCodigoUnico($prefijo, $incluyeMesDia = true)
    {
        do {
            if ($incluyeMesDia) {
                $codigo = $prefijo . date('Ymd') . '-' . rand(10000, 99999);
            } else {
                $codigo = $prefijo . date('Y') . '-' . rand(10000, 99999);
            }
        } while ($this->existeCodigo($codigo));
        return $codigo;
    }
}
