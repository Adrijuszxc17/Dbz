<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageUser = current_user();
$pageCharacter = current_character();
$displayName = display_name($pageUser);
$displayGender = display_gender($pageCharacter);
$level = (int) ($pageCharacter['level'] ?? 0);
$hp = (int) ($pageCharacter['hp'] ?? 100);
$ki = (int) ($pageCharacter['ki'] ?? 100);
$stamina = (int) ($pageCharacter['stamina'] ?? 100);
$xp = (int) ($pageCharacter['xp'] ?? 0);
$requiredXp = required_xp_for_level($level);
$xpPercent = $requiredXp > 0 ? min((int) round(($xp / $requiredXp) * 100), 100) : 0;
$strengthPoints = (int) ($pageCharacter['strength_points'] ?? 5);
$speedPoints = (int) ($pageCharacter['speed_points'] ?? 1);
$kiPoints = (int) ($pageCharacter['ki_points'] ?? 6);
$defensePoints = (int) ($pageCharacter['defense_points'] ?? 4);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Z-Fusion | Game</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
  <script src="script.js" defer></script>
</head>
<body data-user-name="<?= e($displayName) ?>" data-user-gender="<?= e($displayGender) ?>">
  <div class="background-grid" aria-hidden="true"></div>
  <div class="energy-cloud energy-cloud-one" aria-hidden="true"></div>
  <div class="energy-cloud energy-cloud-two" aria-hidden="true"></div>

  <header class="topbar">
    <a class="brand" href="index.php" aria-label="Grįžti į Z-Fusion pradžią">
      <span class="brand-mark">Z</span>
      <span>
        <strong>Z-Fusion</strong>
        <small>Game hub</small>
      </span>
    </a>

    <nav class="nav" aria-label="Žaidimo navigacija">
      <a href="index.php">Pradžia</a>
      <a href="character.php">Veikėjas</a>
      <a href="inventory.php">Inventorius</a>
      <a href="fight.php">Kova</a>
      <a href="#quests">Užduotys</a>
      <a href="#map">Žemėlapis</a>
      <a href="#chat">Chat</a>
    </nav>

    <a class="topbar-action" href="#training">Treniruotis</a>
  </header>

  <main class="game-shell">
    <section class="game-hero">
      <article class="player-status-panel">
        <div class="player-summary">
          <div class="mini-silhouette">
            <img class="game-silhouette game-silhouette-male" src="photo/character/male.png" alt="Vyro veikėjo siluetas">
            <img class="game-silhouette game-silhouette-female" src="photo/character/female.png" alt="Merginos veikėjo siluetas">
          </div>
          <div>
            <p class="label">Žaidėjo statusas</p>
            <h1 id="game-player-name"><?= e($displayName) ?></h1>
            <span class="rank-badge">Rookie • Level <?= $level ?></span>
          </div>
        </div>

        <div class="status-bars" aria-label="Žaidėjo resursai">
          <div class="resource-row hp-row">
            <span>HP</span>
            <div><i style="--value: <?= min($hp, 100) ?>%"></i></div>
            <strong><?= $hp ?>/100</strong>
          </div>
          <div class="resource-row ki-row">
            <span>Ki</span>
            <div><i style="--value: <?= min($ki, 100) ?>%"></i></div>
            <strong><?= $ki ?>/100</strong>
          </div>
          <div class="resource-row stamina-row">
            <span>Stamina</span>
            <div><i style="--value: <?= min($stamina, 100) ?>%"></i></div>
            <strong><?= $stamina ?>/100</strong>
          </div>
          <div class="resource-row xp-row">
            <span>XP</span>
            <div><i style="--value: <?= $xpPercent ?>%"></i></div>
            <strong><?= $xp ?>/<?= $requiredXp ?></strong>
          </div>
        </div>
      </article>

      <article id="training" class="next-action-card">
        <p class="label">Rekomenduojama</p>
        <h2>Pradėti pirmą treniruotę</h2>
        <p>
          Pirmas tikslas: sustiprinti kūną, pajusti Ki energiją ir pasiruošti
          pirmai meistro užduočiai.
        </p>
        <a class="button button-primary" href="fight.php">Pradėti kovą</a>
      </article>
    </section>

    <section class="game-grid">
      <article id="quests" class="game-card daily-card">
        <div class="card-heading">
          <div>
            <p class="label">Dienos progresas</p>
            <h2>Užduotys</h2>
          </div>
          <span class="pill">0/3</span>
        </div>

        <div class="quest-list">
          <div>
            <span>Treniruotės atliktos</span>
            <strong>0/3</strong>
          </div>
          <div>
            <span>Kovos laimėtos</span>
            <strong>0/1</strong>
          </div>
          <div>
            <span>XP iki kito lygio</span>
            <strong><?= $xp ?>/<?= $requiredXp ?></strong>
          </div>
        </div>
      </article>

      <article id="character-stats" class="game-card character-stats-card">
        <div class="card-heading">
          <div>
            <p class="label">Veikėjo statistika</p>
            <h2>DB taškai</h2>
          </div>
          <span class="pill">Level <?= $level ?></span>
        </div>

        <div class="character-stat-list">
          <div>
            <span>Jėga</span>
            <strong><?= $strengthPoints ?></strong>
          </div>
          <div>
            <span>Greitis</span>
            <strong><?= $speedPoints ?></strong>
          </div>
          <div>
            <span>Ki energija</span>
            <strong><?= $kiPoints ?></strong>
          </div>
          <div>
            <span>Gynyba</span>
            <strong><?= $defensePoints ?></strong>
          </div>
        </div>
      </article>

      <article class="game-card locked-card">
        <div class="card-heading">
          <div>
            <p class="label">Užrakintos sistemos</p>
            <h2>Atrakinsi vėliau</h2>
          </div>
          <span class="pill pill-red">Locked</span>
        </div>

        <div class="locked-grid">
          <span>Technikos</span>
          <span>Transformacijos</span>
          <span>Fusion režimas</span>
          <span>Bosų kovos</span>
        </div>
      </article>

      <article class="game-card mentor-status">
        <p class="label">Pirmas mokytojas</p>
        <h2>Dar nežinomas</h2>
        <p>
          Kai atliksi pirmą treniruotę, sistema parinks pirmą meistrą. Gokas,
          Vedžitas arba Pikolas gali tapti tavo pirmu mentoriumi.
        </p>
      </article>
    </section>

    <section id="map" class="game-grid lower-grid">
      <article class="game-card map-card">
        <div class="card-heading">
          <div>
            <p class="label">Mini žemėlapis</p>
            <h2>Lokacijos</h2>
          </div>
          <span class="pill">Hub</span>
        </div>

        <div class="game-map" aria-label="Mini žemėlapis">
          <span class="map-node home-node">Namai</span>
          <span class="map-node training-node">Treniruotės</span>
          <a class="map-node arena-node" href="fight.php">Arena</a>
          <span class="map-node masters-node locked">Meistrai</span>
        </div>
      </article>

      <article class="game-card inventory-card">
        <div class="card-heading">
          <div>
            <p class="label">Inventorius</p>
            <h2>Pradžios daiktai</h2>
          </div>
          <a class="pill" href="inventory.php">Atidaryti</a>
        </div>

        <div class="inventory-grid">
          <div><span>🥤</span><strong>Ki kapsulė</strong><small>x1</small></div>
          <div><span>🏋</span><strong>Svoriai</strong><small>x1</small></div>
          <div><span>🎟</span><strong>Turnyro bilietas</strong><small>locked</small></div>
          <div><span>🌱</span><strong>Energijos pupelė</strong><small>locked</small></div>
        </div>
      </article>
    </section>
  </main>

  <section id="chat" class="bottom-chat" aria-label="Veikėjo chat ir informacija">
    <div class="chat-character">
      <img class="game-silhouette game-silhouette-male" src="photo/character/male.png" alt="Vyro veikėjo siluetas chat lange">
      <img class="game-silhouette game-silhouette-female" src="photo/character/female.png" alt="Merginos veikėjo siluetas chat lange">
    </div>
    <div class="chat-copy">
      <span>Veikėjo info</span>
      <strong id="game-chat-name"><?= e($displayName) ?></strong>
      <p>HP <?= $hp ?> • Ki <?= $ki ?> • Stamina <?= $stamina ?> • XP <?= $xp ?>/<?= $requiredXp ?></p>
    </div>
    <form class="chat-form" action="#" method="post">
      <label for="chat-message">Žinutė</label>
      <input id="chat-message" name="chat-message" type="text" placeholder="Parašyk komandą arba pastabą...">
      <button class="button button-primary" type="submit">Siųsti</button>
    </form>
  </section>
</body>
</html>
