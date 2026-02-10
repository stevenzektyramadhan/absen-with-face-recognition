# Docker Development Setup

This project is fully containerized for local development.

## 🚀 Quick Start

### 1. Copy Environment File
```bash
cp absensi-face/.env.docker absensi-face/.env
```

### 2. Start All Services
```bash
docker-compose up --build
```

### 3. Access the Application
| Service       | URL                          |
|---------------|------------------------------|
| Laravel App   | http://localhost:8000        |
| AI Service    | http://localhost:5000        |
| phpMyAdmin    | http://localhost:8080        |

## 📦 Services

| Service       | Description                          | Port  |
|---------------|--------------------------------------|-------|
| `laravel-app` | Laravel 12 + Vite + Tailwind         | 8000  |
| `ai-service`  | Python Flask + YOLOv8 + InsightFace  | 5000  |
| `db`          | MySQL 8.0                            | 3306  |
| `phpmyadmin`  | Database Management UI               | 8080  |

## 🔧 Common Commands

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# Rebuild containers
docker-compose up --build

# View logs
docker-compose logs -f

# Access Laravel container shell
docker exec -it laravel-app bash

# Run Laravel artisan commands
docker exec -it laravel-app php artisan migrate

# Access AI service container
docker exec -it ai-service bash
```

## 🔑 Default Credentials

| Service    | Username | Password |
|------------|----------|----------|
| MySQL      | root     | password |
| phpMyAdmin | root     | password |

## ⚙️ Environment Variables

The `.env.docker` file is pre-configured for Docker with:
- `DB_HOST=db` (MySQL container name)
- `FACE_SERVICE_URL=http://ai-service:5000` (AI service container)

## 🛠️ Development with Hot Reload

For Vite hot-reload during development, modify the Laravel command in `docker-compose.yml`:
```yaml
command: >
  sh -c "composer install --no-interaction &&
         npm install &&
         npm run dev"
```

Then access Vite dev server at `http://localhost:5173`.
