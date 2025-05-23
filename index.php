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
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3e5f5; /* Fondo lila claro */
            padding: 20px;
            color: #4a148c; /* Texto morado oscuro */
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(149, 117, 205, 0.3); /* Sombra morada */
        }
        h1, h2 {
            color: #7b1fa2; /* Morado medio */
            margin-bottom: 25px;
            border-bottom: 2px solid #e1bee7; /* Borde lila */
            padding-bottom: 10px;
            font-weight: 600;
        }
        
        /* ESTILOS PARA EL FORMULARIO */
        .form-section {
            background: linear-gradient(145deg, #f8bbd0, #e1bee7); /* Degradado rosado a lila */
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 3px 10px rgba(156, 39, 176, 0.1);
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            color: #6a1b9a; /* Morado oscuro */
            font-size: 15px;
        }
        .form-group input {
            width: 100%;
            padding: 14px;
            border: 2px solid #ce93d8; /* Borde lila */
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: rgba(255, 255, 255, 0.8);
        }
        .form-group input:focus {
            border-color: #9c27b0; /* Morado */
            outline: none;
            box-shadow: 0 0 0 3px rgba(156, 39, 176, 0.2);
            background-color: white;
        }
        .btn-submit {
            background: linear-gradient(to right, #9c27b0, #e91e63); /* Degradado morado a rosado */
            color: white;
            border: none;
            padding: 14px 30px;
            font-size: 16px;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: block;
            width: auto;
            margin: 30px auto 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 6px rgba(156, 39, 176, 0.3);
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(156, 39, 176, 0.4);
            background: linear-gradient(to right, #8e24aa, #d81b60);
        }
        
        /* ESTILOS PARA LOS MENSAJES */
        .response {
            padding: 16px;
            margin: 25px 0;
            border-radius: 8px;
            font-weight: 500;
        }
        .response.error {
            background-color: #fce4ec; /* Rosado claro */
            color: #c2185b; /* Rosado oscuro */
            border-left: 4px solid #e91e63; /* Rosado */
        }
        .response.success {
            background-color: #f3e5f5; /* Lila claro */
            color: #7b1fa2; /* Morado */
            border-left: 4px solid #9c27b0; /* Morado */
        }
        
        /* ESTILOS PARA LA TABLA (AJUSTADOS AL TEMA MORADO) */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            box-shadow: 0 2px 10px rgba(156, 39, 176, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 14px;
            border-bottom: 1px solid #e1bee7; /* Borde lila */
            text-align: left;
        }
        th {
            background: linear-gradient(to right, #9c27b0, #7b1fa2); /* Degradado morado */
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
        tr:nth-child(even) {
            background-color: #f8f0fc; /* Lila muy claro */
        }
        tr:hover {
            background-color: #f3e5f5; /* Lila claro */
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
            <button type="submit" name="enviar" class="btn-submit">Enviar Datos</button>
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
