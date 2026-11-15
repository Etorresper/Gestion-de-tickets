<?php
namespace App\Controllers;

use App\Models\Ticket;
use App\Core\Helper;
use App\Core\Database;
use PDO;

/**
 * DashboardControlador — Adaptado para SQL Server 2019.
 * Cambios: DATE_SUB/CURDATE/INTERVAL → DATEADD/CAST(GETDATE() AS DATE)
 */
class DashboardControlador {
    private $ticketModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            Helper::redireccionar('autenticacion/login');
        }
        $this->ticketModel = new Ticket();
    }

    public function index() {
        $filtros = [];
        
        if ($_SESSION['user_role'] == 'Soporte') {
            $filtros['id_tecnico'] = $_SESSION['user_id'];
        } elseif ($_SESSION['user_role'] == 'Empleado') {
            if (!empty($_SESSION['user_tienda_id'])) {
                $filtros['id_tienda'] = $_SESSION['user_tienda_id'];
            } else {
                $filtros['id_solicitante'] = $_SESSION['user_id'];
            }
        }
        
        $stats = $this->ticketModel->obtenerEstadisticas($filtros);

        $pendientes    = $stats['pendientes'] ?? 0;
        $enProceso     = $stats['en_proceso'] ?? 0;
        $resueltos     = $stats['resueltos'] ?? 0;
        $totalTickets  = $stats['total'] ?? 0;
        
        $assignedCount = 0;
        if ($_SESSION['user_role'] === 'Soporte' || $_SESSION['user_role'] === 'Administrador') {
            $assignedCount = $this->ticketModel->obtenerAsignados($_SESSION['user_id']);
        }

        $misTicketsRecientes = [];
        if ($_SESSION['user_role'] === 'Empleado') {
            $filtroTienda = !empty($_SESSION['user_tienda_id']) ? ['id_tienda' => $_SESSION['user_tienda_id']] : ['id_solicitante' => $_SESSION['user_id']];
            $misTicketsRecientes = $this->ticketModel->getAll($filtroTienda);
            $misTicketsRecientes = array_slice($misTicketsRecientes, 0, 8);
        }

        $categoryData = $this->ticketModel->obtenerTicketsPorCategoria();
        $slaData      = $this->ticketModel->obtenerCumplimientoSLA($filtros);

        $escaladosFiltros = $filtros;
        $ticketsEscalados = $this->ticketModel->obtenerEscalados($escaladosFiltros);
        $totalEscalados   = $this->ticketModel->contarEscalados($escaladosFiltros);

        $db = Database::getInstance()->getConnection();

        // Actividad reciente
        $stmt = $db->query("SELECT TOP 5 h.*, t.codigo_ticket, u.nombre as usuario 
                            FROM HISTORIAL_TICKETS h 
                            JOIN TICKETS t ON h.id_ticket = t.id_ticket 
                            JOIN USUARIOS u ON h.id_usuario = u.id_usuario 
                            ORDER BY h.fecha_accion DESC");
        $recentHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Etiquetas de días en español
        $diasSemana = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];

        // Tickets creados por día (últimos 7 días) — SQL Server
        $stmtNew = $db->query("SELECT CAST(fecha_creacion AS DATE) as dia, COUNT(*) as total 
                                FROM TICKETS 
                                WHERE fecha_creacion >= DATEADD(day, -6, CAST(GETDATE() AS DATE))
                                GROUP BY CAST(fecha_creacion AS DATE)
                                ORDER BY dia ASC");
        $newRaw = $stmtNew->fetchAll(PDO::FETCH_ASSOC);

        // Tickets resueltos/cerrados por día (últimos 7 días) — fecha_cierre oficial (Estado 3: Cerrado)
        $stmtRes = $db->query("SELECT CAST(COALESCE(fecha_cierre, fecha_actualizacion) AS DATE) as dia, COUNT(*) as total 
                                FROM TICKETS 
                                WHERE id_estado = 3 
                                AND COALESCE(fecha_cierre, fecha_actualizacion) >= DATEADD(day, -6, CAST(GETDATE() AS DATE))
                                GROUP BY CAST(COALESCE(fecha_cierre, fecha_actualizacion) AS DATE)
                                ORDER BY dia ASC");
        $resRaw = $stmtRes->fetchAll(PDO::FETCH_ASSOC);

        $weeklyLabels   = [];
        $weeklyNew      = [];
        $weeklyResolved = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime("-$i days"));
            $dow = (int)date('w', strtotime($day));
            $weeklyLabels[] = $diasSemana[$dow] . ' ' . date('d', strtotime($day));

            $countNew = 0;
            foreach ($newRaw as $r) {
                // SQL Server puede devolver la fecha como objeto DateTime; normalizamos
                $diaStr = (is_object($r['dia'])) ? $r['dia']->format('Y-m-d') : (string)$r['dia'];
                if ($diaStr === $day) { $countNew = (int)$r['total']; break; }
            }
            $weeklyNew[] = $countNew;

            $countRes = 0;
            foreach ($resRaw as $r) {
                $diaStr = (is_object($r['dia'])) ? $r['dia']->format('Y-m-d') : (string)$r['dia'];
                if ($diaStr === $day) { $countRes = (int)$r['total']; break; }
            }
            $weeklyResolved[] = $countRes;
        }

        $chartData = [
            'labels'   => $weeklyLabels,
            'new'      => $weeklyNew,
            'resolved' => $weeklyResolved
        ];

        require_once '../app/Views/dashboard/index.php';
    }
}
