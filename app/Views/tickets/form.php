<?php require_once '../app/Views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0 text-dark">Registrar Nuevo Ticket</h3>
    <a href="<?= APP_URL ?>/ticket" class="btn btn-outline-secondary shadow-sm"><i class="bi bi-arrow-left me-2"></i>Volver</a>
</div>





<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-4">
        <form action="<?= APP_URL ?>/ticket/crear" method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="asunto" class="form-label fw-semibold">Asunto del Problema</label>
                    <input type="text" class="form-control bg-light border-0" id="asunto" name="asunto" required placeholder="Ej. La pantalla de mi equipo parpadea">
                </div>
                <div class="col-md-4">
                    <label for="id_categoria" class="form-label fw-semibold">Categoría</label>
                    <select class="form-select bg-light border-0" id="id_categoria" name="id_categoria">
                        <option value="auto" selected>Clasificación Automática (Machine Learning)</option>
                        <?php foreach($categorias as $c): ?>
                            <option value="<?= $c['id_categoria'] ?>"><?= htmlspecialchars($c['nombre_categoria']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Tienda / Local -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tienda / Local</label>
                    <?php if ($_SESSION['user_role'] === 'Empleado'): ?>
                        <!-- Si es Encargado de Tienda: BLOQUEADO Y FIJO a su sede asignada -->
                        <div class="input-group">
                            <span class="input-group-text bg-white border-0 text-primary"><i class="bi bi-shop"></i></span>
                            <input type="text" class="form-control bg-light border-0 fw-bold text-dark" 
                                   value="<?= htmlspecialchars($_SESSION['user_tienda_nombre'] ?? 'Mi Tienda') ?> (<?= htmlspecialchars($_SESSION['user_distrito_nombre'] ?? 'Sede') ?>)" 
                                   readonly>
                        </div>
                        <input type="hidden" name="id_tienda" value="<?= $_SESSION['user_tienda_id'] ?? '' ?>">
                        <small class="text-success"><i class="bi bi-shield-check me-1"></i>Asignado automáticamente a tu tienda.</small>
                    <?php else: ?>
                        <!-- Administrador o Soporte pueden seleccionar la tienda -->
                        <select class="form-select bg-light border-0" id="id_tienda" name="id_tienda">
                            <option value="">Oficina Central / No especificado</option>
                            <?php foreach($tiendas as $t): ?>
                                <option value="<?= $t['id_tienda'] ?>"><?= htmlspecialchars($t['nombre_tienda']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <!-- Prioridad -->
                <div class="col-md-4">
                    <label for="prioridad" class="form-label fw-semibold">Prioridad Inicial</label>
                    <select class="form-select bg-light border-0" id="prioridad" name="prioridad" required onchange="toggleMotivoUrgencia()">
                        <option value="Baja">🟢 Baja (No interrumpe operaciones)</option>
                        <option value="Media" selected>🟡 Media (Afecta parcialmente actividades)</option>
                        <option value="Alta">🔴 Alta (Afecta operación importante / ventas)</option>
                        <option value="Crítica">🚨 Crítica (Paraliza tienda - Requiere justificación)</option>
                    </select>
                </div>

                <!-- Modalidad de Atención -->
                <div class="col-md-4">
                    <label for="tipo_atencion" class="form-label fw-semibold">Modalidad de Soporte</label>
                    <select class="form-select bg-light border-0" id="tipo_atencion" name="tipo_atencion">
                        <option value="Remota" selected>💻 Atención Remota (AnyDesk / RDP / Inmediato)</option>
                        <option value="Presencial">🚗 Atención Presencial</option>
                    </select>
                </div>

                <!-- Campo Justificación Obligatoria para Prioridad Crítica -->
                <div class="col-12 d-none" id="bloque_motivo_urgencia">
                    <div class="p-3 bg-danger bg-opacity-10 rounded-3 border border-danger border-opacity-25">
                        <label for="motivo_urgencia" class="form-label fw-bold text-danger">
                            <i class="bi bi-exclamation-octagon-fill me-1"></i>Justificación obligatoria para prioridad Crítica:
                        </label>
                        <input type="text" class="form-control border-danger" id="motivo_urgencia" name="motivo_urgencia" 
                               placeholder="Explique qué operación esencial de la tienda está completamente paralizada...">
                        <small class="text-danger">Las incidencias críticas alertan a la jefatura de soporte técnico de inmediato.</small>
                    </div>
                </div>
                <!-- ¿Desde cuándo presenta la falla? -->
                <div class="col-12">
                    <label for="desde_cuando_falla" class="form-label fw-semibold">
                        <i class="bi bi-clock-history me-1 text-warning"></i>¿Desde cuándo presenta la falla?
                    </label>
                    <input type="text" class="form-control bg-light border-0" id="desde_cuando_falla" name="desde_cuando_falla" 
                           placeholder="Ej. Desde la apertura del turno, Hace 1 hora, Al reiniciar el sistema..." required>
                    <small class="text-muted">Indicar el momento en que se detectó la avería.</small>
                </div>

                <div class="col-12">
                    <label for="descripcion" class="form-label fw-semibold">Descripción Detallada</label>
                    <textarea class="form-control bg-light border-0" id="descripcion" name="descripcion" rows="5" required placeholder="Describe paso a paso lo que ocurre con el equipo o sistema..."></textarea>
                </div>

                <!-- Evidencias Fotográficas ANTES de la atención -->
                <div class="col-12">
                    <div class="p-3 bg-light rounded-3 border">
                        <label for="evidencia_antes" class="form-label fw-bold text-dark">
                            <i class="bi bi-camera-fill me-2 text-primary"></i>Fotografía de la Incidencia (Evidencia ANTES de la Atención)
                        </label>
                        <input type="file" class="form-control bg-white" id="evidencia_antes" name="evidencia_antes" accept="image/*" onchange="previsualizarFoto(this)">
                        <div class="form-text mt-1 text-muted">
                            <i class="bi bi-shield-check me-1"></i>Adjunte una foto del mensaje de error, luces del equipo, cables o problema visual presentado en tienda.
                        </div>
                        <div id="preview_container" class="mt-3 d-none">
                            <span class="badge bg-primary mb-1">Vista previa de la evidencia inicial:</span><br>
                            <img id="img_preview" src="" alt="Vista previa de evidencia" class="img-thumbnail rounded shadow-sm" style="max-height: 220px;">
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-check form-switch p-3 bg-light rounded-3 border">
                        <input class="form-check-input ms-0 me-3" type="checkbox" id="escalar_no_resuelto" name="escalar_no_resuelto" value="1">
                        <label class="form-check-label fw-semibold" for="escalar_no_resuelto">
                            <i class="bi bi-arrow-up-circle-fill text-warning me-1"></i> Escalar automáticamente si no se resuelve dentro del SLA
                        </label>
                        <div class="form-text ms-1 mt-1">Si el ticket no es resuelto a tiempo, se escalará a Soporte Nivel 2.</div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="text-end">
                <button type="reset" class="btn btn-light me-2 fw-bold">Limpiar</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Registrar Ticket con Evidencia</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleMotivoUrgencia() {
    const sel = document.getElementById('prioridad');
    const bloque = document.getElementById('bloque_motivo_urgencia');
    const input = document.getElementById('motivo_urgencia');
    if (sel && bloque) {
        if (sel.value === 'Crítica') {
            bloque.classList.remove('d-none');
            if (input) input.required = true;
        } else {
            bloque.classList.add('d-none');
            if (input) {
                input.required = false;
                input.value = '';
            }
        }
    }
}

function previsualizarFoto(input) {
    const previewContainer = document.getElementById('preview_container');
    const previewImg = document.getElementById('img_preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.classList.add('d-none');
        previewImg.src = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleMotivoUrgencia();
});
</script>

<?php require_once '../app/Views/layout/footer.php'; ?>
