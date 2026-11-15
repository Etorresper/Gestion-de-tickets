<?php require_once '../app/Views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0 text-dark"><?= isset($usuario) ? 'Editar Usuario' : 'Registrar Nuevo Usuario' ?></h3>
    <a href="<?= APP_URL ?>/usuario/index" class="btn btn-outline-secondary shadow-sm"><i class="bi bi-arrow-left me-2"></i>Volver</a>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-4">
        <form action="<?= isset($usuario) ? APP_URL.'/usuario/editar/'.$usuario['id_usuario'] : APP_URL.'/usuario/crear' ?>" method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nombre" class="form-label fw-semibold">Nombre Completo</label>
                    <input type="text" class="form-control bg-light border-0" id="nombre" name="nombre" value="<?= $usuario['nombre'] ?? '' ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                    <input type="email" class="form-control bg-light border-0" id="email" name="email" value="<?= $usuario['email'] ?? '' ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label fw-semibold">Contraseña <?= isset($usuario) ? '(Dejar en blanco para mantener actual)' : '' ?></label>
                    <input type="password" class="form-control bg-light border-0" id="password" name="password" <?= isset($usuario) ? '' : 'required' ?>>
                </div>
                <div class="col-md-3">
                    <label for="id_rol" class="form-label fw-semibold">Rol</label>
                    <select class="form-select bg-light border-0" id="id_rol" name="id_rol" required>
                        <?php foreach($roles as $r): ?>
                            <option value="<?= $r['id_rol'] ?>" <?= (isset($usuario) && $usuario['id_rol'] == $r['id_rol']) ? 'selected' : '' ?>><?= $r['nombre_rol'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="id_area" class="form-label fw-semibold">Área</label>
                    <select class="form-select bg-light border-0" id="id_area" name="id_area" required>
                        <?php foreach($areas as $a): ?>
                            <option value="<?= $a['id_area'] ?>" <?= (isset($usuario) && $usuario['id_area'] == $a['id_area']) ? 'selected' : '' ?>><?= $a['nombre_area'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm"><?= isset($usuario) ? 'Actualizar Usuario' : 'Registrar Usuario' ?></button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../app/Views/layout/footer.php'; ?>
