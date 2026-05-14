<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageUser = current_user();
$pageCharacter = current_character();
$displayName = display_name($pageUser);
$displayGender = display_gender($pageCharacter);
$level = (int) ($pageCharacter['level'] ?? 0);
$hp = (int) ($pageCharacter['hp'] ?? 100);
$kiPoints = (int) ($pageCharacter['ki_points'] ?? 6);
$maxKi = max(1, $kiPoints);
$ki = min((int) ($pageCharacter['ki'] ?? $maxKi), $maxKi);
$stamina = (int) ($pageCharacter['stamina'] ?? 100);
$rankName = rank_name_for_level($level);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Z-Fusion | Kova su NPC</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
  <script src="script.js" defer></script>
</head>
<body data-user-name="<?= e($displayName) ?>" data-user-gender="<?= e($displayGender) ?>" data-player-hp="<?= $hp ?>" data-player-ki="<?= $ki ?>" data-player-max-ki="<?= $maxKi ?>" data-player-stamina="<?= $stamina ?>">
  <div class="background-grid" aria-hidden="true"></div>
  <div class="energy-cloud energy-cloud-one" aria-hidden="true"></div>
  <div class="energy-cloud energy-cloud-two" aria-hidden="true"></div>

  <header class="topbar">
    <a class="brand" href="index.php" aria-label="Grįžti į Z-Fusion pradžią">
      <span class="brand-mark">Z</span>
      <span>
        <strong>Z-Fusion</strong>
        <small>Kova su NPC</small>
      </span>
    </a>

    <nav class="nav" aria-label="Kovos navigacija">
      <a href="game.php">Game</a>
      <a href="character.php">Veikėjas</a>
      <a href="inventory.php">Inventorius</a>
      <a href="#actions">Veiksmai</a>
      <a href="#battle-log">Logas</a>
    </nav>

    <a class="topbar-action" href="game.php">Išeiti</a>
  </header>

  <main class="fight-shell">
    <section class="fight-header">
      <div>
        <p class="label">Pirmoji NPC kova</p>
        <h1>Treniruočių arena</h1>
        <p>
          Išbandyk bazinę kovos sistemą prieš treniruočių NPC. Valdyk HP, Ki ir
          Stamina, rinkis veiksmus ir stebėk kovos logą.
        </p>
      </div>
      <button class="button button-primary" id="reset-fight" type="button">Perkrauti kovą</button>
    </section>

    <section class="fight-arena">
      <article class="combatant-card player-combatant">
        <div class="combatant-top">
          <div class="combatant-silhouette">
            <img class="game-silhouette game-silhouette-male" src="photo/character/male.png" alt="Žaidėjo vyro siluetas">
            <img class="game-silhouette game-silhouette-female" src="photo/character/female.png" alt="Žaidėjo merginos siluetas">
          </div>
          <div>
            <p class="label">Žaidėjas</p>
            <h2 id="fight-player-name"><?= e($displayName) ?></h2>
            <span class="rank-badge"><?= e($rankName) ?> • Level <?= $level ?></span>
          </div>
        </div>

        <div class="fight-bars">
          <div class="fight-bar hp-row">
            <span>HP</span>
            <div><i id="player-hp-bar" style="--value: <?= min($hp, 100) ?>%"></i></div>
            <strong id="player-hp-text"><?= $hp ?>/100</strong>
          </div>
          <div class="fight-bar ki-row">
            <span>Ki</span>
            <div><i id="player-ki-bar" style="--value: <?= (int) round(($ki / $maxKi) * 100) ?>%"></i></div>
            <strong id="player-ki-text"><?= $ki ?>/<?= $maxKi ?></strong>
          </div>
          <div class="fight-bar stamina-row">
            <span>Stamina</span>
            <div><i id="player-stamina-bar" style="--value: <?= min($stamina, 100) ?>%"></i></div>
            <strong id="player-stamina-text"><?= $stamina ?>/100</strong>
          </div>
        </div>
      </article>

      <div class="versus-core" aria-hidden="true">
        <span>VS</span>
      </div>

      <article class="combatant-card npc-combatant">
        <div class="combatant-top npc-top">
          <div>
            <p class="label">NPC</p>
            <h2>Treniruočių kovotojas</h2>
            <span class="rank-badge">AI sparring bot</span>
          </div>
          <div class="npc-avatar">NPC</div>
        </div>

        <div class="fight-bars">
          <div class="fight-bar hp-row">
            <span>HP</span>
            <div><i id="npc-hp-bar" style="--value: 100%"></i></div>
            <strong id="npc-hp-text">100/100</strong>
          </div>
          <div class="fight-bar ki-row">
            <span>Ki</span>
            <div><i id="npc-ki-bar" style="--value: 30%"></i></div>
            <strong id="npc-ki-text">30/100</strong>
          </div>
          <div class="fight-bar stamina-row">
            <span>Stamina</span>
            <div><i id="npc-stamina-bar" style="--value: 70%"></i></div>
            <strong id="npc-stamina-text">70/100</strong>
          </div>
        </div>
      </article>
    </section>

    <section class="fight-control-grid">
      <article id="actions" class="fight-actions-panel">
        <div class="card-heading">
          <div>
            <p class="label">Tavo ėjimas</p>
            <h2>Kovos veiksmai</h2>
          </div>
          <span class="pill" id="fight-turn-label">Žaidėjo ėjimas</span>
        </div>

        <div class="fight-actions">
          <button class="fight-action" type="button" data-fight-action="attack">
            <strong>Smūgis</strong>
            <small>-12 stamina • bazinė žala</small>
          </button>
          <button class="fight-action" type="button" data-fight-action="ki">
            <strong>Ki banga</strong>
            <small>-25 Ki • didesnė žala</small>
          </button>
          <button class="fight-action" type="button" data-fight-action="defend">
            <strong>Gynyba</strong>
            <small>Sumažina kitą žalą</small>
          </button>
          <button class="fight-action" type="button" data-fight-action="charge">
            <strong>Krauti Ki</strong>
            <small>+22 Ki • +8 stamina</small>
          </button>
          <button class="fight-action" type="button" data-fight-action="heal">
            <strong>Gyvybės kapsulė</strong>
            <small>+24 HP • 1 kartas</small>
          </button>
        </div>
      </article>

      <article id="battle-log" class="battle-log-panel">
        <div class="card-heading">
          <div>
            <p class="label">Kovos logas</p>
            <h2>Įvykiai</h2>
          </div>
          <span class="pill pill-red" id="fight-state-label">Active</span>
        </div>

        <ol class="battle-log" id="fight-log">
          <li>Kova prasidėjo. NPC laukia tavo pirmo veiksmo.</li>
        </ol>
      </article>
    </section>
  </main>
</body>
</html>
