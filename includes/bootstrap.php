<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect_to(string $path): void
{
    header("Location: {$path}");
    exit;
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $pdo = db();

    if (!$pdo) {
        return [
            'id' => (int) $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? null,
            'email' => $_SESSION['email'] ?? null,
        ];
    }

    try {
        $statement = $pdo->prepare('SELECT id, username, email, created_at FROM users WHERE id = ? LIMIT 1');
        $statement->execute([$_SESSION['user_id']]);
        $user = $statement->fetch();
    } catch (PDOException) {
        return null;
    }

    return $user ?: null;
}

function current_character(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $pdo = db();

    if (!$pdo) {
        return [
            'name' => $_SESSION['username'] ?? null,
            'gender' => $_SESSION['gender'] ?? 'male',
        ];
    }

    try {
        $statement = $pdo->prepare('SELECT * FROM characters WHERE user_id = ? LIMIT 1');
        $statement->execute([$_SESSION['user_id']]);
        $character = $statement->fetch();
    } catch (PDOException) {
        return null;
    }

    return $character ?: null;
}

function display_name(?array $user): string
{
    if (!empty($user['username'])) {
        return (string) $user['username'];
    }

    return 'Vardas bus įkeltas';
}

function display_gender(?array $character): string
{
    $gender = $character['gender'] ?? $_SESSION['gender'] ?? 'male';

    return in_array($gender, ['male', 'female'], true) ? $gender : 'male';
}

function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function flash_get(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}
