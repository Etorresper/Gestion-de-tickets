# Sistema de Gestión de Tickets - ITIL

Sistema web de gestión de incidencias y tickets de soporte técnico basado en el framework ITIL, desarrollado con PHP y SQL Server 2019.

## 🚀 Características

- Gestión completa de tickets (CRUD) con flujo ITIL
- Dashboard con métricas y KPIs en tiempo real
- Inteligencia Artificial (Gemini API) para clasificación automática de tickets
- Machine Learning (Naive Bayes) para predicción de categorías
- Gestión de SLA con cálculo automático por prioridad y ubicación
- Sistema de evidencias e inventario
- Generación de reportes y analíticas
- Control de acceso basado en roles (Administrador, Técnico, Usuario)

## 📋 Requisitos

- PHP 7.4+ con extensión `pdo_sqlsrv`
- SQL Server 2019
- Microsoft ODBC Driver 17/18 para SQL Server
- Apache/Nginx con mod_rewrite
- Python 3.x (para módulo ML)
- Clave de API de Google Gemini (opcional, para clasificación IA)

## ⚙️ Instalación

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/Etorresper/Gestion-de-tickets.git
   cd Gestion-de-tickets
   ```

2. **Configurar la base de datos:**
   - Ejecutar `database_sqlserver.sql` para crear las tablas
   - Ejecutar `datos_iniciales_sqlserver.sql` para cargar datos de prueba

3. **Configurar la aplicación:**
   ```bash
   cp config.example.php config.php
   ```
   Editar `config.php` con tus credenciales de base de datos y API Key de Gemini.

4. **Configurar el servidor web:**
   - Apuntar el DocumentRoot a la carpeta `public/`
   - Asegurar que mod_rewrite esté habilitado

5. **Módulo de Machine Learning (opcional):**
   ```bash
   cd ml
   pip install scikit-learn pandas joblib
   python train.py
   ```

## 🔐 Variables de Entorno

El sistema soporta variables de entorno como alternativa a editar `config.php`:

| Variable | Descripción | Default |
|----------|-------------|---------|
| `DB_SERVER` | Servidor SQL Server | `localhost` |
| `DB_PORT` | Puerto del servidor | `1433` |
| `DB_USER` | Usuario de base de datos | *(vacío = Windows Auth)* |
| `DB_PASS` | Contraseña de base de datos | *(vacío)* |
| `DB_NAME` | Nombre de la base de datos | `sistema_tickets` |
| `GEMINI_API_KEY` | API Key de Google Gemini | *(vacío)* |

## 📂 Estructura del Proyecto

```
├── app/
│   ├── Controllers/     # Controladores MVC
│   ├── Core/            # Núcleo (Database, Router, Helper)
│   ├── Models/          # Modelos de datos
│   ├── Services/        # Servicios (IA, ML, Upload)
│   └── Views/           # Vistas PHP
├── ml/                  # Módulo de Machine Learning
├── public/              # Punto de entrada web
│   ├── assets/          # CSS, JS, imágenes
│   └── uploads/         # Archivos subidos
├── storage/             # Almacenamiento temporal
├── config.example.php   # Plantilla de configuración
└── README.md
```

## 📄 Licencia

Proyecto académico — Prácticas Pre-Profesionales.
