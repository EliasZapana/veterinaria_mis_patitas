<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
requireLogin();
if ($_SESSION['rol'] == 'admin') { header("Location: dashboard.php"); exit(); }

$productos = $conn->query("SELECT * FROM productos WHERE cantidad > 0 ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pet Shop</title>
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
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .product-card {
            background: white;
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .product-card h3 { color: #2d3748; margin-bottom: 10px; }
        .price { font-size: 24px; color: #667eea; font-weight: bold; margin: 10px 0; }
        .stock { color: #718096; font-size: 12px; margin-bottom: 10px; }
        .btn-buy {
            background: #38a169;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .cart {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
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
            <h1>🛒 Pet Shop</h1>
            
            <div class="cart">
                <h2>🛍️ Mi Carrito</h2>
                <div id="carrito-contenido">
                    <p>No hay productos en el carrito</p>
                </div>
                <button onclick="finalizarCompra()" style="margin-top: 10px;">📦 Finalizar Pedido</button>
            </div>
            
            <div class="products-grid">
                <?php foreach($productos as $p): ?>
                <div class="product-card">
                    <h3><?php echo $p['nombre']; ?></h3>
                    <p><?php echo $p['descripcion']; ?></p>
                    <div class="price">S/ <?php echo number_format($p['precio'], 2); ?></div>
                    <div class="stock">Stock: <?php echo $p['cantidad']; ?> unidades</div>
                    <button class="btn-buy" onclick="agregarAlCarrito(<?php echo $p['id']; ?>, '<?php echo $p['nombre']; ?>', <?php echo $p['precio']; ?>)">
                        🛒 Agregar al carrito
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
    
    <script>
        let carrito = [];
        
        function agregarAlCarrito(id, nombre, precio) {
            let item = carrito.find(p => p.id === id);
            if (item) {
                item.cantidad++;
            } else {
                carrito.push({ id, nombre, precio, cantidad: 1 });
            }
            actualizarCarrito();
        }
        
        function actualizarCarrito() {
            let div = document.getElementById('carrito-contenido');
            if (carrito.length === 0) {
                div.innerHTML = '<p>No hay productos en el carrito</p>';
                return;
            }
            let html = '<table style="width:100%; border-collapse:collapse;">';
            html += '<tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th></th></tr>';
            let total = 0;
            carrito.forEach(item => {
                let subtotal = item.precio * item.cantidad;
                total += subtotal;
                html += `<tr>
                            <td>${item.nombre}</td>
                            <td>S/ ${item.precio}</td>
                            <td>${item.cantidad}</td>
                            <td>S/ ${subtotal.toFixed(2)}</td>
                            <td><button onclick="eliminarDelCarrito(${item.id})">❌</button></td>
                         </tr>`;
            });
            html += `<tr style="font-weight:bold;"><td colspan="3">Total</td><td colspan="2">S/ ${total.toFixed(2)}</td></tr>`;
            html += '</table>';
            div.innerHTML = html;
        }
        
        function eliminarDelCarrito(id) {
            carrito = carrito.filter(p => p.id !== id);
            actualizarCarrito();
        }
        
        function finalizarCompra() {
            if (carrito.length === 0) {
                alert('El carrito está vacío');
                return;
            }
            let mensaje = '🛍️ Pedido:\n';
            carrito.forEach(item => {
                mensaje += `${item.nombre} x${item.cantidad} = S/ ${(item.precio * item.cantidad).toFixed(2)}\n`;
            });
            mensaje += '\n📞 Contactar al veterinario para coordinar la entrega y pago.';
            alert(mensaje);
            carrito = [];
            actualizarCarrito();
        }
    </script>
</body>
</html>