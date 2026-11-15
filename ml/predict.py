import sys
import os
import json
import joblib

# Configurar salida estándar en UTF-8 para evitar errores de codificación en Windows
try:
    sys.stdout.reconfigure(encoding='utf-8')
except AttributeError:
    # Fallback para versiones antiguas de Python
    pass

def predecir():
    resultado = {"categoria": "Software", "error": None}
    
    try:
        # 1. Validar que se reciba el texto a clasificar
        if len(sys.argv) < 2 or not sys.argv[1].strip():
            resultado["error"] = "Error: No se proporcionó el texto de la incidencia."
            print(json.dumps(resultado, ensure_ascii=False))
            sys.exit(1)
            
        texto_incidencia = sys.argv[1]
        
        # 2. Obtener ruta del modelo
        current_dir = os.path.dirname(os.path.abspath(__file__))
        model_path = os.path.join(current_dir, 'modelo_incidencias.pkl')
        
        # 3. Verificar si el modelo existe
        if not os.path.exists(model_path):
            resultado["error"] = "Error: El modelo 'modelo_incidencias.pkl' no existe. Ejecuta primero train.py"
            print(json.dumps(resultado, ensure_ascii=False))
            sys.exit(1)
            
        # 4. Cargar modelo y predecir
        pipeline = joblib.load(model_path)
        prediccion = pipeline.predict([texto_incidencia])[0]
        
        # 5. Retornar resultado en formato JSON
        resultado["categoria"] = prediccion
        print(json.dumps(resultado, ensure_ascii=False))
        
    except Exception as e:
        resultado["error"] = f"Excepción en Python: {str(e)}"
        print(json.dumps(resultado, ensure_ascii=False))
        sys.exit(1)

if __name__ == '__main__':
    predecir()
