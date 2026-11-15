<?php require_once '../app/Views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0 text-dark">Gestión de Usuarios</h3>
    <a href="<?= APP_URL ?>/usuario/crear" class="btn btn-primary shadow-sm"><i class="bi bi-person-plus-fill me-2"></i>Nuevo Usuario</a>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-custom mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Área</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $u): ?>
                    <tr>
                        <td class="ps-4 fw-semibold"><?= htmlspecialchars($u['nombre']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="badge <?= $u['nombre_rol'] == 'Administrador' ? 'bg-dark' : ($u['nombre_rol'] == 'Soporte' ? 'bg-primary' : 'bg-secondary') ?>">
                                <?= $u['nombre_rol'] ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($u['nombre_area'] ?? 'N/A') ?></td>
                        <td>
                            <span class="badge <?= $u['activo'] ? 'bg-success' : 'bg-danger' ?>">
                                <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-outline-warning shadow-sm" 
                                    data-bs-toggle="modal" data-bs-target="#modalRestablecerPass"
                                    data-id="<?= $u['id_usuario'] ?>" 
                                    data-nombre="<?= htmlspecialchars($u['nombre']) ?>" 
                                    data-email="<?= htmlspecialchars($u['email']) ?>"
                                    title="Restablecer Contraseña (Solo Admin)">
                                <i class="bi bi-key-fill text-dark"></i>
                            </button>
                            <a href="<?= APP_URL ?>/usuario/editar/<?= $u['id_usuario'] ?>" class="btn btn-sm btn-outline-primary shadow-sm ms-1" title="Editar"><i class="bi bi-pencil-square"></i></a>
                            <a href="<?= APP_URL ?>/usuario/cambiar/<?= $u['id_usuario'] ?>" class="btn btn-sm <?= $u['activo'] ? 'btn-outline-warning' : 'btn-outline-success' ?> shadow-sm ms-1" title="<?= $u['activo'] ? 'Desactivar' : 'Activar' ?>">
                                <i class="bi <?= $u['activo'] ? 'bi-person-x-fill' : 'bi-person-check-fill' ?>"></i>
                            </a>
                            <a href="<?= APP_URL ?>/usuario/eliminar/<?= $u['id_usuario'] ?>" class="btn btn-sm btn-outline-danger shadow-sm ms-1" title="Eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar permanentemente a este usuario?');">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Restablecer Contraseña (Exclusivo Administrador) -->
<div class="modal fade" id="modalRestablecerPass" tabindex="-1" aria-labelledby="modalRestablecerPassLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="modalRestablecerPassLabel">
                    <i class="bi bi-key-fill text-warning me-2"></i>Restablecer Contraseña
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formRestablecerPass" action="" method="POST">
                <div class="modal-body py-3">
                    <div class="alert alert-info py-2 small border-0 rounded-3 mb-3">
                        <i class="bi bi-shield-lock-fill me-1 text-primary"></i>
                        Como <strong>Administrador</strong>, puedes definir una nueva contraseña provisional para este usuario si la ha olvidado.
                    </div>
                    
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <span class="text-muted text-uppercase d-block" style="font-size:0.75rem;">Usuario a restablecer:</span>
                        <div class="fw-bold fs-6 text-dark" id="modalUsuarioNombre">—</div>
                        <div class="text-muted small" id="modalUsuarioEmail">—</div>
                    </div>

                    <div class="mb-3">
                        <label for="inputNuevaPassword" class="form-label small fw-semibold text-dark">Nueva Contraseña para el Usuario</label>
                        <div class="input-group">
                            <input type="text" class="form-control bg-white" id="inputNuevaPassword" name="nueva_password" value="vega2026*" required minlength="4">
                            <button class="btn btn-outline-secondary" type="button" id="btnGenerarRandom" title="Generar clave aleatoria">
                                <i class="bi bi-arrow-repeat"></i> Generar
                            </button>
                        </div>
                        <div class="form-text" style="font-size: 0.75rem;">Mínimo 4 caracteres. Puedes usar la clave sugerida o escribir una personalizada.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4 shadow-sm text-dark">
                        <i class="bi bi-check2-circle me-1"></i>Guardar Nueva Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalRestablecerPass');
    if (modal) {
        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nombre = button.getAttribute('data-nombre');
            const email = button.getAttribute('data-email');
            
            document.getElementById('modalUsuarioNombre').textContent = nombre;
            document.getElementById('modalUsuarioEmail').textContent = email;
            document.getElementById('formRestablecerPass').action = '<?= APP_URL ?>/usuario/restablecerPassword/' + id;
            document.getElementById('inputNuevaPassword').value = 'vega2026*';
        });

        document.getElementById('btnGenerarRandom').addEventListener('click', function() {
            const chars = 'abcdefghjkmnpqrstuvwxyz23456789!@#';
            let pass = 'vega';
            for (let i = 0; i < 4; i++) {
                pass += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            pass += '*';
            document.getElementById('inputNuevaPassword').value = pass;
        });
    }
});
</script>

<?php require_once '../app/Views/layout/footer.php'; ?>
