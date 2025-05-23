<?php
// Mostrar errores en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de la base de datos (USANDO TU CONEXIÓN)
define('DB_SERVER', 'tcp:database0123.database.windows.net,1433');
define('DB_DATABASE', 'Lab5_1PaaS');
define('DB_USERNAME', 'database0123');
define('DB_PASSWORD', 'Hola12345678');

// Función para validar datos
function validarDatos($datos) {
    $errores = [];
    
    if (empty(trim($datos['nombre']))) {
        $errores[] = 'El nombre es requerido';
    }
    
    if (empty(trim($datos['primer_apellido']))) {
        $errores[] = 'El primer apellido es requerido';
    }
    
    if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo electrónico no es válido';
    }

    if (!preg_match('/^[0-9]{10,15}$/', $datos['telefono'])) {
        $errores[] = 'El teléfono debe contener solo números (10-15 dígitos)';
    }
    
    return $errores;
}

$mensaje = '';
$error = false;
$registros = [];

try {
    $conn = new PDO("sqlsrv:server = ".DB_SERVER."; Database = ".DB_DATABASE, DB_USERNAME, DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['enviar'])) {
        $errores = validarDatos($_POST);
        
        if (empty($errores)) {
            $stmt = $conn->prepare("INSERT INTO [dbo].[usuarios] 
                (nombre, primer_apellido, segundo_apellido, correo, telefono, fecha_registro)
                VALUES (:nombre, :primer_apellido, :segundo_apellido, :correo, :telefono, GETDATE())");
            $stmt->execute([
                ':nombre' => trim($_POST['nombre']),
                ':primer_apellido' => trim($_POST['primer_apellido']),
                ':segundo_apellido' => trim($_POST['segundo_apellido']),
                ':correo' => $_POST['correo'],
                ':telefono' => preg_replace('/[^0-9]/', '', $_POST['telefono'])
            ]);
            $mensaje = 'Datos guardados correctamente.';
        } else {
            $error = true;
            $mensaje = implode('<br>', $errores);
        }
    }

    $stmt = $conn->query("SELECT * FROM [dbo].[usuarios] ORDER BY id DESC");
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = true;
    $mensaje = 'Error de base de datos: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6a3093;
            --primary-light: #a044ff;
            --secondary: #4b0082;
            --dark: #2d0a4a;
            --light: #f9f5ff;
            --text: #3a0ca3;
            --success: #9d4edd;
            --danger: #ff006e;
        }
        
        body {
            background: linear-gradient(135deg, #e0c4fd 0%, #b8b3ff 100%);
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
            padding: 2rem;
            color: var(--dark);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(106, 48, 147, 0.2);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .card-header {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        
        .card-header h1, .card-header h2 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            margin: 0;
            letter-spacing: 1px;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #d9c7f8;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
            background-color: rgba(255, 255, 255, 0.8);
        }
        
        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(160, 68, 255, 0.2);
            outline: none;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(106, 48, 147, 0.4);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .alert-success {
            background-color: rgba(157, 78, 221, 0.1);
            border-left: 4px solid var(--success);
            color: var(--text);
        }
        
        .alert-danger {
            background-color: rgba(255, 0, 110, 0.1);
            border-left: 4px solid var(--danger);
            color: #8a0a40;
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 1.5rem;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(106, 48, 147, 0.1);
        }
        
        th {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0e5ff;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:hover td {
            background-color: #f9f5ff;
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Registro de Usuario</h1>
            </div>
            <div class="card-body">
                <?php if ($mensaje): ?>
                    <div class="alert <?= $error ? 'alert-danger' : 'alert-success' ?>">
                        <?= $mensaje ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre">Nombre(s)</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="primer_apellido">Primer Apellido</label>
                            <input type="text" name="primer_apellido" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="segundo_apellido">Segundo Apellido</label>
                            <input type="text" name="segundo_apellido" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo Electrónico</label>
                            <input type="email" name="correo" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" name="enviar" class="btn">Enviar</button>
                </form>
            </div>
        </div>

        <?php if (!empty($registros)): ?>
        <div class="card">
            <div class="card-header">
                <h2>Usuarios Registrados</h2>
            </div>
            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Primer Apellido</th>
                            <th>Segundo Apellido</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registros as $r): ?>
                            <tr>
                                <td><?= $r['id'] ?></td>
                                <td><?= htmlspecialchars($r['nombre']) ?></td>
                                <td><?= htmlspecialchars($r['primer_apellido']) ?></td>
                                <td><?= htmlspecialchars($r['segundo_apellido'] ?? '') ?></td>
                                <td><?= htmlspecialchars($r['correo']) ?></td>
                                <td><?= htmlspecialchars($r['telefono']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($r['fecha_registro'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
