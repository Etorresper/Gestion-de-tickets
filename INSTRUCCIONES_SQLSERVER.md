# 📋 Guía de Instalación — SQL Server 2019

> Guía completa para configurar el Sistema de Gestión de Incidencias con Microsoft SQL Server 2019.

---

## Requisitos previos

| Componente | Versión mínima |
|-----------|---------------|
| PHP | 7.4+ (recomendado 8.x) |
| Microsoft SQL Server | 2019 (Express, Developer o Enterprise) |
| Microsoft ODBC Driver | 17 o 18 for SQL Server |
| Extensión PHP | `pdo_sqlsrv` |
| Apache/IIS | Cualquier versión compatible |
| Python | 3.8+ (para módulo ML) |

---

## 1. Instalar SQL Server 2019

1. Descarga **SQL Server 2019 Developer** (gratuito para desarrollo):  
   https://www.microsoft.com/es-es/sql-server/sql-server-downloads

2. Ejecuta el instalador y elige **"Personalizada"** o **"Básica"**.

3. Durante la instalación, **anota**:
   - Nombre del servidor (ej: `localhost\MSSQLSERVER` o `localhost\SQLEXPRESS`)
   - Puerto (por defecto: `1433`)
   - Modo de autenticación: elige **"Modo mixto"** (SQL Server + Windows)
   - Contraseña del usuario `sa`

4. Habilita el protocolo **TCP/IP**:
   - Abre **SQL Server Configuration Manager**
   - Ve a `SQL Server Network Configuration > Protocols for MSSQLSERVER`
   - Habilita **TCP/IP**
   - En propiedades de TCP/IP → pestaña `IP Addresses` → al final establece `TCP Port = 1433`
   - Reinicia el servicio SQL Server

---

## 2. Instalar Microsoft ODBC Driver 18

Descarga el driver desde:  
https://docs.microsoft.com/en-us/sql/connect/odbc/download-odbc-driver-for-sql-server

**Windows (x64):**
```
msodbcsql.msi → instalar con configuración predeterminada
```

---

## 3. Instalar extensión PHP para SQL Server

### XAMPP / PHP en Windows

1. Descarga los drivers PHP para SQL Server:  
   https://docs.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server

2. Descarga la versión que corresponda a tu PHP (ej: PHP 8.2 → `php_pdo_sqlsrv_82_ts_x64.dll`)

3. Copia los archivos `.dll` a la carpeta de extensiones de PHP:
   ```
   C:\xampp\php\ext\
   ```

4. Edita `php.ini` (en XAMPP: `C:\xampp\php\php.ini`) y agrega:
   ```ini
   extension=php_sqlsrv_82_ts_x64.dll
   extension=php_pdo_sqlsrv_82_ts_x64.dll
   ```

5. Reinicia Apache.

6. Verifica que la extensión esté activa:
   ```php
   <?php phpinfo(); ?>
   ```
   Busca `pdo_sqlsrv` en la página de phpinfo.

---

## 4. Crear la base de datos

### Opción A: SQL Server Management Studio (SSMS)

1. Abre SSMS y conéctate al servidor.
2. Ve a `File > Open > File...` y abre `database_sqlserver.sql`.
3. Haz clic en **"Execute"** (F5).

### Opción B: Línea de comandos (sqlcmd)

```cmd
sqlcmd -S localhost -U sa -P TuPassword -i "C:\ruta\al\proyecto\database_sqlserver.sql"
```

---

## 5. Configurar `config.php`

Edita el archivo `config.php` en la raíz del proyecto:

```php
define('DB_SERVER', 'localhost');        // O 'localhost\SQLEXPRESS'
define('DB_PORT',   '1433');
define('DB_USER',   'sa');              // Usuario con permisos en la BD
define('DB_PASS',   'TuPassword123');   // Contraseña del usuario
define('DB_NAME',   'sistema_tickets');
```

> [!CAUTION]
> Nunca uses el usuario `sa` en producción. Crea un usuario dedicado con permisos solo sobre `sistema_tickets`.

---

## 6. Instalar dependencias Python (Módulo ML)

```bash
pip install scikit-learn joblib numpy
```

Luego entrena el modelo (solo la primera vez):

```bash
cd ml/
python train.py
```

---

## 7. Configurar el servidor web

### XAMPP (Apache)

1. Copia el proyecto a `C:\xampp\htdocs\PROYECTO PARACTICAS\`
2. Asegúrate de que `public/.htaccess` tenga `mod_rewrite` activado en Apache.
3. En `httpd.conf`, verifica que `AllowOverride All` esté habilitado.
4. Accede a: `http://localhost/PROYECTO%20PARACTICAS/public`

---

## 8. Primer acceso

- **URL**: `http://localhost/PROYECTO%20PARACTICAS/public`
- **Usuario**: `admin@empresa.com`
- **Contraseña**: `admin123`

> [!IMPORTANT]
> Cambia la contraseña del administrador inmediatamente después del primer acceso.

---

## 9. Crear usuarios de Soporte (Técnicos)

La IA asigna tickets basándose en nombres específicos de técnicos en la base de datos. Para que la asignación automática funcione, crea usuarios con rol **"Soporte"** desde el panel de administración → Usuarios.

La lógica de asignación por prioridad está en `app/Models/Usuario.php::getTecnicoPorPrioridad()`. Puedes modificar los nombres de técnicos según tu organización.

---

## 10. Verificar instalación

Accede a `public/check_tickets.php` para verificar que la conexión a SQL Server funciona correctamente.

---

## Solución de problemas frecuentes

| Error | Causa | Solución |
|-------|-------|----------|
| `SQLSTATE[08001]` | No puede conectar al servidor | Verificar que SQL Server esté corriendo y TCP/IP habilitado |
| `could not find driver` | Extensión `pdo_sqlsrv` no instalada | Instalar la DLL y reiniciar Apache |
| `Login failed for user 'sa'` | Modo de autenticación incorrecto | Habilitar modo mixto en SQL Server |
| `Error de conexión a SQL Server` | Contraseña incorrecta en config.php | Verificar credenciales |
| `python: command not found` | Python no en PATH | Ajustar `PYTHON_EXECUTABLE` en config.php a la ruta completa |

---

## Dependencias PHP requeridas

```
php >= 7.4
ext-pdo
ext-pdo_sqlsrv   ← NUEVO (reemplaza pdo_mysql)
ext-curl         ← Para Gemini API
ext-json
ext-fileinfo     ← Para uploads
```

## Módulos PHP opcionales (ya no requeridos)

```
ext-pdo_mysql    ← Ya no necesario (eliminado)
```

