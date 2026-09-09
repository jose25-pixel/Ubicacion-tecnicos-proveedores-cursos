# RepuestosApp-zeta

Aplicación Laravel para gestión y comercio de repuestos con interfaz web, usuarios y contenido comercial.

## Requisitos

Antes de ejecutar el proyecto asegúrate de tener instalado:

- PHP 8.3 o superior
- Composer
- Node.js y npm
- MySQL o MariaDB

## Ejecutar el proyecto

En la raíz del proyecto ejecuta:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Luego configura la base de datos en el archivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=repuestosapp
DB_USERNAME=root
DB_PASSWORD=
```

Después crea la base de datos y ejecuta las migraciones:

```bash
php artisan migrate
```

Para levantar la aplicación en modo local:

```bash
php artisan serve
```

La aplicación quedará disponible en:

```text
http://127.0.0.1:8000
```

Si quieres compilar los assets del frontend en modo desarrollo:

```bash
npm run dev
```

Y para producción:

```bash
npm run build
```

## Uso básico

1. Abre la URL local en el navegador.
2. Registra un usuario o inicia sesión.
3. Navega por las secciones públicas y del panel administrativo.
4. Gestiona perfiles, contenido y operaciones del sistema.
5. Si se cargan archivos, revisa que la carpeta `storage` tenga permisos correctos.

## Socket / WebSocket

Este proyecto puede usar un socket para comunicaciones en tiempo real, como notificaciones, actualización de estados o eventos del sistema.

### Opción 1: Laravel Reverb (recomendado)

Si vas a usar WebSockets con Laravel, instala Reverb:

```bash
composer require laravel/reverb
php artisan reverb:start
```

Esto levanta el servidor de WebSocket en el puerto por defecto, normalmente:

```text
ws://127.0.0.1:8001
```

En el frontend puedes conectarte con JavaScript:

```js
import Echo from 'laravel-echo';

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: 'app-key',
    wsHost: window.location.hostname,
    wsPort: 8080,
    wssPort: 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});

window.Echo.channel('orders').listen('OrderCreated', (event) => {
    console.log('Nuevo pedido:', event);
});
```

### Opción 2: Socket.io

Si prefieres un servidor Socket.IO personalizado:

```bash
npm install socket.io
```

Y en un archivo de servidor:

```js
const io = require('socket.io')(3001);

io.on('connection', (socket) => {
    console.log('Cliente conectado');

    socket.on('join-room', (room) => {
        socket.join(room);
    });

    socket.on('send-message', (payload) => {
        io.to(payload.room).emit('new-message', payload);
    });
});
```

Luego desde el navegador:

```js
const socket = io('http://localhost:3001');

socket.emit('join-room', 'ventas');

socket.on('new-message', (data) => {
    console.log(data);
});
```

## Solución de problemas comunes

### Error de permisos en storage

```bash
chmod -R 775 storage bootstrap/cache
```

### Error de base de datos

Verifica que la base de datos exista y que los datos de `.env` coincidan con tu entorno local.

### Error al compilar frontend

```bash
rm -rf node_modules
npm install
npm run build
```

## Licencia

Este proyecto usa la licencia MIT de Laravel y está sujeto a las condiciones del proyecto base.
