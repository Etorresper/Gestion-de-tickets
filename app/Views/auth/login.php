<?php require_once '../app/Views/layout/header.php'; ?>

<div class="row justify-content-center align-items-center min-vh-100 mt-n5 bg-light">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

            <div class="card-header bg-dark text-white text-center py-4 border-0">
                <i class="bi bi-shield-check fs-1 text-primary"></i>
                <h3 class="mb-0 mt-2 fw-bold">HelpDesk TI</h3>
                <p class="text-white-50 mb-0 small">Sistema de Gestión de Incidencias</p>
            </div>

            <div class="card-body p-5">

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger shadow-sm border-0">
                        <i class="bi bi-exclamation-octagon me-2"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= APP_URL ?>/autenticacion/login" method="POST" autocomplete="off">

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control bg-light" id="email" name="email"
                            placeholder="Correo Electrónico" autocomplete="off" required>
                        <label for="email" class="text-muted">
                            Correo Electrónico
                        </label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" class="form-control bg-light" id="password" name="password"
                            placeholder="Contraseña" autocomplete="new-password" required>
                        <label for="password" class="text-muted">
                            Contraseña
                        </label>
                    </div>

                    <div class="d-grid mt-4">
                        <button class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm py-3" type="submit">
                            Ingresar al Sistema
                        </button>
                    </div>

                    <div class="text-center mt-4 pt-2 border-top">
                        <small class="text-muted" style="font-size: 0.8rem;">
                            <i class="bi bi-shield-lock text-primary me-1"></i>¿Olvidaste tu contraseña?<br>
                            Solicita el restablecimiento al <strong>Administrador de Sistemas</strong>.
                        </small>
                    </div>

                </form>

            </div>

            <div class="card-footer text-center py-3 bg-white border-0">
                <small class="text-muted">
                    Desarrollado por TicketsApp
                </small>
            </div>

        </div>
    </div>
</div>

<?php require_once '../app/Views/layout/footer.php'; ?>