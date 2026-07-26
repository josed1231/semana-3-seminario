# PIAE - Sistema de Información con IA para la Detección Temprana de la Deserción Estudiantil

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![Render](https://img.shields.io/badge/Render-46E3B7?style=for-the-badge&logo=render&logoColor=black)

Plataforma web institucional enfocada en la caracterización, monitoreo preventivo y predicción del riesgo de deserción académica de los estudiantes en COTECNOVA. La herramienta integra un modelo matricial de evaluación socioeducativa, gestión de alertas tempranas, autenticación segura por OTP y exportación de informes institucionales.

---

## Configuración e Instalación Local

### Requisitos Previos
- Docker Desktop y WSL2 (en Windows) o entorno Linux.
- PHP >= 8.2 y Composer instalado localmente.
- Git.

### Pasos de Instalación

1. **Clonar el repositorio:**
   ```bash
   git clone [https://github.com/tu-usuario/tu-repositorio.git](https://github.com/tu-usuario/tu-repositorio.git)
   cd tu-repositorio
   ```

2. **Copiar archivo de variables de entorno:**
   ```bash
   cp .env.example .env
   ```

3. **Instalar dependencias de PHP y Node.js:**
   ```bash
   composer install
   npm install && npm run build
   ```

4. **Iniciar el entorno de desarrollo con Laravel Sail:**
   ```bash
   ./vendor/bin/sail up -d
   ```

5. **Generar la clave de la aplicación:**
   ```bash
   sail artisan key:generate
   ```

6. **Ejecutar migraciones y seeders:**
   ```bash
   sail artisan migrate --seed
   ```

7. **Acceder a la aplicación:**
   Abre tu navegador e ingresa a `http://localhost`.

---

## Características Principales

- **Dashboard de Monitoreo y Alertas:** Vista centralizada con filtros avanzados por carrera/programa, semestre, jornada y género.
- **Modelo Matricial de Evaluación de Riesgo:** Clasificación automatizada del nivel de riesgo (Bajo, Medio, Alto) basada en ponderaciones analíticas.
- **Identificador Único Estudiantil (`EST-YYYY-XXX`):** Generación automatizada de códigos institucionales con trazabilidad de documento de identidad (cédula).
- **Seguridad y Autenticación OTP:** Verificación de código de un solo uso vía correo electrónico para autenticación de usuarios.
- **Control de Acceso Basado en Roles (RBAC):** Gestión diferenciada de permisos para Administrador, Director de Bienestar, Psicólogo y Director de Unidad.
- **Exportación de Reportes:** Generación instantánea de reportes ejecutivos en formato PDF.

---

## Modelo de Ponderación del Riesgo de Deserción

El sistema evalúa el nivel de vulnerabilidad de los estudiantes calculando un índice de riesgo acumulativo basado en cuatro dimensiones estratégicas:

$$Puntaje_{Total} = X_{Acad} + X_{Socioecon} + X_{Psicosoc} + X_{Diferencial}$$

### Escala de Valoración

| Nivel de Riesgo | Rango de Puntuación | Acción Institucional |
| :--- | :--- | :--- |
| **Bajo** | 0 – 3 Puntos | Seguimiento académico regular. |
| **Medio** | 4 – 7 Puntos | Intervención preventiva por bienestar / tutoría. |
| **Alto** | $\ge$ 8 Puntos | Atención prioritaria por psicología y dirección académica. |

---

## Arquitectura y Tecnologías

- **Backend:** Laravel 10+ / PHP 8.2+
- **Frontend:** Blade, Tailwind CSS, Alpine.js
- **Base de Datos:** PostgreSQL (Producción en Render) / MariaDB (Desarrollo local)
- **Contenedores y Entorno:** Docker via Laravel Sail / WSL2
- **Despliegue Continuo:** Render Cloud Platform

---

## Despliegue en Render (PostgreSQL)

Para desplegar la aplicación en la plataforma Render:

1. **Build Command:**
   ```bash
   composer install --no-dev --optimize-autoloader && npm install && npm run build
   ```

2. **Start Command:**
   ```bash
   php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
   ```

3. **Variables de Entorno Críticas en Render:**
   - `APP_ENV` = `production`
   - `APP_KEY` = *(Generada previamente con `php artisan key:generate --show`)*
   - `DB_CONNECTION` = `pgsql`
   - `DATABASE_URL` = *(Internal Database URL proporcionada por la PostgreSQL de Render)*
   - `MAIL_*` = *(Configuración del servicio SMTP para el envío de códigos OTP)*

---

## Licencia

Este proyecto es de uso exclusivo e institucional para COTECNOVA - Corporación de Estudios Tecnológicos del Norte del Valle. Todos los derechos reservados.
