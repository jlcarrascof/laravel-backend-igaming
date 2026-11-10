# Plataforma Base - Evaluación Técnica Backend / Full Stack (Laravel)

Este proyecto corresponde a la base arquitectónica desarrollada para una evaluación técnica en el sector **iGaming**, con foco en buenas prácticas, arquitectura por capas, transaccionalidad, manejo relacional de datos y diseño de APIs RESTful.

---

## 🛠️ Requisitos Previos

- **Docker Desktop** (con soporte WSL2 en Windows o Docker Engine en Linux/macOS).
- **Git**.

> **Nota:** No es necesario tener PHP ni Composer instalados en la máquina anfitriona (host). Todo el flujo corre de forma aislada mediante contenedores con **Laravel Sail**.

---

## 🚀 Instalación y Puesta en Marcha (Desde Cero)

### 1. Clonar el repositorio y acceder a la rama
```bash
git clone <URL_DEL_REPOSITORIO>
cd laravel-backend-igaming
git checkout feat/setup-environment
```

### 2. Variables de Entorno
Copia el archivo de configuración base:
```bash
cp .env.example .env
```

> **Configuración de Base de Datos:**
> - Por defecto está configurado para **MySQL 8.4** mediante Sail.
> - Si se requiere usar **PostgreSQL**, en `.env.example` y `.env` se encuentra la sección comentada con los parámetros para cambiar el motor de forma transparente.

### 3. Instalar Dependencias de Composer (si no existen)
Si clonas en un entorno nuevo sin `vendor/`, ejecuta:
```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/app" -w /app composer:2 composer install --ignore-platform-reqs
```

### 4. Iniciar Contenedores con Laravel Sail
```bash
./vendor/bin/sail up -d
```

### 5. Generar Clave de Aplicación y Migrar Base de Datos
```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

La aplicación estará disponible de inmediato en:
- **Web (Frontend Demo):** [http://localhost](http://localhost)
- **API Health Check:** [http://localhost/api/v1/health](http://localhost/api/v1/health)

---

## 🧪 Pruebas Automatizadas (Testing)

Los tests de integración y feature están configurados para correr sobre una base de datos **SQLite en memoria (`:memory:`)**, lo que garantiza ejecución ultrarrápida sin alterar los datos del entorno de desarrollo:

```bash
./vendor/bin/sail test
```

Para ver la cobertura o detalle de aserciones:
```bash
./vendor/bin/sail artisan test --colors=always
```

---

## 🎨 Calidad de Código y Estilo

El proyecto utiliza **Laravel Pint** para asegurar el cumplimiento del estándar PSR-12 y las convenciones de Laravel:

```bash
# Revisar y corregir formato automáticamente
./vendor/bin/sail pint

# Verificar formato sin modificar archivos
./vendor/bin/sail pint --test
```

---

## 📡 Endpoints de la API (`/api/v1`)

Todos los endpoints retornan respuestas estructuradas en formato JSON uniforme.

| Método | Endpoint | Descripción | Códigos HTTP |
| :--- | :--- | :--- | :--- |
| **GET** | `/api/v1/health` | Estado del servicio y conectividad con la BD | `200` |
| **GET** | `/api/v1/notes` | Listado paginado de notas registradas | `200` |
| **POST** | `/api/v1/notes` | Creación de una nueva nota (valida campos) | `201`, `422` |

### Formato Uniforme de Respuestas de Error
Toda excepción bajo el prefijo `/api/*` retorna un payload JSON estandarizado:
```json
{
  "message": "Error de validación en la solicitud.",
  "errors": {
    "title": [
      "El título es obligatorio."
    ]
  }
}
```

---

## 📐 Decisiones Técnicas y Convenciones

Para consultar en detalle las decisiones de diseño arquitectónico, el estándar de controladores delgados, la gestión de transacciones monetarias y la prevención de condiciones de carrera (*race conditions*), consulta el documento:
👉 **[ARCHITECTURE.md](ARCHITECTURE.md)**
