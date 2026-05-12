<?php
session_start();
require_once '../config/Database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$accion = $data['accion'] ?? '';
$usuario_id = $_SESSION['user_id'];

$db = new Database();
$conn = $db->getConnection();

if ($accion === 'desbloquear') {
    $curso_id = intval($data['curso_id'] ?? 0);

    // Verificar que el curso existe y está activo
    $stmt = $conn->prepare("SELECT id, titulo, costo_puntos FROM cursos WHERE id = ? AND activo = 1");
    $stmt->execute([$curso_id]);
    $curso = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$curso) {
        echo json_encode(['success' => false, 'message' => 'Curso no encontrado']);
        exit;
    }

    // Verificar que no lo tenga ya desbloqueado
    $stmt = $conn->prepare("SELECT curso_id FROM cursos_desbloqueados WHERE usuario_id = ? AND curso_id = ?");
    $stmt->execute([$usuario_id, $curso_id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Ya tienes este curso desbloqueado']);
        exit;
    }

    // Verificar puntos disponibles
    $stmt = $conn->prepare("SELECT puntos_disponibles FROM usuario_puntos WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $puntosRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $puntos_disponibles = $puntosRow ? $puntosRow['puntos_disponibles'] : 0;

    if ($puntos_disponibles < $curso['costo_puntos']) {
        echo json_encode([
            'success' => false,
            'message' => 'No tienes suficientes puntos',
            'necesitas' => $curso['costo_puntos'],
            'tienes' => $puntos_disponibles
        ]);
        exit;
    }

    // Desbloquear curso y descontar puntos
    $conn->beginTransaction();
    try {
        $stmt = $conn->prepare("INSERT INTO cursos_desbloqueados (usuario_id, curso_id) VALUES (?, ?)");
        $stmt->execute([$usuario_id, $curso_id]);

        $stmt = $conn->prepare("UPDATE usuario_puntos SET puntos_disponibles = puntos_disponibles - ? WHERE usuario_id = ?");
        $stmt->execute([$curso['costo_puntos'], $usuario_id]);

        $conn->commit();

        // Retornar puntos actualizados
        $stmt = $conn->prepare("SELECT puntos_disponibles FROM usuario_puntos WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        $nuevoPuntos = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'message' => '¡Curso desbloqueado! "' . $curso['titulo'] . '"',
            'puntos_disponibles' => $nuevoPuntos['puntos_disponibles']
        ]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error al desbloquear el curso']);
    }

} elseif ($accion === 'listar') {
    // Obtener todos los cursos activos con estado de desbloqueo del usuario
    $stmt = $conn->prepare("
        SELECT c.*,
               CASE WHEN cd.curso_id IS NOT NULL THEN 1 ELSE 0 END AS desbloqueado
        FROM cursos c
        LEFT JOIN cursos_desbloqueados cd ON cd.curso_id = c.id AND cd.usuario_id = ?
        WHERE c.activo = 1
        ORDER BY c.costo_puntos ASC
    ");
    $stmt->execute([$usuario_id]);
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Puntos disponibles del usuario
    $stmt = $conn->prepare("SELECT puntos_disponibles, puntos_totales FROM usuario_puntos WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $puntosRow = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'cursos' => $cursos,
        'puntos_disponibles' => $puntosRow ? $puntosRow['puntos_disponibles'] : 0,
        'puntos_totales' => $puntosRow ? $puntosRow['puntos_totales'] : 0
    ]);

} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
?>
