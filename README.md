# La Compu de Lolo — Tienda Online

Sistema de comercio electrónico para venta de equipos y componentes de cómputo.

## 🛠 Tecnologías

- **Backend:** PHP 8 (mysqli con consultas preparadas)
- **Base de datos:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3, Bootstrap 4/5, Bootstrap Icons
- **JavaScript:** Vanilla JS + Fetch API para el panel admin
- **Servidor local:** WAMP

## ✨ Funcionalidades

### Cliente
- Registro e inicio de sesión con contraseñas hasheadas (`password_hash`)
- Catálogo de productos con categorías y búsqueda
- Carrito de compras (agregar, actualizar cantidad, eliminar)
- Checkout con múltiples métodos de pago (Yape/Plin, transferencia, contra entrega)
- Confirmación de pedido con opción de contacto por WhatsApp
- Formulario de asesoría gratuita

### Administrador
- Panel de administración con login separado
- Dashboard con estadísticas (productos, pedidos, ingresos, usuarios)
- CRUD de productos, categorías, pedidos y usuarios
- Cambio de estado de pedidos y roles de usuario

## 🔒 Seguridad implementada

- **Consultas preparadas** (`prepare` + `bind_param`) en todas las operaciones SQL
- **Hashing de contraseñas** con `password_hash` / `password_verify`
- **Protección IDOR**: verificación de `usuario_id` en accesos a pedidos
- **Transacciones** en el procesamiento de pedidos (rollback en caso de error)
- **Validación y sanitización** de entradas del usuario
- **htmlspecialchars()** en toda salida dinámica (previene XSS)
- **Credenciales de admin externas** (`config.php` fuera del repositorio)
- **Sesiones seguras**: `session_regenerate_id`, cookies HttpOnly

## 📁 Estructura
laclolo/
├── admin.php # Panel de administración
├── index.php # Página principal
├── tienda.php # Catálogo de productos
├── producto.php # Detalle de producto
├── carrito.php # Carrito del usuario
├── checkout.php # Formulario de pago
├── procesar-pedido.php # Lógica de creación de pedidos
├── confirmacion-pedido.php # Resumen del pedido
├── registrar.php # Registro de usuarios
├── iniciar-sesion.php # Login
├── cerrar-sesion.php # Logout
├── asesoria.php # Formulario de contacto
├── enviar-asesoria.php # Procesa el contacto
├── conexion.php # Conexión a la BD
├── header.php / footer.php # Layout global
├── css/ js/ img/ # Recursos estáticos
└── lacompudelolo.sql # Script de base de datos

## 🚀 Instalación local

1. Clona el repositorio en `C:\wamp64\www\laclolo`.
2. Importa `lacompudelolo.sql` en phpMyAdmin.
3. Cree `config.php` con mis credenciales de admin (no se sube a GitHub).
4. Ajusta `conexion.php` con los datos de tu base de datos local.
5. Accede a `http://localhost/laclolo/`.

## 👨‍💻 Autor

Proyecto académico desarrollado por [Alex Portillo].