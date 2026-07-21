<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>README - Sistema de Gestion y Notificaciones API</title>
</head>
<body>

    <h1>Sistema de Gestion y Notificaciones API</h1>

    <p>Aplicacion web y API REST desarrollada con Laravel, orientada a la gestion de tareas y envio de notificaciones automatizadas por correo electronico utilizando una arquitectura desacoplada basada en Eventos y Listeners.</p>

    <hr>

    <h2>Requisitos Previos</h2>

    <p>Antes de iniciar con la instalacion, asegurate de contar con las siguientes herramientas instaladas en tu sistema operativo:</p>

    <ul>
        <li>Git</li>
        <li>Docker Desktop (necesario para ejecutar Laravel Sail con MySQL)</li>
        <li>PHP (version 8.2 o superior)</li>
        <li>Composer</li>
        <li>Node.js (version 18 o superior) y NPM</li>
    </ul>

    <hr>

    <h2>Tecnologias e Instalaciones Principales</h2>

    <p>El proyecto requiere e integra las siguientes herramientas:</p>

    <ol>
        <li>Framework Laravel 11: Backend principal y API REST.</li>
        <li>Laravel Sail: Entorno de desarrollo basado en Docker.</li>
        <li>MySQL 8.0: Base de datos relacional (gestionada mediante el contenedor de Sail).</li>
        <li>Laravel Breeze: Sistema de autenticacion de usuarios.</li>
        <li>Node.js y NPM: Compilacion de scripts y estilos frontend con Vite.</li>
        <li>Mailtrap o Driver Log: Servidor SMTP para pruebas de notificaciones por correo.</li>
    </ol>

    <hr>

    <h2>Guia Explicativa de Instalacion Paso a Paso</h2>

    <p>Sigue detenidamente cada uno de los siguientes pasos para configurar e iniciar la aplicacion localmente.</p>

    <h3>Paso 1: Clonar el repositorio</h3>
    <p>Abre tu terminal, navega a la carpeta donde guardas tus proyectos y clona este repositorio:</p>
    <pre><code>git clone https://github.com/tu-usuario/tu-repositorio.git
cd tu-repositorio</code></pre>

    <h3>Paso 2: Instalar dependencias de PHP con Composer</h3>
    <p>Ejecuta el siguiente comando para descargar e instalar los paquetes de Laravel necesarios:</p>
    <pre><code>composer install</code></pre>

    <h3>Paso 3: Configurar el archivo de variables de entorno</h3>
    <p>Crea una copia del archivo <code>.env.example</code> y nombralo <code>.env</code>:</p>
    <pre><code>cp .env.example .env</code></pre>

    <p>Abre el archivo <code>.env</code> recien creado con tu editor de codigo y verifica/ajusta la configuracion de la base de datos y del correo electronico:</p>
    <pre><code>APP_NAME=Laravel
APP_URL=http://localhost

# Configuracion de Base de Datos para Laravel Sail
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=chat_db
DB_USERNAME=sail
DB_PASSWORD=password

# Configuracion de Envio de Correos (Mailtrap)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_usuario_mailtrap
MAIL_PASSWORD=tu_password_mailtrap
MAIL_FROM_ADDRESS="no-reply@tuapp.com"
MAIL_FROM_NAME="${APP_NAME}"</code></pre>

    <p>Genera la clave unica de encriptacion de la aplicacion:</p>
    <pre><code>php artisan key:generate</code></pre>

    <h3>Paso 4: Levantar los contenedores de Docker con Laravel Sail</h3>
    <p>Asegurate de que <strong>Docker Desktop</strong> este en ejecucion. Luego, inicia los servicios de la aplicacion (Laravel y MySQL) ejecutando:</p>
    <pre><code>./vendor/bin/sail up -d</code></pre>
    <p><em>(Nota: Si deseas usar el comando abreviado <code>sail</code> en lugar de <code>./vendor/bin/sail</code>, puedes configurar un alias en tu terminal con: <code>alias sail='[ -f sail ] &amp;&amp; sh sail || ./vendor/bin/sail'</code>).</em></p>

    <h3>Paso 5: Ejecutar las migraciones de la base de datos</h3>
    <p>Una vez que el contenedor de Sail este corriendo, ejecuta las migraciones para crear las tablas necesarias en MySQL:</p>
    <pre><code>sail artisan migrate</code></pre>

    <p>Si el proyecto cuenta con datos de prueba preconfigurados, puedes ejecutarlos con:</p>
    <pre><code>sail artisan migrate --seed</code></pre>

    <h3>Paso 6: Instalar dependencias de Node.js y ejecutar NPM</h3>
    <p>Este paso es fundamental para que los componentes de la interfaz grafica, estilos y scripts del frontend funcionen correctamente.</p>
    <ol>
        <li>
            <p>Instala los paquetes de Node.js listados en el archivo <code>package.json</code>:</p>
            <pre><code>npm install</code></pre>
        </li>
        <li>
            <p>Ejecuta el servidor de desarrollo del frontend (Vite) para compilar los assets en tiempo real:</p>
            <pre><code>npm run dev</code></pre>
        </li>
    </ol>
    <p><strong>Importante:</strong> Deja esta terminal abierta mientras trabajes en la aplicacion para que el frontend responda correctamente.</p>

    <hr>

    <h2>Como Acceder a la Aplicacion</h2>

    <p>Una vez completados los pasos anteriores:</p>
    <ul>
        <li><strong>Aplicacion Web:</strong> Abre tu navegador e ingresa a <code>http://localhost</code>.</li>
        <li><strong>Registro de Usuario:</strong> Ingresa a <code>http://localhost/register</code> para crear una cuenta.</li>
        <li><strong>Inicio de Sesion:</strong> Accede mediante <code>http://localhost/login</code>.</li>
    </ul>

    <hr>

    <h2>Documentacion de la API y Pruebas con Postman</h2>

    <p>La API REST acepta peticiones en formato JSON agregando el encabezado <code>Accept: application/json</code>.</p>

    <h3>Endpoints Disponibles</h3>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Metodo</th>
                <th>Ruta</th>
                <th>Descripcion</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>POST</td>
                <td><code>/api/login</code></td>
                <td>Autenticacion y obtencion de token de acceso</td>
            </tr>
            <tr>
                <td>GET</td>
                <td><code>/api/tasks</code></td>
                <td>Obtener la lista de tareas</td>
            </tr>
            <tr>
                <td>POST</td>
                <td><code>/api/tasks</code></td>
                <td>Crear una tarea (Dispara el evento TaskCreated enviando correo)</td>
            </tr>
            <tr>
                <td>PUT</td>
                <td><code>/api/tasks/{id}</code></td>
                <td>Actualizar una tarea (Dispara el evento TaskUpdated enviando correo)</td>
            </tr>
            <tr>
                <td>DELETE</td>
                <td><code>/api/tasks/{id}</code></td>
                <td>Eliminar una tarea</td>
            </tr>
        </tbody>
    </table>

    <hr>

    <h2>Recordatorio de Mantenimiento</h2>

    <p>Este documento sera actualizado progresivamente si el proyecto requiere la incorporacion de nuevos paquetes de Composer, dependencias de NPM, configuraciones de colas de correo o herramientas adicionales.</p>

</body>
</html>
