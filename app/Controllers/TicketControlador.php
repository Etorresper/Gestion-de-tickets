<?php
namespace App\Controllers;

use App\Models\Ticket;
use App\Models\Categoria;
use App\Models\Historial;
use App\Models\Comentario;
use App\Models\Adjunto;
use App\Models\Tienda;
use App\Models\Usuario;
use App\Models\Evidencia;

use App\Services\ServicioIA;
use App\Services\ServicioML;
use App\Services\UploadService;
use App\Core\Helper;

class TicketControlador
{
    private $ticketModel;
    private $categoriaModel;
    private $historialModel;
    private $comentarioModel;
    private $adjuntoModel;
    private $tiendaModel;
    private $usuarioModel;
    private $evidenciaModel;

    private $servicioIA;
    private $servicioML;
    private $uploadService;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            Helper::redireccionar('autenticacion/login');
        }
        $this->ticketModel = new Ticket();
        $this->categoriaModel = new Categoria();
        $this->historialModel = new Historial();
        $this->comentarioModel = new Comentario();
        $this->adjuntoModel = new Adjunto();
        $this->tiendaModel = new Tienda();
        $this->usuarioModel = new Usuario();
        $this->evidenciaModel = new Evidencia();

        $this->servicioIA = new ServicioIA();
        $this->servicioML = new ServicioML();
        $this->uploadService = new UploadService();
    }

    public function index()
    {
        $filtros = [];
        // Si es Soporte, solo ve los tickets asignados a él
        if ($_SESSION['user_role'] == 'Soporte') {
            $filtros['id_tecnico'] = $_SESSION['user_id'];
        }
        // Si es Encargado/Empleado, ve únicamente los tickets de su propia tienda
        elseif ($_SESSION['user_role'] == 'Empleado') {
            if (!empty($_SESSION['user_tienda_id'])) {
                $filtros['id_tienda'] = $_SESSION['user_tienda_id'];
            } else {
                $filtros['id_solicitante'] = $_SESSION['user_id'];
            }
        }
        // El Administrador (rol 1) no tiene filtros aquí, ve todo

        // Filtro por estado desde el Dashboard
        if (isset($_GET['status']) && is_numeric($_GET['status'])) {
            $filtros['id_estado'] = $_GET['status'];
        }

        if (!empty($_GET['q'])) {
            $filtros['q'] = Helper::limpiar($_GET['q']);
        }

        $tickets = $this->ticketModel->getAll($filtros);
        require_once '../app/Views/tickets/index.php';
    }

    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // RESTRICCIÓN DE TIENDA: Si es Encargado/Empleado, forzar su tienda asignada (no puede elegir otra)
            if ($_SESSION['user_role'] === 'Empleado') {
                $id_tienda = !empty($_SESSION['user_tienda_id']) ? $_SESSION['user_tienda_id'] : null;
            } else {
                $id_tienda = !empty($_POST['id_tienda']) ? (int)$_POST['id_tienda'] : null;
            }

            // RESTRICCIÓN DE PRIORIDADES:
            // Encargado solo puede elegir Baja, Media o Alta.
            // Si elige Crítica, es obligatorio registrar la justificación en 'motivo_urgencia'
            $prioridad = $_POST['prioridad'] ?? 'Media';
            $motivoUrgencia = Helper::limpiar($_POST['motivo_urgencia'] ?? '');

            if ($_SESSION['user_role'] === 'Empleado' && $prioridad === 'Crítica' && empty($motivoUrgencia)) {
                $prioridad = 'Alta'; // Reclasifica a Alta si no fundamenta la urgencia crítica
            }

            // Verificación de duplicidad
            $duplicado = $this->ticketModel->buscarDuplicado($_SESSION['user_id'], Helper::limpiar($_POST['asunto']));
            if ($duplicado) {
                $_SESSION['alerta'] = ['tipo' => 'warning', 'mensaje' => 'Ya existe un ticket similar abierto recientemente. Te hemos redirigido al ticket existente.'];
                Helper::redireccionar('ticket/detalle/' . $duplicado['id_ticket']);
                exit;
            }

            $categoriaSeleccionada = $_POST['id_categoria'];
            $fueAutoclasificado = false;
            $categoriaPredicha = null;

            if ($categoriaSeleccionada === 'auto') {
                $resultadoML = $this->servicioML->clasificarTexto(Helper::limpiar($_POST['asunto']) . ' ' . Helper::limpiar($_POST['descripcion']));
                $categoriaPredicha = $resultadoML['categoria'];
                $categoriaSeleccionada = $this->servicioML->obtenerIdCategoriaPorNombre($categoriaPredicha);
                $fueAutoclasificado = true;
            }

            $desdeCuandoFalla = !empty($_POST['desde_cuando_falla']) ? Helper::limpiar($_POST['desde_cuando_falla']) : null;
            $tipoAtencion = !empty($_POST['tipo_atencion']) ? Helper::limpiar($_POST['tipo_atencion']) : 'Presencial';

            $vencimiento = $this->servicioIA->calcularSLA($prioridad, $id_tienda, $tipoAtencion);

            $data = [
                'codigo_ticket'       => $this->ticketModel->generarCodigoUnico('TKT-', false),
                'id_solicitante'      => $_SESSION['user_id'],
                'asunto'              => Helper::limpiar($_POST['asunto']),
                'descripcion'         => Helper::limpiar($_POST['descripcion']),
                'id_categoria'        => $categoriaSeleccionada,
                'id_estado'           => 1, // Pendiente
                'prioridad'           => $prioridad,
                'prioridad_original'  => $prioridad,
                'fecha_vencimiento'   => $vencimiento,
                'id_tienda'           => $id_tienda,
                'escalar_no_resuelto' => isset($_POST['escalar_no_resuelto']) ? 1 : 0,
                'tipo_atencion'       => $tipoAtencion,
                'desde_cuando_falla'  => $desdeCuandoFalla
            ];

            $ticketId = $this->ticketModel->create($data);

            // Asignación automática por Prioridad
            $tecnico = $this->usuarioModel->getTecnicoPorPrioridad($prioridad);

            if ($tecnico) {
                $sql_asignacion = "INSERT INTO ASIGNACIONES (id_ticket, id_tecnico) VALUES (:id_ticket, :id_tecnico)";
                $stmt_asig = \App\Core\Database::getInstance()->getConnection()->prepare($sql_asignacion);
                $stmt_asig->execute([
                    ':id_ticket' => $ticketId,
                    ':id_tecnico' => $tecnico['id_usuario']
                ]);
                $this->historialModel->add($ticketId, $_SESSION['user_id'], 'Asignación Automática', 'Ticket asignado automáticamente a ' . $tecnico['nombre'] . ' según la prioridad ' . $prioridad . '.');
            } else {
                $this->historialModel->add($ticketId, $_SESSION['user_id'], 'Sin Asignar', 'No se encontró ningún técnico de Soporte disponible. Requiere asignación manual.');
            }

            // Registrar justificación de urgencia si fue reportada como Crítica
            if (!empty($motivoUrgencia)) {
                $this->historialModel->add($ticketId, $_SESSION['user_id'], 'Justificación de Urgencia', 'El solicitante fundamentó la prioridad Crítica: ' . $motivoUrgencia);
            }

            if ($fueAutoclasificado) {
                $this->historialModel->add($ticketId, $_SESSION['user_id'], 'Clasificación ML', 'Ticket clasificado automáticamente por Machine Learning en la categoría: ' . $categoriaPredicha);
            }
            $this->historialModel->add($ticketId, $_SESSION['user_id'], 'Creación', 'Ticket registrado en el sistema.');

            // Manejo de evidencias fotográficas iniciales (Antes de la atención) y adjuntos
            $archivoEvidencia = !empty($_FILES['evidencia_antes']['name']) ? $_FILES['evidencia_antes'] : (!empty($_FILES['adjunto']['name']) ? $_FILES['adjunto'] : null);
            if ($archivoEvidencia) {
                $uploadResult = $this->uploadService->upload($archivoEvidencia);
                if ($uploadResult['status'] === 'success') {
                    // 1. Guardar en tabla oficial de evidencias fotográficas
                    $descFalla = 'Evidencia fotográfica del problema (Antes de la atención).';
                    if (!empty($desdeCuandoFalla)) {
                        $descFalla .= ' Falla presente desde: ' . $desdeCuandoFalla . '.';
                    }
                    $this->evidenciaModel->add(
                        $ticketId,
                        $_SESSION['user_id'],
                        'Antes',
                        $uploadResult['name'],
                        $uploadResult['path'],
                        $uploadResult['mime'],
                        $descFalla
                    );

                    // 2. Guardar en adjuntos para compatibilidad
                    $this->adjuntoModel->create(
                        $ticketId,
                        $uploadResult['name'],
                        $uploadResult['path'],
                        $uploadResult['mime']
                    );
                    $this->historialModel->add($ticketId, $_SESSION['user_id'], 'Evidencia Inicial (Antes)', 'Se adjuntó fotografía del estado inicial de la incidencia: ' . $uploadResult['name']);
                }
            }

            Helper::redireccionar('ticket/index');
        } else {
            $categorias = $this->categoriaModel->getAll();
            $tiendas = $this->tiendaModel->getAll();

            require_once '../app/Views/tickets/form.php';
        }
    }

    public function detalle($id)
    {
        $ticket = $this->ticketModel->getById($id);
        if (!$ticket) {
            Helper::redireccionar('ticket/index');
        }

        // VALIDACIÓN DE SEGURIDAD BACKEND PARA ENCARGADOS:
        // No pueden visualizar tickets pertenecientes a otras tiendas
        if ($_SESSION['user_role'] === 'Empleado') {
            if (!empty($_SESSION['user_tienda_id']) && !empty($ticket['id_tienda']) && $ticket['id_tienda'] != $_SESSION['user_tienda_id']) {
                $_SESSION['alerta'] = [
                    'tipo' => 'danger',
                    'mensaje' => 'Acceso denegado: Solo tienes permisos para consultar las incidencias de tu propia tienda.'
                ];
                Helper::redireccionar('ticket/index');
                return;
            }
        }

        $historial = $this->historialModel->getByTicketId($id);
        $comentarios = $this->comentarioModel->getByTicketId($id);
        $adjuntos = $this->adjuntoModel->getByTicketId($id);

        // Evidencias fotográficas (Antes, Durante, Después)
        $evidencias = $this->evidenciaModel->getByTicketId($id);
        $totales_evidencias = $this->evidenciaModel->contarPorFase($id);

        // Cargar técnicos de Soporte para el panel de asignación (solo admin)
        $tecnicos_soporte = $this->usuarioModel->getTecnicosGlobales();

        // Cargar asignación actual
        $asignacion_actual = $this->ticketModel->getAsignacionActual($id);

        require_once '../app/Views/tickets/detail.php';
    }

    public function actualizarEquipo($id)
    {
        if ($_SESSION['user_role'] === 'Empleado') {
            $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Solo el personal técnico o administrador puede registrar o modificar la ficha del equipo.'];
            Helper::redireccionar('ticket/detalle/' . $id);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = Helper::limpiar($_POST['equipo_nombre'] ?? '');
            $modelo = Helper::limpiar($_POST['equipo_modelo'] ?? '');
            $serie  = Helper::limpiar($_POST['equipo_serie'] ?? '');

            $this->ticketModel->guardarDatosEquipoTecnico($id, $nombre, $modelo, $serie);
            $this->historialModel->add($id, $_SESSION['user_id'], 'Ficha de Equipo Actualizada', "El técnico registró el activo intervenido: $nombre - $modelo (Serie: " . ($serie ?: 'S/N') . ").");

            $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => 'Ficha técnica del equipo guardada con éxito.'];
        }

        Helper::redireccionar('ticket/detalle/' . $id);
    }

    public function reprogramarSLA($id)
    {
        if ($_SESSION['user_role'] === 'Empleado') {
            $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Solo el personal técnico o administrador puede reprogramar la fecha compromiso de visita.'];
            Helper::redireccionar('ticket/detalle/' . $id);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nuevaFecha = Helper::limpiar($_POST['nueva_fecha_vencimiento'] ?? '');
            $motivo     = Helper::limpiar($_POST['motivo_reprogramacion'] ?? '');

            if (empty($nuevaFecha)) {
                $_SESSION['alerta'] = ['tipo' => 'warning', 'mensaje' => 'Debes indicar una fecha y hora válida.'];
                Helper::redireccionar('ticket/detalle/' . $id);
                return;
            }

            $this->ticketModel->reprogramarFechaSLA($id, $nuevaFecha);
            $this->historialModel->add(
                $id, 
                $_SESSION['user_id'], 
                'Fecha de Visita Reprogramada', 
                "Nueva fecha compromiso: " . date('d/m/Y H:i', strtotime($nuevaFecha)) . ". Motivo de ruta: " . ($motivo ?: 'Ajuste de itinerario por distancia.')
            );

            $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => 'Fecha compromiso de visita actualizada correctamente.'];
        }

        Helper::redireccionar('ticket/detalle/' . $id);
    }

    public function cambiarPrioridad($id)
    {
        // Solo Administrador o Soporte pueden modificar la prioridad o gestionar escalamientos
        if ($_SESSION['user_role'] === 'Empleado') {
            $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'No tienes permisos para modificar la prioridad de la incidencia.'];
            Helper::redireccionar('ticket/detalle/' . $id);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nuevaPrioridad = Helper::limpiar($_POST['nueva_prioridad'] ?? '');
            $motivo = Helper::limpiar($_POST['motivo_cambio'] ?? '');

            if (empty($nuevaPrioridad) || empty($motivo)) {
                $_SESSION['alerta'] = ['tipo' => 'warning', 'mensaje' => 'Debes indicar la nueva prioridad y el motivo justificado del cambio.'];
                Helper::redireccionar('ticket/detalle/' . $id);
                return;
            }

            $ticket = $this->ticketModel->getById($id);
            if (!$ticket) {
                Helper::redireccionar('ticket/index');
                return;
            }

            $prioAnterior = $ticket['prioridad'];
            $this->ticketModel->cambiarPrioridad($id, $nuevaPrioridad, $_SESSION['user_id'], $motivo);

            // Registrar en historial de auditoría
            $accion = "Cambio de Prioridad: $prioAnterior ➔ $nuevaPrioridad";
            $detalleAuditoria = "Modificado por {$_SESSION['user_name']} ({$_SESSION['user_role']}). Motivo justificado: $motivo";
            $this->historialModel->add($id, $_SESSION['user_id'], $accion, $detalleAuditoria);

            $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => "Prioridad actualizada correctamente a $nuevaPrioridad."];
        }

        Helper::redireccionar('ticket/detalle/' . $id);
    }

    public function agregarComentario($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['comentario'])) {
            $comentario = Helper::limpiar($_POST['comentario']);
            $this->comentarioModel->add($id, $_SESSION['user_id'], $comentario);

            // Registrar en historial para auditoría y visualización en la línea de tiempo
            $this->historialModel->add($id, $_SESSION['user_id'], 'Comentario', $comentario);
        }
        Helper::redireccionar('ticket/detalle/' . $id);
    }

    public function actualizar($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_estado_nuevo = $_POST['id_estado'];
            $comentario = Helper::limpiar($_POST['comentario']);

            // Validar que el ticket realmente exista antes de proceder
            $t = $this->ticketModel->getById($id);
            if (!$t) {
                $_SESSION['alerta'] = [
                    'tipo' => 'danger',
                    'mensaje' => 'No se pudo actualizar el ticket porque este ya no existe en el sistema (puede haber sido eliminado como duplicado).'
                ];
                Helper::redireccionar('ticket/index');
                exit;
            }

            // Check if ticket is being escalated (ID 4: Escalado)
            if ($id_estado_nuevo == 4) {

                $tecnico_id = 3;
                $tecnico_nombre = 'Jofre Melgarejo';

                if (($t['prioridad'] ?? 'Media') === 'Baja') {
                    $tecnico_id = 2;
                    $tecnico_nombre = 'Edwin Torres';
                } elseif (($t['prioridad'] ?? 'Media') === 'Crítica') {
                    $tecnico_id = 4;
                    $tecnico_nombre = 'Henri Gonzales';
                }

                // Perform reassignment
                $db_conn = \App\Core\Database::getInstance()->getConnection();
                $sql_asig = "INSERT INTO ASIGNACIONES (id_ticket, id_tecnico) VALUES (:id_ticket, :id_tecnico)";
                $stmt_asig = $db_conn->prepare($sql_asig);
                $stmt_asig->execute([
                    ':id_ticket' => $id,
                    ':id_tecnico' => $tecnico_id
                ]);

                if (empty($comentario)) {
                    $comentario = 'El ticket no se pudo resolver y fue escalado a ' . $tecnico_nombre;
                } else {
                    $comentario = 'Escalado a ' . $tecnico_nombre . '. Nota: ' . $comentario;
                }
            }

            // Actualizar estado
            $this->ticketModel->updateStatus($id, $id_estado_nuevo);

            // Diccionario oficial unificado de 4 estados
            $estados = [
                1 => 'Pendiente',
                2 => 'En Proceso',
                3 => 'Cerrado',
                4 => 'Escalado'
            ];
            $nombre_estado = $estados[$id_estado_nuevo] ?? 'Actualizado';

            // Guardar historial
            $accion = "Cambio de Estado a " . $nombre_estado;
            $this->historialModel->add($id, $_SESSION['user_id'], $accion, $comentario);

            Helper::redireccionar('ticket/detalle/' . $id);
        }
    }


    public function asignar($id)
    {
        // Solo el Administrador puede asignar manualmente
        if ($_SESSION['user_role'] !== 'Administrador') {
            $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'No tienes permisos para realizar asignaciones manuales.'];
            Helper::redireccionar('ticket/detalle/' . $id);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_tecnico = (int) ($_POST['id_tecnico'] ?? 0);

            if ($id_tecnico <= 0) {
                $_SESSION['alerta'] = ['tipo' => 'warning', 'mensaje' => 'Debes seleccionar un técnico válido.'];
                Helper::redireccionar('ticket/detalle/' . $id);
                return;
            }

            // Verificar que el ticket existe
            $ticket = $this->ticketModel->getById($id);
            if (!$ticket) {
                Helper::redireccionar('ticket/index');
                return;
            }

            // Verificar que el técnico existe y es de Soporte (no admin)
            $tecnico = $this->usuarioModel->getUsuarioById($id_tecnico);
            if (!$tecnico || $tecnico['id_rol'] == 1) {
                $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Técnico no válido para asignación.'];
                Helper::redireccionar('ticket/detalle/' . $id);
                return;
            }

            // Insertar la nueva asignación
            $db_conn = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db_conn->prepare(
                "INSERT INTO ASIGNACIONES (id_ticket, id_tecnico) VALUES (:id_ticket, :id_tecnico)"
            );
            $stmt->execute([':id_ticket' => $id, ':id_tecnico' => $id_tecnico]);

            // Registrar en historial
            $nota = Helper::limpiar($_POST['nota'] ?? '');
            $detalle = 'Ticket reasignado manualmente por el Administrador a ' . $tecnico['nombre'] . '.';
            if (!empty($nota)) {
                $detalle .= ' Nota: ' . $nota;
            }
            $this->historialModel->add($id, $_SESSION['user_id'], 'Reasignación Manual', $detalle);

            $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => 'Ticket asignado correctamente a ' . $tecnico['nombre'] . '.'];
        }

        Helper::redireccionar('ticket/detalle/' . $id);
    }

    public function subirEvidencia($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ticket = $this->ticketModel->getById($id);
            if (!$ticket) {
                Helper::redireccionar('ticket/index');
                return;
            }

            $fase = Helper::limpiar($_POST['fase_evidencia'] ?? 'Durante');
            if (!in_array($fase, ['Antes', 'Durante', 'Después'])) {
                $fase = 'Durante';
            }

            $descripcion = Helper::limpiar($_POST['descripcion_evidencia'] ?? '');
            $estado_equipo = !empty($_POST['estado_equipo']) ? Helper::limpiar($_POST['estado_equipo']) : null;

            if (empty($_FILES['foto_evidencia']['name'])) {
                $_SESSION['alerta'] = ['tipo' => 'warning', 'mensaje' => 'Debes seleccionar una fotografía de la evidencia.'];
                Helper::redireccionar('ticket/detalle/' . $id);
                return;
            }

            $uploadResult = $this->uploadService->upload($_FILES['foto_evidencia']);
            if ($uploadResult['status'] !== 'success') {
                $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => $uploadResult['message']];
                Helper::redireccionar('ticket/detalle/' . $id);
                return;
            }

            // Actualizar datos de equipo si el técnico los proporcionó
            $eqNombre = !empty($_POST['equipo_nombre']) ? Helper::limpiar($_POST['equipo_nombre']) : null;
            $eqModelo = !empty($_POST['equipo_modelo']) ? Helper::limpiar($_POST['equipo_modelo']) : null;
            $eqSerie  = !empty($_POST['equipo_serie'])  ? Helper::limpiar($_POST['equipo_serie'])  : null;
            if ($eqNombre || $eqModelo || $eqSerie) {
                $this->ticketModel->guardarDatosEquipoTecnico($id, $eqNombre, $eqModelo, $eqSerie);
            }

            // Registrar en EVIDENCIAS_TICKET
            $this->evidenciaModel->add(
                $id,
                $_SESSION['user_id'],
                $fase,
                $uploadResult['name'],
                $uploadResult['path'],
                $uploadResult['mime'],
                $descripcion,
                $estado_equipo
            );

            // Registrar también en ADJUNTOS
            $this->adjuntoModel->create(
                $id,
                $uploadResult['name'],
                $uploadResult['path'],
                $uploadResult['mime']
            );

            // Si es fase 'Después'
            if ($fase === 'Después') {
                if (isset($_POST['marcar_resuelto']) && $_POST['marcar_resuelto'] == '1') {
                    $this->ticketModel->finalizarAtencion($id, $descripcion, $estado_equipo);
                    $this->historialModel->add($id, $_SESSION['user_id'], 'Incidencia Cerrada', 'Atención técnica finalizada y solución documentada. Estado equipo: ' . ($estado_equipo ?? 'Operativo') . '. Ticket cerrado formalmente.');
                } else {
                    $this->historialModel->add($id, $_SESSION['user_id'], 'Evidencia de Solución (Después)', $descripcion);
                }
            } else {
                $this->historialModel->add($id, $_SESSION['user_id'], 'Evidencia ' . $fase, $descripcion);
            }

            $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => "Evidencia fotográfica ($fase de la atención) guardada exitosamente."];
        }
        Helper::redireccionar('ticket/detalle/' . $id);
    }



    public function darConformidad($id)
    {
        $ticket = $this->ticketModel->getById($id);
        if (!$ticket) {
            Helper::redireccionar('ticket/index');
            return;
        }

        // Restricción: Encargado solo de su tienda (o Admin)
        if ($_SESSION['user_role'] === 'Empleado' && !empty($_SESSION['user_tienda_id']) && $ticket['id_tienda'] != $_SESSION['user_tienda_id']) {
            $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Solo el encargado de la tienda asignada puede otorgar conformidad.'];
            Helper::redireccionar('ticket/index');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $comentario = Helper::limpiar($_POST['comentario_conformidad'] ?? 'El equipo quedó operativo y funcionando con normalidad.');
            $this->ticketModel->darConformidad($id, $comentario);
            $this->historialModel->add($id, $_SESSION['user_id'], 'Conformidad de Solución (Encargado)', 'El Encargado verificó el equipo y otorgó su CONFORMIDAD. Ticket cerrado formalmente. Nota: ' . $comentario);

            $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => '¡Conformidad registrada con éxito! El ticket ha sido cerrado formalmente.'];
        }

        Helper::redireccionar('ticket/detalle/' . $id);
    }

    public function reporte($id)
    {
        $ticket = $this->ticketModel->getById($id);
        if (!$ticket) {
            Helper::redireccionar('ticket/index');
            return;
        }

        // Validación de permisos por tienda
        if ($_SESSION['user_role'] === 'Empleado') {
            if (!empty($_SESSION['user_tienda_id']) && !empty($ticket['id_tienda']) && $ticket['id_tienda'] != $_SESSION['user_tienda_id']) {
                Helper::redireccionar('ticket/index');
                return;
            }
        }

        $evidencias = $this->evidenciaModel->getByTicketId($id);

        $asignacion_actual = $this->ticketModel->getAsignacionActual($id);
        $historial = $this->historialModel->getByTicketId($id);

        require_once '../app/Views/tickets/reporte.php';
    }

    public function limpiarDuplicados()
    {
        if ($_SESSION['user_role'] !== 'Administrador' && $_SESSION['user_role'] !== 'Soporte') {
            Helper::redireccionar('dashboard');
        }

        $cantidad = $this->ticketModel->eliminarDuplicadosExistentes();
        Helper::redireccionar('ticket/index');
    }
}
