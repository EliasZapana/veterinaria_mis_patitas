<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
requireLogin();
if ($_SESSION['rol'] == 'admin') { header("Location: dashboard.php"); exit(); }

$user_id = $_SESSION['user_id'];
$dueno = $conn->query("SELECT * FROM duenos WHERE user_id = $user_id")->fetch();
$mascotas = $conn->query("SELECT * FROM mascotas WHERE dueno_id = {$dueno['id']}")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reservar'])) {
    $stmt = $conn->prepare("INSERT INTO reservaciones (nombre_cliente, telefono, email, nombre_mascota, especie, motivo_consulta, fecha_solicitada, hora_solicitada, tipo_cita, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')");
    $stmt->execute([
        $dueno['nombre_completo'],
        $dueno['telefono'],
        $dueno['email'],
        $_POST['nombre_mascota'],
        $_POST['especie'],
        $_POST['motivo'],
        $_POST['fecha'],
        $_POST['hora'],
        $_POST['tipo_cita']
    ]);
    $mensaje = "✅ Cita reservada exitosamente";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reservar Cita</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; }
        .dashboard { display: flex; }
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #1a202c 0%, #2d3748 100%);
            color: white;
            position: fixed;
            height: 100vh;
            padding: 20px;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: #e2e8f0;
            text-decoration: none;
            border-radius: 10px;
        }
        .sidebar a:hover { background: #667eea; }
        .content { flex: 1; margin-left: 280px; padding: 30px; }
        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        button {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <nav class="sidebar">
            <h3>🐾 Mis Patitas</h3>
            <a href="cliente_dashboard.php">📊 Mi Panel</a>
            <a href="cliente_mascotas.php">🐕 Mis Mascotas</a>
            <a href="cliente_petshop.php">🛒 Pet Shop</a>
            <a href="cliente_citas.php">📅 Reservar Cita</a>
            <a href="cliente_mis_citas.php">📋 Mis Citas</a>
            <a href="perfil_cliente.php">👤 Mi Perfil</a>
            <a href="logout.php">🚪 Salir</a>
        </nav>
        
        <main class="content">
            <h1>📅 Reservar una cita</h1>
            <?php if(isset($mensaje)) echo "<div class='card' style='background:#c6f6d5;'>$mensaje</div>"; ?>
            
            <div class="card">
                <form method="POST">
                    <label>Mascota</label>
                    <select name="nombre_mascota" required>
                        <option value="">Selecciona una mascota</option>
                        <?php foreach($mascotas as $m): ?>
                        <option value="<?php echo $m['nombre']; ?>"><?php echo $m['nombre']; ?> (<?php echo $m['especie']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    
                    <label>Especie</label>
                    <input type="text" name="especie" required>
                    
                    <label>Motivo de consulta</label>
                    <textarea name="motivo" rows="3" required></textarea>
                    
                    <label>Fecha</label>
                    <input type="date" name="fecha" min="<?php echo date('Y-m-d'); ?>" required>
                    
                    <label>Hora</label>
                    <input type="time" name="hora" required>
                    
                    <label>Tipo de cita</label>
                    <select name="tipo_cita">
                        <option value="presencial">🏥 Presencial</option>
                        <option value="domicilio">🏠 Domicilio</option>
                    </select>
                    
                    <button type="submit" name="reservar">✅ Reservar cita</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>