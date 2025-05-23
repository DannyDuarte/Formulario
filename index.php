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
    <title>Formulario de Registro</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3e8ff;
            padding: 40px;
            color: #4a006e;
        }
        .form-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            max-width: 650px;
            margin: auto;
            box-shadow: 0 0 25px rgba(74, 0, 110, 0.2);
        }
        h1, h2 {
            text-align: center;
            color: #6a1b9a;
        }
        .form-group {
            margin-bottom: 16px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
        }
        input {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border: 1px solid #b39ddb;
            border-radius: 8px;
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #8e24aa;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-submit:hover {
            background-color: #6a1b9a;
        }
        .response {
            margin-top: 20px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ce93d8;
        }
        th {
            background-color: #7b1fa2;
            color: white;
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
