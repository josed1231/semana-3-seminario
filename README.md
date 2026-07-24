Aquí tienes la versión del README.md completamente limpia, sin ningún emoji o icono, lista para copiar y pegar:

Markdown
# Sistema de Monitoreo y Prevención de la Deserción Estudiantil (PIAE) - COTECNOVA

¡Bienvenido al repositorio oficial del **Sistema de Información para la Detección Temprana de la Deserción Estudiantil** de la corporación universitaria **COTECNOVA**!

Este sistema integra el **PIAE** (*Programa Integrado de Acompañamiento Estudiantil*) para caracterizar a los estudiantes, calcular automáticamente su nivel de riesgo académico, psicosocial y socioeconómico, y generar rutas de orientación personalizadas e institucionales.

---

## ¿Qué hace esta aplicación?

El objetivo principal es identificar a tiempo a los estudiantes que puedan estar en riesgo de abandonar sus estudios y brindarles el acompañamiento adecuado según sus necesidades.

### Funcionalidades Clave

* **Cuestionario de Caracterización:** Los estudiantes responden un formulario sobre sus condiciones académicas, laborales, socioeconómicas y psicosociales.
* **Cálculo Automático del Riesgo:** Mediante una matriz de ponderación interna, el sistema evalúa los datos y clasifica al estudiante en un nivel de riesgo (**Bajo**, **Medio** o **Alto**).
* **Diagnóstico y Orientación Automática PIAE:** Se genera de forma automática una recomendación institucional con rutas específicas hacia:
  * *Área de Psicología / Bienestar* (Estrés, salud mental y manejo emocional).
  * *Área Financiera* (Subsidios, convenios de pago y apoyos económicos).
  * *Área de Acompañamiento Académico* (Tutorías pares, nivelación y hábitos de estudio).
* **Dashboard y Monitoreo de Alertas:** Panel de control centralizado para visualizar, buscar y filtrar estudiantes por programa académico, semestre, jornada o palabras clave.
* **Exportación de Reportes en PDF:** Generación de informes consolidados en formato PDF con diseño horizontal listo para imprimir o presentar en comités.
* **Gestión por Roles:** Control de acceso adaptado según el usuario:
  * **Administrador / Director de Bienestar:** Acceso completo a registros, edición y métricas globales.
  * **Director de Unidad / Programa:** Visualización filtrada únicamente para los estudiantes pertenecientes a sus carreras asignadas.
  * **Estudiante:** Acceso exclusivo a su cuestionario y perfil.

---

## Requisitos Previos y Dependencias

Para ejecutar este proyecto en tu equipo local o en un servidor de producción, asegúrate de contar con los siguientes programas e instalaciones básicas:

### 1. Entorno de Desarrollo Base
* **PHP:** Versión 8.1 o superior (con extensiones habilitadas: `mbstring`, `pdo_mysql`, `bcmath`, `openssl`, `tokenizer`, `xml`).
* **Base de Datos:** MySQL o MariaDB (versión 10.4 o superior).
* **Composer:** Gestor de paquetes y dependencias para PHP.
* **Node.js y NPM:** Para la compilación de estilos visuales e interactividad del frontend.

### 2. Librerías y Paquetes Clave del Proyecto
* **Framework Backend:** [Laravel 10.x / 11.x](https://laravel.com/) - Núcleo principal de la arquitectura MVC.
* **Generador de PDF:** [`barryvdh/laravel-dompdf`](https://github.com/barryvdh/laravel-dompdf) - Encargado de renderizar la vista del reporte e imprimir el documento en PDF.
* **Frontend y Estilos:** 
  * [Tailwind CSS](https://tailwindcss.com/) - Diseño responsivo y moderno.
  * [Alpine.js](https://alpinejs.dev/) - Interactividad ligera para componentes en la interfaz.

---

## Guía de Instalación Paso a Paso

Sigue estos sencillos pasos para instalar y ejecutar el proyecto en un entorno local:

### 1. Clonar el Repositorio
Abre tu terminal y descarga el código fuente:
```bash
git clone [https://github.com/tu-usuario/desercion-estudiantil-cotecnova.git](https://github.com/tu-usuario/desercion-estudiantil-cotecnova.git)
cd desercion-estudiantil-cotecnova
2. Instalar Dependencias de PHP
Ejecuta Composer para descargar el framework Laravel y las librerías necesarias:

Bash
composer install
3. Instalar Dependencias de Frontend
Instala los componentes de diseño y scripts:

Bash
npm install
npm run build
4. Configurar las Variables de Entorno (.env)
Duplica el archivo de ejemplo para crear tu configuración personal:

Bash
cp .env.example .env
Abre el archivo .env recién creado en un editor de texto y configura la conexión a tu base de datos:

Fragmento de código
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_tu_basededatos
DB_USERNAME=root
DB_PASSWORD=tu_contraseña
5. Generar la Clave de la Aplicación
Bash
php artisan key:generate
6. Ejecutar Migraciones y Datos Iniciales
Crea la estructura de tablas e inserta los registros base en la base de datos:

Bash
php artisan migrate --seed
7. Iniciar el Servidor de Desarrollo
Corre el servidor local con:

Bash
php artisan serve
El sistema estará disponible para navegar en tu navegador en: http://127.0.0.1:8000

Estructura Principal del Proyecto
Un resumen rápido de los archivos principales para ubicarte dentro del código:

Plaintext
app/
├── Http/
│   └── Controllers/
│       ├── AlertasController.php     # Gestión del Dashboard de Monitoreo e impresión PDF
│       ├── CuestionarioController.php# Captura del formulario, cálculo de riesgo y sincronización
│       └── EstudianteController.php  # Edición y mantenimiento de perfiles estudiantiles
├── Models/
│   ├── Estudiante.php               # Modelo de estudiantes y relaciones de base de datos
│   ├── OrientacionPsicologica.php   # Modelo para almacenar la orientación PIAE
│   └── RiesgoDesercion.php          # Modelo con la clasificación del nivel de riesgo
├── Observers/
│   └── RiesgoDesercionObserver.php  # Disparador automático que genera la orientación al guardar un riesgo
└── Services/
    └── Orientacion.php              # Lógica de negocio del PIAE y generación de recomendaciones
Contribución y Soporte
Este proyecto fue diseñado para el fortalecimiento institucional de COTECNOVA. Si deseas reportar un fallo o proponer una mejora en la lógica de evaluación:

Crea un Issue describiendo la situación o sugerencia.

Abre un Pull Request explicando los cambios propuestos en la rama de desarrollo correspondiente.

Desarrollado para la comunidad académica de COTECNOVA.
