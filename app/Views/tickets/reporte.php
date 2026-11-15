<?php
if (!function_exists('arreglarTexto')) {
    function arreglarTexto($texto)
    {
        if (empty($texto))
            return '';

        // Corregir corrupciones comunes de AÑO / AÃO / AÃ'O
        $texto = preg_replace('/A[Ãã][^\w\s]?O/u', 'AÑO', $texto);
        $texto = preg_replace('/A[Ãã][\'’‘`\x{2018}\x{2019}\x{0091}\x{0092}\x{00B4}]?O/u', 'AÑO', $texto);

        $reemplazos = [
            "AÃ'O" => 'AÑO',
            "AÃ’O" => 'AÑO',
            "AÃ‘O" => 'AÑO',
            "AÃ±o" => 'Año',
            "AÃO" => 'AÑO',
            "Ã¡" => 'á',
            "Ã©" => 'é',
            "Ã­" => 'í',
            "Ã³" => 'ó',
            "Ãº" => 'ú',
            "Ã " => 'Á',
            "Ã‰" => 'É',
            "Ã " => 'Í',
            "Ã“" => 'Ó',
            "Ãš" => 'Ú',
            "Ã±" => 'ñ',
            "Ã‘" => 'Ñ'
        ];
        $texto = strtr($texto, $reemplazos);
        $texto = str_replace(["Ã'", "Ã‘", "Ã’"], "Ñ", $texto);

        return $texto;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Técnico <?= htmlspecialchars($ticket['codigo_ticket']) ?> - HelpDesk TI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 10mm 8mm 10mm;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-sizing: border-box;
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 0.86rem;
            line-height: 1.38;
        }

        .report-page {
            background: #ffffff;
            max-width: 860px;
            min-height: 275mm;
            margin: 20px auto;
            padding: 30px 36px 25px 36px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.08);
            border-radius: 6px;
            page-break-inside: avoid;
            break-inside: avoid;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .header-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* El logo corporativo conserva sus colores originales vivos */
        .header-logo img {
            width: 52px;
            height: 52px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header-title-main {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f2942;
            letter-spacing: 0.4px;
            line-height: 1.15;
        }

        .section-title {
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 1.5px solid #cbd5e1;
            padding-bottom: 3px;
            margin-top: 14px;
            margin-bottom: 8px;
            color: #0f2942;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .section-title i {
            color: #0284c7;
            font-size: 0.9rem;
        }

        .info-label {
            font-size: 0.68rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 0.86rem;
            font-weight: 600;
            color: #0f172a;
        }

        .card-custom {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 8px 12px;
        }

        .evidence-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #ffffff;
            padding: 8px;
            height: 100%;
        }

        .evidence-img {
            width: 100%;
            height: 175px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .no-evidence-box {
            height: 175px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1.5px dashed #cbd5e1;
            border-radius: 4px;
            background: #f8fafc;
            color: #64748b;
            font-size: 0.8rem;
        }

        /* Sección de Firmas empujada hacia la parte inferior */
        .signatures-container {
            margin-top: auto;
            padding-top: 35px;
            padding-bottom: 5px;
        }

        .signature-box {
            border-top: 1.5px dashed #94a3b8;
            padding-top: 8px;
            text-align: center;
            max-width: 250px;
            margin-left: auto;
            margin-right: auto;
            color: #1e293b;
        }

        @media print {

            html,
            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 11px !important;
                line-height: 1.32 !important;
                color: #1e293b !important;
                height: 100% !important;
            }

            .report-page {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                height: 275mm !important;
                min-height: 275mm !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .no-print {
                display: none !important;
            }

            .header-logo img {
                width: 48px !important;
                height: 48px !important;
            }

            .header-title-main {
                font-size: 1.18rem !important;
                color: #0f2942 !important;
            }

            .section-title {
                font-size: 0.8rem !important;
                margin-top: 12px !important;
                margin-bottom: 6px !important;
                padding-bottom: 2px !important;
                border-bottom: 1.5px solid #cbd5e1 !important;
                color: #0f2942 !important;
            }

            .info-label {
                font-size: 0.65rem !important;
                color: #64748b !important;
            }

            .info-value {
                font-size: 0.82rem !important;
                color: #0f172a !important;
            }

            .evidence-img,
            .no-evidence-box {
                height: 170px !important;
            }

            .signatures-container {
                margin-top: auto !important;
                padding-top: 40px !important;
            }

            .signature-box {
                border-top: 1.5px dashed #94a3b8 !important;
                padding-top: 6px !important;
            }
        }
    </style>
</head>

<body>

    <!-- Barra de Controles en Pantalla -->
    <div class="container text-center my-3 no-print">
        <div class="btn-group shadow-sm">
            <a href="<?= APP_URL ?>/ticket/detalle/<?= $ticket['id_ticket'] ?>" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Volver al Detalle
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold px-4"
                style="background-color: #0f2942; border-color: #0f2942;">
                <i class="bi bi-printer-fill me-2"></i>Imprimir / Guardar en PDF (1 Hoja)
            </button>
        </div>
    </div>

    <div class="report-page">
        <!-- Contenido Superior del Reporte -->
        <div class="report-content-body">
            <!-- Encabezado Corporativo del Reporte -->
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2"
                style="border-color: #cbd5e1 !important;">
                <div class="header-logo">
                    <img src="<?= APP_URL ?>/assets/img/logo_vega.png" alt="Logo Corporativo">
                    <div>
                        <div class="header-title-main">INFORME TÉCNICO DE SOPORTE</div>
                        <div class="small fw-semibold text-secondary" style="font-size:0.76rem;">Área de Soporte Técnico
                            & Sistemas TI • Sede Central</div>
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge px-3 py-1 fw-bold"
                        style="background-color: #0f2942; color: #ffffff; font-size: 0.88rem;">
                        <?= htmlspecialchars($ticket['codigo_ticket']) ?>
                    </span>
                    <div class="text-muted mt-1 fw-semibold" style="font-size: 0.72rem;">Emisión:
                        <?= date('d/m/Y H:i') ?>
                    </div>
                </div>
            </div>

            <!-- Barra de Asunto y Estado Compacta -->
            <div class="d-flex justify-content-between align-items-center px-3 py-2 rounded mb-2"
                style="background-color: #f8fafc; border: 1px solid #cbd5e1; font-size: 0.82rem;">
                <div>
                    <strong style="color: #0f2942;">ASUNTO:</strong> <span
                        class="fw-semibold text-dark"><?= htmlspecialchars(arreglarTexto($ticket['asunto'])) ?></span>
                </div>
                <div>
                    <span class="badge rounded-pill px-3 py-1 fw-bold"
                        style="background-color: #e2e8f0; color: #0f2942; border: 1px solid #cbd5e1;">
                        Estado: <?= htmlspecialchars($ticket['nombre_estado']) ?>
                    </span>
                </div>
            </div>

            <!-- 1. Información General del Ticket y Ubicación -->
            <?php
            $nombreTiendaLimpio = arreglarTexto($ticket['nombre_tienda'] ?? 'Sede');
            $solicitanteRaw = arreglarTexto($ticket['solicitante'] ?? '');

            // Si el ticket fue creado por la cuenta de TI 'Admin Sistema' o 'Administrador' central,
            // no ponemos 'Admin Sistema' en la tienda, sino la referencia directa de la tienda.
            if (empty($solicitanteRaw) || stripos($solicitanteRaw, 'Admin Sistema') !== false || strcasecmp($solicitanteRaw, 'Administrador') === 0) {
                $nombreAdminMostrado = 'Administrador - ' . $nombreTiendaLimpio;
                $textoFirmaTienda = 'Firma y Sello de Recepción';
            } else {
                $nombreAdminMostrado = preg_replace('/^Encargado\s*[-–—:]?\s*/iu', 'Administrador - ', $solicitanteRaw);
                $textoFirmaTienda = $nombreAdminMostrado;
            }
            ?>
            <div class="section-title"><i class="bi bi-info-circle"></i> 1. Datos de la Incidencia y Tienda</div>
            <div class="row g-2 mb-2">
                <div class="col-4">
                    <div class="info-label">Sede / Tienda</div>
                    <div class="info-value"><?= htmlspecialchars($nombreTiendaLimpio) ?></div>
                    <small class="text-muted"
                        style="font-size: 0.74rem;"><?= htmlspecialchars(arreglarTexto($ticket['nombre_distrito'] ?? 'Lima')) ?></small>
                </div>
                <div class="col-4">
                    <div class="info-label">Administrador de Tienda</div>
                    <div class="info-value"><?= htmlspecialchars($nombreAdminMostrado) ?></div>
                </div>
                <div class="col-4">
                    <div class="info-label">Especialista Responsable</div>
                    <div class="info-value fw-bold" style="color: #0f2942;">
                        <?= htmlspecialchars($asignacion_actual['tecnico_nombre'] ?? 'Sin asignar') ?>
                    </div>
                </div>
                <div class="col-4">
                    <div class="info-label">Categoría / Área</div>
                    <div class="info-value"><?= htmlspecialchars($ticket['nombre_categoria'] ?? 'General') ?></div>
                </div>
                <div class="col-4">
                    <div class="info-label">Prioridad / Modalidad</div>
                    <div class="info-value">
                        <span class="badge"
                            style="background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; font-size:0.72rem;">
                            <?= htmlspecialchars($ticket['prioridad']) ?>
                        </span>
                        <span class="text-secondary ms-1 fw-semibold"
                            style="font-size:0.76rem;">(<?= htmlspecialchars($ticket['tipo_atencion'] ?? 'Presencial') ?>)</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="info-label">Fecha de Cierre (SLA)</div>
                    <div class="info-value fw-bold" style="color: #0f2942;">
                        <?= !empty($ticket['fecha_cierre']) ? date('d/m/Y H:i', strtotime($ticket['fecha_cierre'])) : date('d/m/Y H:i') ?>
                    </div>
                </div>
                <div class="col-12 mt-1">
                    <div class="info-label">Detalle del Requerimiento / Falla Reportada</div>
                    <div class="card-custom text-dark"
                        style="font-size: 0.8rem; min-height: 38px; background-color: #ffffff;">
                        <?= htmlspecialchars(arreglarTexto($ticket['descripcion'])) ?>
                    </div>
                </div>
            </div>

            <!-- 2. Información del Activo / Equipo Afectado -->
            <div class="section-title"><i class="bi bi-cpu"></i> 2. Ficha Técnica del Equipo Intervenido</div>
            <div class="row g-2 card-custom mx-0" style="font-size: 0.8rem; background-color: #ffffff;">
                <div class="col-3">
                    <span class="info-label d-block">Tipo / Equipo:</span>
                    <strong><?= htmlspecialchars(!empty($ticket['equipo_nombre']) ? $ticket['equipo_nombre'] : 'No registrado') ?></strong>
                </div>
                <div class="col-3">
                    <span class="info-label d-block">Marca / Modelo:</span>
                    <strong><?= htmlspecialchars(!empty($ticket['equipo_modelo']) ? $ticket['equipo_modelo'] : 'Estándar') ?></strong>
                </div>
                <div class="col-3">
                    <span class="info-label d-block">N° Serie (S/N):</span>
                    <strong><?= htmlspecialchars(!empty($ticket['equipo_serie']) ? $ticket['equipo_serie'] : 'S/N') ?></strong>
                </div>
                <div class="col-3">
                    <span class="info-label d-block">Estado Final:</span>
                    <span class="badge"
                        style="background-color: #f1f5f9; color: #1e293b; border: 1px solid #cbd5e1; font-size:0.74rem;">
                        <?= htmlspecialchars($ticket['estado_final_equipo'] ?? 'Operativo') ?>
                    </span>
                </div>
            </div>

            <!-- 3. Galería de Evidencias Fotográficas: Exclusivo ANTES y DESPUÉS -->
            <?php
            $evAntes = null;
            $evDespues = null;

            if (!empty($evidencias)) {
                foreach ($evidencias as $ev) {
                    if ($ev['fase_evidencia'] === 'Antes' && !$evAntes) {
                        $evAntes = $ev;
                    } elseif ($ev['fase_evidencia'] === 'Después' && !$evDespues) {
                        $evDespues = $ev;
                    }
                }
                if (!$evAntes && isset($evidencias[0])) {
                    $evAntes = $evidencias[0];
                }
                if (!$evDespues && count($evidencias) > 1) {
                    $evDespues = end($evidencias);
                }
            }
            ?>
            <div class="section-title"><i class="bi bi-camera"></i> 3. Evidencias Fotográficas de la Intervención</div>
            <div class="row g-3">
                <!-- Columna ANTES -->
                <div class="col-6">
                    <div class="evidence-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge px-2 py-1"
                                style="background-color: #334155; color: #ffffff; font-size:0.72rem;">
                                <i class="bi bi-camera me-1"></i>FASE: ANTES (Falla Reportada)
                            </span>
                            <?php if ($evAntes): ?>
                                <span class="text-muted fw-semibold"
                                    style="font-size:0.72rem;"><?= date('d/m H:i', strtotime($evAntes['fecha_registro'])) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($evAntes && !empty($evAntes['ruta_archivo'])): ?>
                            <img src="<?= APP_URL . '/' . htmlspecialchars($evAntes['ruta_archivo']) ?>"
                                class="evidence-img" alt="Evidencia Antes">
                            <div class="text-secondary mt-2 text-truncate fw-semibold" style="font-size: 0.74rem;"
                                title="<?= htmlspecialchars($evAntes['descripcion'] ?? '') ?>">
                                <?= htmlspecialchars(!empty($evAntes['descripcion']) ? $evAntes['descripcion'] : 'Estado inicial del equipo al reportar la incidencia.') ?>
                            </div>
                        <?php else: ?>
                            <div class="no-evidence-box">
                                <i class="bi bi-image fs-1 mb-2 opacity-50"></i>
                                <span>Sin foto previa registrada</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Columna DESPUÉS -->
                <div class="col-6">
                    <div class="evidence-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge px-2 py-1"
                                style="background-color: #0f2942; color: #ffffff; font-size:0.72rem;">
                                <i class="bi bi-check2-circle me-1"></i>FASE: DESPUÉS (Solución y Operatividad)
                            </span>
                            <?php if ($evDespues): ?>
                                <span class="text-muted fw-semibold"
                                    style="font-size:0.72rem;"><?= date('d/m H:i', strtotime($evDespues['fecha_registro'])) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($evDespues && !empty($evDespues['ruta_archivo'])): ?>
                            <img src="<?= APP_URL . '/' . htmlspecialchars($evDespues['ruta_archivo']) ?>"
                                class="evidence-img" alt="Evidencia Después">
                            <div class="text-secondary mt-2 text-truncate fw-semibold" style="font-size: 0.74rem;"
                                title="<?= htmlspecialchars($evDespues['descripcion'] ?? '') ?>">
                                <?= htmlspecialchars(!empty($evDespues['descripcion']) ? $evDespues['descripcion'] : 'Equipo reparado, configurado y operativo en tienda.') ?>
                            </div>
                        <?php else: ?>
                            <div class="no-evidence-box">
                                <i class="bi bi-image fs-1 mb-2 opacity-50"></i>
                                <span>Sin foto de solución registrada</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 4. Conformidad de Cierre de la Administración -->
            <div class="section-title"><i class="bi bi-award"></i> 4. Conformidad del Administrador de Tienda</div>
            <?php if (!empty($ticket['conformidad_encargado'])): ?>
                <div class="card-custom" style="font-size: 0.8rem; background-color: #ffffff;">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong style="color: #0f2942;"><i class="bi bi-check-circle-fill text-success me-1"></i>SOLUCIÓN
                            CONFORME Y VERIFICADA EN SEDE</strong>
                        <span
                            class="text-muted fw-semibold"><?= !empty($ticket['fecha_conformidad']) ? date('d/m/Y H:i', strtotime($ticket['fecha_conformidad'])) : '' ?></span>
                    </div>
                    <div class="text-secondary fst-italic mt-1">
                        "<?= htmlspecialchars(arreglarTexto($ticket['comentario_conformidad'] ?? 'El equipo quedó operativo y funcionando con total normalidad.')) ?>"
                    </div>
                </div>
            <?php else: ?>
                <div class="card-custom text-secondary fst-italic" style="font-size: 0.78rem; background-color: #ffffff;">
                    <i class="bi bi-hourglass-split me-1"></i>Atención técnica finalizada. La conformidad de operatividad ha
                    sido constatada por el especialista de soporte.
                </div>
            <?php endif; ?>
        </div>

        <!-- 5. Firmas (Ubicadas en la parte inferior de la hoja) -->
        <div class="signatures-container">
            <div class="row">
                <div class="col-6">
                    <div class="signature-box">
                        <div class="fw-bold" style="font-size: 0.84rem; color: #0f2942;">
                            <?= htmlspecialchars($asignacion_actual['tecnico_nombre'] ?? 'Técnico Especialista TI') ?>
                        </div>
                        <div class="text-secondary" style="font-size: 0.74rem;">Soporte Técnico de Sistemas<br>Área de
                            Sistemas TI </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="signature-box">
                        <div class="fw-bold" style="font-size: 0.84rem; color: #0f2942;">
                            <?= htmlspecialchars($textoFirmaTienda) ?>
                        </div>
                        <div class="text-secondary" style="font-size: 0.74rem;">Administrador de Tienda<br>Sede
                            <?= htmlspecialchars($nombreTiendaLimpio) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>