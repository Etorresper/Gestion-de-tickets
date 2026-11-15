<?php require_once '../app/Views/layout/header.php'; ?>

<div class="container-fluid animate__animated animate__fadeIn">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="bi bi-camera-reels-fill text-primary me-2"></i>Galería de Evidencias Fotográficas
            </h3>
            <p class="text-muted small mb-0">
                Auditoría visual de averías, diagnósticos y soluciones aplicadas en tienda.
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-light text-dark border px-3 py-2 fs-6 shadow-sm">
                Total Registros: <strong><?= count($evidencias) ?></strong>
            </span>
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="<?= APP_URL ?>/evidencia/index" method="GET" class="row g-2 align-items-center">
                <!-- Filtro por Fase -->
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Fase de la Atención</label>
                    <select name="fase" class="form-select form-select-sm bg-light border-0">
                        <option value="">-- Todas las Fases --</option>
                        <option value="Antes" <?= (!empty($_GET['fase']) && $_GET['fase'] === 'Antes') ? 'selected' : '' ?>>📸 1. Antes de la Atención</option>
                        <option value="Durante" <?= (!empty($_GET['fase']) && $_GET['fase'] === 'Durante') ? 'selected' : '' ?>>🔍 2. Durante la Atención</option>
                        <option value="Después" <?= (!empty($_GET['fase']) && $_GET['fase'] === 'Después') ? 'selected' : '' ?>>✅ 3. Después de la Atención</option>
                    </select>
                </div>

                <!-- Filtro por Tienda (Para Admin y Soporte) -->
                <?php if ($_SESSION['user_role'] !== 'Empleado'): ?>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold mb-1">Filtrar por Tienda</label>
                    <select name="id_tienda" class="form-select form-select-sm bg-light border-0">
                        <option value="">-- Todas las Tiendas --</option>
                        <?php foreach($tiendas as $t): ?>
                            <option value="<?= $t['id_tienda'] ?>" <?= (!empty($_GET['id_tienda']) && $_GET['id_tienda'] == $t['id_tienda']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['nombre_tienda']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php else: ?>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold mb-1">Sede Asignada</label>
                    <input type="text" class="form-control form-select-sm bg-light border-0 fw-bold" 
                           value="<?= htmlspecialchars($_SESSION['user_tienda_nombre'] ?? 'Mi Tienda') ?>" readonly>
                </div>
                <?php endif; ?>

                <!-- Filtro por ID de Ticket -->
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">ID Ticket (Opcional)</label>
                    <input type="number" name="id_ticket" class="form-control form-select-sm bg-light border-0" 
                           placeholder="Ej. 8" value="<?= htmlspecialchars($_GET['id_ticket'] ?? '') ?>">
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2 mt-md-4">
                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold flex-grow-1 shadow-sm">
                        <i class="bi bi-filter me-1"></i>Filtrar
                    </button>
                    <a href="<?= APP_URL ?>/evidencia/index" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Rejilla de Evidencias Fotográficas -->
    <?php if (empty($evidencias)): ?>
    <div class="card shadow-sm border-0 rounded-4 text-center py-5">
        <div class="card-body">
            <i class="bi bi-images fs-1 text-muted opacity-50 d-block mb-3"></i>
            <h5 class="fw-bold text-dark">No se encontraron evidencias fotográficas</h5>
            <p class="text-muted small mb-0">No hay registros con los filtros seleccionados o aún no se han cargado fotografías para esta sede.</p>
        </div>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <?php foreach ($evidencias as $ev): ?>
        <?php 
            $badgeColor = 'primary';
            if ($ev['fase_evidencia'] === 'Durante') $badgeColor = 'warning text-dark';
            elseif ($ev['fase_evidencia'] === 'Después') $badgeColor = 'success';
        ?>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                <!-- Imagen con botón para agrandar -->
                <div class="position-relative">
                    <img src="<?= APP_URL . '/' . htmlspecialchars($ev['ruta_archivo']) ?>" 
                         class="card-img-top object-fit-cover" 
                         style="height: 200px; cursor: pointer;" 
                         alt="Evidencia"
                         onclick="verFotoModal('<?= APP_URL . '/' . htmlspecialchars($ev['ruta_archivo']) ?>', '<?= htmlspecialchars($ev['fase_evidencia']) ?> - <?= htmlspecialchars($ev['codigo_ticket'] ?? '') ?>')">
                    
                    <span class="badge bg-<?= $badgeColor ?> position-absolute top-0 start-0 m-3 shadow-sm px-2 py-1">
                        Fase: <?= htmlspecialchars($ev['fase_evidencia']) ?>
                    </span>

                    <?php if (!empty($ev['estado_equipo'])): ?>
                    <span class="badge bg-<?= $ev['estado_equipo'] === 'Operativo' ? 'success' : 'danger' ?> position-absolute top-0 end-0 m-3 shadow-sm">
                        <?= htmlspecialchars($ev['estado_equipo']) ?>
                    </span>
                    <?php endif; ?>
                </div>

                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Información del Ticket y Tienda -->
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <a href="<?= APP_URL ?>/ticket/detalle/<?= $ev['id_ticket'] ?>" class="fw-bold text-decoration-none small">
                                <?= htmlspecialchars($ev['codigo_ticket'] ?? ('#' . $ev['id_ticket'])) ?>
                            </a>
                            <span class="text-xs text-muted">
                                <?= date('d/m/Y H:i', strtotime($ev['fecha_registro'])) ?>
                            </span>
                        </div>
                        
                        <div class="fw-semibold text-dark text-truncate small mb-1" title="<?= htmlspecialchars($ev['ticket_asunto'] ?? '') ?>">
                            <?= htmlspecialchars($ev['ticket_asunto'] ?? 'Incidencia') ?>
                        </div>

                        <div class="text-xs text-muted mb-2">
                            <i class="bi bi-shop text-primary me-1"></i><?= htmlspecialchars($ev['nombre_tienda'] ?? 'Tienda') ?> (<?= htmlspecialchars($ev['nombre_distrito'] ?? '') ?>)
                        </div>

                        <!-- Descripción de la Evidencia -->
                        <p class="card-text small text-secondary bg-light p-2 rounded-3 mb-2" style="font-size: 0.8rem; min-height: 48px;">
                            <?= htmlspecialchars($ev['descripcion'] ?? 'Sin descripción.') ?>
                        </p>
                    </div>

                    <div class="border-top pt-2 mt-2 d-flex justify-content-between align-items-center">
                        <small class="text-muted text-truncate" style="font-size: 0.75rem; max-width: 140px;">
                            <i class="bi bi-person me-1 text-primary"></i><?= htmlspecialchars($ev['usuario']) ?>
                        </small>
                        <div class="btn-group">
                            <a href="<?= APP_URL ?>/ticket/detalle/<?= $ev['id_ticket'] ?>" class="btn btn-xs btn-outline-primary rounded-pill px-2" title="Ver Detalle del Ticket" style="font-size: 0.75rem;">
                                Ticket
                            </a>
                            <a href="<?= APP_URL ?>/ticket/reporte/<?= $ev['id_ticket'] ?>" target="_blank" class="btn btn-xs btn-outline-dark rounded-pill px-2 ms-1" title="Ver Reporte Técnico" style="font-size: 0.75rem;">
                                <i class="bi bi-printer"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal para ver foto ampliada -->
<div class="modal fade" id="modalFotoGaleria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border-0 rounded-4 overflow-hidden">
            <div class="modal-header border-0 text-white">
                <h6 class="modal-title fw-bold" id="modalFotoTitulo">Evidencia Fotográfica</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="modalFotoSrc" src="" alt="Evidencia" class="img-fluid" style="max-height: 80vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<script>
function verFotoModal(src, titulo) {
    const img = document.getElementById('modalFotoSrc');
    const title = document.getElementById('modalFotoTitulo');
    if (img) img.src = src;
    if (title && titulo) title.innerText = titulo;
    const modalEl = document.getElementById('modalFotoGaleria');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}
</script>

<style>
.text-xs { font-size: 0.75rem; }
</style>

<?php require_once '../app/Views/layout/footer.php'; ?>

