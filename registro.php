<?php
// registro.php - Registro de nuevos usuarios (CLIENTES)
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['rol'] == 'admin' ? 'dashboard.php' : 'cliente_dashboard.php'));
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once __DIR__ . '/config/database.php';
    
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($nombre) || empty($email) || empty($password)) {
        $error = "Complete todos los campos obligatorios";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email inválido";
    } elseif (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden";
    } else {
        try {
            $conn->beginTransaction();
            
            // Verificar si el email ya existe
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "El email ya está registrado";
                $conn->rollBack();
            } else {
                // 1. Insertar en usuarios (rol = 'cliente')
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO usuarios (nombre_completo, email, telefono, contraseña, rol) VALUES (?, ?, ?, ?, 'cliente')");
                $stmt->execute([$nombre, $email, $telefono, $hashed_password]);
                $user_id = $conn->lastInsertId();
                
                // 2. Insertar en duenos (vinculado al user_id)
                $stmt2 = $conn->prepare("INSERT INTO duenos (user_id, nombre_completo, telefono, email, direccion) VALUES (?, ?, ?, ?, ?)");
                $stmt2->execute([$user_id, $nombre, $telefono, $email, $direccion]);
                
                $conn->commit();
                $success = "✅ ¡Registro exitoso! Ahora puedes iniciar sesión.";
                $_POST = [];
            }
        } catch(PDOException $e) {
            $conn->rollBack();
            $error = "Error al registrar: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registro Cliente - Mis Patitas</title>
    <style>
        /* Tus estilos aquí */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .register-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .alert-error { background: #fed7d7; color: #742a2a; padding: 10px; border-radius: 8px; margin-bottom: 15px; }
        .alert-success { background: #c6f6d5; color: #22543d; padding: 10px; border-radius: 8px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="register-card">
        <h1>🐾 Mis Patitas</h1>
        <h2>Registro de Cliente</h2>
        
        <?php if ($error): ?>
            <div class="alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert-success"><?php echo $success; ?></div>
            <meta http-equiv="refresh" content="2;url=login.php">
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Nombre completo *</label>
                <input type="text" name="nombre" required value="<?php echo $_POST['nombre'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required value="<?php echo $_POST['email'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label>Teléfono *</label>
                <input type="tel" name="telefono" required value="<?php echo $_POST['telefono'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label>Dirección</label>
                <textarea name="direccion" rows="2"><?php echo $_POST['direccion'] ?? ''; ?></textarea>
            </div>
            <div class="form-group">
                <label>Contraseña * (mínimo 6 caracteres)</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirmar contraseña *</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit">Registrarse</button>
        </form>
        <p style="text-align: center; margin-top: 20px;">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    </div>
</body>
</html>