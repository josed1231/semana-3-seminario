Markdown# Sistema de Gestión y Notificaciones API

Aplicación web y API REST desarrollada con Laravel. Está diseñada para la gestión de tareas, control de usuarios, programas académicos, riesgos de deserción y el envío automatizado de notificaciones, estructurada mediante una arquitectura limpia y desacoplada.

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
3. **MySQL 8.0:** Sistema de gestión de bases de datos relacional para almacenar la información de usuarios, estudiantes, programas y registros del sistema.
4. **Laravel Passport / Sanctum / JWT:** Mecanismos de autenticación mediante tokens de acceso seguro para proteger los endpoints de la API.
5. **Node.js, NPM y Vite:** Herramientas encargadas de procesar, compilar y servir los activos del frontend (CSS/JS) en tiempo real durante el desarrollo.
6. **Mailtrap / Driver Log:** Servicio SMTP de pruebas para interceptar y previsualizar los correos electrónicos enviados por la aplicación sin afectar a usuarios reales.

---

## 3. Guía Explicativa de Instalación Paso a Paso

Sigue detenidamente cada paso para desplegar el proyecto correctamente en tu entorno local.

### Paso 1: Clonar el repositorio
Abre tu terminal, navega hacia el directorio donde organizas tus proyectos y clona el código fuente:

```bash
# Guía de Instalación y Configuración del Proyecto

```bash
git clone [https://github.com/josed1231/semana-3-seminario.git](https://github.com/josed1231/semana-3-seminario.git)
cd semana-3-seminario
Explicación: Descarga la copia exacta del proyecto desde GitHub a tu máquina y te ubica dentro del directorio raíz del proyecto.

Paso 2: Instalar dependencias de PHP con Composer
Ejecuta el comando para descargar las librerías necesarias:

Bash
composer install
Explicación: Lee el archivo composer.json y crea la carpeta vendor con todas las dependencias requeridas por Laravel para que el framework pueda funcionar.

Paso 3: Configurar el archivo de variables de entorno
Crea una copia del archivo .env.example y nómbralo .env:

Bash
cp .env.example .env
Explicación: El archivo .env almacena las credenciales locales y configuraciones sensibles (claves de API, puertos, contraseñas de BD) que no se suben al repositorio de Git por seguridad.

Abre el archivo .env en tu editor y verifica que la configuración coincida con el entorno optimizado para el repositorio
