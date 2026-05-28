<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
requireLogin();
if ($_SESSION['rol'] == 'admin') { header("Location: dashboard.php"); exit(); }

$user_id = $_SESSION['user_id'];
$dueno = $conn->query("SELECT * FROM duenos WHERE user_id = $user_id")->fetch();
$dueno_id = $dueno['id'];

// Agregar mascota
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
    $stmt = $conn->prepare("INSERT INTO mascotas (nombre, especie, raza, edad, sexo, peso, dueno_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['nombre'], $_POST['especie'], $_POST['raza'], $_POST['edad'], $_POST['sexo'], $_POST['peso'], $dueno_id]);
    $mensaje = "✅ Mascota registrada";
}

$mascotas = $conn->query("SELECT * FROM mascotas WHERE dueno_id = $dueno_id ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mis Mascotas</title>
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
        input, select {
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
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .btn-danger { background: #fc8181; }
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
            <h1>🐕 Mis Mascotas</h1>
            <?php if(isset($mensaje)) echo "<div class='card' style='background:#c6f6d5;'>$mensaje</div>"; ?>
            
            <div class="card">
                <h2>📝 Registrar nueva mascota</h2>
                <form method="POST">
                    <input type="text" name="nombre" placeholder="Nombre" required>
                    <input type="text" name="especie" placeholder="Especie (Perro/Gato)" required>
                    <input type="text" name="raza" placeholder="Raza">
                    <input type="number" name="edad" placeholder="Edad">
                    <select name="sexo">
                        <option value="Macho">Macho</option>
                        <option value="Hembra">Hembra</option>
                    </select>
                    <input type="number" step="0.01" name="peso" placeholder="Peso (kg)">
                    <button type="submit" name="crear">✅ Registrar</button>
                </form>
            </div>
            
            <div class="card">
                <h2>📋 Lista de mis mascotas</h2>
                <table>
                    <thead><tr><th>Nombre</th><th>Especie</th><th>Raza</th><th>Edad</th><th>Sexo</th><th>Peso</th></tr></thead>
                    <tbody>
                        <?php foreach($mascotas as $m): ?>
                        <tr>
                            <td><?php echo $m['nombre']; ?></td>
                            <td><?php echo $m['especie']; ?></td>
                            <td><?php echo $m['raza']; ?></td>
                            <td><?php echo $m['edad']; ?></td>
                            <td><?php echo $m['sexo']; ?></td>
                            <td><?php echo $m['peso']; ?> kg</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>