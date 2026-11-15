import os
import pandas as pd
import joblib
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.naive_bayes import MultinomialNB
from sklearn.pipeline import Pipeline

def entrenar():
    # Obtener la ruta del directorio actual
    current_dir = os.path.dirname(os.path.abspath(__file__))
    dataset_path = os.path.join(current_dir, 'dataset.csv')
    
    # 1. Cargar el dataset
    print(f"Cargando dataset desde: {dataset_path}")
    if not os.path.exists(dataset_path):
        print("Error: No se encontró el archivo dataset.csv")
        return
        
    df = pd.read_csv(dataset_path)
    
    # Lista de stopwords (conectores) en español para evitar ruido
    stopwords_espanol = [
        'de', 'la', 'que', 'el', 'en', 'y', 'a', 'los', 'del', 'se', 'las', 'por', 'un', 'para', 'con', 'no', 'una', 
        'su', 'al', 'lo', 'como', 'más', 'pero', 'sus', 'le', 'ya', 'o', 'este', 'sí', 'porque', 'esta', 'entre', 
        'cuando', 'muy', 'sin', 'sobre', 'también', 'me', 'hasta', 'hay', 'donde', 'quien', 'desde', 'todo', 
        'nos', 'durante', 'todos', 'uno', 'les', 'ni', 'contra', 'otros', 'ese', 'eso', 'ante', 'ellos', 'e', 
        'esto', 'mí', 'antes', 'algunos', 'qué', 'unos', 'yo', 'otro', 'otras', 'otra', 'él', 'tanto', 'esa', 
        'estos', 'mucho', 'quienes', 'nada', 'muchos', 'cual', 'poco', 'ella', 'estar', 'estas', 'algunas', 
        'algo', 'nosotros', 'mi', 'mis', 'tú', 'te', 'ti'
    ]

    # 2. Definir el Pipeline (Procesamiento de texto TF-IDF + Clasificador Naive Bayes)
    # lowercase=True: Convierte todo a minúsculas
    # stop_words: Elimina conectores sin valor semántico en español usando nuestra lista
    # ngram_range=(1, 2): Analiza palabras sueltas y combinaciones de dos palabras (ej. "no imprime")
    pipeline = Pipeline([
        ('vectorizer', TfidfVectorizer(stop_words=stopwords_espanol, lowercase=True, ngram_range=(1, 2))),
        ('classifier', MultinomialNB(alpha=0.1)) # alpha=0.1 ayuda con datasets pequeños
    ])
    
    # 3. Entrenar el modelo
    print("Entrenando el modelo Naive Bayes...")
    pipeline.fit(df['texto'], df['categoria'])
    
    # 4. Evaluar precisión del entrenamiento
    precision = pipeline.score(df['texto'], df['categoria'])
    print(f"Entrenamiento completado. Precisión (Accuracy): {precision * 100:.2f}%")
    
    # 5. Guardar el Pipeline completo (Vectorizador + Modelo)
    model_path = os.path.join(current_dir, 'modelo_incidencias.pkl')
    joblib.dump(pipeline, model_path)
    print(f"Modelo guardado exitosamente en: {model_path}")

if __name__ == '__main__':
    entrenar()
