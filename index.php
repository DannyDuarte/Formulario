<?php
// [El código PHP anterior permanece igual hasta la parte del HTML]
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Registro</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* [Estilos anteriores del body y form-container permanecen igual] */
        
        /* Nuevos estilos para la tabla */
        .registros-container {
            margin-top: 30px;
            overflow-x: auto;
        }
        
        .styled-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            font-size: 14px;
        }
        
        .styled-table thead tr {
            background: linear-gradient(135deg, #8e24aa 0%, #6a1b9a 100%);
            color: white;
            text-align: left;
        }
        
        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .styled-table th {
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .styled-table tbody tr {
            transition: all 0.2s ease;
        }
        
        .styled-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        .styled-table tbody tr:nth-child(odd) {
            background-color: white;
        }
        
        .styled-table tbody tr:hover {
            background-color: #f3e5f5;
            transform: translateX(2px);
        }
        
        .styled-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* Estilos para el encabezado de la sección */
        .section-title {
            color: #6a1b9a;
            margin-bottom: 15px;
            font-size: 1.4rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 8px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #8e24aa, #ce93d8);
            border-radius: 3px;
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
    <div class="registros-container">
        <h2 class="section-title">Usuarios Registrados</h2>
        <table class="styled-table">
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
