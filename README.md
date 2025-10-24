# 🪐 **MeteoraAPI**

**MeteoraAPI** es un proyecto  desarrollado por **Holman Alba**, diseñado como un *chatbot inteligente del clima* que integra **OpenAI** y **Open-Meteo** mediante un backend **Laravel 12** totalmente **Dockerizado**.

Su objetivo es permitir consultas naturales del clima en diferentes ubicaciones, con respuestas generadas por inteligencia artificial, persistencia en base de datos y soporte para pruebas mediante **Postman**.
---

## ⚙️ **Guía de instalación**

### 📋 **Pre-requisitos**
- [Git](https://git-scm.com/downloads)
- [Docker Desktop](https://www.docker.com/products/docker-desktop)
- [Composer](https://getcomposer.org/download/)
- PHP 8.3 (solo si deseas correr el proyecto sin Docker)
- [Postman](https://www.postman.com/downloads/) para pruebas de API

---

## 🚀 **Instalación**

### 1️⃣ Clonar el proyecto
```bash
git clone https://github.com/holmanalba/meteoraapi.git
cd meteoraapi
```

### 2️⃣ Crear archivo `.env`
Copia el ejemplo y configúralo según tu entorno:
```bash
cp .env.example .env
```

Luego edita las variables principales:
```env
APP_URL=http://localhost:8080
DB_HOST=mysql
DB_DATABASE=weather_chat
DB_USERNAME=laravel
DB_PASSWORD=laravel
OPENAI_API_KEY=sk-tu-clave
OPEN_METEO_BASE=https://api.open-meteo.com/v1
```

---

## 🐳 **Ejecución con Docker**

El archivo `docker-compose.yml` define los servicios base:

| Servicio | Imagen | Puerto | Descripción |
|-----------|--------|---------|--------------|
| `nginx` | nginx:1.25-alpine | 8080 | Servidor web |
| `api` | php:8.3-fpm | interno 9000 | Backend Laravel |
| `mysql` | mysql:8.0 | 3308 | Base de datos |
| `redis` | redis:7-alpine | 6380 | Cache y colas |

### 🔧 Construir imágenes
```bash
docker compose build
```

### ▶️ Levantar los servicios
```bash
docker compose up -d
```

### 🧩 Instalar dependencias dentro del contenedor
```bash
docker compose exec api composer install
docker compose exec api php artisan key:generate
docker compose exec api php artisan migrate
```

### 🧱 Verificar que todo funcione
Abre en el navegador:
```
http://localhost:8080/
```

Deberías ver la página de bienvenida de Laravel.

---

## 🧪 **Pruebas de API**

### Endpoint de salud
```bash
GET http://localhost:8080/api/v1/health
```
Respuesta esperada:
```json
{
  "status": true,
  "checks": {
    "db": true,
    "redis": true,
    "openMeteo": true
  }
}
```

### Flujo de chat (Postman)
1. `POST /api/v1/chats` → crea un nuevo chat  
2. `POST /api/v1/chats/{chatId}/messages` → envía mensaje  
3. `GET /api/v1/chats/{chatId}/messages` → historial  
4. `POST /api/v1/messages/{messageId}/retry` → reintenta respuesta

Colección Postman disponible en:  
📁 `/postman/MeteoraAPI.postman_collection.json`

---

## 🧰 **Estructura del proyecto**
```
Meteora/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Requests/
│   ├── Models/
│   └── Services/
├── config/
├── database/
│   └── migrations/
├── docker/
│   ├── api/Dockerfile
│   └── nginx/default.conf
├── routes/api.php
├── docker-compose.yml
└── README.md
```

---

## 🧠 **Comandos útiles**

| Acción | Comando |
|--------|----------|
| Ver logs de PHP | `docker compose logs -f api` |
| Ver logs de Nginx | `docker compose logs -f nginx` |
| Ejecutar Artisan | `docker compose exec api php artisan <comando>` |
| Acceder a contenedor | `docker compose exec api bash` |
| Detener servicios | `docker compose down` |

---


## 📊 Estructura de Base de Datos

Meteora utiliza MySQL 8 y Laravel Eloquent con las siguientes tablas principales:

- **chats** → contenedor de conversación.
- **messages** → mensajes enviados/recibidos.
- **tool_calls** → registros de llamadas a APIs externas.

Cada tabla usa **UUIDs (CHAR(36))** y mantiene relaciones normalizadas.


## 🧭 **Notas de desarrollo**
- Proyecto basado en **Laravel 11** con arquitectura limpia (Controllers, Services, DTOs, Actions).  
- Todos los endpoints responden en **JSON**.  
- Se incluyen **comentarios de validación** (``) al final de cada archivo `.php` y `.vue`.  
- Preparado para pruebas **PHPUnit** y despliegue **Dockerizado**.  

---

## 💡 **Futuras mejoras**
- Implementar frontend **Vue.js 3 + Tailwind CSS**.
- Autenticación con tokens.
- Logs estructurados por sesión.
- Integración con OpenAI Realtime API.

---

## 👨‍💻 **Autor**
Desarrollado con 💙 por **Holman Alba**  
📧 Contacto: [albaholman803@gmail.com](mailto:albaholman803@gmail.com)  

---
