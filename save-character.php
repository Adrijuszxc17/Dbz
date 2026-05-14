<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('character.php');
}

if (empty($_SESSION['user_id'])) {
    flash_set('error', 'Pirma prisijunk arba užsiregistruok.');
    redirect_to('index.php#auth');
}

$pdo = db();

if (!$pdo) {
    flash_set('error', 'Nepavyko prisijungti prie MySQL duomenų bazės.');
    redirect_to('character.php');
}

$user = current_user();
$name = display_name($user);
$gender = (string) ($_POST['gender'] ?? 'male');
$gender = in_array($gender, ['male', 'female'], true) ? $gender : 'male';

$strength = max(0, min(100, (int) ($_POST['strength'] ?? 5)));
$speed = max(0, min(100, (int) ($_POST['speed'] ?? 1)));
$ki = max(0, min(100, (int) ($_POST['ki'] ?? 6)));
$defense = max(0, min(100, (int) ($_POST['defense'] ?? 4)));

if (($strength + $speed + $ki + $defense) > 100) {
    flash_set('error', 'Pradiniai taškai negali viršyti 100.');
    redirect_to('character.php#stats');
}

$statement = $pdo->prepare(
    'INSERT INTO characters (user_id, name, gender, strength_points, speed_points, ki_points, defense_points)
     VALUES (:user_id, :name, :gender, :strength, :speed, :ki, :defense)
     ON DUPLICATE KEY UPDATE
       name = VALUES(name),
       gender = VALUES(gender),
       strength_points = VALUES(strength_points),
       speed_points = VALUES(speed_points),
       ki_points = VALUES(ki_points),
       defense_points = VALUES(defense_points)'
);

$statement->execute([
    'user_id' => (int) $_SESSION['user_id'],
    'name' => $name,
    'gender' => $gender,
    'strength' => $strength,
    'speed' => $speed,
    'ki' => $ki,
    'defense' => $defense,
]);

$_SESSION['gender'] = $gender;

flash_set('success', 'Veikėjas išsaugotas.');
redirect_to('game.php');
