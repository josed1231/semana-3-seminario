# Sistema de Gestión y Notificaciones API

Aplicación web y API REST desarrollada con Laravel. Está diseñada para la gestión de tareas y el envío automatizado de notificaciones por correo electrónico mediante una arquitectura desacoplada basada en Eventos y Listeners.

---

## 1. Requisitos Previos y Justificación

Antes de comenzar la instalación, asegúrate de tener instaladas las siguientes herramientas en tu sistema operativo:

* **Git:** Control de versiones necesario para clonar el repositorio y gestionar el código.
* **Docker Desktop:** Entorno de virtualización necesario para ejecutar la infraestructura (servidor web, base de datos) en contenedores aislados sin necesidad de instalar servicios directamente en tu sistema operativo.
* **PHP (versión 8.2 o superior):** Lenguaje en el que está construido Laravel. Es necesario localmente para la ejecución de scripts iniciales de Composer.
* **Composer:** Gestor de dependencias de PHP. Se utiliza para descargar e instalar Laravel y todos los paquetes PHP requeridos por el proyecto.
* **Node.js (versión 18 o superior) y NPM:** Entorno de ejecución de JavaScript y su gestor de paquetes. Se requiere obligatoriamente para descargar la infraestructura del frontend y compilar estilos/scripts.

---

## 2. Tecnologías e Instalaciones Principales

El proyecto requiere e integra las siguientes herramientas en su arquitectura:

1. **Framework Laravel 11:** Proporciona el núcleo del sistema, el enrutamiento de la API REST y la lógica de negocio.
2. **Laravel Sail:** Interfaz de línea de comandos integrada que simplifica la interacción con Docker para ejecutar el entorno local de desarrollo.
3. **MySQL 8.0:** Sistema de gestión de bases de datos relacional para almacenar la información de usuarios, tareas y registros del sistema.
4. **Laravel Breeze:** Kit de inicio para autenticación que provee el flujo de registro, inicio de sesión y protección de rutas mediante tokens/sesiones.
5. **Node.js, NPM y Vite:** Herramientas encargadas de procesar, compilar y servir los activos del frontend (CSS/JS) en tiempo real durante el desarrollo.
6. **Mailtrap / Driver Log:** Servicio SMTP de pruebas para interceptar y previsualizar los correos electrónicos enviados por la aplicación sin afectar a usuarios reales.

---

## 3. Guía Explicativa de Instalación Paso a Paso

Sigue detenidamente cada paso para desplegar el proyecto correctamente en tu entorno local.

### Paso 1: Clonar el repositorio
Abre tu terminal, navega hacia el directorio donde organizas tus proyectos y clona el código fuente:

```bash
git clone https://github.com/tu-usuario/tu-repositorio.git
cd tu-repositorio
```
* **Explicación:** Descarga la copia exacta del proyecto desde GitHub a tu máquina y te ubica dentro del directorio raíz del proyecto.

---

### Paso 2: Instalar dependencias de PHP con Composer
Ejecuta el comando para descargar las librerías necesarias:

```bash
composer install
```
* **Explicación:** Lee el archivo `composer.json` y crea la carpeta `vendor` con todas las dependencias requeridas por Laravel para que el framework pueda funcionar.

---

### Paso 3: Configurar el archivo de variables de entorno
Crea una copia del archivo `.env.example` y nómbralo `.env`:

```bash
cp .env.example .env
```
* **Explicación:** El archivo `.env` almacena las credenciales locales y configuraciones sensibles (claves de API, puertos, contraseñas de BD) que no se suben al repositorio de Git por seguridad.

Abre el archivo `.env` en tu editor y verifica la configuración de la base de datos y del servidor de correo:

