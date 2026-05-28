<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
requireLogin();

// Redirigir si es admin
if ($_SESSION['rol'] == 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Obtener datos del dueño (cliente)
$user_id = $_SESSION['user_id'];
$dueno = $conn->query("SELECT * FROM duenos WHERE user_id = $user_id")->fetch(PDO::FETCH_ASSOC);
$dueno_id = $dueno['id'];

// Obtener sus mascotas
$mascotas = $conn->query("SELECT * FROM mascotas WHERE dueno_id = $dueno_id")->fetchAll();

// Obtener sus citas próximas
$citas = $conn->query("SELECT * FROM reservaciones WHERE email = '{$dueno['email']}' ORDER BY fecha_solicitada DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Panel - Cliente</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; }
        .dashboard { display: flex; min-height: 100vh; }
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #1a202c 0%, #2d3748 100%);
            color: white;
            position: fixed;
            height: 100vh;
            padding: 20px;
        }
        .sidebar h3 { text-align: center; margin-bottom: 30px; }
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: #e2e8f0;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 5px;
        }
        .sidebar a:hover, .sidebar a.active { background: #667eea; }
        .logout { margin-top: 50px; color: #fc8181 !important; }
        .content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
        }
        .top-bar {
            background: white;
            padding: 15px 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
        }
        .stat-card h3 { font-size: 32px; color: #667eea; }
        .card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        .card h2 { margin-bottom: 15px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .badge-pendiente { background: #fefcbf; padding: 4px 10px; border-radius: 20px; }
        .badge-confirmada { background: #c6f6d5; padding: 4px 10px; border-radius: 20px; }
    </style>
</head>
<body>
    <div class="dashboard">
        <nav class="sidebar">
            <h3>🐾 Mis Patitas</h3>
            <a href="cliente_dashboard.php" class="active">📊 Mi Panel</a>
            <a href="cliente_mascotas.php">🐕 Mis Mascotas</a>
            <a href="cliente_petshop.php">🛒 Pet Shop</a>
            <a href="cliente_citas.php">📅 Reservar Cita</a>
            <a href="cliente_mis_citas.php">📋 Mis Citas</a>
            <a href="perfil_cliente.php">👤 Mi Perfil</a>
            <a href="logout.php" class="logout">🚪 Salir</a>
        </nav>
        
        <main class="content">
            <div class="top-bar">
                <h1>¡Bienvenido, <?php echo $_SESSION['nombre']; ?>!</h1>
                <div>🐾 Cliente</div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo count($mascotas); ?></h3>
                    <p>🐕 Mis Mascotas</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count($citas); ?></h3>
                    <p>📅 Mis Citas</p>
                </div>
            </div>
            
            <div class="card">
                <h2>📋 Mis Mascotas</h2>
                <table>
                    <thead><tr><th>Nombre</th><th>Especie</th><th>Raza</th><th>Edad</th></tr></thead>
                    <tbody>
                        <?php foreach($mascotas as $m): ?>
                        <tr>
                            <td><?php echo $m['nombre']; ?></td>
                            <td><?php echo $m['especie']; ?></td>
                            <td><?php echo $m['raza']; ?></td>
                            <td><?php echo $m['edad']; ?> años</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card">
                <h2>📅 Últimas Citas</h2>
                <table>
                    <thead><tr><th>Mascota</th><th>Fecha</th><th>Hora</th><th>Estado</th></tr></thead>
                    <tbody>
                        <?php foreach($citas as $c): ?>
                        <tr>
                            <td><?php echo $c['nombre_mascota']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($c['fecha_solicitada'])); ?></td>
                            <td><?php echo $c['hora_solicitada']; ?></td>
                            <td><span class="badge-<?php echo $c['estado']; ?>"><?php echo $c['estado']; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>