# 📑 DOCUMENTACIÓN TÉCNICA Y OPERATIVA DEL SISTEMA
## HelpDesk TI — Sistema de Gestión de Incidencias, Control de SLA y Evidencias para Retail
**Entidad / Caso de Estudio:** Corporación Vega (Área de Soporte Técnico y Sistemas TI)  
**Proyecto:** Sistema Web de Mesa de Ayuda con Clasificación IA y Control de Activos  

---

## 1. RESUMEN EJECUTIVO Y JUSTIFICACIÓN DEL PROYECTO

### 1.1. Realidad Problemática
En empresas de retail y distribución con múltiples tiendas o sucursales físicas, el área de Soporte Técnico y Sistemas enfrenta constantes desafíos:
1. **Llamadas y mensajes informales:** Los encargados de tienda reportaban fallas (impresoras térmicas, balanzas, red, terminales POS) por WhatsApp o llamadas, generando desorden y pérdida de trazabilidad.
2. **Falta de métricas y SLAs:** No existía un control exacto de si los técnicos cumplían los tiempos de respuesta según la urgencia de la falla.
3. **Ausencia de control de calidad (Sin evidencias):** No había constatación de cómo se encontró el equipo (*Antes*) y cómo quedó operativo (*Después*).
4. **Falta de respaldo de auditoría:** Al cerrar las atenciones, no se generaba un acta formal firmada por la administración de la tienda.

### 1.2. Solución Desarrollada
Se implementó una plataforma web modular, segura y centralizada bajo la arquitectura **MVC (Modelo - Vista - Controlador)**, conectada a **Microsoft SQL Server 2019**, con:
* Categorización y estimación de prioridad asistida por **Inteligencia Artificial / Machine Learning**.
* Gestión estricta de **Acuerdos de Nivel de Servicio (SLA)** con alertas visuales de tiempo restante o vencimiento.
* Módulo de **Evidencias Fotográficas** pareadas (*Antes* y *Después*).
* Generador de **Informes Técnicos Ejecutivos A4 en 1 sola hoja**, listos para firma y archivo de auditoría.

---

## 2. FICHA TÉCNICA Y ARQUITECTURA DEL SOFTWARE

### 2.1. Ficha Técnica General
* **Nombre del Sistema:** Sistema de Gestión de Incidencias TI & Control de SLA.
* **Versión:** 2.0 (Edición Corporativa).
* **Base de Datos:** Microsoft SQL Server 2019 / Express / Developer Edition.
* **Lenguaje Backend:** PHP 8.x (Programación Orientada a Objetos, PDO, Prepared Statements).
* **Motor Frontend:** HTML5, CSS3, JavaScript (ES6), Bootstrap 5.3, Bootstrap Icons.
* **Componente de Inteligencia Artificial:** Servicio de Machine Learning en Python (Scikit-Learn / TF-IDF / Clasificador Naive Bayes) + Heurística NLP en PHP y API Gemini.
* **Servidor Web:** Apache / Microsoft IIS.

### 2.2. Arquitectura del Software (Patrón MVC)
El sistema divide estrictamente sus responsabilidades:
* **Modelos (`app/Models`):** Encapsulan el acceso a datos en SQL Server (`Ticket.php`, `Usuario.php`, `Evidencia.php`, `Tienda.php`, etc.) asegurando transacciones atómicas y seguridad contra inyecciones SQL.
* **Vistas (`app/Views`):** Interfaces visuales limpias, modales interactivos y reportes con maquetación `@media print` en una página estricta.
* **Controladores (`app/Controllers`):** Coordinan la lógica de negocio, validaciones de sesión, enrutamiento y control de acceso por roles.
* **Servicios (`app/Services`):** Módulos especializados desacoplados (`ServicioIA.php`, `UploadService.php`) para procesamiento de archivos e inferencia predictiva.

---

## 3. MATRIZ DE ROLES Y PERMISOS (RBAC)

El sistema implementa un control de acceso basado en roles con sesiones blindadas:

| Rol | Alcance y Permisos |
| :--- | :--- |
| **Administrador de Sistemas** | Acceso global total. Visualización de todas las tiendas, reasignación de técnicos, cambio de prioridades, gestión de usuarios (creación, edición y restablecimiento seguro de contraseñas), métricas analíticas globales e impresión de reportes de auditoría. |
| **Soporte Técnico (Especialista TI)** | Bandeja de tickets asignados. Actualización de estado (En Progreso, Resuelto), registro de ficha técnica del equipo intervenido (marca, modelo, serie), subida de evidencias fotográficas (*Antes* y *Después*) e impresión de conformidad de servicio. |
| **Empleado / Tienda (Administrador de Sede)** | Vista restringida exclusivamente a su sede asignada. Creación simplificada de incidencias, seguimiento en tiempo real del técnico asignado, registro de conformidad final y consulta de sus informes técnicos. |

---

## 4. FLUJO DEL CICLO DE VIDA DEL TICKET (WORKFLOW)

