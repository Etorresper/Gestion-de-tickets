<?php
namespace App\Controllers;

use App\Models\Evidencia;
use App\Models\Tienda;
use App\Core\Helper;

class EvidenciaControlador
{
    private $evidenciaModel;
    private $tiendaModel;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            Helper::redireccionar('autenticacion/login');
        }
        $this->evidenciaModel = new Evidencia();
        $this->tiendaModel = new Tienda();
    }

    public function index()
    {
        $filtros = [];

        // RESTRICCIÓN: Si es Encargado de Tienda, solo ve evidencias de su propia sede
        if ($_SESSION['user_role'] === 'Empleado') {
            if (!empty($_SESSION['user_tienda_id'])) {
                $filtros['id_tienda'] = $_SESSION['user_tienda_id'];
            }
        } elseif (!empty($_GET['id_tienda'])) {
            $filtros['id_tienda'] = (int)$_GET['id_tienda'];
        }

        if (!empty($_GET['fase']) && in_array($_GET['fase'], ['Antes', 'Durante', 'Después'])) {
            $filtros['fase_evidencia'] = $_GET['fase'];
        }

        if (!empty($_GET['id_ticket'])) {
            $filtros['id_ticket'] = (int)$_GET['id_ticket'];
        }

        $evidencias = $this->evidenciaModel->getAll($filtros);
        $tiendas = $this->tiendaModel->getAll();

        require_once '../app/Views/evidencias/index.php';
    }
}

