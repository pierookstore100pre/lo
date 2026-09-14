<?php
// ============================================================
// PANEL DE ADMINISTRACIÓN - LA COMPU DE LOLO
// Acceso restringido - Solo personal autorizado
// ============================================================

// ── SESIÓN SEGURA ──────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    // Configurar cookies de sesión de forma compatible con cualquier PHP
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    } else {
        session_set_cookie_params(0, '/; samesite=Lax', '', false, true);
    }
    ini_set('session.use_strict_mode', '1');
    session_start();
}

include_once('conexion.php');

// Verificar que las credenciales de admin existan (fallback por si falta config.php)
if (!defined('ADMIN_USER'))     define('ADMIN_USER', 'admin');
if (!defined('ADMIN_PASS_HASH')) define('ADMIN_PASS_HASH', '');

// ── LOGOUT ─────────────────────────────────────────────────
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged']);
    session_regenerate_id(true);
    header('Location: admin.php');
    exit;
}

// ── PROCESAR LOGIN ─────────────────────────────────────────
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $user_ok = hash_equals(ADMIN_USER, (string)($_POST['admin_user'] ?? ''));
    $pass_ok = ADMIN_PASS_HASH !== '' && password_verify((string)($_POST['admin_pass'] ?? ''), ADMIN_PASS_HASH);

    if ($user_ok && $pass_ok) {
        session_regenerate_id(true); // Prevenir session fixation
        $_SESSION['admin_logged'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $login_error = 'Credenciales incorrectas.';
    }
}

// ── PROTECCIÓN ─────────────────────────────────────────────
$logged = isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;

