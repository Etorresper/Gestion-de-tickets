<?php require_once '../app/Views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark">Analítica Avanzada</h3>
        <p class="text-muted mb-0">Visualiza métricas en tiempo real del desempeño de soporte.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Carga por Técnico -->
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0 h-100 rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-person-workspace me-2"></i>Carga de Trabajo por Técnico (Pendientes)</h6>
            </div>
            <div class="card-body">
                <canvas id="techChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Tickets por Categoría -->
    <div class="col-12 col-md-6 col-lg-5">
        <div class="card shadow-sm border-0 h-100 rounded-3 overflow-hidden">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-pie-chart-fill me-2"></i>Distribución por Categoría</h6>
            </div>
            <div class="card-body d-flex justify-content-center">
                <canvas id="catChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Cumplimiento SLA -->
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-3 bg-gradient-primary text-white" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="fw-bold mb-1"><i class="bi bi-shield-check me-2"></i>Estado de Cumplimiento SLA</h5>
                        <p class="small opacity-75 mb-0">Porcentaje de tickets resueltos o pendientes dentro del tiempo estipulado.</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <h2 class="fw-bold mb-0" id="sla-percentage">0%</h2>
                        <span class="text-xs text-uppercase opacity-75">Meta: 95%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Tickets por Tienda -->
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-shop me-2"></i>Tickets por Tienda</h6>
            </div>
            <div class="card-body">
                <canvas id="storeChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Tickets por Distrito -->
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-geo-alt me-2"></i>Incidencias por Distrito</h6>
            </div>
            <div class="card-body">
                <canvas id="districtChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>


<!-- Actividad Reciente -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-clock-history me-2"></i>Feed de Actividad Reciente</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Ticket</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Detalle</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentHistory as $h): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-primary">
                                    <a href="<?= APP_URL ?>/ticket/detail/<?= $h['id_ticket'] ?>" class="text-decoration-none"><?= htmlspecialchars($h['codigo_ticket']) ?></a>
                                </td>
                                <td><?= htmlspecialchars($h['usuario']) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($h['accion']) ?></span></td>
                                <td class="text-muted small"><?= htmlspecialchars($h['detalle'] ?? '') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($h['fecha_accion'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentHistory)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No hay actividad reciente registrada.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts for Charts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data from PHP
    const catDataRaw = <?= json_encode($categoryData) ?>;
    const techDataRaw = <?= json_encode($technicianData) ?>;

    // Process Category Data
    const catLabels = catDataRaw.map(item => item.label);
    const catTotals = catDataRaw.map(item => item.total);
    const catColors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0'];

    if(document.getElementById('catChart') && catLabels.length > 0) {
        new Chart(document.getElementById('catChart'), {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catTotals,
                    backgroundColor: catColors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });
    }

    // Process Technician Data
    const techLabels = techDataRaw.map(item => item.label);
    const techTotals = techDataRaw.map(item => item.total);

    if(document.getElementById('techChart') && techLabels.length > 0) {
        new Chart(document.getElementById('techChart'), {
            type: 'bar',
            data: {
                labels: techLabels,
                datasets: [{
                    label: 'Tickets Asignados',
                    data: techTotals,
                    backgroundColor: '#6f42c1',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    // Datos de Tiendas (Barras)
    const storeDataRaw = <?= json_encode($storeData) ?>;
    if(document.getElementById('storeChart') && storeDataRaw.length > 0) {
        new Chart(document.getElementById('storeChart'), {
            type: 'bar',
            data: {
                labels: storeDataRaw.map(item => item.label),
                datasets: [{
                    label: 'Tickets por Tienda',
                    data: storeDataRaw.map(item => item.total),
                    backgroundColor: '#0d6efd',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }

    // Datos de Distritos (Torta/Doughnut)
    const districtDataRaw = <?= json_encode($districtData) ?>;
    if(document.getElementById('districtChart') && districtDataRaw.length > 0) {
        new Chart(document.getElementById('districtChart'), {
            type: 'doughnut',
            data: {
                labels: districtDataRaw.map(item => item.label),
                datasets: [{
                    data: districtDataRaw.map(item => item.total),
                    backgroundColor: ['#20c997', '#0dcaf0', '#6610f2', '#e83e8c', '#fd7e14', '#ffc107'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });
    }


    // SLA Calculation
    const slaData = <?= json_encode($slaData) ?>;
    const aTiempo = parseInt(slaData.a_tiempo || 0);
    const fueraTiempo = parseInt(slaData.fuera_tiempo || slaData.vencidos || 0);
    const totalSla = aTiempo + fueraTiempo;
    if (totalSla > 0) {
        const percent = Math.round((aTiempo / totalSla) * 100);
        document.getElementById('sla-percentage').innerText = percent + '%';
    }
});
</script>

<?php require_once '../app/Views/layout/footer.php'; ?>
