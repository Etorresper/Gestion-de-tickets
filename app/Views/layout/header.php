<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Incidencias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= APP_URL ?>/assets/css/custom.css" rel="stylesheet">
    <script>window.APP_URL = '<?= str_replace(' ', '%20', APP_URL) ?>';</script>
    <style>
        /* Ajustes para móviles */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                z-index: 1050;
                left: -100%;
                transition: 0.3s;
                height: 100vh;
                width: 250px !important;
            }
            .sidebar.show {
                left: 0;
            }
            .main-content {
                padding-top: 60px !important;
            }
            .mobile-header {
                display: flex !important;
            }
        }
        .mobile-header {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: #212529;
            z-index: 1040;
            padding: 0 15px;
            align-items: center;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <?php if(isset($_SESSION['user_id'])): ?>
    
    <!-- Header para Móvil -->
    <div class="mobile-header shadow-sm text-white">
        <button class="btn text-white p-0" id="toggleSidebar">
            <i class="bi bi-list fs-2"></i>
        </button>
        <span class="fw-bold d-flex align-items-center">
            <i class="bi bi-shield-check text-primary fs-4 me-2"></i>
            HelpDesk TI
        </span>
        <div class="dropdown">
            <a href="#" class="text-white" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle fs-3"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                <li><a class="dropdown-item" href="<?= APP_URL ?>/autenticacion/logout">Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark sidebar" id="sidebarMenu">
                <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                    <a href="<?= APP_URL ?>/dashboard" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none mt-3">
                        <i class="bi bi-shield-check fs-3 me-2 text-primary"></i>
                        <span class="fs-5 d-none d-sm-inline fw-bold text-white">HelpDesk TI</span>
                    </a>
                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start w-100" id="menu">
                        <li class="nav-item w-100 mb-1">
                            <a href="<?= APP_URL ?>/dashboard" class="nav-link px-3 text-white">
                                <i class="fs-5 bi-speedometer2"></i> <span class="ms-2 d-none d-sm-inline">Escritorio</span>
                            </a>
                        </li>
                        <li class="nav-item w-100 mb-1">
                            <a href="<?= APP_URL ?>/ticket/index" class="nav-link px-3 text-white">
                                <i class="fs-5 bi-ticket-perforated"></i> <span class="ms-2 d-none d-sm-inline">Tickets</span>
                            </a>
                        </li>
                        <li class="nav-item w-100 mb-1">
                            <a href="<?= APP_URL ?>/evidencia/index" class="nav-link px-3 text-white">
                                <i class="fs-5 bi-camera-reels"></i> <span class="ms-2 d-none d-sm-inline">Evidencias</span>
                            </a>
                        </li>
                        <?php if($_SESSION['user_role'] !== 'Empleado'): ?>
                        <li class="nav-item w-100 mb-1">
                            <a href="<?= APP_URL ?>/analitica/index" class="nav-link px-3 text-white">
                                <i class="fs-5 bi-graph-up"></i> <span class="ms-2 d-none d-sm-inline">Analítica</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($_SESSION['user_role'] === 'Administrador'): ?>
                        <li class="nav-item w-100 mb-1">
                            <a href="<?= APP_URL ?>/usuario/index" class="nav-link px-3 text-white">
                                <i class="fs-5 bi-people"></i> <span class="ms-2 d-none d-sm-inline">Usuarios</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <hr class="w-100 border-secondary d-none d-md-block">
                    <div class="dropdown pb-4 w-100 d-none d-md-block">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle px-3" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fs-4 bi-person-circle"></i>
                            <span class="d-none d-sm-inline mx-2"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                            <li><h6 class="dropdown-header"><?= $_SESSION['user_role'] ?></h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/autenticacion/logout">Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col py-3 px-4 main-content">
                <!-- Topbar (Hidden on Mobile, replaced by mobile-header) -->
                <div class="d-none d-md-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h2 class="h4 text-secondary"><span class="text-dark fw-bold">Panel</span> de Control</h2>
                    <div class="input-group w-25">
                        <input type="text" class="form-control bg-white border-0 shadow-sm" id="quickSearch" placeholder="Buscar ticket..." value="<?= $_GET['q'] ?? '' ?>">
                        <button class="btn btn-primary shadow-sm" type="button" id="btnQuickSearch"><i class="bi bi-search"></i></button>
                    </div>
                </div>

                <?php if (isset($_SESSION['alerta'])): ?>
                    <div class="alert alert-<?= $_SESSION['alerta']['tipo'] ?> alert-dismissible fade show shadow-sm border-0" role="alert">
                        <i class="bi <?= $_SESSION['alerta']['tipo'] == 'warning' ? 'bi-exclamation-triangle' : 'bi-info-circle' ?> me-2"></i>
                        <?= $_SESSION['alerta']['mensaje'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['alerta']); ?>
                <?php endif; ?>
    <?php else: ?>
    <!-- Layout para Login u otras pantallas sin sesión -->
    <div class="container mt-5">
    <?php endif; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Sidebar en móvil
        const btnToggle = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebarMenu');
        if (btnToggle && sidebar) {
            btnToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }

        // Lógica de búsqueda rápida
        const quickSearch = document.getElementById('quickSearch');
        const btnSearch = document.getElementById('btnQuickSearch');
        
        const doSearch = () => {
            const query = quickSearch.value.trim();
            if (query) {
                window.location.href = window.APP_URL + '/ticket/index?q=' + encodeURIComponent(query);
            }
        };

        if (quickSearch) {
            quickSearch.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') doSearch();
            });
        }
        if (btnSearch) {
            btnSearch.addEventListener('click', doSearch);
        }
    });
    </script>
</body>
