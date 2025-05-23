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
        
        /* Contenedor responsivo para la tabla */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-top: 25px;
            -webkit-overflow-scrolling: touch; /* Suaviza el scroll en móviles */
        }
        
        /* Tabla con diseño compacto y alineado */
        .perfect-table {
            width: 100%;
            min-width: 600px; /* Ancho mínimo para no comprimirse demasiado */
            border-collapse: collapse;
            font-size: 14px;
            table-layout: fixed; /* Forzar distribución equitativa */
        }
        
        .perfect-table th, 
        .perfect-table td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            word-break: break-word; /* Romper palabras largas */
        }
        
        /* Cabecera con diseño moderno */
        .perfect-table thead th {
            background-color: #7b1fa2;
            color: white;
            position: sticky;
            top: 0;
            font-weight: 500;
        }
        
        /* Filas alternas para mejor legibilidad */
        .perfect-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Efecto hover sutil */
        .perfect-table tbody tr:hover {
            background-color: #f3e5f5;
        }
        
        /* Ajuste para columnas específicas */
        .perfect-table td:nth-child(1), /* ID */
        .perfect-table th:nth-child(1) {
            width: 50px;
        }
        
        .perfect-table td:nth-child(7), /* Fecha */
        .perfect-table th:nth-child(7) {
            width: 120px;
        }
        
        /* Borde redondeado para el contenedor */
        .form-container {
            overflow: hidden; /* Esto evita que los hijos se salgan */
        }
    </style>
</head>
<body>

<div class="form-container">
    <!-- [El formulario anterior permanece igual] -->

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
