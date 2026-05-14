<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('index.php#auth');
}

$pdo = db();

if (!$pdo) {
    flash_set('error', 'Nepavyko prisijungti prie MySQL duomenų bazės.');
    redirect_to('index.php#auth');
}

$action = $_POST['action'] ?? '';

if ($action === 'register') {
    $username = trim((string) ($_POST['register-name'] ?? ''));
    $email = trim((string) ($_POST['register-email'] ?? ''));
    $password = (string) ($_POST['register-password'] ?? '');

    if ($username === '' || $email === '' || $password === '') {
        flash_set('error', 'Užpildyk visus registracijos laukus.');
        redirect_to('index.php#auth');
    }

    try {
        $statement = $pdo->prepare(
            'INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)'
        );
        $statement->execute([
            $username,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
        ]);

        $_SESSION['user_id'] = (int) $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;

        flash_set('success', 'Registracija sėkminga. Dabar sukurk veikėją.');
        redirect_to('character.php');
    } catch (PDOException) {
        flash_set('error', 'Toks vardas arba el. paštas jau naudojamas.');
        redirect_to('index.php#auth');
    }
}

if ($action === 'login') {
    $login = trim((string) ($_POST['login-name'] ?? ''));
    $password = (string) ($_POST['login-password'] ?? '');

    $statement = $pdo->prepare(
        'SELECT id, username, email, password_hash FROM users WHERE username = ? OR email = ? LIMIT 1'
    );
    $statement->execute([$login, $login]);
    $user = $statement->fetch();

    if (!$user || !password_verify($password, (string) $user['password_hash'])) {
        flash_set('error', 'Neteisingas vartotojo vardas, el. paštas arba slaptažodis.');
        redirect_to('index.php#auth');
    }

    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['username'] = (string) $user['username'];
    $_SESSION['email'] = (string) $user['email'];

    flash_set('success', 'Prisijungta sėkmingai.');
    redirect_to('game.php');
}

flash_set('error', 'Nežinomas veiksmas.');
redirect_to('index.php#auth');
