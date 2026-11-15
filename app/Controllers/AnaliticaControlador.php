<?php
namespace App\Controllers;

use App\Models\Ticket;
use App\Models\Historial;
use App\Core\Helper;
use PDO;

class AnaliticaControlador {
    private $ticketModel;
    private $historialModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            Helper::redireccionar('autenticacion/login');
        }
        // Los encargados/empleados no tienen acceso a analítica corporativa
        if ($_SESSION['user_role'] === 'Empleado') {
            Helper::redireccionar('dashboard');
        }
        $this->ticketModel = new Ticket();
        $this->historialModel = new Historial();
    }

    public function index() {
        $categoryData = $this->ticketModel->obtenerTicketsPorCategoria();
        $technicianData = $this->ticketModel->obtenerTicketsPorTecnico();
        $slaData = $this->ticketModel->obtenerCumplimientoSLA();
        $storeData = $this->ticketModel->obtenerTicketsPorTienda();
        $districtData = $this->ticketModel->obtenerTicketsPorDistrito();
        
        // Obtener historial reciente para el feed
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT TOP 8 h.*, t.codigo_ticket, u.nombre as usuario 
                            FROM HISTORIAL_TICKETS h 
                            JOIN TICKETS t ON h.id_ticket = t.id_ticket 
                            JOIN USUARIOS u ON h.id_usuario = u.id_usuario 
                            ORDER BY h.fecha_accion DESC");
        $recentHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once '../app/Views/dashboard/analytics.php';
    }
}
