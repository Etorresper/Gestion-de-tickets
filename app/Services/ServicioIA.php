<?php
namespace App\Services;

use App\Core\Database;
use PDO;

class ServicioIA
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Analiza una descripción corta provista por el usuario y genera los datos del ticket
     * usando la API de Gemini (con fallback a reglas si falla).
     */
    public function analizarDescripcionTicket($text)
    {
        // Solo usamos reglas si NO hay una clave configurada en config.php
        if (empty(GEMINI_API_KEY)) {
            return $this->analizarPorReglas($text);
        }

        $contexto = $this->obtenerContextoSistema();
        $prompt = "Actúa como un experto en soporte técnico de TI Nivel 2.
        Analiza el siguiente reporte de un usuario y genera un JSON estrictamente válido.

        ESTRUCTURA DE RESPUESTA (JSON):
        {
            \"asunto\": \"Título técnico conciso (Ej: 'Fallo en servicio de impresión Spooler', no 'Ayuda')\",
            \"descripcion_mejorada\": \"Redacción técnica detallada, profesional y estructurada del problema\",
            \"id_categoria\": (ID numérico de la categoría más adecuada),
            \"prioridad\": \"Crítica|Alta|Media|Baja\",
            \"id_tienda\": (ID numérico de la tienda detectada o null),
            \"tecnico_sugerido\": (ID usuario del técnico más apto o null),
            \"feedback_requerido\": \"Si falta info crítica (ej: número de serie), pregunta aquí, sino null\"
        }

        CONTEXTO ACTUAL:
        - Categorías: {$contexto['categorias']}
        - Tiendas: {$contexto['tiendas']}
        - Técnicos: {$contexto['tecnicos']}

        USUARIO REPORTA:
        \"$text\"

        REGLAS DE ORO:
        1. ASUNTO: Debe ser profesional y específico. Evita 'Asistencia requerida'.
        2. PRIORIDAD CRÍTICA: Asignar si es un Servidor, Red caída en toda la tienda, o problemas que impiden la venta.
        3. DESCRIPCIÓN: Mejora la redacción del usuario para que un técnico entienda el impacto.
        
        Responde SOLO el JSON.";

        $resultado = $this->llamarGemini($prompt);

        if ($resultado && isset($resultado['asunto'])) {
            $resultado['estado'] = 'success';
            return $resultado;
        }

        // Fallback si la IA falla
        return $this->analizarPorReglas($text);
    }

    private function obtenerContextoSistema()
    {
        // Obtener categorías
        $stmt = $this->db->query("SELECT id_categoria, nombre_categoria FROM CATEGORIAS");
        $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $catStr = implode(", ", array_map(fn($c) => "{$c['id_categoria']}:{$c['nombre_categoria']}", $cats));

        // Obtener tiendas
        $stmt = $this->db->query("SELECT t.id_tienda, t.nombre_tienda, d.nombre_distrito FROM TIENDAS t LEFT JOIN DISTRITOS d ON t.id_distrito = d.id_distrito");
        $tiendas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tiendaStr = implode(", ", array_map(fn($t) => "{$t['id_tienda']}:{$t['nombre_tienda']}({$t['nombre_distrito']})", $tiendas));

        // Obtener técnicos
        $stmt = $this->db->query("SELECT u.id_usuario, u.nombre, a.nombre_area FROM USUARIOS u JOIN AREAS a ON u.id_area = a.id_area WHERE u.id_rol = 2");
        $tecnicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tecStr = implode(", ", array_map(fn($t) => "{$t['id_usuario']}:{$t['nombre']}({$t['nombre_area']})", $tecnicos));

        return [
            'categorias' => $catStr,
            'tiendas' => $tiendaStr,
            'tecnicos' => $tecStr
        ];
    }

    private function llamarGemini($prompt)
    {
        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.2,
                "topK" => 1,
                "topP" => 1,
                "maxOutputTokens" => 1000,
                "response_mime_type" => "application/json"
            ]
        ];

        $ch = curl_init(GEMINI_API_URL . "?key=" . GEMINI_API_KEY);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $decoded = json_decode($response, true);
            $textResponse = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if ($textResponse) {
                return json_decode($textResponse, true);
            }
        }
        return null;
    }

    /**
     * Motor de reglas simple (Fallback)
     */
    private function analizarPorReglas($text)
    {
        $textLower = strtolower($text);
        $response = [
            'estado' => 'success',
            'asunto' => 'Asistencia técnica requerida',
            'descripcion_mejorada' => 'Reporte: ' . $text,
            'id_categoria' => 3,
            'feedback_requerido' => null,
            'tecnico_sugerido' => null,
            'prioridad' => 'Media',
            'id_tienda' => null
        ];

        // Detección de Tienda por palabras clave de forma dinámica desde base de datos
        try {
            $stmt = $this->db->query("SELECT id_tienda, nombre_tienda FROM TIENDAS");
            $tiendasDb = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($tiendasDb as $tDb) {
                $cleanName = str_replace(['tienda', 'express', 'central', 'business', 'industrial'], '', strtolower($tDb['nombre_tienda']));
                $cleanName = trim($cleanName);
                if (!empty($cleanName) && strpos($textLower, $cleanName) !== false) {
                    $response['id_tienda'] = $tDb['id_tienda'];
                    break;
                }
                if (strpos($textLower, strtolower($tDb['nombre_tienda'])) !== false) {
                    $response['id_tienda'] = $tDb['id_tienda'];
                    break;
                }
            }
        } catch (\Exception $e) {
            if (strpos($textLower, 'surco') !== false)
                $response['id_tienda'] = 1;
            elseif (strpos($textLower, 'miraflores') !== false)
                $response['id_tienda'] = 2;
            elseif (strpos($textLower, 'san isidro') !== false)
                $response['id_tienda'] = 3;
            elseif (strpos($textLower, 'olivos') !== false)
                $response['id_tienda'] = 4;
        }

        if (strpos($textLower, 'impresora') !== false || strpos($textLower, 'ticketera') !== false || strpos($textLower, 'térmica') !== false || strpos($textLower, 'scaner') !== false || strpos($textLower, 'escáner') !== false || strpos($textLower, 'barras') !== false) {
            $response['id_categoria'] = 2; // Hardware/Equipos (ID 2 en BD)
            $response['prioridad'] = 'Alta';
            $response['asunto'] = 'Falla en Equipo de Tienda (Impresora/Scanner)';
        } elseif (strpos($textLower, 'lenta') !== false || strpos($textLower, 'lentitud') !== false || strpos($textLower, 'pc') !== false) {
            $response['id_categoria'] = 3; // Software/Sistemas (ID 3 en BD)
            $response['prioridad'] = 'Media';
            $response['asunto'] = 'Reporte de Lentitud en Equipo';
        } elseif (strpos($textLower, 'internet') !== false || strpos($textLower, 'wifi') !== false || strpos($textLower, 'red') !== false) {
            $response['id_categoria'] = 1; // Problema de Red (ID 1 en BD)
            $response['prioridad'] = 'Alta';
            $response['asunto'] = 'Problemas de Conectividad / Red';
        }

        if (
            strpos($textLower, 'urgente') !== false ||
            strpos($textLower, 'caído') !== false ||
            strpos($textLower, 'no corta') !== false ||
            strpos($textLower, 'servidor') !== false ||
            strpos($textLower, 'no responde') !== false ||
            strpos($textLower, 'offline') !== false
        ) {
            $response['prioridad'] = 'Crítica';
        }

        return $response;
    }

    public function calcularSLA($prioridad, $id_tienda = null, $tipoAtencion = 'Presencial')
    {
        $ahora = new \DateTime();

        // 1. Tiempo base según prioridad y modalidad (Remota vs Presencial en Ruta)
        if ($tipoAtencion === 'Remota') {
            // Atención remota (AnyDesk / RDP) desde Sede Central Comas
            switch ($prioridad) {
                case 'Crítica':
                    $ahora->modify('+2 hours');
                    break;
                case 'Alta':
                    $ahora->modify('+6 hours');
                    break;
                case 'Media':
                    $ahora->modify('+24 hours');
                    break;
                case 'Baja':
                default:
                    $ahora->modify('+48 hours');
                    break;
            }
        } else {
            // Atención presencial con itinerario de ruta entre tiendas
            switch ($prioridad) {
                case 'Crítica':
                    // Crítica en tienda: Atención en el mismo turno o máximo 6 horas de desplazamiento urgente
                    $ahora->modify('+6 hours');
                    break;
                case 'Alta':
                    // Alta: Atención dentro de las 24 horas hábiles (itinerario del día)
                    $ahora->modify('+24 hours');
                    break;
                case 'Media':
                    // Media: Consolidación de ruta geográfica (visita en 48 horas)
                    $ahora->modify('+48 hours');
                    break;
                case 'Baja':
                default:
                    // Baja: Visita programada en la ronda semanal de la zona
                    $ahora->modify('+72 hours');
                    break;
            }
        }

        // 2. Compensación por distancia y rutas desde Sede Central Comas
        if ($id_tienda) {
            $sql = "SELECT d.nombre_distrito 
                    FROM TIENDAS t 
                    LEFT JOIN DISTRITOS d ON t.id_distrito = d.id_distrito 
                    WHERE t.id_tienda = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id_tienda]);
            $distrito = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($distrito) {
                $nombre = strtolower($distrito['nombre_distrito'] ?? '');

                // Zonas alejadas de Lima Sur / Playas / Este (requieren mayor tiempo de traslado en ruta)
                $zonasAlejadas = ['villa el salvador', 'chumbe', 'lurin', 'pucusana', 'san bartolo', 'villa maria', 'san juan de miraflores', 'ate', 'chosica', 'huaycan', 'puente piedra', 'ancon', 'ventanilla'];

                if (in_array($nombre, $zonasAlejadas)) {
                    // +6 horas extra para consolidación de ruta en sedes distantes
                    $ahora->modify('+6 hours');
                } else {
                    // Distritos intermedios
                    $zonasCercanasComas = ['comas', 'los olivos', 'independencia', 'carabayllo', 'san martin de porres'];
                    if (!in_array($nombre, $zonasCercanasComas)) {
                        $ahora->modify('+3 hours');
                    }
                }
            }
        }

        // Formato ISO-8601 canónico para SQL Server (evita inversión de día y mes bajo cualquier idioma/DATEFORMAT)
        return $ahora->format('Y-m-d\TH:i:s');
    }
}
