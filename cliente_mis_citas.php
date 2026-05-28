<?php
// cliente_mis_citas.php - Ver mis citas (para clientes)
require_once 'includes/auth.php';
require_once 'config/database.php';
requireLogin();

// Redirigir si es admin
if ($_SESSION['rol'] == 'admin') {
    header("Location: reservaciones.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Obtener datos del dueño (cliente)
$dueno = $conn->query("SELECT * FROM duenos WHERE user_id = $user_id")->fetch(PDO::FETCH_ASSOC);

if (!$dueno) {
    // Si no existe dueño, crear uno
    $usuario = $conn->query("SELECT * FROM usuarios WHERE id = $user_id")->fetch(PDO::FETCH_ASSOC);
    $stmt = $conn->prepare("INSERT INTO duenos (user_id, nombre_completo, telefono, email) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $usuario['nombre_completo'], $usuario['telefono'], $usuario['email']]);
    $dueno = $conn->query("SELECT * FROM duenos WHERE user_id = $user_id")->fetch(PDO::FETCH_ASSOC);
}

// Obtener todas las citas del cliente
$citas = $conn->prepare("
    SELECT * FROM reservaciones 
    WHERE email = ? OR telefono = ? 
    ORDER BY fecha_solicitada DESC, hora_solicitada DESC
");
$citas->execute([$dueno['email'], $dueno['telefono']]);
$citas = $citas->fetchAll(PDO::FETCH_ASSOC);

// Contar estadísticas
$total_citas = count($citas);
$citas_pendientes = 0;
$citas_confirmadas = 0;
$citas_atendidas = 0;
$citas_canceladas = 0;
$citas_proximas = 0;

foreach ($citas as $c) {
    if ($c['estado'] == 'pendiente') $citas_pendientes++;
    if ($c['estado'] == 'confirmada') $citas_confirmadas++;
    if ($c['estado'] == 'atendido') $citas_atendidas++;
    if ($c['estado'] == 'cancelada') $citas_canceladas++;
    if (strtotime($c['fecha_solicitada']) >= strtotime(date('Y-m-d'))) $citas_proximas++;
}

// Cancelar cita
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancelar_cita'])) {
    $id_cita = intval($_POST['id']);
    $stmt = $conn->prepare("UPDATE reservaciones SET estado = 'cancelada' WHERE id = ? AND (email = ? OR telefono = ?)");
    $stmt->execute([$id_cita, $dueno['email'], $dueno['telefono']]);
    $mensaje = "✅ Cita cancelada exitosamente";
    // Recargar citas
    $citas = $conn->prepare("SELECT * FROM reservaciones WHERE email = ? OR telefono = ? ORDER BY fecha_solicitada DESC");
    $citas->execute([$dueno['email'], $dueno['telefono']]);
    $citas = $citas->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Citas - Cliente</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
        }

        /* Layout */
        .dashboard-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #1a202c 0%, #2d3748 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 100;
        }

        .sidebar-header {
            padding: 25px;
            text-align: center;
            border-bottom: 1px solid #4a5568;
        }

        .sidebar-header h2 {
            font-size: 1.5em;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 12px;
            color: #a0aec0;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: #e2e8f0;
            text-decoration: none;
            transition: all 0.3s;
            gap: 12px;
        }

        .sidebar-nav a:hover {
            background: #4a5568;
            padding-left: 30px;
        }

        .sidebar-nav a.active {
            background: #667eea;
            border-left: 4px solid white;
        }

        .sidebar-nav .logout {
            margin-top: 30px;
            color: #fc8181;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
        }

        /* Top Bar */
        .top-bar {
            background: white;
            border-radius: 15px;
            padding: 15px 25px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            flex-wrap: wrap;
            gap: 15px;
        }

        .welcome h1 {
            font-size: 1.5em;
            color: #2d3748;
        }

        .welcome p {
            color: #718096;
            font-size: 14px;
            margin-top: 5px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #f7fafc;
            padding: 8px 20px;
            border-radius: 50px;
        }

        .user-info .avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
        }

        .user-info .badge-role {
            background: #e2e8f0;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            color: #4a5568;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            font-size: 28px;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-card p {
            color: #718096;
            font-size: 13px;
        }

        /* Cards */
        .table-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
            flex-wrap: wrap;
            gap: 10px;
        }

        .card-header h2 {
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Tabla */
        .table-container {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .data-table th {
            background: #f7fafc;
            font-weight: 600;
            color: #4a5568;
        }

        .data-table tr:hover {
            background: #f7fafc;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .badge-pendiente {
            background: #fefcbf;
            color: #975a16;
        }

        .badge-confirmada {
            background: #c6f6d5;
            color: #22543d;
        }

        .badge-atendido {
            background: #bee3f8;
            color: #2c5282;
        }

        .badge-cancelada {
            background: #fed7d7;
            color: #742a2a;
        }

        /* Botones */
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-danger {
            background: #fc8181;
            color: white;
        }

        .btn-danger:hover {
            background: #f56565;
        }

        .btn-primary {
            background: #667eea;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary:hover {
            background: #5a67d8;
        }

        /* Alertas */
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #c6f6d5;
            color: #22543d;
            border-left: 4px solid #38a169;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #718096;
        }

        .empty-state span {
            font-size: 48px;
            display: block;
            margin-bottom: 15px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            .sidebar-header h2, .sidebar-header p, .sidebar-nav a span {
                display: none;
            }
            .main-content {
                margin-left: 70px;
                padding: 15px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .data-table th, .data-table td {
                padding: 8px;
                font-size: 12px;
            }
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>🐾 Mis Patitas</h2>
                <p>Clínica Veterinaria</p>
            </div>
            <div class="sidebar-nav">
                <a href="cliente_dashboard.php">
                    <span>📊</span> <span>Mi Panel</span>
                </a>
                <a href="cliente_mascotas.php">
                    <span>🐕</span> <span>Mis Mascotas</span>
                </a>
                <a href="cliente_petshop.php">
                    <span>🛒</span> <span>Pet Shop</span>
                </a>
                <a href="cliente_citas.php">
                    <span>📅</span> <span>Reservar Cita</span>
                </a>
                <a href="cliente_mis_citas.php" class="active">
                    <span>📋</span> <span>Mis Citas</span>
                </a>
                <a href="perfil_cliente.php">
                    <span>👤</span> <span>Mi Perfil</span>
                </a>
                <a href="logout.php" class="logout">
                    <span>🚪</span> <span>Cerrar Sesión</span>
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="welcome">
                    <h1>📋 Mis Citas</h1>
                    <p>Consulta y gestiona todas tus citas veterinarias</p>
                </div>
                <div class="user-info">
                    <div class="avatar">
                        <?php echo strtoupper(substr($_SESSION['nombre'] ?? 'U', 0, 1)); ?>
                    </div>
                    <div>
                        <strong><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></strong>
                        <div class="badge-role">🐾 Cliente</div>
                    </div>
                </div>
            </div>

            <!-- Mensajes -->
            <?php if (isset($mensaje)): ?>
                <div class="alert alert-success">
                    <span>✅</span> <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <!-- Estadísticas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo $total_citas; ?></h3>
                    <p>📋 Total Citas</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $citas_pendientes; ?></h3>
                    <p>⏰ Pendientes</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $citas_confirmadas; ?></h3>
                    <p>✅ Confirmadas</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $citas_atendidas; ?></h3>
                    <p>🏥 Atendidas</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $citas_proximas; ?></h3>
                    <p>📅 Próximas</p>
                </div>
            </div>

            <!-- Lista de Citas -->
            <div class="table-card">
                <div class="card-header">
                    <h2>
                        <span>📋</span> Historial de Citas
                    </h2>
                    <a href="cliente_citas.php" class="btn btn-primary">+ Nueva Cita</a>
                </div>
                
                <?php if (count($citas) > 0): ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mascota</th>
                                    <th>Motivo</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($citas as $c): ?>
                                <tr>
                                    <td><?php echo $c['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($c['nombre_mascota']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($c['especie']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars(substr($c['motivo_consulta'], 0, 50)); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($c['fecha_solicitada'])); ?></td>
                                    <td><?php echo $c['hora_solicitada']; ?></td>
                                    <td>
                                        <?php echo $c['tipo_cita'] == 'presencial' ? '🏥 Presencial' : '🏠 Domicilio'; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $clase = '';
                                        if ($c['estado'] == 'pendiente') $clase = 'badge-pendiente';
                                        elseif ($c['estado'] == 'confirmada') $clase = 'badge-confirmada';
                                        elseif ($c['estado'] == 'atendido') $clase = 'badge-atendido';
                                        else $clase = 'badge-cancelada';
                                        ?>
                                        <span class="badge <?php echo $clase; ?>">
                                            <?php echo $c['estado']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($c['estado'] == 'pendiente' || $c['estado'] == 'confirmada'): ?>
                                            <form method="POST" onsubmit="return confirm('¿Cancelar esta cita?')">
                                                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                                <button type="submit" name="cancelar_cita" class="btn btn-danger">
                                                    ❌ Cancelar
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color: #718096;">No aplica</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <span>📅</span>
                        <p>No tienes citas registradas</p>
                        <a href="cliente_citas.php" class="btn btn-primary" style="margin-top: 15px;">+ Reservar mi primera cita</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Información adicional -->
            <div class="table-card">
                <div class="card-header">
                    <h2>
                        <span>ℹ️</span> Información importante
                    </h2>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div>
                        <strong>⏰ Llegar 10 minutos antes</strong>
                        <p style="font-size: 13px; color: #718096;">Para evitar contratiempos, llega con anticipación a tu cita.</p>
                    </div>
                    <div>
                        <strong>📞 Cancelaciones</strong>
                        <p style="font-size: 13px; color: #718096;">Puedes cancelar tu cita hasta 24 horas antes sin costo.</p>
                    </div>
                    <div>
                        <strong>📋 Documentación</strong>
                        <p style="font-size: 13px; color: #718096;">Trae el carnet de vacunación de tu mascota si lo tienes.</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>