// Helper: responder JSON y salir
function json_out($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

// ── PROCESAR ACCIONES AJAX / POST (solo si está logueado) ──
if ($logged) {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    // ---- ELIMINAR PRODUCTO ----
    if ($action === 'delete_producto' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $conexion->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        json_out(['ok' => true]);
    }

    // ---- TOGGLE ACTIVO PRODUCTO ----
    if ($action === 'toggle_producto' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $conexion->prepare("UPDATE productos SET activo = IF(activo=1,0,1) WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conexion->prepare("SELECT activo FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        json_out(['activo' => (int)($r['activo'] ?? 0)]);
    }

    // ---- GUARDAR PRODUCTO (nuevo o edición) ----
    if ($action === 'save_producto') {
        $id          = (int)($_POST['id'] ?? 0);
        $nombre      = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio      = (float)($_POST['precio'] ?? 0);
        $precio_of   = (isset($_POST['precio_oferta']) && $_POST['precio_oferta'] !== '')
                        ? (float)$_POST['precio_oferta']
                        : null;
        $cat_id      = (int)($_POST['categoria_id'] ?? 0);
        $stock       = (int)($_POST['stock'] ?? 0);
        $destacado   = isset($_POST['destacado']) ? 1 : 0;
        $activo      = isset($_POST['activo'])    ? 1 : 0;
        $imagen      = trim($_POST['imagen'] ?? '');

        if ($id > 0) {
            // UPDATE
            if ($precio_of === null) {
                $stmt = $conexion->prepare(
                    "UPDATE productos SET nombre=?, descripcion=?, precio=?, precio_oferta=NULL,
                     imagen=?, categoria_id=?, stock=?, destacado=?, activo=? WHERE id=?"
                );
                $stmt->bind_param("ssdsiiiii",
                    $nombre, $descripcion, $precio, $imagen,
                    $cat_id, $stock, $destacado, $activo, $id
                );
            } else {
                $stmt = $conexion->prepare(
                    "UPDATE productos SET nombre=?, descripcion=?, precio=?, precio_oferta=?,
                     imagen=?, categoria_id=?, stock=?, destacado=?, activo=? WHERE id=?"
                );
                $stmt->bind_param("ssddsiiiii",
                    $nombre, $descripcion, $precio, $precio_of, $imagen,
                    $cat_id, $stock, $destacado, $activo, $id
                );
            }
            $stmt->execute();
            $stmt->close();
        } else {
            // INSERT
            if ($precio_of === null) {
                $stmt = $conexion->prepare(
                    "INSERT INTO productos (nombre,descripcion,precio,precio_oferta,imagen,
                     categoria_id,stock,destacado,activo)
                     VALUES (?,?,?,NULL,?,?,?,?,?)"
                );
                $stmt->bind_param("ssdsiiii",
                    $nombre, $descripcion, $precio, $imagen,
                    $cat_id, $stock, $destacado, $activo
                );
            } else {
                $stmt = $conexion->prepare(
                    "INSERT INTO productos (nombre,descripcion,precio,precio_oferta,imagen,
                     categoria_id,stock,destacado,activo)
                     VALUES (?,?,?,?,?,?,?,?,?)"
                );
                $stmt->bind_param("ssddsiiii",
                    $nombre, $descripcion, $precio, $precio_of, $imagen,
                    $cat_id, $stock, $destacado, $activo
                );
            }
            $stmt->execute();
            $new_id = $conexion->insert_id;
            $stmt->close();
            $id = $new_id;
        }
        json_out(['ok' => true, 'id' => $id]);
    }

    // ---- ACTUALIZAR ESTADO PEDIDO ----
    if ($action === 'update_pedido' && isset($_POST['id'], $_POST['estado'])) {
        $id     = (int)$_POST['id'];
        $estado = (string)$_POST['estado'];
        $stmt = $conexion->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
        $stmt->bind_param("si", $estado, $id);
        $stmt->execute();
        $stmt->close();
        json_out(['ok' => true]);
    }

    // ---- CAMBIAR ROL USUARIO ----
    if ($action === 'update_rol' && isset($_POST['id'], $_POST['rol'])) {
        $id  = (int)$_POST['id'];
        $rol = (string)$_POST['rol'];
        $stmt = $conexion->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
        $stmt->bind_param("si", $rol, $id);
        $stmt->execute();
        $stmt->close();
        json_out(['ok' => true]);
    }

    // ---- ELIMINAR USUARIO ----
    if ($action === 'delete_usuario' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        json_out(['ok' => true]);
    }

    // ---- GUARDAR CATEGORÍA ----
    if ($action === 'save_categoria') {
        $id     = (int)($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $slug   = trim($_POST['slug'] ?? '');
        $desc   = trim($_POST['descripcion'] ?? '');

        if ($id > 0) {
            $stmt = $conexion->prepare("UPDATE categorias SET nombre=?, slug=?, descripcion=? WHERE id=?");
            $stmt->bind_param("sssi", $nombre, $slug, $desc, $id);
        } else {
            $stmt = $conexion->prepare("INSERT INTO categorias (nombre,slug,descripcion) VALUES (?,?,?)");
            $stmt->bind_param("sss", $nombre, $slug, $desc);
        }
        $stmt->execute();
        $stmt->close();
        json_out(['ok' => true]);
    }

    // ---- ELIMINAR CATEGORÍA ----
    if ($action === 'delete_categoria' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $conexion->prepare("DELETE FROM categorias WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        json_out(['ok' => true]);
    }
}

// ── LEER DATOS (solo si logueado) ──────────────────────────
if ($logged) {
    $stats = [];

    $stats['productos']  = (int)$conexion->query("SELECT COUNT(*) c FROM productos")->fetch_assoc()['c'];
    $stats['usuarios']   = (int)$conexion->query("SELECT COUNT(*) c FROM usuarios")->fetch_assoc()['c'];
    $stats['pedidos']    = (int)$conexion->query("SELECT COUNT(*) c FROM pedidos")->fetch_assoc()['c'];
    $stats['categorias'] = (int)$conexion->query("SELECT COUNT(*) c FROM categorias")->fetch_assoc()['c'];
    $r = $conexion->query("SELECT SUM(total) s FROM pedidos WHERE estado != 'cancelado'");
    $stats['ingresos']   = number_format((float)($r->fetch_assoc()['s'] ?? 0), 2);
    $stats['pendientes'] = (int)$conexion->query("SELECT COUNT(*) c FROM pedidos WHERE estado='pendiente'")->fetch_assoc()['c'];

    $productos  = $conexion->query("SELECT p.*, c.nombre cat FROM productos p LEFT JOIN categorias c ON p.categoria_id=c.id ORDER BY p.id DESC")->fetch_all(MYSQLI_ASSOC);
    $categorias = $conexion->query("SELECT * FROM categorias ORDER BY id")->fetch_all(MYSQLI_ASSOC);
    $pedidos    = $conexion->query("SELECT p.*, u.nombre u_nombre, u.email u_email FROM pedidos p LEFT JOIN usuarios u ON p.usuario_id=u.id ORDER BY p.id DESC")->fetch_all(MYSQLI_ASSOC);
    $usuarios   = $conexion->query("SELECT * FROM usuarios ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
    $ultimos_pedidos = $conexion->query("SELECT p.*, u.nombre u_nombre FROM pedidos p LEFT JOIN usuarios u ON p.usuario_id=u.id ORDER BY p.id DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Admin — La Compu de Lolo</title>
<meta name="robots" content="noindex, nofollow">
<!-- Bootstrap 4 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ═══════════════════════════════════════════════
   VARIABLES & BASE
═══════════════════════════════════════════════ */
:root {
    --bg:        #0d1117;
    --surface:   #161b22;
    --surface2:  #1c2230;
    --border:    rgba(255,255,255,0.07);
    --primary:   #3b82f6;
    --primary-d: #2563eb;
    --success:   #10b981;
    --warning:   #f59e0b;
    --danger:    #ef4444;
    --info:      #06b6d4;
    --text:      #e2e8f0;
    --muted:     #64748b;
    --sidebar-w: 240px;
}

* { box-sizing: border-box; }

body {
    font-family: 'Inter', sans-serif;
    background: var(--bg);
    color: var(--text);
    margin: 0;
    font-size: 0.9rem;
}

/* ─── LOGIN PAGE ─── */
.login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(ellipse at 60% 20%, #1e3a5f 0%, #0d1117 60%);
}
.login-box {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 2.5rem 2rem;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 24px 60px rgba(0,0,0,0.5);
}
.login-box h2 { font-size: 1.4rem; font-weight: 700; color: #fff; }

/* ─── LAYOUT ─── */
.admin-wrap { display: flex; min-height: 100vh; }

/* ─── SIDEBAR ─── */
.sidebar {
    width: var(--sidebar-w);
    background: var(--surface);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0; left: 0; bottom: 0;
    z-index: 100;
    transition: transform .25s;
}
.sidebar-brand {
    padding: 1.25rem 1.2rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 10px;
}
.sidebar-brand img { height: 32px; }
.sidebar-brand span {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--primary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.sidebar nav { flex: 1; padding: 1rem 0; overflow-y: auto; }
.nav-section {
    padding: 0.35rem 1.2rem;
    font-size: 0.68rem;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 0.75rem;
}
.nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0.55rem 1.2rem;
    color: #94a3b8 !important;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 0;
    transition: background .15s, color .15s;
    cursor: pointer;
    text-decoration: none;
    border: none;
    background: none;
    width: 100%;
}
.nav-link i { font-size: 1rem; width: 20px; text-align: center; }
.nav-link:hover, .nav-link.active {
    background: rgba(59,130,246,0.12);
    color: #fff !important;
}
.nav-link.active { color: var(--primary) !important; border-left: 3px solid var(--primary); }
.sidebar-footer {
    padding: 1rem 1.2rem;
    border-top: 1px solid var(--border);
    font-size: 0.8rem;
    color: var(--muted);
}

/* ─── MAIN CONTENT ─── */
.main-content {
    margin-left: var(--sidebar-w);
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}
.topbar {
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    padding: 0.75rem 1.75rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 90;
}
.topbar-title { font-size: 1.05rem; font-weight: 700; color: #fff; }
.topbar-right { display: flex; align-items: center; gap: 12px; }
.admin-badge {
    background: rgba(59,130,246,0.15);
    color: var(--primary);
    border-radius: 20px;
    padding: 3px 12px;
    font-size: 0.78rem;
    font-weight: 600;
}

/* ─── CONTENT AREA ─── */
.content-area { padding: 1.75rem; flex: 1; }

/* ─── TABS / SECTIONS ─── */
.section { display: none; }
.section.active { display: block; }

/* ─── STAT CARDS ─── */
.stat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px,1fr)); gap: 1rem; margin-bottom: 1.75rem; }
.stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.25rem;
    position: relative;
    overflow: hidden;
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
}
.stat-card.blue::before  { background: var(--primary); }
.stat-card.green::before { background: var(--success); }
.stat-card.yellow::before{ background: var(--warning); }
.stat-card.red::before   { background: var(--danger); }
.stat-card.cyan::before  { background: var(--info); }
.stat-card.purple::before{ background: #8b5cf6; }

.stat-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    margin-bottom: 0.75rem;
}
.stat-icon.blue   { background: rgba(59,130,246,0.15); color: var(--primary); }
.stat-icon.green  { background: rgba(16,185,129,0.15); color: var(--success); }
.stat-icon.yellow { background: rgba(245,158,11,0.15); color: var(--warning); }
.stat-icon.red    { background: rgba(239,68,68,0.15);  color: var(--danger); }
.stat-icon.cyan   { background: rgba(6,182,212,0.15);  color: var(--info); }
.stat-icon.purple { background: rgba(139,92,246,0.15); color: #8b5cf6; }

.stat-value { font-size: 1.6rem; font-weight: 800; color: #fff; line-height: 1; }
.stat-label { font-size: 0.78rem; color: var(--muted); margin-top: 4px; }

/* ─── PANEL CARDS ─── */
.panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    margin-bottom: 1.5rem;
}
.panel-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.panel-title { font-size: 0.95rem; font-weight: 700; color: #fff; margin: 0; }
.panel-body { padding: 1.25rem; }

/* ─── TABLES ─── */
.admin-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
.admin-table thead th {
    background: var(--surface2);
    color: var(--muted);
    font-weight: 600;
    padding: 0.7rem 0.9rem;
    text-align: left;
    border-bottom: 1px solid var(--border);
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}
.admin-table tbody td {
    padding: 0.7rem 0.9rem;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
    color: var(--text);
}
.admin-table tbody tr:last-child td { border-bottom: none; }
.admin-table tbody tr:hover { background: rgba(255,255,255,0.025); }

/* ─── BADGES ─── */
.badge-estado {
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
    display: inline-block;
}
.badge-pendiente  { background: rgba(245,158,11,0.15);  color: var(--warning); }
.badge-pagado     { background: rgba(16,185,129,0.15);  color: var(--success); }
.badge-enviado    { background: rgba(59,130,246,0.15);  color: var(--primary); }
.badge-entregado  { background: rgba(6,182,212,0.15);   color: var(--info); }
.badge-cancelado  { background: rgba(239,68,68,0.15);   color: var(--danger); }
.badge-admin      { background: rgba(139,92,246,0.15);  color: #a78bfa; }
.badge-cliente    { background: rgba(100,116,139,0.15); color: #94a3b8; }
.badge-activo     { background: rgba(16,185,129,0.15);  color: var(--success); }
.badge-inactivo   { background: rgba(239,68,68,0.15);   color: var(--danger); }

/* ─── FORM CONTROLS ─── */
.form-control, .form-select {
    background: var(--surface2) !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
    color: var(--text) !important;
    border-radius: 8px !important;
    font-size: 0.875rem !important;
}
.form-control:focus, .form-select:focus {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.15) !important;
    outline: none !important;
}
.form-control::placeholder { color: var(--muted) !important; }
label { color: #94a3b8; font-size: 0.8rem; font-weight: 500; margin-bottom: 4px; }

/* ─── BUTTONS ─── */
.btn-admin {
    padding: 5px 12px;
    border-radius: 7px;
    font-size: 0.8rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: opacity .15s, transform .1s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.btn-admin:hover { opacity: 0.85; transform: translateY(-1px); }
.btn-admin:active { transform: translateY(0); }
.btn-primary-admin  { background: var(--primary);  color: #fff; }
.btn-success-admin  { background: var(--success);  color: #fff; }
.btn-warning-admin  { background: var(--warning);  color: #000; }
.btn-danger-admin   { background: var(--danger);   color: #fff; }
.btn-ghost {
    background: rgba(255,255,255,0.07);
    color: var(--text);
}

/* ─── MODAL ─── */
.modal-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.75);
    z-index: 9000;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}
.modal-overlay.open { display: flex; }
.modal-box {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    width: 100%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    animation: fadeInUp .2s ease;
}
@keyframes fadeInUp {
    from { opacity:0; transform: translateY(20px); }
    to   { opacity:1; transform: translateY(0); }
}
.modal-header {
    padding: 1.1rem 1.4rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.modal-header h5 { margin:0; font-size: 1rem; font-weight: 700; color:#fff; }
.modal-close { background:none; border:none; color:var(--muted); font-size: 1.3rem; cursor:pointer; line-height:1; }
.modal-close:hover { color: #fff; }
.modal-body { padding: 1.25rem 1.4rem; }
.modal-footer { padding: 1rem 1.4rem; border-top: 1px solid var(--border); display:flex; gap:.5rem; justify-content:flex-end; }

/* ─── SEARCH ─── */
.search-bar { position:relative; }
.search-bar input { padding-left: 34px !important; }
.search-bar i { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--muted); font-size:.9rem; }

/* ─── TOAST ─── */
#toast {
    position: fixed;
    bottom: 1.5rem; right: 1.5rem;
    background: var(--success);
    color: #fff;
    padding: 0.65rem 1.2rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    z-index: 99999;
    opacity: 0;
    transform: translateY(10px);
    transition: all .3s;
    pointer-events: none;
}
#toast.show { opacity:1; transform:translateY(0); }
#toast.error { background: var(--danger); }

/* ─── PRODUCT IMG PREVIEW ─── */
.img-preview {
    width: 40px; height: 40px;
    border-radius: 8px;
    object-fit: cover;
    background: var(--surface2);
}

/* ─── SCROLLBAR ─── */
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

/* ─── RECENT TABLE ─── */
.recent-badge { font-size: 0.72rem; font-weight: 600; padding: 2px 8px; border-radius: 12px; }
</style>
</head>
<body>

<!-- ══════════════════════════════════════
     PANTALLA DE LOGIN
══════════════════════════════════════ -->
<?php if (!$logged): ?>
<div class="login-page">
    <div class="login-box">
        <div class="text-center mb-4">
            <img src="./img/logo-clean.png" alt="Logo" style="height:44px; margin-bottom:1rem;">
            <h2>Panel Administrador</h2>
            <p style="color:var(--muted); font-size:.85rem; margin:0;">Acceso restringido — solo personal autorizado</p>
        </div>

        <?php if ($login_error): ?>
        <div style="background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); color:#fca5a5; border-radius:8px; padding:.65rem 1rem; font-size:.85rem; margin-bottom:1rem;">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars($login_error) ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="admin_login" value="1">
            <div class="mb-3">
                <label>Usuario</label>
                <div style="position:relative;">
                    <i class="bi bi-person" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);"></i>
                    <input type="text" name="admin_user" class="form-control" style="padding-left:34px;" placeholder="admin" autocomplete="off" required>
                </div>
            </div>
            <div class="mb-4">
                <label>Contraseña</label>
                <div style="position:relative;">
                    <i class="bi bi-lock" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);"></i>
                    <input type="password" name="admin_pass" class="form-control" style="padding-left:34px;" placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" style="width:100%; padding:.65rem; background:var(--primary); color:#fff; border:none; border-radius:8px; font-weight:700; font-size:.95rem; cursor:pointer; transition:opacity .2s;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <i class="bi bi-shield-lock me-1"></i> Ingresar al Panel
            </button>
        </form>

        <p style="text-align:center; margin-top:1.5rem; color:var(--muted); font-size:.78rem;">
            <i class="bi bi-arrow-left me-1"></i><a href="index.php" style="color:var(--muted); text-decoration:none;">Volver al sitio</a>
        </p>
    </div>
</div>

<?php else: // ══ PANEL PRINCIPAL ══ ?>

<div id="toast"></div>

<div class="admin-wrap">

<!-- ─── SIDEBAR ─── -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="./img/logo-clean.png" alt="Logo">
        <span>Admin Panel</span>
    </div>
    <nav>
        <div class="nav-section">General</div>
        <a class="nav-link active" onclick="showSection('dashboard')">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <div class="nav-section">Catálogo</div>
        <a class="nav-link" onclick="showSection('productos')">
            <i class="bi bi-box-seam"></i> Productos
        </a>
        <a class="nav-link" onclick="showSection('categorias')">
            <i class="bi bi-tags"></i> Categorías
        </a>

        <div class="nav-section">Ventas</div>
        <a class="nav-link" onclick="showSection('pedidos')">
            <i class="bi bi-receipt"></i> Pedidos
            <?php if ($stats['pendientes'] > 0): ?>
            <span style="margin-left:auto; background:var(--warning); color:#000; border-radius:10px; padding:1px 7px; font-size:.7rem; font-weight:700;"><?= $stats['pendientes'] ?></span>
            <?php endif; ?>
        </a>

        <div class="nav-section">Usuarios</div>
        <a class="nav-link" onclick="showSection('usuarios')">
            <i class="bi bi-people"></i> Clientes
        </a>

        <div class="nav-section">Sitio</div>
        <a class="nav-link" href="index.php" target="_blank">
            <i class="bi bi-globe"></i> Ver Tienda
        </a>
    </nav>
    <div class="sidebar-footer">
        <div style="color:#fff; font-size:.82rem; font-weight:600; margin-bottom:4px;">
            <i class="bi bi-shield-check me-1" style="color:var(--success);"></i> Administrador
        </div>
        <a href="?logout=1" style="color:var(--muted); font-size:.78rem; text-decoration:none;">
            <i class="bi bi-box-arrow-left me-1"></i> Cerrar sesión
        </a>
    </div>
</aside>

<!-- ─── MAIN ─── -->
<div class="main-content">
    <div class="topbar">
        <span class="topbar-title" id="topbarTitle">Dashboard</span>
        <div class="topbar-right">
            <span class="admin-badge"><i class="bi bi-shield-fill me-1"></i>Admin</span>
            <a href="?logout=1" class="btn-admin btn-ghost" style="font-size:.78rem; padding:4px 10px; text-decoration:none;">
                <i class="bi bi-box-arrow-left"></i> Salir
            </a>
        </div>
    </div>

    <div class="content-area">

        <!-- ══════════════════ DASHBOARD ══════════════════ -->
        <div class="section active" id="section-dashboard">
            <div class="stat-grid">
                <div class="stat-card blue">
                    <div class="stat-icon blue"><i class="bi bi-box-seam"></i></div>
                    <div class="stat-value"><?= $stats['productos'] ?></div>
                    <div class="stat-label">Productos</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-icon green"><i class="bi bi-currency-dollar"></i></div>
                    <div class="stat-value">S/ <?= $stats['ingresos'] ?></div>
                    <div class="stat-label">Ingresos totales</div>
                </div>
                <div class="stat-card yellow">
                    <div class="stat-icon yellow"><i class="bi bi-receipt"></i></div>
                    <div class="stat-value"><?= $stats['pedidos'] ?></div>
                    <div class="stat-label">Pedidos</div>
                </div>
                <div class="stat-card red">
                    <div class="stat-icon red"><i class="bi bi-clock-history"></i></div>
                    <div class="stat-value"><?= $stats['pendientes'] ?></div>
                    <div class="stat-label">Pendientes</div>
                </div>
                <div class="stat-card cyan">
                    <div class="stat-icon cyan"><i class="bi bi-people"></i></div>
                    <div class="stat-value"><?= $stats['usuarios'] ?></div>
                    <div class="stat-label">Usuarios</div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-icon purple"><i class="bi bi-tags"></i></div>
                    <div class="stat-value"><?= $stats['categorias'] ?></div>
                    <div class="stat-label">Categorías</div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <h6 class="panel-title"><i class="bi bi-receipt me-2" style="color:var(--primary);"></i>Últimos pedidos</h6>
                    <button class="btn-admin btn-ghost" onclick="showSection('pedidos')">Ver todos →</button>
                </div>
                <div class="panel-body" style="padding:0;">
                    <?php if (empty($ultimos_pedidos)): ?>
                        <div style="padding:2rem; text-align:center; color:var(--muted);">
                            <i class="bi bi-inbox" style="font-size:2rem;"></i>
                            <p style="margin-top:.5rem;">Aún no hay pedidos registrados.</p>
                        </div>
                    <?php else: ?>
                    <table class="admin-table">
                        <thead><tr>
                            <th>#</th><th>Cliente</th><th>Total</th><th>Estado</th><th>Fecha</th>
                        </tr></thead>
                        <tbody>
                        <?php foreach ($ultimos_pedidos as $p): ?>
                        <tr>
                            <td><span style="font-weight:700; color:var(--primary);">#<?= $p['id'] ?></span></td>
                            <td><?= htmlspecialchars($p['u_nombre'] ?? 'N/A') ?></td>
                            <td style="font-weight:600;">S/ <?= number_format($p['total'],2) ?></td>
                            <td><span class="badge-estado badge-<?= $p['estado'] ?>"><?= ucfirst($p['estado']) ?></span></td>
                            <td style="color:var(--muted);"><?= date('d/m/Y', strtotime($p['fecha_pedido'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="panel">
                    <div class="panel-header">
                        <h6 class="panel-title"><i class="bi bi-box-seam me-2" style="color:var(--primary);"></i>Accesos rápidos</h6>
                    </div>
                    <div class="panel-body">
                        <button class="btn-admin btn-primary-admin mb-2 w-100" onclick="openProductoModal()">
                            <i class="bi bi-plus-lg"></i> Nuevo Producto
                        </button>
                        <button class="btn-admin btn-ghost mb-2 w-100" onclick="showSection('pedidos')">
                            <i class="bi bi-receipt"></i> Ver Pedidos
                        </button>
                        <button class="btn-admin btn-ghost w-100" onclick="showSection('categorias')">
                            <i class="bi bi-tags"></i> Gestionar Categorías
                        </button>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-header">
                        <h6 class="panel-title"><i class="bi bi-info-circle me-2" style="color:var(--info);"></i>Info del sistema</h6>
                    </div>
                    <div class="panel-body" style="font-size:.83rem; color:var(--muted); line-height:2;">
                        <div><i class="bi bi-server me-2"></i>PHP <?= PHP_VERSION ?></div>
                        <div><i class="bi bi-database me-2"></i>MariaDB — lacompudelolo</div>
                        <div><i class="bi bi-clock me-2"></i><?= date('d/m/Y H:i') ?></div>
                        <div><i class="bi bi-shield-check me-2" style="color:var(--success);"></i>Sesión activa</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══════════════════ PRODUCTOS ══════════════════ -->
        <div class="section" id="section-productos">
            <div class="panel">
                <div class="panel-header">
                    <h6 class="panel-title"><i class="bi bi-box-seam me-2" style="color:var(--primary);"></i>Productos (<?= count($productos) ?>)</h6>
                    <button class="btn-admin btn-primary-admin" onclick="openProductoModal()">
                        <i class="bi bi-plus-lg"></i> Nuevo
                    </button>
                </div>
                <div class="panel-body" style="padding:1rem 1.25rem;">
                    <div class="search-bar mb-3">
                        <i class="bi bi-search"></i>
                        <input type="text" class="form-control" id="searchProducto" placeholder="Buscar producto..." oninput="filterTable('tblProductos', this.value)">
                    </div>
                </div>
                <div style="overflow-x:auto;">
                <table class="admin-table" id="tblProductos">
                    <thead><tr>
                        <th>ID</th><th>Img</th><th>Nombre</th><th>Categoría</th>
                        <th>Precio</th><th>Oferta</th><th>Stock</th><th>Estado</th><th>Acciones</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($productos as $p): ?>
                    <tr data-id="<?= $p['id'] ?>">
                        <td style="color:var(--muted);"><?= $p['id'] ?></td>
                        <td><img src="./img/<?= htmlspecialchars($p['imagen']) ?>" class="img-preview" onerror="this.src='https://via.placeholder.com/40x40/1c2230/64748b?text=?'"></td>
                        <td style="max-width:200px;"><span style="font-weight:600;"><?= htmlspecialchars($p['nombre']) ?></span></td>
                        <td><span style="font-size:.78rem; color:var(--muted);"><?= htmlspecialchars($p['cat'] ?? '—') ?></span></td>
                        <td>S/ <?= number_format($p['precio'],2) ?></td>
                        <td><?= $p['precio_oferta'] ? 'S/ '.number_format($p['precio_oferta'],2) : '<span style="color:var(--muted);">—</span>' ?></td>
                        <td><?= $p['stock'] ?></td>
                        <td>
                            <button class="btn-admin" style="padding:2px 9px; font-size:.72rem; background:<?= $p['activo'] ? 'rgba(16,185,129,.15)' : 'rgba(239,68,68,.15)' ?>; color:<?= $p['activo'] ? 'var(--success)' : 'var(--danger)' ?>; border-radius:12px;" onclick="toggleProducto(<?= $p['id'] ?>, this)">
                                <?= $p['activo'] ? 'Activo' : 'Inactivo' ?>
                            </button>
                        </td>
                        <td>
                            <button class="btn-admin btn-warning-admin" onclick='editProducto(<?= json_encode($p) ?>)' title="Editar"><i class="bi bi-pencil"></i></button>
                            <button class="btn-admin btn-danger-admin" onclick="deleteProducto(<?= $p['id'] ?>, this)" title="Eliminar"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <!-- ══════════════════ CATEGORÍAS ══════════════════ -->
        <div class="section" id="section-categorias">
            <div class="panel">
                <div class="panel-header">
                    <h6 class="panel-title"><i class="bi bi-tags me-2" style="color:var(--primary);"></i>Categorías (<?= count($categorias) ?>)</h6>
                    <button class="btn-admin btn-primary-admin" onclick="openCatModal()">
                        <i class="bi bi-plus-lg"></i> Nueva
                    </button>
                </div>
                <div style="overflow-x:auto;">
                <table class="admin-table" id="tblCategorias">
                    <thead><tr>
                        <th>ID</th><th>Nombre</th><th>Slug</th><th>Descripción</th><th>Acciones</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($categorias as $c): ?>
                    <tr data-id="<?= $c['id'] ?>">
                        <td style="color:var(--muted);"><?= $c['id'] ?></td>
                        <td style="font-weight:600;"><?= htmlspecialchars($c['nombre']) ?></td>
                        <td><code style="background:var(--surface2); padding:2px 6px; border-radius:4px; font-size:.78rem;"><?= htmlspecialchars($c['slug']) ?></code></td>
                        <td style="color:var(--muted); max-width:200px;"><?= htmlspecialchars($c['descripcion'] ?? '—') ?></td>
                        <td>
                            <button class="btn-admin btn-warning-admin" onclick='editCat(<?= json_encode($c) ?>)'><i class="bi bi-pencil"></i></button>
                            <button class="btn-admin btn-danger-admin" onclick="deleteCat(<?= $c['id'] ?>, this)"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <!-- ══════════════════ PEDIDOS ══════════════════ -->
        <div class="section" id="section-pedidos">
            <div class="panel">
                <div class="panel-header">
                    <h6 class="panel-title"><i class="bi bi-receipt me-2" style="color:var(--primary);"></i>Pedidos (<?= count($pedidos) ?>)</h6>
                </div>
                <div class="panel-body" style="padding:1rem 1.25rem;">
                    <div class="search-bar">
                        <i class="bi bi-search"></i>
                        <input type="text" class="form-control" placeholder="Buscar por cliente o ID..." oninput="filterTable('tblPedidos', this.value)">
                    </div>
                </div>
                <div style="overflow-x:auto;">
                <table class="admin-table" id="tblPedidos">
                    <thead><tr>
                        <th>#</th><th>Cliente</th><th>Email</th><th>Total</th>
                        <th>Dirección</th><th>Teléfono</th><th>Fecha</th><th>Estado</th>
                    </tr></thead>
                    <tbody>
                    <?php if (empty($pedidos)): ?>
                    <tr><td colspan="8" style="text-align:center; color:var(--muted); padding:2rem;">Sin pedidos aún</td></tr>
                    <?php else: ?>
                    <?php foreach ($pedidos as $p): ?>
                    <tr data-id="<?= $p['id'] ?>">
                        <td style="font-weight:700; color:var(--primary);">#<?= $p['id'] ?></td>
                        <td style="font-weight:600;"><?= htmlspecialchars($p['u_nombre'] ?? 'N/A') ?></td>
                        <td style="color:var(--muted); font-size:.8rem;"><?= htmlspecialchars($p['u_email'] ?? '') ?></td>
                        <td style="font-weight:700; color:var(--success);">S/ <?= number_format($p['total'],2) ?></td>
                        <td style="font-size:.8rem; max-width:150px; color:var(--muted);"><?= htmlspecialchars($p['direccion_envio']) ?></td>
                        <td style="font-size:.8rem;"><?= htmlspecialchars($p['telefono']) ?></td>
                        <td style="color:var(--muted); font-size:.8rem;"><?= date('d/m/Y H:i', strtotime($p['fecha_pedido'])) ?></td>
                        <td>
                            <select class="form-control" style="width:130px; padding:4px 6px; font-size:.78rem;" onchange="updatePedido(<?= $p['id'] ?>, this.value)">
                                <?php foreach (['pendiente','pagado','enviado','entregado','cancelado'] as $e): ?>
                                <option value="<?= $e ?>" <?= $p['estado'] === $e ? 'selected' : '' ?>><?= ucfirst($e) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <!-- ══════════════════ USUARIOS ══════════════════ -->
        <div class="section" id="section-usuarios">
            <div class="panel">
                <div class="panel-header">
                    <h6 class="panel-title"><i class="bi bi-people me-2" style="color:var(--primary);"></i>Usuarios (<?= count($usuarios) ?>)</h6>
                </div>
                <div class="panel-body" style="padding:1rem 1.25rem;">
                    <div class="search-bar">
                        <i class="bi bi-search"></i>
                        <input type="text" class="form-control" placeholder="Buscar por nombre o email..." oninput="filterTable('tblUsuarios', this.value)">
                    </div>
                </div>
                <div style="overflow-x:auto;">
                <table class="admin-table" id="tblUsuarios">
                    <thead><tr>
                        <th>ID</th><th>Nombre</th><th>Email</th><th>Teléfono</th>
                        <th>Rol</th><th>Registro</th><th>Acciones</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($usuarios as $u): ?>
                    <tr data-id="<?= $u['id'] ?>">
                        <td style="color:var(--muted);"><?= $u['id'] ?></td>
                        <td style="font-weight:600;"><?= htmlspecialchars($u['nombre']) ?></td>
                        <td style="font-size:.83rem;"><?= htmlspecialchars($u['email']) ?></td>
                        <td style="color:var(--muted); font-size:.83rem;"><?= htmlspecialchars($u['telefono'] ?? '—') ?></td>
                        <td>
                            <select class="form-control" style="width:110px; padding:3px 6px; font-size:.78rem;" onchange="updateRol(<?= $u['id'] ?>, this.value)">
                                <option value="cliente" <?= $u['rol']==='cliente'?'selected':'' ?>>Cliente</option>
                                <option value="admin"   <?= $u['rol']==='admin'  ?'selected':'' ?>>Admin</option>
                            </select>
                        </td>
                        <td style="color:var(--muted); font-size:.8rem;"><?= date('d/m/Y', strtotime($u['fecha_registro'])) ?></td>
                        <td>
                            <button class="btn-admin btn-danger-admin" onclick="deleteUsuario(<?= $u['id'] ?>, this)"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

    </div><!-- /content-area -->
</div><!-- /main-content -->
</div><!-- /admin-wrap -->

<!-- ══════════════════ MODAL PRODUCTO ══════════════════ -->
<div class="modal-overlay" id="modalProducto">
    <div class="modal-box">
        <div class="modal-header">
            <h5 id="modalProductoTitle">Nuevo Producto</h5>
            <button class="modal-close" onclick="closeModal('modalProducto')">&#x2715;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="pId">
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label>Nombre del producto *</label>
                    <input type="text" id="pNombre" class="form-control" placeholder="Ej: Mouse Logitech G203">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Categoría *</label>
                    <select id="pCategoria" class="form-control">
                        <?php foreach ($categorias as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label>Descripción</label>
                <textarea id="pDescripcion" class="form-control" rows="2" placeholder="Descripción del producto..."></textarea>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Precio (S/) *</label>
                    <input type="number" id="pPrecio" class="form-control" step="0.01" placeholder="0.00">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Precio oferta (S/)</label>
                    <input type="number" id="pOferta" class="form-control" step="0.01" placeholder="Vacío = sin oferta">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Stock *</label>
                    <input type="number" id="pStock" class="form-control" placeholder="0">
                </div>
            </div>
            <div class="mb-3">
                <label>Nombre de imagen (en /img/)</label>
                <input type="text" id="pImagen" class="form-control" placeholder="Ej: img1.jpg">
            </div>
            <div class="d-flex gap-3">
                <div class="custom-control custom-checkbox mr-3">
                    <input type="checkbox" class="custom-control-input" id="pDestacado">
                    <label class="custom-control-label" for="pDestacado" style="color:var(--text);">Destacado</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="pActivo" checked>
                    <label class="custom-control-label" for="pActivo" style="color:var(--text);">Activo</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-admin btn-ghost" onclick="closeModal('modalProducto')">Cancelar</button>
            <button class="btn-admin btn-primary-admin" onclick="saveProducto()">
                <i class="bi bi-check-lg"></i> Guardar
            </button>
        </div>
    </div>
</div>

<!-- ══════════════════ MODAL CATEGORÍA ══════════════════ -->
<div class="modal-overlay" id="modalCat">
    <div class="modal-box" style="max-width:440px;">
        <div class="modal-header">
            <h5 id="modalCatTitle">Nueva Categoría</h5>
            <button class="modal-close" onclick="closeModal('modalCat')">&#x2715;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="cId">
            <div class="mb-3">
                <label>Nombre *</label>
                <input type="text" id="cNombre" class="form-control" placeholder="Ej: Laptops Gamer">
            </div>
            <div class="mb-3">
                <label>Slug (URL amigable) *</label>
                <input type="text" id="cSlug" class="form-control" placeholder="Ej: laptops-gamer">
            </div>
            <div class="mb-3">
                <label>Descripción</label>
                <textarea id="cDesc" class="form-control" rows="2"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-admin btn-ghost" onclick="closeModal('modalCat')">Cancelar</button>
            <button class="btn-admin btn-primary-admin" onclick="saveCat()">
                <i class="bi bi-check-lg"></i> Guardar
            </button>
        </div>
    </div>
</div>

<!-- ══════════════════ JAVASCRIPT ══════════════════ -->
<script>
const SELF = 'admin.php';

// ── NAV ────────────────────────────────────────────
function showSection(name) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.getElementById('section-' + name).classList.add('active');
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    event && event.currentTarget && event.currentTarget.classList.add('active');
    document.getElementById('topbarTitle').textContent = {
        dashboard:'Dashboard', productos:'Productos', categorias:'Categorías',
        pedidos:'Pedidos', usuarios:'Clientes'
    }[name] || name;
}

// ── TOAST ──────────────────────────────────────────
function toast(msg, type='') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'show' + (type ? ' ' + type : '');
    setTimeout(() => t.className = '', 2800);
}

// ── MODAL ──────────────────────────────────────────
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

// Click fuera cierra
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});

// ── SEARCH / FILTER ────────────────────────────────
function filterTable(tableId, q) {
    q = q.toLowerCase();
    const rows = document.getElementById(tableId).querySelectorAll('tbody tr');
    rows.forEach(r => r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none');
}

// ── AJAX helper ────────────────────────────────────
async function api(data) {
    const fd = new FormData();
    Object.entries(data).forEach(([k,v]) => fd.append(k,v));
    const r = await fetch(SELF, { method:'POST', body:fd });
    return r.json();
}

// ══ PRODUCTOS ══════════════════════════════════════

function openProductoModal(data = null) {
    document.getElementById('pId').value         = data ? data.id : '';
    document.getElementById('pNombre').value     = data ? data.nombre : '';
    document.getElementById('pDescripcion').value= data ? (data.descripcion||'') : '';
    document.getElementById('pPrecio').value     = data ? data.precio : '';
    document.getElementById('pOferta').value     = data ? (data.precio_oferta||'') : '';
    document.getElementById('pStock').value      = data ? data.stock : '';
    document.getElementById('pImagen').value     = data ? data.imagen : '';
    document.getElementById('pCategoria').value  = data ? data.categoria_id : '';
    document.getElementById('pDestacado').checked= data ? data.destacado == 1 : false;
    document.getElementById('pActivo').checked   = data ? data.activo == 1 : true;
    document.getElementById('modalProductoTitle').textContent = data ? 'Editar Producto' : 'Nuevo Producto';
    openModal('modalProducto');
}

function editProducto(data) { openProductoModal(data); }

async function saveProducto() {
    const nombre = document.getElementById('pNombre').value.trim();
    const precio = document.getElementById('pPrecio').value;
    if (!nombre || !precio) { toast('Nombre y precio son obligatorios', 'error'); return; }
    const res = await api({
        action:'save_producto',
        id:          document.getElementById('pId').value,
        nombre,
        descripcion: document.getElementById('pDescripcion').value,
        precio,
        precio_oferta: document.getElementById('pOferta').value,
        categoria_id: document.getElementById('pCategoria').value,
        stock:       document.getElementById('pStock').value,
        imagen:      document.getElementById('pImagen').value,
        destacado:   document.getElementById('pDestacado').checked ? '1' : '',
        activo:      document.getElementById('pActivo').checked ? '1' : '',
    });
    if (res.ok) { toast('✅ Producto guardado'); closeModal('modalProducto'); setTimeout(()=>location.reload(),600); }
}

async function deleteProducto(id, btn) {
    if (!confirm('¿Eliminar este producto? Esta acción no se puede deshacer.')) return;
    const res = await api({ action:'delete_producto', id });
    if (res.ok) { btn.closest('tr').remove(); toast('🗑️ Producto eliminado'); }
}

async function toggleProducto(id, btn) {
    const res = await api({ action:'toggle_producto', id });
    if (res.activo === 1) {
        btn.textContent = 'Activo';
        btn.style.background = 'rgba(16,185,129,.15)';
        btn.style.color = 'var(--success)';
    } else {
        btn.textContent = 'Inactivo';
        btn.style.background = 'rgba(239,68,68,.15)';
        btn.style.color = 'var(--danger)';
    }
    toast('Estado actualizado');
}

// ══ CATEGORÍAS ═════════════════════════════════════

function openCatModal(data = null) {
    document.getElementById('cId').value     = data ? data.id : '';
    document.getElementById('cNombre').value = data ? data.nombre : '';
    document.getElementById('cSlug').value   = data ? data.slug : '';
    document.getElementById('cDesc').value   = data ? (data.descripcion||'') : '';
    document.getElementById('modalCatTitle').textContent = data ? 'Editar Categoría' : 'Nueva Categoría';
    openModal('modalCat');
}

function editCat(data) { openCatModal(data); }

async function saveCat() {
    const nombre = document.getElementById('cNombre').value.trim();
    const slug   = document.getElementById('cSlug').value.trim();
    if (!nombre || !slug) { toast('Nombre y slug son obligatorios', 'error'); return; }
    const res = await api({
        action:'save_categoria',
        id: document.getElementById('cId').value,
        nombre, slug,
        descripcion: document.getElementById('cDesc').value
    });
    if (res.ok) { toast('✅ Categoría guardada'); closeModal('modalCat'); setTimeout(()=>location.reload(),600); }
}

async function deleteCat(id, btn) {
    if (!confirm('¿Eliminar esta categoría?')) return;
    const res = await api({ action:'delete_categoria', id });
    if (res.ok) { btn.closest('tr').remove(); toast('🗑️ Categoría eliminada'); }
}

// ── Autoslug ──
document.getElementById('cNombre')?.addEventListener('input', function() {
    const slug = this.value.toLowerCase()
        .replace(/[áàä]/g,'a').replace(/[éèë]/g,'e').replace(/[íìï]/g,'i')
        .replace(/[óòö]/g,'o').replace(/[úùü]/g,'u').replace(/ñ/g,'n')
        .replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
    document.getElementById('cSlug').value = slug;
});

// ══ PEDIDOS ════════════════════════════════════════

async function updatePedido(id, estado) {
    const res = await api({ action:'update_pedido', id, estado });
    if (res.ok) toast('✅ Pedido #' + id + ' → ' + estado);
}

// ══ USUARIOS ═══════════════════════════════════════

async function updateRol(id, rol) {
    const res = await api({ action:'update_rol', id, rol });
    if (res.ok) toast('✅ Rol actualizado');
}

async function deleteUsuario(id, btn) {
    if (!confirm('¿Eliminar este usuario? Se eliminarán sus datos.')) return;
    const res = await api({ action:'delete_usuario', id });
    if (res.ok) { btn.closest('tr').remove(); toast('🗑️ Usuario eliminado'); }
}

// ── Nav click fix ──────────────────────────────────
document.querySelectorAll('.nav-link[onclick]').forEach(link => {
    link.addEventListener('click', function() {
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>

<?php endif; ?>
</body>
</html>
