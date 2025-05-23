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
    <title>Registro de Usuario</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600&display=swap');
        
        body {
            font-family: 'Quicksand', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            color: #4a148c; /* Morado oscuro */
            line-height: 1.6;
        }
        
        h1 {
            color: #7b1fa2; /* Morado */
            text-align: left;
            font-size: 28px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e1bee7; /* Lila claro */
            padding-bottom: 10px;
        }
        
        h2 {
            color: #7b1fa2; /* Morado */
            font-size: 22px;
            margin: 30px 0 15px 0;
        }
        
        /* FORMULARIO ESTILO LISTA (como en tu segunda imagen) */
        .form-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 15px;
            padding-left: 15px;
            border-left: 3px solid #ba68c8; /* Morado medio */
        }
        
        .form-group label {
            display: block;
            font-weight: 500;
            color: #6a1b9a; /* Morado oscuro */
            margin-bottom: 5px;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ce93d8; /* Lila */
            border-radius: 5px;
            font-family: 'Quicksand', sans-serif;
            font-size: 15px;
        }
        
        hr {
            border: 0;
            height: 1px;
            background: #e1bee7; /* Lila claro */
            margin: 25px 0;
        }
        
        /* BOTÓN */
        .btn-submit {
            background-color: #9c27b0; /* Morado */
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Quicksand', sans-serif;
            font-weight: 600;
            display: block;
            margin: 20px 0;
            transition: background 0.3s;
        }
        
        .btn-submit:hover {
            background-color: #7b1fa2; /* Morado más oscuro */
        }
        
        /* TABLA */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e1bee7; /* Lila claro */
        }
        
        th {
            background-color: #9c27b0; /* Morado */
            color: white;
            font-weight: 500;
        }
        
        tr:nth-child(even) {
            background-color: #f3e5f5; /* Lila muy claro */
        }
        
        tr:hover {
            background-color: #e1bee7; /* Lila claro */
        }
        
        /* MENSAJES */
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .error {
            background-color: #fce4ec; /* Rosado claro */
            color: #c2185b; /* Rosado oscuro */
        }
        
        .success {
            background-color: #f3e5f5; /* Lila claro */
            color: #7b1fa2; /* Morado */
        }
    </style>
</head>
<body>

<div class="form-container">
    <h1>Registro de Usuario</h1>
    
    <?php if ($mensaje): ?>
        <div class="message <?= $error ? 'error' : 'success' ?>">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <h2>Nombre(s)</h2>
        <div class="form-group">
            <input type="text" name="nombre" required>
        </div>
        
        <h2>Primer Apellido</h2>
        <div class="form-group">
            <input type="text" name="primer_apellido" required>
        </div>
        
        <h2>Segundo Apellido</h2>
        <div class="form-group">
            <input type="text" name="segundo_apellido">
        </div>
        
        <h2>Correo Electrónico</h2>
        <div class="form-group">
            <input type="email" name="correo" required>
        </div>
        
        <h2>Teléfono</h2>
        <div class="form-group">
            <input type="text" name="telefono" required>
        </div>
        
        <hr>
        
        <button type="submit" name="enviar" class="btn-submit">Enviar Datos</button>
    </form>
</div>

<?php if (!empty($registros)): ?>
<div class="form-container">
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
</div>
<?php endif; ?>

</body>
</html>
