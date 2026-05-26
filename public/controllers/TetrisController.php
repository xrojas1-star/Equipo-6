<?php
session_start();
require_once __DIR__ . '/../config/Database.php';

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

if ($accion === 'guardar_partida') {
    $puntaje = intval($data['puntaje'] ?? 0);
    $puntos_ganados = floor($puntaje / 100);

    // Guardar partida
    $stmt = $conn->prepare("INSERT INTO tetris_partidas (usuario_id, puntaje, puntos_ganados) VALUES (?, ?, ?)");
    $stmt->execute([$usuario_id, $puntaje, $puntos_ganados]);

    // Actualizar o insertar puntos del usuario
    $stmt = $conn->prepare("SELECT usuario_id FROM usuario_puntos WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);

    if ($stmt->fetch()) {
        $stmt = $conn->prepare("UPDATE usuario_puntos SET puntos_totales = puntos_totales + ?, puntos_disponibles = puntos_disponibles + ? WHERE usuario_id = ?");
        $stmt->execute([$puntos_ganados, $puntos_ganados, $usuario_id]);
    } else {
        $stmt = $conn->prepare("INSERT INTO usuario_puntos (usuario_id, puntos_totales, puntos_disponibles) VALUES (?, ?, ?)");
        $stmt->execute([$usuario_id, $puntos_ganados, $puntos_ganados]);
    }

    // Obtener puntos actuales
    $stmt = $conn->prepare("SELECT puntos_disponibles, puntos_totales FROM usuario_puntos WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $puntos = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'puntos_ganados' => $puntos_ganados,
        'puntos_disponibles' => $puntos['puntos_disponibles'],
        'puntos_totales' => $puntos['puntos_totales']
    ]);

} elseif ($accion === 'leaderboard') {
    // Top 10 global por mejor puntaje
    $stmt = $conn->prepare("
        SELECT u.nombre, u.foto_perfil,
               MAX(tp.puntaje) AS mejor_puntaje,
               SUM(tp.puntos_ganados) AS puntos_totales
        FROM tetris_partidas tp
        JOIN usuarios u ON u.id = tp.usuario_id
        GROUP BY tp.usuario_id, u.nombre, u.foto_perfil
        ORDER BY mejor_puntaje DESC
        LIMIT 10
    ");
    $stmt->execute();
    $top10 = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Stats personales
    $stmt = $conn->prepare("
        SELECT MAX(puntaje) AS mejor_puntaje,
               COUNT(*) AS partidas_jugadas,
               SUM(puntos_ganados) AS puntos_ganados_total
        FROM tetris_partidas
        WHERE usuario_id = ?
    ");
    $stmt->execute([$usuario_id]);
    $personal = $stmt->fetch(PDO::FETCH_ASSOC);

    // Puntos disponibles
    $stmt = $conn->prepare("SELECT puntos_disponibles FROM usuario_puntos WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $puntosRow = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'top10' => $top10,
        'personal' => $personal,
        'puntos_disponibles' => $puntosRow ? $puntosRow['puntos_disponibles'] : 0
    ]);

} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
?>