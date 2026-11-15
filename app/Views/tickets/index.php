<?php require_once '../app/Views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0 text-dark">Listado de Tickets</h3>
    <div>
        <?php if(!empty($_GET['q'])): ?>
            <a href="<?= APP_URL ?>/ticket/index" class="btn btn-outline-secondary me-2"><i class="bi bi-x-circle me-2"></i>Limpiar Búsqueda</a>
        <?php endif; ?>
        <a href="<?= APP_URL ?>/ticket/crear" class="btn btn-primary shadow-sm"><i class="bi bi-plus-lg me-2"></i>Nuevo Ticket</a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Código</th>
                        <th>Asunto</th>
                        <th>Creación</th>
                        <th>Prioridad</th>
                        <th>SLA (Duración)</th>
                        <th>Vencimiento (SLA)</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No se encontraron tickets.</td></tr>
                    <?php else: ?>
                        <?php foreach($tickets as $t): ?>
                        <?php
                            $nombre_distrito = strtolower($t['nombre_distrito'] ?? '');
                            $zonasCentrales = ['surco', 'miraflores', 'san isidro'];
                            $tiene_bono_2h = (!empty($nombre_distrito) && !in_array($nombre_distrito, $zonasCentrales));
                            
                            $sla_base = '48h';
                            switch ($t['prioridad'] ?? 'Media') {
                                case 'Crítica': $sla_base = '4h'; break;
                                case 'Alta': $sla_base = '24h'; break;
                                case 'Media': $sla_base = '48h'; break;
                                case 'Baja': $sla_base = '72h'; break;
                            }
                        ?>
                        <tr>
                            <td class="ps-4 fw-semibold"><a href="<?= APP_URL ?>/ticket/detalle/<?= $t['id_ticket'] ?>" class="text-decoration-none"><?= $t['codigo_ticket'] ?></a></td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($t['asunto']) ?></div>
                                <div class="small">
                                    <span class="text-muted"><?= $t['nombre_categoria'] ?? 'Sin asignar' ?> • por <?= htmlspecialchars($t['solicitante'] ?? 'Sistema') ?></span>
                                    <?php if(!empty($t['tecnico_asignado'])): ?>
                                        <span class="badge bg-light text-dark border ms-1" title="Técnico Asignado"><i class="bi bi-person-check-fill text-success me-1"></i><?= htmlspecialchars($t['tecnico_asignado']) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary border ms-1"><i class="bi bi-person-x me-1"></i>Sin Asignar</span>
                                    <?php endif; ?>
                                    <?php if(!empty($t['tipo_atencion'])): ?>
                                        <span class="badge bg-light text-primary border ms-1">
                                            <i class="bi bi-<?= $t['tipo_atencion'] === 'Presencial' ? 'car-front-fill' : 'laptop' ?> me-1"></i><?= htmlspecialchars($t['tipo_atencion']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if(!empty($t['conformidad_encargado'])): ?>
                                        <span class="badge bg-success-subtle text-success border border-success ms-1"><i class="bi bi-patch-check-fill me-1"></i>Conforme</span>
                                    <?php endif; ?>
                                    <?php if(!empty($t['nombre_tienda'])): ?>
                                        <br><span class="text-primary fw-semibold"><i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($t['nombre_tienda']) ?> (<?= htmlspecialchars($t['nombre_distrito']) ?>)</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="small">
                                <?= date('d/m/Y H:i', strtotime($t['fecha_creacion'])) ?>
                            </td>
                            <td>
                                <span class="badge <?= $t['prioridad'] == 'Crítica' ? 'bg-danger' : ($t['prioridad'] == 'Alta' ? 'bg-warning text-dark' : 'bg-info text-dark') ?>">
                                    <?= $t['prioridad'] ?? 'Media' ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark"><?= $sla_base ?></span>
                                <?php if($tiene_bono_2h): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" title="Incluye 2h de traslado por distrito alejado">+2h traslado</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($t['id_estado'] == 3): ?>
                                    <?php 
                                        $fCierre = !empty($t['fecha_cierre']) ? $t['fecha_cierre'] : $t['fecha_actualizacion'];
                                        $okSla = (strtotime($fCierre) <= strtotime($t['fecha_vencimiento']));
                                    ?>
                                    <?php if ($okSla): ?>
                                        <span class="badge bg-success-subtle text-success border border-success" title="Cerrado a tiempo dentro del SLA"><i class="bi bi-check2-circle me-1"></i><?= date('d/m H:i', strtotime($t['fecha_vencimiento'])) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger" title="Cerrado fuera de plazo (con retraso)"><i class="bi bi-clock-history me-1"></i><?= date('d/m H:i', strtotime($t['fecha_vencimiento'])) ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="<?= (strtotime($t['fecha_vencimiento'] ?? '') < time()) ? 'text-danger fw-bold' : '' ?>">
                                        <?= $t['fecha_vencimiento'] ? date('d/m H:i', strtotime($t['fecha_vencimiento'])) : '-' ?>
                                        <?php if(strtotime($t['fecha_vencimiento'] ?? '') < time()): ?>
                                            <i class="bi bi-clock-fill ms-1" title="SLA vencido"></i>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge rounded-pill" style="background-color: <?= $t['color_hex'] ?>; color: #fff;">
                                    <?= $t['nombre_estado'] ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="<?= APP_URL ?>/ticket/detalle/<?= $t['id_ticket'] ?>" class="btn btn-sm btn-outline-primary shadow-sm"><i class="bi bi-eye-fill"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../app/Views/layout/footer.php'; ?>
