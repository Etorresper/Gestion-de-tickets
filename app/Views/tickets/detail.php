<?php require_once '../app/Views/layout/header.php'; ?>

<?php
$evidencias_antes = array_filter($evidencias ?? [], function($e) { return $e['fase_evidencia'] === 'Antes'; });
$evidencias_durante = array_filter($evidencias ?? [], function($e) { return $e['fase_evidencia'] === 'Durante'; });
$evidencias_despues = array_filter($evidencias ?? [], function($e) { return $e['fase_evidencia'] === 'Después'; });
?>

<div class="container-fluid animate__animated animate__fadeIn">

<?php if (isset($_SESSION['alerta'])): ?>
<div class="alert alert-<?= $_SESSION['alerta']['tipo'] ?> alert-dismissible fade show mx-0 mb-3 shadow-sm" role="alert">
    <i class="bi bi-<?= $_SESSION['alerta']['tipo'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill me-2"></i>
    <?= htmlspecialchars($_SESSION['alerta']['mensaje']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['alerta']); ?>
<?php endif; ?>

    <!-- Banner de Conformidad del Encargado (Cuando el ticket está Resuelto) -->
    <?php if ($ticket['id_estado'] == 3 && empty($ticket['conformidad_encargado'])): ?>
    <div class="card shadow-sm border-0 rounded-4 mb-4 border-start border-5 border-success bg-white">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <span class="badge bg-success mb-2 px-3 py-1"><i class="bi bi-check-circle-fill me-1"></i>Atención Finalizada por Soporte</span>
                    <h5 class="fw-bold text-dark mb-1">Verificación y Conformidad de la Tienda</h5>
                    <p class="text-secondary small mb-0">El técnico ha solucionado la avería. Verifique que el equipo funcione correctamente y otorgue su conformidad para cerrar formalmente el ticket.</p>
                </div>
                <?php if ($_SESSION['user_role'] === 'Empleado' || $_SESSION['user_role'] === 'Administrador'): ?>
                <button type="button" class="btn btn-success fw-bold px-4 py-2 shadow-sm rounded-pill flex-shrink-0" data-bs-toggle="modal" data-bs-target="#modalConformidad">
                    <i class="bi bi-award-fill me-1"></i>Otorgar Conformidad y Cerrar
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php elseif (!empty($ticket['conformidad_encargado'])): ?>
    <div class="alert alert-success border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center">
        <i class="bi bi-patch-check-fill text-success fs-3 me-3"></i>
        <div>
            <strong class="text-success">Conformidad Registrada por el Encargado:</strong> Ticket cerrado satisfactoriamente.
            <?php if (!empty($ticket['comentario_conformidad'])): ?>
                <div class="small text-muted mt-1 fst-italic">"<?= htmlspecialchars($ticket['comentario_conformidad']) ?>"</div>
            <?php endif; ?>
            <div class="small text-muted"><?= $ticket['fecha_conformidad'] ? \App\Core\Helper::formatearFecha($ticket['fecha_conformidad']) : '' ?></div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Header del Ticket -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= APP_URL ?>/ticket/index" class="text-decoration-none">Tickets</a></li>
                    <li class="breadcrumb-item active fw-semibold"><?= $ticket['codigo_ticket'] ?></li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0"><?= htmlspecialchars($ticket['asunto']) ?></h2>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end align-items-center gap-2">
            <span class="badge rounded-pill px-3 py-2 fs-6 shadow-sm" style="background-color: <?= $ticket['color_hex'] ?>; color: #fff;">
                <i class="bi bi-circle-fill me-2 small"></i><?= $ticket['nombre_estado'] ?>
            </span>
            <a href="<?= APP_URL ?>/ticket/reporte/<?= $ticket['id_ticket'] ?>" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-2 shadow-sm fw-semibold">
                <i class="bi bi-file-earmark-text-fill text-primary me-1"></i>Reporte Técnico
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Columna Principal (Izquierda) -->
        <div class="col-lg-8">
            <!-- Descripción del Problema y Equipo -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-light rounded-circle p-3 text-primary">
                                <i class="bi bi-chat-left-text fs-4"></i>
                            </div>
                        </div>
                        <div class="ms-3 w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0">Detalles de la Incidencia</h5>
                                <small class="text-muted">Reportado por <strong><?= htmlspecialchars($ticket['solicitante'] ?? 'N/A') ?></strong></small>
                            </div>
                            <div class="mt-2 text-muted small d-flex flex-wrap gap-3">
                                <span><i class="bi bi-shop text-primary me-1"></i><strong>Tienda:</strong> <?= htmlspecialchars($ticket['nombre_tienda'] ?? 'Oficina') ?> (<?= htmlspecialchars($ticket['nombre_distrito'] ?? '') ?>)</span>
                                <span><i class="bi bi-tag-fill text-primary me-1"></i><?= htmlspecialchars($ticket['nombre_categoria'] ?? 'Sin categoría') ?></span>
                                <span><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Prioridad: <strong><?= htmlspecialchars($ticket['prioridad'] ?? 'Media') ?></strong></span>
                                <span><i class="bi bi-laptop me-1 text-info"></i>Modalidad: <strong><?= htmlspecialchars($ticket['tipo_atencion'] ?? 'Remota') ?></strong></span>
                                <span><i class="bi bi-calendar-check-fill text-success me-1"></i>SLA: <?= $ticket['fecha_vencimiento'] ? \App\Core\Helper::formatearFecha($ticket['fecha_vencimiento']) : 'Sin SLA' ?></span>
                            </div>

                            <?php if (!empty($ticket['prioridad_original']) && $ticket['prioridad_original'] !== $ticket['prioridad']): ?>
                            <div class="alert alert-warning py-2 px-3 small mt-2 mb-0 border-0 rounded-3">
                                <i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i>
                                <strong>Prioridad Re-escalada:</strong> Inicialmente reportada como <em><?= htmlspecialchars($ticket['prioridad_original']) ?></em>. 
                                Modificada por <strong><?= htmlspecialchars($ticket['usuario_modifico_prio'] ?? 'Sistemas') ?></strong>.
                                <?php if (!empty($ticket['motivo_cambio_prio'])): ?>
                                    <br><span class="text-dark"><strong>Motivo:</strong> <?= htmlspecialchars($ticket['motivo_cambio_prio']) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Ficha del Equipo Afectado -->
                    <?php if (!empty($ticket['equipo_nombre']) || !empty($ticket['equipo_modelo']) || !empty($ticket['equipo_serie'])): ?>
                    <div class="p-3 mb-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark small"><i class="bi bi-cpu-fill text-primary me-2"></i>Ficha del Equipo Afectado</span>
                            <span class="badge bg-secondary"><?= htmlspecialchars($ticket['equipo_nombre'] ?? 'Equipo') ?></span>
                        </div>
                        <div class="row g-2 small text-secondary">
                            <div class="col-sm-6">
                                <strong>Marca / Modelo:</strong> <?= htmlspecialchars($ticket['equipo_modelo'] ?? 'No especificado') ?>
                            </div>
                            <div class="col-sm-6">
                                <strong>N° de Serie (S/N):</strong> <?= htmlspecialchars($ticket['equipo_serie'] ?? 'S/N') ?>
                            </div>
                            <?php if (!empty($ticket['desde_cuando_falla'])): ?>
                            <div class="col-12 mt-1 text-danger">
                                <i class="bi bi-clock-history me-1"></i><strong>¿Desde cuándo presenta la falla?:</strong> <?= htmlspecialchars($ticket['desde_cuando_falla']) ?>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($ticket['estado_final_equipo'])): ?>
                            <div class="col-12 mt-1">
                                <strong>Estado Final del Equipo:</strong> 
                                <span class="badge bg-<?= $ticket['estado_final_equipo'] === 'Operativo' ? 'success' : 'danger' ?>">
                                    <?= htmlspecialchars($ticket['estado_final_equipo']) ?>
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="bg-light p-4 rounded-3 text-secondary" style="white-space: pre-wrap; font-size: 1.02rem; line-height: 1.6;">
                        <?= htmlspecialchars($ticket['descripcion']) ?>
                    </div>
                </div>
            </div>

            <!-- MÓDULO DE EVIDENCIAS FOTOGRÁFICAS EN 3 FASES -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-camera-reels-fill text-primary me-2"></i>Evidencias Fotográficas
                            </h5>
                            <small class="text-muted">Registro visual antes, durante y después de la atención técnica</small>
                        </div>
                        <?php if ($_SESSION['user_role'] !== 'Empleado'): ?>
                        <button type="button" class="btn btn-sm btn-primary fw-semibold px-3 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSubirEvidencia">
                            <i class="bi bi-plus-circle me-1"></i>Nueva Evidencia
                        </button>
                        <?php endif; ?>
                    </div>

                    <!-- Nav Pestañas de Fases -->
                    <ul class="nav nav-pills nav-fill mb-3 bg-light p-1 rounded-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold small" id="pills-antes-tab" data-bs-toggle="pill" data-bs-target="#pills-antes" type="button" role="tab">
                                <i class="bi bi-camera me-1"></i>1. Antes de la Atención 
                                <span class="badge bg-primary ms-1"><?= count($evidencias_antes) ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold small" id="pills-durante-tab" data-bs-toggle="pill" data-bs-target="#pills-durante" type="button" role="tab">
                                <i class="bi bi-tools me-1"></i>2. Durante la Atención 
                                <span class="badge bg-warning text-dark ms-1"><?= count($evidencias_durante) ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold small" id="pills-despues-tab" data-bs-toggle="pill" data-bs-target="#pills-despues" type="button" role="tab">
                                <i class="bi bi-check2-circle me-1"></i>3. Después de la Atención 
                                <span class="badge bg-success ms-1"><?= count($evidencias_despues) ?></span>
                            </button>
                        </li>
                    </ul>

                    <!-- Contenido de las Pestañas -->
                    <div class="tab-content" id="pills-tabContent">
                        <!-- FASE 1: ANTES -->
                        <div class="tab-pane fade show active" id="pills-antes" role="tabpanel">
                            <?php if (empty($evidencias_antes)): ?>
                                <div class="text-center py-4 text-muted bg-light rounded-3">
                                    <i class="bi bi-image fs-1 opacity-50"></i>
                                    <p class="small mb-0 mt-2">No se adjuntaron fotografías al registrar el ticket.</p>
                                </div>
                            <?php else: ?>
                                <div class="row g-3">
                                    <?php foreach ($evidencias_antes as $ev): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 border shadow-none rounded-3 overflow-hidden">
                                            <a href="javascript:void(0)" onclick="verFotoGrande('<?= APP_URL . '/' . htmlspecialchars($ev['ruta_archivo']) ?>', 'Evidencia: Antes de la Atención')">
                                                <img src="<?= APP_URL . '/' . htmlspecialchars($ev['ruta_archivo']) ?>" 
                                                     class="card-img-top object-fit-cover" 
                                                     style="height: 160px; cursor: pointer;" 
                                                     alt="Evidencia Antes">
                                            </a>
                                            <div class="card-body p-2">
                                                <p class="card-text small text-secondary mb-1"><?= htmlspecialchars($ev['descripcion'] ?? 'Foto inicial reportada.') ?></p>
                                                <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-1">
                                                    <small class="text-primary fw-semibold" style="font-size: 0.75rem;"><i class="bi bi-person me-1"></i><?= htmlspecialchars($ev['usuario']) ?></small>
                                                    <small class="text-muted" style="font-size: 0.7rem;"><?= \App\Core\Helper::formatearFecha($ev['fecha_registro']) ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- FASE 2: DURANTE -->
                        <div class="tab-pane fade" id="pills-durante" role="tabpanel">
                            <?php if (empty($evidencias_durante)): ?>
                                <div class="text-center py-4 text-muted bg-light rounded-3">
                                    <i class="bi bi-tools fs-1 opacity-50"></i>
                                    <p class="small mb-2 mt-2">No hay evidencias de diagnóstico registradas aún.</p>
                                    <?php if ($_SESSION['user_role'] !== 'Empleado'): ?>
                                    <button class="btn btn-sm btn-outline-warning fw-semibold rounded-pill" data-bs-toggle="modal" data-bs-target="#modalSubirEvidencia" onclick="prepararFaseModal('Durante')">
                                        <i class="bi bi-plus-circle me-1"></i>Adjuntar Diagnóstico / Componentes Dañados
                                    </button>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="row g-3">
                                    <?php foreach ($evidencias_durante as $ev): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 border shadow-none rounded-3 overflow-hidden">
                                            <a href="javascript:void(0)" onclick="verFotoGrande('<?= APP_URL . '/' . htmlspecialchars($ev['ruta_archivo']) ?>', 'Evidencia: Diagnóstico y Proceso')">
                                                <img src="<?= APP_URL . '/' . htmlspecialchars($ev['ruta_archivo']) ?>" 
                                                     class="card-img-top object-fit-cover" 
                                                     style="height: 160px; cursor: pointer;" 
                                                     alt="Evidencia Durante">
                                            </a>
                                            <div class="card-body p-2">
                                                <span class="badge bg-warning text-dark mb-1" style="font-size: 0.65rem;">Diagnóstico / Proceso</span>
                                                <p class="card-text small text-secondary mb-1"><?= htmlspecialchars($ev['descripcion'] ?? 'Procedimiento técnico realizado.') ?></p>
                                                <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-1">
                                                    <small class="text-primary fw-semibold" style="font-size: 0.75rem;"><i class="bi bi-wrench me-1"></i><?= htmlspecialchars($ev['usuario']) ?></small>
                                                    <small class="text-muted" style="font-size: 0.7rem;"><?= \App\Core\Helper::formatearFecha($ev['fecha_registro']) ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- FASE 3: DESPUÉS -->
                        <div class="tab-pane fade" id="pills-despues" role="tabpanel">
                            <?php if (empty($evidencias_despues)): ?>
                                <div class="text-center py-4 text-muted bg-light rounded-3">
                                    <i class="bi bi-check2-all fs-1 opacity-50"></i>
                                    <p class="small mb-2 mt-2">Aún no se han cargado evidencias de solución final.</p>
                                    <?php if ($_SESSION['user_role'] !== 'Empleado'): ?>
                                    <button class="btn btn-sm btn-outline-success fw-semibold rounded-pill" data-bs-toggle="modal" data-bs-target="#modalSubirEvidencia" onclick="prepararFaseModal('Después')">
                                        <i class="bi bi-plus-circle me-1"></i>Registrar Solución y Evidencia Final
                                    </button>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="row g-3">
                                    <?php foreach ($evidencias_despues as $ev): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 border border-success border-opacity-50 shadow-none rounded-3 overflow-hidden">
                                            <a href="javascript:void(0)" onclick="verFotoGrande('<?= APP_URL . '/' . htmlspecialchars($ev['ruta_archivo']) ?>', 'Evidencia: Solución Aplicada')">
                                                <img src="<?= APP_URL . '/' . htmlspecialchars($ev['ruta_archivo']) ?>" 
                                                     class="card-img-top object-fit-cover" 
                                                     style="height: 160px; cursor: pointer;" 
                                                     alt="Evidencia Después">
                                            </a>
                                            <div class="card-body p-2">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="badge bg-success" style="font-size: 0.65rem;">Solución Aplicada</span>
                                                    <?php if (!empty($ev['estado_equipo'])): ?>
                                                    <span class="badge bg-<?= $ev['estado_equipo'] === 'Operativo' ? 'success' : 'danger' ?>" style="font-size: 0.65rem;">
                                                        <?= htmlspecialchars($ev['estado_equipo']) ?>
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                                <p class="card-text small text-secondary mb-1"><?= htmlspecialchars($ev['descripcion'] ?? 'Solución documentada.') ?></p>
                                                <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-1">
                                                    <small class="text-success fw-semibold" style="font-size: 0.75rem;"><i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($ev['usuario']) ?></small>
                                                    <small class="text-muted" style="font-size: 0.7rem;"><?= \App\Core\Helper::formatearFecha($ev['fecha_registro']) ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial y Comentarios (Timeline) -->
            <h5 class="fw-bold mb-4 mt-5"><i class="bi bi-clock-history me-2 text-primary"></i>Historial de Actividad & Auditoría</h5>
            <div class="timeline-container position-relative ps-4 ms-2">
                <div class="timeline-line position-absolute start-0 top-0 bottom-0 bg-light" style="width: 2px;"></div>
                
                <?php foreach($historial as $index => $h): ?>
                <div class="timeline-item position-relative mb-4">
                    <div class="timeline-marker position-absolute start-0 translate-middle-x rounded-circle bg-white border border-3 <?= $index === 0 ? 'border-primary' : 'border-secondary' ?>" style="width: 16px; height: 16px; left: -16px; top: 12px; z-index: 2;"></div>
                    <div class="card shadow-none border-0 bg-white ms-3">
                        <div class="card-body p-0 ps-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark"><?= htmlspecialchars($h['accion']) ?></span>
                                <small class="text-muted"><?= \App\Core\Helper::formatearFecha($h['fecha_accion']) ?></small>
                            </div>
                            <p class="text-secondary small mb-1"><?= htmlspecialchars($h['detalle']) ?></p>
                            <small class="text-primary fw-semibold"><i class="bi bi-person me-1"></i><?= htmlspecialchars($h['usuario'] ?? 'Sistema') ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Caja de Comentarios -->
            <div class="card shadow-sm border-0 rounded-4 mt-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-chat-dots me-2"></i>Añadir Comentario o Nota</h5>
                    <form action="<?= APP_URL ?>/ticket/agregarComentario/<?= $ticket['id_ticket'] ?>" method="POST">
                        <textarea class="form-control bg-light border-0 mb-3" name="comentario" rows="3" placeholder="Escribe aquí tu actualización u observación..." required></textarea>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill shadow-sm">Enviar Comentario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Columna Sidebar (Derecha) -->
        <div class="col-lg-4">

            <?php if ($_SESSION['user_role'] !== 'Empleado'): ?>
            <!-- Ficha Técnica del Equipo (Ingreso / Edición por Soporte) -->
            <div class="card shadow-sm border-0 rounded-4 mb-4 border-start border-4 border-info">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase text-muted mb-2 small">
                        <i class="bi bi-cpu-fill me-1 text-info"></i>Ficha del Equipo (Técnico)
                    </h6>
                    <form action="<?= APP_URL ?>/ticket/actualizarEquipo/<?= $ticket['id_ticket'] ?>" method="POST">
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Nombre / Tipo</label>
                            <input type="text" class="form-control bg-light border-0 form-control-sm" name="equipo_nombre"
                                   placeholder="Ej: Impresora Térmica, POS, PC Caja"
                                   value="<?= htmlspecialchars($ticket['equipo_nombre'] ?? '') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Marca / Modelo</label>
                            <input type="text" class="form-control bg-light border-0 form-control-sm" name="equipo_modelo"
                                   placeholder="Ej: Epson TM-T20III"
                                   value="<?= htmlspecialchars($ticket['equipo_modelo'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">N° de Serie (S/N)</label>
                            <input type="text" class="form-control bg-light border-0 form-control-sm" name="equipo_serie"
                                   placeholder="Ej: SN-48192039"
                                   value="<?= htmlspecialchars($ticket['equipo_serie'] ?? '') ?>">
                        </div>
                        <button type="submit" class="btn btn-info btn-sm w-100 fw-bold text-white shadow-sm">
                            <i class="bi bi-save me-1"></i>Guardar Ficha del Equipo
                        </button>
                    </form>
                </div>
            </div>

            <!-- Gestión de Estado (Solo Admin/Soporte) -->
            <div class="card shadow-sm border-0 rounded-4 mb-4 border-start border-4 border-warning">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase text-muted mb-3 small">Acciones de Soporte</h6>
                    <form action="<?= APP_URL ?>/ticket/actualizar/<?= $ticket['id_ticket'] ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Cambiar Estado</label>
                            <select class="form-select bg-light border-0" name="id_estado">
                                <option value="1" <?= $ticket['id_estado'] == 1 ? 'selected' : '' ?>>🔴 1. Pendiente</option>
                                <option value="2" <?= $ticket['id_estado'] == 2 ? 'selected' : '' ?>>🔄 2. En Proceso</option>
                                <option value="3" <?= $ticket['id_estado'] == 3 ? 'selected' : '' ?>>🏁 3. Cerrado</option>
                                <option value="4" <?= $ticket['id_estado'] == 4 ? 'selected' : '' ?>>🔺 4. Escalado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nota Interna (Opcional)</label>
                            <textarea class="form-control bg-light border-0" name="comentario" rows="2" placeholder="Motivo del cambio de estado..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 fw-bold shadow-sm">Actualizar Estado</button>
                    </form>
                </div>
            </div>

            <!-- Gestión de Prioridad y Escalamiento (Solo Admin/Soporte) -->
            <div class="card shadow-sm border-0 rounded-4 mb-4 border-start border-4 border-danger">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase text-muted mb-2 small">
                        <i class="bi bi-speedometer2 me-1 text-danger"></i>Escalar / Modificar Prioridad
                    </h6>
                    <form action="<?= APP_URL ?>/ticket/cambiarPrioridad/<?= $ticket['id_ticket'] ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nueva Prioridad</label>
                            <select class="form-select bg-light border-0" name="nueva_prioridad" required>
                                <option value="Baja" <?= $ticket['prioridad'] === 'Baja' ? 'selected' : '' ?>>🟢 Baja (Sin interrupción)</option>
                                <option value="Media" <?= $ticket['prioridad'] === 'Media' ? 'selected' : '' ?>>🟡 Media (Afecta parcialmente)</option>
                                <option value="Alta" <?= $ticket['prioridad'] === 'Alta' ? 'selected' : '' ?>>🔴 Alta (Afecta ventas/cajas)</option>
                                <option value="Crítica" <?= $ticket['prioridad'] === 'Crítica' ? 'selected' : '' ?>>🚨 Crítica (Paraliza tienda)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Motivo Justificado (Auditoría)</label>
                            <textarea class="form-control bg-light border-0" name="motivo_cambio" rows="2" placeholder="Indique la justificación del cambio..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-danger w-100 fw-bold shadow-sm">Guardar Nueva Prioridad</button>
                    </form>
                </div>
            </div>

            <?php if ($_SESSION['user_role'] === 'Administrador'): ?>
            <!-- Panel de Asignación Manual (Solo Administrador) -->
            <div class="card shadow-sm border-0 rounded-4 mb-4 border-start border-4 border-primary">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase text-muted mb-1 small">
                        <i class="bi bi-person-fill-gear me-1 text-primary"></i>Asignación Manual
                    </h6>
                    <p class="text-muted small mb-3">Solo el Administrador puede reasignar tickets.</p>

                    <?php if ($asignacion_actual): ?>
                    <div class="d-flex align-items-center mb-3 p-2 bg-light rounded-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="bi bi-person-check-fill text-primary fs-5"></i>
                        </div>
                        <div>
                            <div class="text-xs text-muted">Asignado actualmente a</div>
                            <div class="fw-bold"><?= htmlspecialchars($asignacion_actual['tecnico_nombre']) ?></div>
                            <div class="text-xs text-muted"><?= htmlspecialchars($asignacion_actual['nombre_area'] ?? '') ?></div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning py-2 small mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Sin técnico asignado
                    </div>
                    <?php endif; ?>

                    <form action="<?= APP_URL ?>/ticket/asignar/<?= $ticket['id_ticket'] ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Seleccionar Técnico</label>
                            <select class="form-select bg-light border-0" name="id_tecnico" required id="select-tecnico-<?= $ticket['id_ticket'] ?>">
                                <option value="" disabled selected>-- Elige un técnico --</option>
                                <?php foreach ($tecnicos_soporte as $tec): ?>
                                <option value="<?= $tec['id_usuario'] ?>"
                                    <?= ($asignacion_actual && $asignacion_actual['id_tecnico'] == $tec['id_usuario']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tec['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nota de Asignación (Opcional)</label>
                            <input type="text" class="form-control bg-light border-0" name="nota"
                                   placeholder="Ej: Técnico especializado en red...">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                            <i class="bi bi-person-fill-check me-1"></i>Confirmar Asignación
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <!-- Información General del Ticket -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase text-muted mb-4 small">Información General</h6>
                    
                    <!-- Técnico Asignado -->
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-blue-soft rounded-3 p-2 text-primary me-3">
                            <i class="bi bi-person-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="text-xs text-muted">Técnico de Soporte</div>
                            <div class="fw-bold">
                                <?php if ($asignacion_actual): ?>
                                    <span class="text-primary"><?= htmlspecialchars($asignacion_actual['tecnico_nombre']) ?></span>
                                <?php else: ?>
                                    <span class="text-danger fst-italic">Sin asignar</span>
                                <?php endif; ?>
                            </div>
                            <?php if ($asignacion_actual): ?>
                            <div class="text-xs text-muted"><?= htmlspecialchars($asignacion_actual['nombre_area'] ?? 'Sistemas Comas') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-blue-soft rounded-3 p-2 text-primary me-3">
                            <i class="bi bi-tag fs-5"></i>
                        </div>
                        <div>
                            <div class="text-xs text-muted">Categoría</div>
                            <div class="fw-bold"><?= htmlspecialchars($ticket['nombre_categoria'] ?? 'Sin categoría') ?></div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-red-soft rounded-3 p-2 text-danger me-3">
                            <i class="bi bi-lightning-charge fs-5"></i>
                        </div>
                        <div>
                            <div class="text-xs text-muted">Prioridad</div>
                            <div class="fw-bold"><?= htmlspecialchars($ticket['prioridad'] ?? 'Media') ?></div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-green-soft rounded-3 p-2 text-success me-3">
                            <i class="bi bi-calendar-check fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-xs text-muted">Vencimiento SLA</span>
                                <?php if (($_SESSION['user_role'] === 'Administrador' || $_SESSION['user_role'] === 'Soporte') && $ticket['id_estado'] < 3): ?>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-primary" data-bs-toggle="modal" data-bs-target="#modalReprogramarSLA" title="Reprogramar por ruta de visitas">
                                    <i class="bi bi-pencil-square"></i> <small style="font-size:0.75rem;">Ajustar ruta</small>
                                </button>
                                <?php endif; ?>
                            </div>
                            <div class="fw-bold <?= (strtotime($ticket['fecha_vencimiento'] ?? '') < time() && $ticket['id_estado'] < 3) ? 'text-danger' : '' ?>">
                                <?= $ticket['fecha_vencimiento'] ? \App\Core\Helper::formatearFecha($ticket['fecha_vencimiento']) : 'Sin SLA' ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($ticket['id_estado'] == 3): ?>
                    <?php 
                        $fc = !empty($ticket['fecha_cierre']) ? $ticket['fecha_cierre'] : $ticket['fecha_actualizacion'];
                        $fv = !empty($ticket['fecha_vencimiento']) ? $ticket['fecha_vencimiento'] : date('Y-m-d H:i:s', strtotime($ticket['fecha_creacion'] . ' +48 hours'));
                        $cumplioSla = (strtotime($fc) <= strtotime($fv));
                    ?>
                    <div class="d-flex align-items-center mb-3">
                        <div class="<?= $cumplioSla ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> rounded-3 p-2 me-3">
                            <i class="bi <?= $cumplioSla ? 'bi-check2-circle' : 'bi-clock-history' ?> fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <span class="text-xs text-muted">Fecha y Resultado de Cierre</span>
                            <div>
                                <span class="fw-bold text-dark"><?= \App\Core\Helper::formatearFecha($fc) ?></span>
                                <div class="mt-1">
                                    <?php if ($cumplioSla): ?>
                                        <span class="badge bg-success text-white"><i class="bi bi-check-circle me-1"></i>Cumplió SLA a tiempo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger text-white"><i class="bi bi-x-circle me-1"></i>Cerrado fuera de SLA (Con retraso)</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <hr class="my-4 opacity-25">

                    <h6 class="fw-bold text-uppercase text-muted mb-3 small">Sede de Atención</h6>
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-geo-alt-fill text-primary me-3 fs-5"></i>
                        <div>
                            <div class="fw-bold"><?= htmlspecialchars($ticket['nombre_tienda']) ?></div>
                            <div class="text-xs text-muted"><?= htmlspecialchars($ticket['nombre_distrito']) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Auditoría -->
            <div class="card shadow-sm border-0 rounded-4 bg-light">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Registrado</span>
                        <span class="small fw-semibold"><?= \App\Core\Helper::formatearFecha($ticket['fecha_creacion']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Última Actividad</span>
                        <span class="small fw-semibold"><?= \App\Core\Helper::formatearFecha($ticket['fecha_actualizacion']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: SUBIR EVIDENCIA (DURANTE O DESPUÉS) -->
<div class="modal fade" id="modalSubirEvidencia" tabindex="-1" aria-labelledby="modalSubirEvidenciaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalSubirEvidenciaLabel">
                    <i class="bi bi-camera-fill text-primary me-2"></i>Registrar Evidencia Fotográfica
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="<?= APP_URL ?>/ticket/subirEvidencia/<?= $ticket['id_ticket'] ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Fase de la Atención</label>
                        <select class="form-select bg-light border-0" name="fase_evidencia" id="modal_fase_evidencia" onchange="cambiarFaseModal(this.value)">
                            <option value="Durante">🔍 Durante la Atención (Diagnóstico / Componentes Dañados)</option>
                            <option value="Después">✅ Después de la Atención (Solución Final / Equipo Operativo)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Fotografía de la Evidencia (JPG, PNG)</label>
                        <input type="file" class="form-control bg-light border-0" name="foto_evidencia" accept="image/*" required>
                        <div class="form-text">Adjunte una captura clara de los componentes, conexiones o equipo reparado.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Descripción de Procedimientos / Diagnóstico / Solución</label>
                        <textarea class="form-control bg-light border-0" name="descripcion_evidencia" rows="3" 
                                  placeholder="Detalle los componentes revisados, piezas reemplazadas o configuraciones realizadas..." required></textarea>
                    </div>

                    <!-- Datos del Equipo Técnico (Nombre, Marca/Modelo, Serie) -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <label class="form-label small fw-bold text-dark mb-2">
                            <i class="bi bi-cpu-fill text-primary me-1"></i>Ficha Técnica del Equipo Intervenido
                        </label>
                        <div class="row g-2">
                            <div class="col-12">
                                <input type="text" class="form-control form-control-sm bg-white" name="equipo_nombre" 
                                       placeholder="Nombre/Tipo (Ej: Impresora Térmica, POS, PC Caja)"
                                       value="<?= htmlspecialchars($ticket['equipo_nombre'] ?? '') ?>">
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-sm bg-white" name="equipo_modelo" 
                                       placeholder="Marca / Modelo (Ej: Epson TM-T20III)"
                                       value="<?= htmlspecialchars($ticket['equipo_modelo'] ?? '') ?>">
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-sm bg-white" name="equipo_serie" 
                                       placeholder="N° Serie (Ej: SN-48192039)"
                                       value="<?= htmlspecialchars($ticket['equipo_serie'] ?? '') ?>">
                            </div>
                        </div>
                        <small class="text-muted" style="font-size: 0.72rem;">El técnico debe ingresar o verificar estos datos para que figuren en el informe.</small>
                    </div>

                    <!-- Bloque exclusivo para fase DESPUÉS -->
                    <div id="bloque_fase_despues" class="d-none p-3 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-25 mb-3">
                        <label class="form-label small fw-bold text-success">Estado Final del Equipo</label>
                        <select class="form-select border-success mb-3" name="estado_equipo">
                            <option value="Operativo" selected>🟢 Operativo (Funcionando 100%)</option>
                            <option value="Requiere Nueva Intervención">🔴 Requiere Nueva Intervención (Repuestos pendientes)</option>
                        </select>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="marcar_resuelto" name="marcar_resuelto" value="1" checked>
                            <label class="form-check-label small fw-bold text-dark" for="marcar_resuelto">
                                Finalizar atención y marcar ticket como RESUELTO
                            </label>
                            <div class="text-muted" style="font-size: 0.75rem;">Permite al encargado verificar la operatividad y dar su conformidad.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Guardar Evidencia</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: CONFORMIDAD DEL ENCARGADO -->
<div class="modal fade" id="modalConformidad" tabindex="-1" aria-labelledby="modalConformidadLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-success" id="modalConformidadLabel">
                    <i class="bi bi-award-fill me-2"></i>Conformidad de Solución
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="<?= APP_URL ?>/ticket/darConformidad/<?= $ticket['id_ticket'] ?>" method="POST">
                <div class="modal-body">
                    <div class="alert alert-info py-2 small border-0 rounded-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Al confirmar, certificas que el personal de soporte atendió la falla y que el equipo de tu tienda ha quedado en óptimas condiciones operativas.
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Observaciones o Calificación del Servicio</label>
                        <textarea class="form-control bg-light border-0" name="comentario_conformidad" rows="3" 
                                  placeholder="Ej: Equipo verificado e imprimiendo comprobantes correctamente. Atención puntual y adecuada.">Equipo operativo y verificado en tienda. Conforme con la solución.</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Regresar</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i>Confirmar y Cerrar Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: VISOR DE FOTO GRANDE (LIGHTBOX) -->
<div class="modal fade" id="modalVerFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border-0 rounded-4 overflow-hidden">
            <div class="modal-header border-0 text-white">
                <h6 class="modal-title fw-bold" id="modalVerFotoTitulo">Evidencia Fotográfica</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="modalVerFotoImg" src="" alt="Evidencia" class="img-fluid" style="max-height: 80vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<!-- MODAL: REPROGRAMAR / AJUSTAR FECHA LÍMITE DE VISITA (SLA) -->
<div class="modal fade" id="modalReprogramarSLA" tabindex="-1" aria-labelledby="modalReprogramarSLALabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalReprogramarSLALabel">
                    <i class="bi bi-calendar-event-fill text-primary me-2"></i>Ajustar Fecha Compromiso de Visita (Ruta)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="<?= APP_URL ?>/ticket/reprogramarSLA/<?= $ticket['id_ticket'] ?>" method="POST">
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Úsalo cuando la atención esté programada en una ruta itinerante de visitas a varias sedes para evitar que el ticket se marque indebidamente como vencido.
                    </p>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nueva Fecha y Hora de Visita Programada</label>
                        <input type="datetime-local" class="form-control bg-light border-0" name="nueva_fecha_vencimiento" 
                               value="<?= $ticket['fecha_vencimiento'] ? date('Y-m-d\TH:i', strtotime($ticket['fecha_vencimiento'])) : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Motivo de Reprogramación / Ruta</label>
                        <textarea class="form-control bg-light border-0" name="motivo_reprogramacion" rows="2" 
                                  placeholder="Ej: Visita coordinada en itinerario de tiendas Lima Sur para el turno de la tarde." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Actualizar Fecha de Ruta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function cambiarFaseModal(fase) {
    const bloqueDespues = document.getElementById('bloque_fase_despues');
    if (bloqueDespues) {
        if (fase === 'Después') {
            bloqueDespues.classList.remove('d-none');
        } else {
            bloqueDespues.classList.add('d-none');
        }
    }
}

function prepararFaseModal(fase) {
    const sel = document.getElementById('modal_fase_evidencia');
    if (sel) {
        sel.value = fase;
        cambiarFaseModal(fase);
    }
}

function verFotoGrande(src, titulo) {
    const modalImg = document.getElementById('modalVerFotoImg');
    const modalTitulo = document.getElementById('modalVerFotoTitulo');
    if (modalImg) modalImg.src = src;
    if (modalTitulo && titulo) modalTitulo.innerText = titulo;
    const modalEl = document.getElementById('modalVerFoto');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}
</script>

<style>
.bg-blue-soft { background-color: rgba(13, 110, 253, 0.1); }
.bg-red-soft { background-color: rgba(220, 53, 69, 0.1); }
.bg-green-soft { background-color: rgba(25, 135, 84, 0.1); }
.bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
.text-xs { font-size: 0.75rem; }
.timeline-item:last-child .timeline-line { display: none; }
</style>

<?php require_once '../app/Views/layout/footer.php'; ?>
