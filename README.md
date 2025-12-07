<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Gestor de Proyectos

Sistema integral para la gestión de proyectos, eventos, y participación estudiantil, diseñado para facilitar la organización de hackathons, concursos, y la administración de proyectos académicos o de innovación.

## Características Principales

*   **Gestión de Usuarios con Roles**: Administradores, Jueces, y Participantes con permisos diferenciados.
*   **Administración de Eventos**: Creación, configuración y seguimiento de hackathons o concursos.
*   **Gestión de Equipos**: Formación de equipos por parte de los participantes y manejo de solicitudes de unión/rechazo.
*   **Registro de Participantes**: Perfiles detallados para estudiantes, incluyendo carrera y número de control.
*   **Envío y Seguimiento de Proyectos**: Los equipos pueden registrar y subir avances de sus proyectos.
*   **Evaluación de Proyectos**: Jueces asignados pueden calificar y dejar comentarios en los proyectos.
*   **Interacciones con Solicitudes**: Sistema de notificación y respuesta para solicitudes de equipos.
*   **Vistas Personalizadas**: Paneles de control adaptados para cada tipo de usuario (Administrador, Juez, Participante).

## Tecnologías Utilizadas

*   **Backend**: Laravel (PHP)
*   **Frontend**: Blade (con Alpine.js para interactividad), Tailwind CSS
*   **Base de Datos**: MySQL (o compatible con Eloquent ORM)
*   **Otras**: Composer, NPM/Yarn, Vite

## Requisitos

*   PHP >= 8.2
*   Composer
*   Node.js y NPM (o Yarn)
*   Servidor web (Nginx o Apache)
*   Base de datos (MySQL, PostgreSQL, SQLite, etc.)

## Instalación

Sigue estos pasos para poner el proyecto en marcha en tu máquina local:

1.  **Clona el repositorio:**
    ```bash
    git clone [URL_DEL_REPOSITORIO] gestor-proyectos
    cd gestor-proyectos
    ```
2.  **Instala las dependencias de Composer:**
    ```bash
    composer install
    ```
3.  **Instala las dependencias de NPM y compila los assets:**
    ```bash
    npm install
    npm run dev # O npm run build para producción
    ```
4.  **Crea el archivo `.env`:**
    Copia el archivo `.env.example` y renómbralo a `.env`.
    ```bash
    cp .env.example .env
    ```
5.  **Genera la clave de la aplicación:**
    ```bash
    php artisan key:generate
    ```
6.  **Configura tu base de datos:**
    Abre el archivo `.env` y configura tus credenciales de base de datos (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

7.  **Ejecuta las migraciones y siembra la base de datos (opcional):**
    ```bash
    php artisan migrate --seed
    ```
    El comando `--seed` llenará la base de datos con datos de prueba, incluyendo roles y usuarios iniciales.

8.  **Inicia el servidor de desarrollo (opcional):**
    ```bash
    php artisan serve
    ```
    La aplicación estará disponible en `http://127.0.0.1:8000`.

## Contribución

Si deseas contribuir a este proyecto, por favor, sigue las directrices de código y los estándares establecidos.

## Licencia

Este proyecto está bajo la licencia MIT.
