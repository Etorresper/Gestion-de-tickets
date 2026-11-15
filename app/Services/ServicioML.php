<?php
namespace App\Services;

use App\Core\Database;
use PDO;

class ServicioML {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Envía el texto de la incidencia al script de Python para su clasificación automática.
     *
     * @param string $texto El asunto y la descripción de la incidencia.
     * @return array Un arreglo asociativo con la categoría predicha y el estado del proceso.
     */
    public function clasificarTexto($texto) {
        // 1. Limpiar el texto para remover saltos de línea innecesarios o caracteres extraños
        $textoLimpio = trim(preg_replace('/\s+/', ' ', $texto));
        
        // Evitar procesar cadenas vacías
        if (empty($textoLimpio)) {
            return [
                'estado' => 'error',
                'categoria' => 'Software',
                'mensaje' => 'El texto ingresado está vacío.'
            ];
        }

        // 2. Construir el comando de consola seguro usando escapeshellarg
        // escapeshellarg previene la inyección de comandos de consola, garantizando la seguridad.
        $cmd = PYTHON_EXECUTABLE . " " . escapeshellarg(ML_PREDICT_SCRIPT) . " " . escapeshellarg($textoLimpio);

        // 3. Ejecutar el script y capturar la salida
        $salida = shell_exec($cmd);

        // 4. Procesar la respuesta
        if ($salida === null) {
            return [
                'estado' => 'error',
                'categoria' => 'Software', // Fallback por defecto
                'mensaje' => 'No se recibió respuesta del script de Python. Verifica que Python esté instalado.'
            ];
        }

        $resultado = json_decode(trim($salida), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'estado' => 'error',
                'categoria' => 'Software', // Fallback por defecto
                'mensaje' => 'Error al decodificar la respuesta JSON del script: ' . json_last_error_msg(),
                'raw_output' => $salida
            ];
        }

        if (!empty($resultado['error'])) {
            return [
                'estado' => 'error',
                'categoria' => 'Software', // Fallback
                'mensaje' => 'Error reportado por el script de ML: ' . $resultado['error']
            ];
        }

        return [
            'estado' => 'success',
            'categoria' => $resultado['categoria'],
            'mensaje' => 'Clasificado exitosamente mediante Machine Learning.'
        ];
    }

    /**
     * Obtiene el ID numérico de la categoría en MySQL a partir de su nombre.
     *
     * @param string $nombreCategoria Nombre de la categoría (ej: "Hardware", "Red", "Software", "Accesos").
     * @return int El ID de la categoría para guardar en la base de datos.
     */
    public function obtenerIdCategoriaPorNombre($nombreCategoria) {
        // Mapear nombres del modelo ML a los nombres exactos en la base de datos
        $mapeo = [
            'Red' => 'Problema de Red',
            'Hardware' => 'Hardware/Equipos',
            'Software' => 'Software/Sistemas',
            'Accesos' => 'Software/Sistemas' // o la categoría que corresponda
        ];

        $nombreBuscado = $mapeo[$nombreCategoria] ?? $nombreCategoria;

        $sql = "SELECT TOP 1 id_categoria FROM CATEGORIAS WHERE nombre_categoria = :nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nombre' => $nombreBuscado]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            return (int)$resultado['id_categoria'];
        }

        // Si no se encuentra con el mapeo, intentar una búsqueda parcial
        $sqlLike = "SELECT TOP 1 id_categoria FROM CATEGORIAS WHERE nombre_categoria LIKE :nombreLimitado";
        $stmtLike = $this->db->prepare($sqlLike);
        $stmtLike->execute([':nombreLimitado' => '%' . $nombreCategoria . '%']);
        $resultadoLike = $stmtLike->fetch(PDO::FETCH_ASSOC);

        if ($resultadoLike) {
            return (int)$resultadoLike['id_categoria'];
        }

        // Fallback: si no coincide con ninguna, asignar "Software/Sistemas" (ID 3 en nuestro seed)
        return 3;
    }
}
