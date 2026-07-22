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
git clone https://github.com/josed1231/semana-3-seminario.git
cd semana-3-seminario.git
Explicación: Descarga la copia exacta del proyecto desde GitHub a tu máquina y te ubica dentro del directorio raíz del proyecto.Paso 2: Instalar dependencias de PHP con ComposerEjecuta el comando para descargar las librerías necesarias:Bashcomposer install
Explicación: Lee el archivo composer.json y crea la carpeta vendor con todas las dependencias requeridas por Laravel para que el framework pueda funcionar.Paso 3: Configurar el archivo de variables de entornoCrea una copia del archivo .env.example y nómbralo .env:Bashcp .env.example .env
Explicación: El archivo .env almacena las credenciales locales y configuraciones sensibles (claves de API, puertos, contraseñas de BD) que no se suben al repositorio de Git por seguridad.Abre el archivo .env en tu editor y verifica la configuración de la base de datos y del servidor de correo:Fragmento de códigoAPP_NAME=Laravel
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
Genera la clave única de encriptación para la aplicación:Bashphp artisan key:generate
Explicación: Genera el parámetro APP_KEY en tu .env. Esta clave se utiliza para cifrar datos sensibles de las sesiones de los usuarios y tokens de seguridad.Paso 4: Levantar los contenedores de Docker con Laravel SailAsegúrate de que Docker Desktop esté abierto y ejecutándose. Luego inicia los servicios en segundo plano:Bash./vendor/bin/sail up -d
Explicación del comando:./vendor/bin/sail: Llama al script ejecutable de Sail.up: Descarga las imágenes necesarias y levanta los contenedores (Laravel, MySQL).-d (detached): Ejecuta los contenedores en segundo plano, dejando tu terminal libre para ingresar otros comandos.(Nota opcional: Puedes crear un alias ejecutando alias sail='[ -f sail ] && sh sail || ./vendor/bin/sail' en tu terminal para utilizar únicamente la palabra sail en lugar del comando completo).Paso 5: Ejecutar las migraciones de la base de datosCrea la estructura de tablas dentro del contenedor de MySQL ejecutando:Bashsail artisan migrate
Explicación: Toma los archivos de migración ubicados en database/migrations y genera las tablas correspondientes en la base de datos dentro de Docker.Si el proyecto cuenta con datos iniciales o de prueba configurados, puedes ejecutarlos con:Bashsail artisan migrate --seed
Explicación: Limpia las tablas y las puebla automáticamente con datos ficticios para facilitar las pruebas.Paso 6: Instalar dependencias de Node.js y compilar el Frontend con NPMEste paso es indispensable para que los scripts, componentes interactivos y estilos gráficos del frontend carguen correctamente.Instala los paquetes de Node.js requeridos por el proyecto:Bashnpm install
Explicación: Lee el archivo package.json e instala las librerías necesarias en la carpeta node_modules (incluyendo Vite, Tailwind CSS y componentes de interfaz).Inicia el servidor de desarrollo del frontend:Bashnpm run dev
Explicación: Arranca el servidor de desarrollo de Vite. Este proceso compila y sirve los activos de código JavaScript y CSS en tiempo real con recarga automática (Hot Module Replacement).Importante: Debes mantener esta terminal abierta mientras estés trabajando en la aplicación para asegurar la carga continua de los estilos e interfaces del frontend.4. Cómo Acceder a la AplicaciónUna vez que los contenedores estén corriendo (sail up -d) y el servidor de Vite esté activo (npm run dev), ingresa desde tu navegador web:Aplicación Web Principal: http://localhostRegistro de Usuarios: http://localhost/registerInicio de Sesión: http://localhost/login5. Documentación de la API y Endpoints DisponiblesPara interactuar con los endpoints de la API, asegúrate de incluir siempre el encabezado HTTP: Accept: application/json. Las rutas protegidas requieren además el token de autenticación Bearer (Authorization: Bearer <tu_token>).Endpoints de Autenticación (Públicos y Protegidos)MétodoRutaDescripciónAccesoPOST/api/registerRegistro de un nuevo usuario en el sistemaPúblicoPOST/api/loginAutenticación de usuario y retorno del token de accesoPúblicoPOST/api/logoutCierre de sesión y revocación del token activoProtegido (auth:api)GET/api/meObtiene los datos del usuario autenticado actualmenteProtegido (auth:api)Endpoints de Módulos del Sistema (Protegidos por auth:api)MétodoRutaDescripciónGET/api/usuariosObtiene la lista completa de usuarios del sistemaGET/api/usuarios/{id}Muestra los detalles de un usuario específico por su IDGET/api/programasLista los programas académicos registradosGET/api/programas/{id}Muestra los detalles de un programa académico específicoPOST/api/programasRegistra un nuevo programa académicoGET/api/estudiantesObtiene el listado de estudiantes (con filtrado por rol y seguridad)GET/api/estudiantes/{codigo}Muestra la información detallada de un estudiante por su códigoPOST/api/estudiantesRegistra un nuevo estudiante en el sistemaGET/api/riesgosLista los registros de riesgos de deserción estudiantilGET/api/riesgos/{id}Muestra el detalle de un riesgo de deserción específicoPOST/api/riesgosRegistra un nuevo análisis o nivel de riesgoGET/api/tareasLista completa de tareas almacenadasPOST/api/tareasCrea una nueva tarea (Soporta apiResource)GET/api/tareas/{id}Muestra una tarea específicaPUT/PATCH/api/tareas/{id}Actualiza una tarea existenteDELETE/api/tareas/{id}Elimina una tarea por su identificador único6. Recordatorio de MantenimientoA medida que el proyecto incorpore nuevas características (como gestión de colas con Redis, WebSockets para chat en tiempo real o nuevos paquetes de Composer/NPM), las instrucciones de este archivo README.md serán actualizadas para reflejar los nuevos requisitos.