```env
APP_NAME=Laravel
APP_URL=http://localhost

# Configuración de Base de Datos para Laravel Sail (Docker)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=chat_db
DB_USERNAME=sail
DB_PASSWORD=password

# Configuración de Envío de Correos (Mailtrap)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_usuario_mailtrap
MAIL_PASSWORD=tu_password_mailtrap
MAIL_FROM_ADDRESS="no-reply@tuapp.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Genera la clave única de encriptación para la aplicación:

```bash
php artisan key:generate
```
* **Explicación:** Genera el parámetro `APP_KEY` en tu `.env`. Esta clave se utiliza para cifrar datos sensibles de las sesiones de los usuarios y tokens de seguridad.

---

### Paso 4: Levantar los contenedores de Docker con Laravel Sail
Asegúrate de que **Docker Desktop** esté abierto y ejecutándose. Luego inicia los servicios en segundo plano:

```bash
./vendor/bin/sail up -d
```
* **Explicación del comando:**
  * `./vendor/bin/sail`: Llama al script ejecutable de Sail.
  * `up`: Descarga las imágenes necesarias y levanta los contenedores (Laravel, MySQL).
  * `-d` (*detached*): Ejecuta los contenedores en segundo plano, dejando tu terminal libre para ingresar otros comandos.

*(Nota opcional: Puedes crear un alias ejecutando `alias sail='[ -f sail ] && sh sail || ./vendor/bin/sail'` en tu terminal para utilizar únicamente la palabra `sail` en lugar del comando completo).*

---

### Paso 5: Ejecutar las migraciones de la base de datos
Crea la estructura de tablas dentro del contenedor de MySQL ejecutando:

```bash
sail artisan migrate
```
* **Explicación:** Toma los archivos de migración ubicados en `database/migrations` y genera las tablas correspondientes en la base de datos dentro de Docker.

Si el proyecto cuenta con datos iniciales o de prueba configurados, puedes ejecutarlos con:

```bash
sail artisan migrate --seed
```
* **Explicación:** Limpia las tablas y las puebla automáticamente con datos ficticios para facilitar las pruebas.

---

### Paso 6: Instalar dependencias de Node.js y compilar el Frontend con NPM
Este paso es indispensable para que los scripts, componentes interactivos y estilos gráficos del frontend carguen correctamente.

1. Instala los paquetes de Node.js requeridos por el proyecto:

```bash
npm install
```
* **Explicación:** Lee el archivo `package.json` e instala las librerías necesarias en la carpeta `node_modules` (incluyendo Vite, Tailwind CSS y componentes de interfaz).

2. Inicia el servidor de desarrollo del frontend:

```bash
npm run dev
```
* **Explicación:** Arranca el servidor de desarrollo de Vite. Este proceso compila y sirve los activos de código JavaScript y CSS en tiempo real con recarga automática (*Hot Module Replacement*).

> **Importante:** Debes mantener esta terminal abierta mientras estés trabajando en la aplicación para asegurar la carga continua de los estilos e interfaces del frontend.

---

## 4. Cómo Acceder a la Aplicación

Una vez que los contenedores estén corriendo (`sail up -d`) y el servidor de Vite esté activo (`npm run dev`), ingresa desde tu navegador web:

* **Aplicación Web Principal:** `http://localhost`
* **Registro de Usuarios:** `http://localhost/register`
* **Inicio de Sesión:** `http://localhost/login`

---

## 5. Documentación de la API y Pruebas con Postman

Para interactuar con los endpoints de la API, asegúrate de incluir siempre el encabezado HTTP: `Accept: application/json`.

### Endpoints Disponibles

| Método | Ruta | Descripción |
| :--- | :--- | :--- |
| `POST` | `/api/login` | Autenticación de usuario y retorno del token de acceso (Sanctum) |
| `GET` | `/api/tasks` | Obtiene la lista completa de tareas almacenadas |
| `POST` | `/api/tasks` | Crea una nueva tarea (Dispara el evento `TaskCreated` que envía notificación por correo) |
| `PUT` | `/api/tasks/{id}` | Actualiza una tarea existente (Dispara el evento `TaskUpdated` enviando correo) |
| `DELETE` | `/api/tasks/{id}` | Elimina una tarea por su identificador único |

---

## 6. Recordatorio de Mantenimiento

A medida que el proyecto incorpore nuevas características (como gestión de colas con Redis, WebSockets para chat en tiempo real o nuevos paquetes de Composer/NPM), las instrucciones de este archivo `README.md` serán actualizadas para reflejar los nuevos requisitos.
