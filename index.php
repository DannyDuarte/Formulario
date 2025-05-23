<?php
// Mostrar errores en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de la base de datos
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
    <title>Formulario de Registro</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3e8ff;
            padding: 20px;
            color: #4a006e;
            line-height: 1.6;
        }
        
        .form-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            max-width: 900px;
            margin: 20px auto;
            box-shadow: 0 0 25px rgba(74, 0, 110, 0.1);
            overflow: hidden;
        }
        
        h1 {
            text-align: center;
            color: #6a1b9a;
            margin-bottom: 25px;
            font-size: 28px;
        }
        
        h2 {
            color: #6a1b9a;
            margin: 25px 0 15px;
            font-size: 22px;
            position: relative;
            padding-bottom: 8px;
        }
        
        h2::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #8e24aa, #ce93d8);
            border-radius: 3px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        
        input {
            width: 100%;
            padding: 12px;
            font-size: 15px;
            border: 1px solid #b39ddb;
            border-radius: 8px;
            transition: border-color 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: #8e24aa;
            box-shadow: 0 0 0 2px rgba(142, 36, 170, 0.2);
        }
        
        .btn-submit {
            width: 100%;
            padding: 14px;
            background-color: #8e24aa;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-submit:hover {
            background-color: #6a1b9a;
        }
        
        .response {
            margin: 20px 0;
            padding: 15px;
            border-left: 5px solid;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .response.error {
            background-color: #fbe9f1;
            border-color: #d81b60;
            color: #ad1457;
        }
        
        .response.success {
            background-color: #e8f5e9;
            border-color: #43a047;
            color: #2e7d32;
        }
        
        /* Estilos para la tabla responsiva */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-top: 25px;
            -webkit-overflow-scrolling: touch;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .perfect-table {
            width: 100%;
            min-width: 600px;
            border-collapse: collapse;
            font-size: 14px;
            table-layout: fixed;
        }
        
        .perfect-table th, 
        .perfect-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            word-break: break-word;
        }
        
        .perfect-table thead th {
            background: linear-gradient(135deg, #8e24aa 0%, #6a1b9a 100%);
            color: white;
            position: sticky;
            top: 0;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        
        .perfect-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        .perfect-table tbody tr:hover {
            background-color: #f3e5f5;
        }
        
        .perfect-table td:nth-child(1),
        .perfect-table th:nth-child(1) {
            width: 50px;
        }
        
        .perfect-table td:nth-child(7),
        .perfect-table th:nth-child(7) {
            width: 120px;
        }
        
        @media (max-width: 768px) {
            .form-container {
                padding: 20px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            h2 {
                font-size: 20px;
            }
            
            input, .btn-submit {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="form-container">
    <h1>Registro de Usuario</h1>

    <?php if ($mensaje): ?>
        <div class="response <?= $error ? 'error' : 'success' ?>">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="nombre">Nombre(s)</label>
            <input type="text" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="primer_apellido">Primer Apellido</label>
            <input type="text" name="primer_apellido" required>
        </div>
        <div class="form-group">
            <label for="segundo_apellido">Segundo Apellido</label>
            <input type="text" name="segundo_apellido">
        </div>
        <div class="form-group">
            <label for="correo">Correo Electrónico</label>
            <input type="email" name="correo" required>
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" required>
        </div>
        <button type="submit" name="enviar" class="btn-submit">Enviar</button>
    </form>

    <?php if (!empty($registros)): ?>
    <h2>Usuarios Registrados</h2>
    <div class="table-responsive">
        <table class="perfect-table">
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
    <?php endif; ?>
</div>

</body>
</html>
