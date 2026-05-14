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

function dashboard_stats(): array
{
    $stats = [
        'active_fighters' => 0,
        'new_fighters' => 0,
        'total_fights' => 0,
        'completed_fights' => 0,
        'avg_power' => 0,
        'critical_threats' => 0,
    ];

    $pdo = db();

    if (!$pdo) {
        return $stats;
    }

    try {
        $characterStats = $pdo->query(
            "SELECT
                COUNT(*) AS active_fighters,
                SUM(created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS new_fighters,
                COALESCE(AVG((strength_points * 1200) + (speed_points * 1000) + (ki_points * 1300) + (defense_points * 900) + (level * 500)), 0) AS avg_power
             FROM characters"
        )->fetch();

        if ($characterStats) {
            $stats['active_fighters'] = (int) $characterStats['active_fighters'];
            $stats['new_fighters'] = (int) $characterStats['new_fighters'];
            $stats['avg_power'] = (int) round((float) $characterStats['avg_power']);
        }

        $fightStats = $pdo->query(
            "SELECT
                COUNT(*) AS total_fights,
                SUM(result IN ('win', 'loss')) AS completed_fights,
                SUM(result = 'active') AS critical_threats
             FROM fight_logs"
        )->fetch();

        if ($fightStats) {
            $stats['total_fights'] = (int) $fightStats['total_fights'];
            $stats['completed_fights'] = (int) $fightStats['completed_fights'];
            $stats['critical_threats'] = (int) $fightStats['critical_threats'];
        }
    } catch (PDOException) {
        return $stats;
    }

    return $stats;
}

function top_fighters(int $limit = 5): array
{
    $pdo = db();

    if (!$pdo) {
        return [];
    }

    try {
        $statement = $pdo->prepare(
            "SELECT
                c.name,
                c.level,
                c.hp,
                c.updated_at,
                ((c.strength_points * 1200) + (c.speed_points * 1000) + (c.ki_points * 1300) + (c.defense_points * 900) + (c.level * 500)) AS power_level,
                COALESCE(SUM(fl.result = 'win'), 0) AS wins,
                COALESCE(SUM(fl.result IN ('win', 'loss')), 0) AS total_completed
             FROM characters c
             LEFT JOIN fight_logs fl ON fl.user_id = c.user_id
             GROUP BY c.id
             ORDER BY power_level DESC, wins DESC, c.level DESC
             LIMIT :limit"
        );
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll() ?: [];
    } catch (PDOException) {
        return [];
    }
}

function format_stat_number(int $value): string
{
    if ($value >= 1000000) {
        return round($value / 1000000, 1) . 'M';
    }

    if ($value >= 1000) {
        return round($value / 1000, 1) . 'K';
    }

    return (string) $value;
}

function required_xp_for_level(int $level): int
{
    $level = max(0, $level);

    return (int) round(200 * pow($level + 1, 1.5));
}
