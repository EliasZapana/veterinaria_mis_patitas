<?php
// index.php - Login
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['rol'] == 'admin') {
        header("Location: dashboard.php");
    } else {
        header("Location: cliente_dashboard.php");
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/includes/auth.php';
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = "Complete todos los campos";
    } else {
        if (login($email, $password, $conn)) {
            if ($_SESSION['rol'] == 'admin') {
                header("Location: dashboard.php");
            } else {
                header("Location: cliente_dashboard.php");
            }
            exit();
        } else {
            $error = "Email o contraseña incorrectos";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Clínica Veterinaria</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .login-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
        }

        .login-card h1 {
            color: #764ba2;
            font-size: 2em;
            margin-bottom: 5px;
        }

        .login-card h2 {
            color: #666;
            font-size: 1em;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .login-card input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
        }

        .login-card input:focus {
            outline: none;
            border-color: #764ba2;
        }

        .login-card button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .alert-error {
            background: #fed7d7;
            color: #742a2a;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: left;
        }

        .register-link {
            margin-top: 20px;
            font-size: 14px;
        }

        .register-link a {
            color: #764ba2;
            text-decoration: none;
        }

        .demo-creds {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h1>🐾 Mis Patitas</h1>
            <h2>Clínica Veterinaria</h2>
            
            <?php if ($error): ?>
                <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>📧 Correo Electrónico</label>
                    <input type="email" name="email" placeholder="admin@veterinaria.com" required>
                </div>
                <div class="form-group">
                    <label>🔒 Contraseña</label>
                    <input type="password" name="password" placeholder="••••••" required>
                </div>
                <button type="submit">Iniciar Sesión</button>
            </form>
            
            <div class="register-link">
                ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
            </div>
            
            <div class="demo-creds">
                <strong>🔐 Credenciales de prueba:</strong><br>
                Admin: admin@veterinaria.com / admin123<br>
                Cliente: juan@example.com / admin123
            </div>
        </div>
    </div>
</body>
</html>