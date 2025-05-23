<?php
// Mostrar errores en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de la base de datos (USANDO TU CONEXIÓN)
define('DB_SERVER', 'tcp:database0123.database.windows.net,1433');
define('DB_DATABASE', 'Lab5_1PaaS');
define('DB_USERNAME', 'database0123');
define('DB_PASSWORD', 'Hola12345678'); // Cambia esto por tu contraseña real

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
    // Conexión usando PDO con tu configuración
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

    // Obtener registros
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
    <title>Formulario de Registro</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #444;
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        /* ESTILOS PARA EL FORMULARIO (MODIFICADOS) */
        .form-section {
            background: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s;
        }
        .form-group input:focus {
            border-color: #4a90e2;
            outline: none;
            box-shadow: 0 0 0 3px rgba(74,144,226,0.1);
        }
        .btn-submit {
            background-color: #4a90e2;
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
            display: block;
            width: auto;
            margin: 25px auto 0;
        }
        .btn-submit:hover {
            background-color: #3a7bc8;
        }
        
        /* ESTILOS PARA LOS MENSAJES */
        .response {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .response.error {
            background-color: #ffebee;
            color: #c62828;
            border-left: 4px solid #f44336;
        }
        .response.success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #4caf50;
        }
        
        /* ESTILOS PARA LA TABLA (MANTENIDOS COMO ESTABAN) */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #6c757d;
            color: white;
            font-weight: 500;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Registro de Usuario</h1>

    <?php if ($mensaje): ?>
        <div class="response <?= $error ? 'error' : 'success' ?>">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <div class="form-section">
        <form method="post">
            <div class="form-grid">
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
            </div>
            <button type="submit" name="enviar" class="btn-submit">ENVIAR</button>
        </form>
    </div>

    <?php if (!empty($registros)): ?>
    <h2>Usuarios Registrados</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Primer Apellido</th>
            <th>Segundo Apellido</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Fecha Registro</th>
        </tr>
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
    </table>
    <?php endif; ?>
</div>

</body>
</html>