```text
[1. Tienda Reporta] ──▶ [2. Clasificación IA] ──▶ [3. Asignación TI]
                                │                           │
                                ▼                           ▼
                      (Categoría y SLA)           (Notificación a Técnico)
                                                            │
                                                            ▼
[6. Cierre y Reporte A4] ◀── [5. Conformidad Tienda] ◀── [4. Atención Técnica]
    (Acta Firmada)             (Aprobación Admin)       (Fotos Antes / Después)
```

1. **Creación de Incidencia:** La tienda registra el problema con título y descripción breve.
2. **Evaluación Inteligente (IA):** El motor predictivo analiza el texto, detecta si es duplicado en la última hora, clasifica la categoría (Software, Hardware, Redes, POS) y asigna la prioridad y fecha límite (SLA).
3. **Asignación:** El administrador o supervisor asigna el especialista de sistemas idóneo.
4. **Intervención y Evidencias:** El técnico ingresa a la tienda, sube la foto del equipo con la falla (*Fase Antes*), realiza el mantenimiento, registra los datos del hardware (Serie/Modelo) y sube la foto del equipo funcionando (*Fase Después*).
5. **Conformidad de Recepción:** El Administrador de Tienda valida la operatividad in situ y registra su comentario de aprobación.
6. **Informe Ejecutivo Final:** El sistema genera automáticamente el Informe Técnico oficial en 1 sola hoja A4 con las firmas correspondientes.

---

## 5. POLÍTICAS DE SLA (SERVICE LEVEL AGREEMENTS)

| Nivel de Prioridad | Tiempo Máximo de Solución | Ejemplos Típicos en Retail |
| :--- | :---: | :--- |
| **Crítica** | **2 horas** | Caída de internet en tienda, falla general de cajas registradoras POS, servidor de tienda inoperativo. |
| **Alta** | **4 horas** | Falla en impresora de tickets de caja principal, balanza de caja rápida descalibrada. |
| **Media** | **8 horas** | Falla en lector de código de barras secundario, problemas en correo corporativo o software contable auxiliar. |
| **Baja** | **24 horas** | Mantenimiento preventivo, solicitud de nuevo accesorio, actualización programada de periféricos. |

---

## 6. SEGURIDAD Y AUDITORÍA

1. **Protección contra Inyección SQL:** Todas las consultas SQL Server se ejecutan a través de PDO con sentencias preparadas y parámetros tipados (`:param`).
2. **Almacenamiento Seguro de Credenciales:** Contraseñas encriptadas mediante el algoritmo unidireccional `password_hash()` con `PASSWORD_BCRYPT`.
3. **Restablecimiento Administrativo de Contraseñas:** Modal con generador de claves seguras alfanuméricas con confirmación visual de cambio.
4. **Prevención XSS (Cross-Site Scripting):** Sanitización y escape riguroso mediante `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` en todas las salidas visuales y reportes.
5. **Sanitización de Archivos:** Validación estricta de tipos MIME en imágenes (JPEG, PNG, WEBP), nombres aleatorizados para evitar sobrescrituras y límites de tamaño.

---

## 7. MANUAL RÁPIDO DE USUARIO (PASO A PASO)

### Para el Administrador de Tienda:
1. Iniciar sesión con sus credenciales de sede.
2. Hacer clic en **"Nuevo Ticket"**.
3. Describir la falla de manera clara (ej. *"Impresora de caja 2 no enciende"*).
4. El sistema calculará el SLA y mostrará el ticket en estado **Pendiente**.
5. Cuando el técnico culmine, ingresar al ticket y hacer clic en **"Dar Conformidad"**.

### Para el Especialista de Soporte Técnico:
1. Iniciar sesión y revisar los tickets asignados en el Dashboard.
2. Al iniciar la atención, cambiar el estado a **"En Progreso"**.
3. En la pestaña de Evidencias, subir la foto de la falla (**Fase: Antes**).
4. Llenar la ficha técnica del equipo (Tipo, Marca/Modelo, Número de Serie).
5. Al solucionar el problema, subir la foto del equipo operativo (**Fase: Después**) y marcar como **"Resuelto"**.
6. Abrir la vista de **"Informe Técnico"**, presionar `Ctrl + P` (o botón de Imprimir), desmarcar encabezados de navegador y guardar o imprimir el reporte en 1 hoja A4 para firma.

---

## 8. IMPACTO Y BENEFICIOS PARA LA ORGANIZACIÓN

* **Reducción del 65% en Tiempos de Respuesta:** Eliminación de los cuellos de botella en la comunicación mediante WhatsApp o correos dispersos.
* **100% de Trazabilidad y Control de Activos:** Cada intervención queda ligada al número de serie del equipo y a las evidencias fotográficas.
* **Cero Papel Innecesario y Auditoría Impecable:** Los reportes son digitales y solo se imprime el acta final condensada en una hoja ejecutiva.
* **Medición Real del Rendimiento Técnico:** Reportes gerenciales con métricas objetivas de cumplimiento de SLA por cada especialista.
