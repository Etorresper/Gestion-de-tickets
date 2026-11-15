<?php require_once '../app/Views/layout/header.php'; ?>

<?php if ($_SESSION['user_role'] === 'Empleado'): ?>
<!-- ========================================================================= -->
<!-- VISTA ESPECÍFICA PARA ENCARGADO DE TIENDA (EMPLEADO)                      -->
<!-- ========================================================================= -->
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-shop me-2 text-primary"></i>Panel de Tienda: <?= htmlspecialchars($_SESSION['user_name']) ?>
        </h3>
        <p class="text-muted mb-0">Consulta el estado en tiempo real de los requerimientos técnicos de tu sede.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="<?= APP_URL ?>/ticket/crear" class="btn btn-primary btn-lg shadow-sm">
            <i class="bi bi-plus-circle-fill me-2"></i>Reportar Nueva Incidencia
        </a>
    </div>
</div>

<!-- Tarjetas de Estado para Encargado -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="<?= APP_URL ?>/ticket/index" class="text-decoration-none">
            <div class="card shadow-sm border-0 border-start border-primary border-4 h-100 rounded-3 card-hover" style="transition: transform 0.2s;">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total de Incidencias</div>
                            <div class="h2 mb-0 fw-bold text-dark"><?= $totalTickets ?? 0 ?></div>
                            <small class="text-muted">Requerimientos registrados en mi sede</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-ticket-perforated-fill fs-1 text-primary opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <a href="<?= APP_URL ?>/ticket?status=1" class="text-decoration-none">
            <div class="card shadow-sm border-0 border-start border-danger border-4 h-100 rounded-3 card-hover" style="transition: transform 0.2s;">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">En Espera / Pendientes</div>
                            <div class="h2 mb-0 fw-bold text-dark"><?= $pendientes ?? 0 ?></div>
                            <small class="text-muted">Esperando asignación o revisión inicial</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-hourglass-split fs-1 text-danger opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <a href="<?= APP_URL ?>/ticket?status=2" class="text-decoration-none">
            <div class="card shadow-sm border-0 border-start border-warning border-4 h-100 rounded-3 card-hover" style="transition: transform 0.2s;">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">En Atención / En Proceso</div>
                            <div class="h2 mb-0 fw-bold text-dark"><?= $enProceso ?? 0 ?></div>
                            <small class="text-muted">Un técnico especialista está trabajando</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-tools fs-1 text-warning opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <a href="<?= APP_URL ?>/ticket?status=3" class="text-decoration-none">
            <div class="card shadow-sm border-0 border-start border-success border-4 h-100 rounded-3 card-hover" style="transition: transform 0.2s;">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Tickets Cerrados</div>
                            <div class="h2 mb-0 fw-bold text-dark"><?= $resueltos ?? 0 ?></div>
                            <small class="text-muted">Incidencias solucionadas y finalizadas</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle-fill fs-1 text-success opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Tabla de Mis Incidencias Recientes -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-ticket-detailed me-2 text-primary"></i>Incidencias de mi Sede
        </h5>
        <a href="<?= APP_URL ?>/ticket/index" class="btn btn-sm btn-outline-primary">Ver todos mis tickets</a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($misTicketsRecientes)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-emoji-smile fs-1 d-block mb-2 text-success"></i>
                <h5 class="fw-bold text-dark">¡Tu tienda no tiene incidencias pendientes!</h5>
                <p class="text-muted mb-3">Si ocurre alguna falla con los puntos de venta (POS), impresoras o red, repórtala aquí.</p>
                <a href="<?= APP_URL ?>/ticket/crear" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Reportar una Falla
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Código</th>
                            <th>Asunto</th>
                            <th>Categoría</th>
                            <th>Prioridad</th>
                            <th>Técnico Asignado</th>
                            <th>Tiempo Solución (SLA)</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($misTicketsRecientes as $t): ?>
                        <tr>
                            <td class="ps-4 fw-bold">
                                <a href="<?= APP_URL ?>/ticket/detalle/<?= $t['id_ticket'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($t['codigo_ticket']) ?>
                                </a>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($t['asunto']) ?></div>
                                <small class="text-muted">Creado: <?= date('d/m/Y H:i', strtotime($t['fecha_creacion'])) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?= htmlspecialchars($t['nombre_categoria'] ?? 'General') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= strtolower($t['prioridad']) === 'alta' || strtolower($t['prioridad']) === 'crítica' ? 'danger' : (strtolower($t['prioridad']) === 'media' ? 'warning' : 'secondary') ?>">
                                    <?= htmlspecialchars($t['prioridad'] ?? 'Media') ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($t['tecnico_asignado'])): ?>
                                    <span class="text-success fw-semibold">
                                        <i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($t['tecnico_asignado']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted small"><i class="bi bi-hourglass me-1"></i>Por asignar</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($t['id_estado'] == 3): ?>
                                    <?php 
                                        $fCierre = !empty($t['fecha_cierre']) ? $t['fecha_cierre'] : $t['fecha_actualizacion'];
                                        $fVenc = !empty($t['fecha_vencimiento']) ? $t['fecha_vencimiento'] : date('Y-m-d H:i:s', strtotime($t['fecha_creacion'] . ' +48 hours'));
                                        $okSla = (strtotime($fCierre) <= strtotime($fVenc));
                                    ?>
                                    <?php if ($okSla): ?>
                                        <span class="badge bg-success-subtle text-success border border-success" title="Cerrado el <?= date('d/m/Y H:i', strtotime($fCierre)) ?>"><i class="bi bi-check2-circle me-1"></i>A tiempo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger" title="Cerrado el <?= date('d/m/Y H:i', strtotime($fCierre)) ?>"><i class="bi bi-clock-history me-1"></i>Fuera de SLA</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <small class="text-muted <?= (strtotime($t['fecha_vencimiento'] ?? '') < time()) ? 'text-danger fw-bold' : '' ?>">
                                        <i class="bi bi-clock me-1"></i><?= $t['fecha_vencimiento'] ? date('d/m/Y H:i', strtotime($t['fecha_vencimiento'])) : 'SLA estándar' ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2" style="background-color: <?= $t['color_hex'] ?? '#6c757d' ?>;">
                                    <?= htmlspecialchars($t['nombre_estado']) ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="<?= APP_URL ?>/ticket/detalle/<?= $t['id_ticket'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>Ver Detalle
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Guía de Atención para el Encargado -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="p-3 bg-light rounded-3 h-100 border">
            <h6 class="fw-bold text-primary"><i class="bi bi-1-circle-fill me-2"></i>1. Reportas la Incidencia</h6>
            <p class="small text-muted mb-0">Describe qué equipo o sistema está fallando. Puedes adjuntar fotos del error.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="p-3 bg-light rounded-3 h-100 border">
            <h6 class="fw-bold text-primary"><i class="bi bi-2-circle-fill me-2"></i>2. La IA Asigna al Especialista</h6>
            <p class="small text-muted mb-0">El sistema clasifica la urgencia y asigna automáticamente al técnico de hardware, sistemas o redes.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="p-3 bg-light rounded-3 h-100 border">
            <h6 class="fw-bold text-primary"><i class="bi bi-3-circle-fill me-2"></i>3. Seguimiento en Tiempo Real</h6>
            <p class="small text-muted mb-0">Revisa las notas del técnico, comunícate por los comentarios y valida la solución.</p>
        </div>
    </div>
</div>

<style>
.card-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
</style>

<?php else: ?>
<!-- ========================================================================= -->
<!-- VISTA PARA ADMINISTRADOR Y TÉCNICOS DE SOPORTE                            -->
<!-- ========================================================================= -->

<?php if (isset($assignedCount) && ($_SESSION['user_role'] === 'Soporte' || $_SESSION['user_role'] === 'Administrador')): ?>
<div class="alert <?= $assignedCount > 0 ? 'alert-warning' : 'alert-success' ?> shadow-sm border-0 d-flex align-items-center mb-4">
    <i class="bi <?= $assignedCount > 0 ? 'bi-bell-fill text-warning' : 'bi-check-circle-fill text-success' ?> fs-4 me-3"></i>
    <div>
        <h5 class="mb-0 fw-bold">
            <?= $assignedCount > 0 
                ? "¡Hola " . explode(' ', $_SESSION['user_name'])[0] . ", tienes $assignedCount tickets asignados pendientes de atención!" 
                : "¡Al día! No tienes tickets pendientes asignados a ti por el momento." ?>
        </h5>
        <?php if($assignedCount > 0): ?>
        <p class="mb-0 small text-dark mt-1">La Inteligencia Artificial ha derivado estos requerimientos a tu bandeja según tu área de especialidad. Por favor, revisa la pestaña de Mis Tickets.</p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Alerta de tickets escalados -->
<?php if (isset($totalEscalados) && $totalEscalados > 0): ?>
<?php 
    $urlEscaladoDirecto = (count($ticketsEscalados ?? []) === 1) 
        ? APP_URL . '/ticket/detalle/' . $ticketsEscalados[0]['id_ticket'] 
        : APP_URL . '/ticket?status=4';
?>
<div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-4 escalado-alert">
    <i class="bi bi-arrow-up-circle-fill fs-3 me-3 text-danger"></i>
    <div class="flex-grow-1">
        <h5 class="mb-0 fw-bold">⚠️ <?= $totalEscalados ?> ticket<?= $totalEscalados > 1 ? 's' : '' ?> escalado<?= $totalEscalados > 1 ? 's' : '' ?> requiere<?= $totalEscalados > 1 ? 'n' : '  ' ?> atención urgente</h5>
        <p class="mb-0 small mt-1">Estos tickets fueron escalados por el técnico o por vencimiento de SLA.</p>
    </div>
    <a href="<?= $urlEscaladoDirecto ?>" class="btn btn-danger btn-sm ms-3 fw-bold shadow-sm">
        <i class="bi bi-box-arrow-up-right me-1"></i><?= (count($ticketsEscalados ?? []) === 1) ? 'Abrir Ticket Escalado' : 'Ver Tickets Escalados' ?>
    </a>
</div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <!-- Total Tickets -->
    <div class="col-12 col-sm-6 col-md-4 col-xl">
        <a href="<?= APP_URL ?>/ticket/index" class="text-decoration-none">
            <div class="card shadow-sm border-0 border-start border-primary border-4 h-100 rounded-3 card-hover" style="transition: transform 0.2s;">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Tickets</div>
                            <div class="h3 mb-0 fw-bold text-dark"><?= $totalTickets ?? 0 ?></div>
                            <small class="text-muted d-block mt-1" style="font-size:0.75rem;">Historial general</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-ticket-perforated-fill fs-1 text-primary opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Tickets Pendientes -->
    <div class="col-12 col-sm-6 col-md-4 col-xl">
        <a href="<?= APP_URL ?>/ticket?status=1" class="text-decoration-none">
            <div class="card shadow-sm border-0 border-start border-danger border-4 h-100 rounded-3 card-hover" style="transition: transform 0.2s;">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">Pendientes</div>
                            <div class="h3 mb-0 fw-bold text-dark"><?= $pendientes ?? 0 ?></div>
                            <small class="text-muted d-block mt-1" style="font-size:0.75rem;">Por atender</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle-fill fs-1 text-danger opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- En Proceso -->
    <div class="col-12 col-sm-6 col-md-4 col-xl">
        <a href="<?= APP_URL ?>/ticket?status=2" class="text-decoration-none">
            <div class="card shadow-sm border-0 border-start border-warning border-4 h-100 rounded-3 card-hover" style="transition: transform 0.2s;">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">En Proceso</div>
                            <div class="h3 mb-0 fw-bold text-dark"><?= $enProceso ?? 0 ?></div>
                            <small class="text-muted d-block mt-1" style="font-size:0.75rem;">En atención</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-tools fs-1 text-warning opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Tickets Cerrados -->
    <div class="col-12 col-sm-6 col-md-4 col-xl">
        <a href="<?= APP_URL ?>/ticket?status=3" class="text-decoration-none">
            <div class="card shadow-sm border-0 border-start border-success border-4 h-100 rounded-3 card-hover" style="transition: transform 0.2s;">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Cerrados</div>
                            <div class="h3 mb-0 fw-bold text-dark"><?= $resueltos ?? 0 ?></div>
                            <small class="text-muted d-block mt-1" style="font-size:0.75rem;">Solucionados</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle-fill fs-1 text-success opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Tickets Escalados -->
    <div class="col-12 col-sm-6 col-md-4 col-xl">
        <a href="<?= (isset($totalEscalados) && $totalEscalados === 1 && !empty($ticketsEscalados[0]['id_ticket'])) ? APP_URL . '/ticket/detalle/' . $ticketsEscalados[0]['id_ticket'] : APP_URL . '/ticket?status=4' ?>" class="text-decoration-none">
            <div class="card shadow-sm border-0 border-start border-4 h-100 rounded-3 card-hover" style="border-left-color: #fd7e14 !important; transition: transform 0.2s;">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-uppercase mb-1" style="color: #fd7e14;">Escalados</div>
                            <div class="h3 mb-0 fw-bold text-dark"><?= $totalEscalados ?? 0 ?></div>
                            <small class="text-muted d-block mt-1" style="font-size:0.75rem;">Atención crítica</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-arrow-up-circle-fill fs-1 opacity-25" style="color: #fd7e14;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<style>
.card-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
.escalado-alert {
    animation: pulse-border 2s infinite;
}
@keyframes pulse-border {
    0%   { border-left: 5px solid #dc3545; }
    50%  { border-left: 5px solid #ff6b6b; }
    100% { border-left: 5px solid #dc3545; }
}
</style>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100 rounded-3">
            <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between border-0">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-graph-up me-2"></i>Volumen de Tickets (Real)</h6>
            </div>
            <div class="card-body">
                <canvas id="ticketsChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100 rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-list-stars me-2"></i>Top Problemas Reportados</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (empty($categoryData)): ?>
                        <li class="list-group-item text-center text-muted p-3">No hay datos de categorías</li>
                    <?php else: ?>
                        <?php foreach (array_slice($categoryData, 0, 5) as $cat): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                <span class="text-secondary fw-semibold"><?= htmlspecialchars($cat['label']) ?></span>
                                <span class="badge bg-primary rounded-pill px-3"><?= $cat['total'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- ======================== Actividad Reciente + Gráficos Circulares ======================== -->
<div class="row g-4 mb-4">
    <!-- Actividad Reciente -->
    <div class="col-lg-6 col-12">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-clock-history me-2"></i>Actividad Reciente</h6>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentHistory)): ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    No hay actividad registrada aún.
                </div>
                <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($recentHistory as $h): ?>
                    <?php
                        $accion = strtolower($h['accion'] ?? '');
                        $dotColor = '#6c757d';
                        if (str_contains($accion,'cre')) $dotColor = '#0d6efd';
                        elseif (str_contains($accion,'resu')) $dotColor = '#198754';
                        elseif (str_contains($accion,'escal')) $dotColor = '#dc3545';
                        elseif (str_contains($accion,'asig')) $dotColor = '#fd7e14';
                        elseif (str_contains($accion,'coment')) $dotColor = '#6f42c1';
                    ?>
                    <li class="list-group-item px-3 py-2 d-flex align-items-start gap-2" style="font-size: 0.85rem;">
                        <span class="recent-dot mt-2" style="background:<?= $dotColor ?>; width: 8px; height: 8px; border-radius: 50%; display: inline-block; flex-shrink: 0;"></span>
                        <div class="flex-grow-1 min-width-0">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                                <span class="fw-semibold text-dark">
                                    <a href="<?= APP_URL ?>/ticket/detalle/<?= $h['id_ticket'] ?>" class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($h['codigo_ticket']) ?>
                                    </a>
                                </span>
                                <small class="text-muted" style="font-size: 0.75rem;"><?= date('d/m/Y H:i', strtotime($h['fecha_accion'])) ?></small>
                            </div>
                            <div class="text-secondary small mt-0.5" style="font-size: 0.8rem;">
                                <strong><?= htmlspecialchars($h['usuario']) ?></strong> — <?= htmlspecialchars($h['accion']) ?>
                                <?php if (!empty($h['detalle'])): ?>
                                <span class="text-muted">: <?= htmlspecialchars(mb_substr($h['detalle'], 0, 60)) ?><?= strlen($h['detalle']) > 60 ? '…' : '' ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Gráfico de Torta: Estado de Resolución (Abiertos vs Resueltos) -->
    <div class="col-lg-3 col-md-6 col-12">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-pie-chart me-2"></i>Estado de Resolución</h6>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <?php
                $abiertos = ($pendientes ?? 0) + ($enProceso ?? 0);
                $resueltosCount = $resueltos ?? 0;
                $totalIncidencias = $abiertos + $resueltosCount;
                ?>
                <?php if ($totalIncidencias === 0): ?>
                <div class="text-center text-muted py-3">
                    <i class="bi bi-pie-chart-fill fs-2 d-block mb-2"></i>
                    Sin incidencias registradas
                </div>
                <?php else: ?>
                <div style="height: 160px; width: 160px; position: relative;">
                    <canvas id="resolutionPieChart"></canvas>
                </div>
                <div class="text-center mt-3">
                    <div class="h5 mb-0 fw-bold text-dark">Total: <?= $totalIncidencias ?></div>
                    <div class="d-flex justify-content-center gap-2 mt-2" style="font-size:0.75rem;">
                        <span><span class="badge bg-warning text-dark">●</span> Abiertos: <strong><?= $abiertos ?></strong></span>
                        <span><span class="badge bg-success">●</span> Resueltos: <strong><?= $resueltosCount ?></strong></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cumplimiento SLA -->
    <div class="col-lg-3 col-md-6 col-12">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-stopwatch me-2"></i>Cumplimiento SLA</h6>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <?php
                $aTiempo    = (int)($slaData['a_tiempo']   ?? 0);
                $fueraTiempo= (int)($slaData['fuera_tiempo']?? 0);
                $totalSla   = $aTiempo + $fueraTiempo;
                $pctOk      = $totalSla > 0 ? round(($aTiempo / $totalSla) * 100) : 0;
                ?>
                <?php if ($totalSla === 0): ?>
                <div class="text-center text-muted py-3">
                    <i class="bi bi-stopwatch fs-2 d-block mb-2 text-primary opacity-50"></i>
                    <span class="small">Sin tickets cerrados con SLA medible</span>
                </div>
                <?php else: ?>
                <div style="height: 160px; width: 160px; position: relative;">
                    <canvas id="slaChart"></canvas>
                </div>
                <div class="text-center mt-3">
                    <div class="h5 mb-0 fw-bold <?= $pctOk >= 80 ? 'text-success' : ($pctOk >= 50 ? 'text-warning' : 'text-danger') ?>">
                        <?= $pctOk ?>%
                    </div>
                    <div class="text-muted small" style="font-size:0.75rem;">Cerrados dentro del plazo</div>
                    <div class="d-flex justify-content-center gap-2 mt-1" style="font-size:0.75rem;">
                        <span class="text-success fw-bold">✓ <?= $aTiempo ?> a tiempo</span>
                        <span class="text-danger fw-bold">✗ <?= $fueraTiempo ?> tarde</span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ======================== SECCIÓN ESCALADOS ======================== -->
<div class="row g-4 mb-4" id="seccion-escalados">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-danger">
                    <i class="bi bi-arrow-up-circle-fill me-2"></i>Tickets Escalados Activos
                </h6>
            </div>
            <div class="card-body p-0">
                <?php if (empty($ticketsEscalados)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                        <p class="mb-0">No hay tickets escalados en este momento.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Código</th>
                                    <th>Asunto</th>
                                    <th>Solicitante</th>
                                    <th>Categoría</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                    <th>Técnico</th>
                                    <th>Tienda</th>
                                    <th>Fecha Creación</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ticketsEscalados as $t): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold text-danger">
                                            <?= htmlspecialchars($t['codigo_ticket']) ?>
                                            <span class="badge bg-danger ms-1">Escalado</span>
                                        </td>
                                        <td><?= htmlspecialchars($t['asunto']) ?></td>
                                        <td><?= htmlspecialchars($t['solicitante'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($t['nombre_categoria'] ?? '—') ?></td>
                                        <td>
                                            <span class="badge bg-<?= strtolower($t['prioridad']) === 'alta' ? 'danger' : (strtolower($t['prioridad']) === 'media' ? 'warning' : 'secondary') ?>">
                                                <?= htmlspecialchars($t['prioridad'] ?? '—') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill" style="background-color:<?= htmlspecialchars($t['color_hex'] ?? '#6c757d') ?>;">
                                                <?= htmlspecialchars($t['nombre_estado'] ?? '—') ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($t['tecnico_asignado'] ?? 'Sin asignar') ?></td>
                                        <td><?= htmlspecialchars($t['nombre_tienda'] ?? '—') ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($t['fecha_creacion'])) ?></td>
                                        <td class="text-center">
                                            <a href="<?= APP_URL ?>/ticket/detalle/<?= $t['id_ticket'] ?>" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Gráfico de volumen semanal (Línea)
    const ctx = document.getElementById('ticketsChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($chartData['labels'] ?? []) ?>,
                datasets: [
                    {
                        label: 'Nuevos',
                        data: <?= json_encode($chartData['new'] ?? []) ?>,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13,110,253,0.05)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Resueltos',
                        data: <?= json_encode($chartData['resolved'] ?? []) ?>,
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25,135,84,0.05)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // 2. Gráfico de Torta: Estado de Resolución (Total vs Resueltos)
    const pieCtx = document.getElementById('resolutionPieChart');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Abiertos', 'Resueltos'],
                datasets: [{
                    data: [<?= $abiertos ?? 0 ?>, <?= $resueltosCount ?? 0 ?>],
                    backgroundColor: ['#ffc107', '#198754'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // 3. Gráfico Donut: Cumplimiento SLA
    const slaCtx = document.getElementById('slaChart');
    if (slaCtx) {
        new Chart(slaCtx, {
            type: 'doughnut',
            data: {
                labels: ['A tiempo', 'Tarde'],
                datasets: [{
                    data: [<?= $aTiempo ?? 0 ?>, <?= $fueraTiempo ?? 0 ?>],
                    backgroundColor: ['#198754', '#dc3545'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>

<?php endif; ?>

<?php require_once '../app/Views/layout/footer.php'; ?>
