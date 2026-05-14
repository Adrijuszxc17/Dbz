<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = db();

if (!$pdo) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'DB neprijungta']);
    exit;
}

$user = current_user();
$username = display_name($user);
$userId = !empty($user['id']) ? (int) $user['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim((string) ($_POST['message'] ?? ''));

    if ($message === '') {
        http_response_code(422);
        echo json_encode(['ok' => false, 'error' => 'Tuščia žinutė']);
        exit;
    }

    $message = substr($message, 0, 500);

    try {
        $statement = $pdo->prepare(
            'INSERT INTO chat_messages (user_id, username, message) VALUES (:user_id, :username, :message)'
        );
        $statement->execute([
            'user_id' => $userId,
            'username' => $username,
            'message' => $message,
        ]);
    } catch (PDOException) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Nepavyko išsaugoti žinutės']);
        exit;
    }
}

try {
    $messages = $pdo->query(
        'SELECT username, message, DATE_FORMAT(created_at, "%H:%i") AS sent_at
         FROM chat_messages
         ORDER BY id DESC
         LIMIT 30'
    )->fetchAll();
} catch (PDOException) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Nepavyko gauti žinučių']);
    exit;
}

echo json_encode([
    'ok' => true,
    'messages' => $messages,
]);
