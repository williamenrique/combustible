<?php
require_once '../system/core/Config/config.system.php';
function conectarDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
    return $conn;
}
function obtenerBienesPorDepartamento($departamento_id) {
    $conn = conectarDB();
    $sql = "SELECT b.id_bien, b.descripcion_bien, b.bien_depatamento_id, b.grupo_id, b.subgrupo_id, b.seccion_id,
            d.departamento_bien, g.grupo, s.subgrupo, b.status_bien, b.fecha_adquisicion
            FROM table_bienes_inventario b
            INNER JOIN table_bienes_departamentos d ON b.bien_depatamento_id = d.depatamento_bien_id
            LEFT JOIN table_bienes_grupo g ON b.grupo_id = g.id_grupo
            LEFT JOIN table_bienes_subgrupo s ON b.subgrupo_id = s.subgrupo_id
            WHERE b.bien_depatamento_id = ? AND b.status = 1
            ORDER BY b.id_bien ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $departamento_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bienes = [];
    while ($row = $result->fetch_assoc()) {
        $bienes[] = $row;
    }
    $stmt->close();
    $conn->close();
    return $bienes;
}
function obtenerDepartamento($departamento_id) {
    $conn = conectarDB();
    $sql = "SELECT * FROM table_bienes_departamentos WHERE depatamento_bien_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $departamento_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $departamento = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $departamento;
}
$departamento_id = isset($_GET['departamento_id']) ? intval($_GET['departamento_id']) : 0;
if ($departamento_id <= 0) {
    die('
        <div style="padding: 20px; text-align: center;">
            <h2>Error: Departamento no válido</h2>
            <p>El ID de departamento proporcionado no es válido.</p>
            <a href="javascript:history.back()">← Volver atrás</a>
        </div>
    ');
}
$departamento = obtenerDepartamento($departamento_id);
$bienes = obtenerBienesPorDepartamento($departamento_id);
if (!$departamento) {
    die('
        <div style="padding: 20px; text-align: center;">
            <h2>Error: Departamento no encontrado</h2>
            <p>El departamento solicitado no existe en el sistema.</p>
            <a href="javascript:history.back()">← Volver atrás</a>
        </div>
    ');
}
$fecha_generacion = date('d/m/Y H:i');
$page_title = 'Bienes del Departamento: ' . htmlspecialchars($departamento['departamento_bien']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #f3f4f6; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .page-header { margin-bottom: 30px; border-bottom: 2px solid #1f2937; padding-bottom: 10px; }
        .table-container { background-color: white; border-radius: 8px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); padding: 20px; }
    </style>
</head>
<body class="antialiased">
    <div class="container">
        <div class="page-header">
            <h1 class="text-center text-3xl font-bold text-gray-800"><?= htmlspecialchars($departamento['departamento_bien']) ?></h1>
            <p class="text-center text-gray-500">Reporte de Bienes generado el: <?= $fecha_generacion ?></p>
        </div>
        <?php if(empty($bienes)): ?>
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 text-center" role="alert">
                <p>No hay bienes asignados a este departamento.</p>
            </div>
        <?php else: ?>
            <div class="table-container overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grupo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subgrupo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Adquisición</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach($bienes as $bien): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($bien['id_bien']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($bien['descripcion_bien']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $bien['grupo'] ? htmlspecialchars($bien['grupo']) : 'N/A' ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $bien['subgrupo'] ? htmlspecialchars($bien['subgrupo']) : 'N/A' ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                     <?php 
                                        $estadoText = htmlspecialchars($bien['status_bien']);
                                        $estadoClass = 'bg-gray-200 text-gray-800'; // Default
                                        switch($bien['status_bien']) {
                                            case 'EN USO': $estadoClass = 'bg-green-100 text-green-800'; break;
                                            case 'EXTRAVIADO': $estadoClass = 'bg-yellow-100 text-yellow-800'; break;
                                            case 'EN REPARACION': $estadoClass = 'bg-blue-100 text-blue-800'; break;
                                            case 'DAÑADO': $estadoClass = 'bg-red-100 text-red-800'; break;
                                        }
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $estadoClass ?>">
                                        <?= $estadoText ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $bien['fecha_adquisicion'] ? date('d/m/Y', strtotime($bien['fecha_adquisicion'])) : 'N/A' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>