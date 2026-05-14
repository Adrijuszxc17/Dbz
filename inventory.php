<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageUser = current_user();
$pageCharacter = current_character();
$displayName = display_name($pageUser);
$displayGender = display_gender($pageCharacter);
$inventoryCapacity = 500;
$inventoryUsed = 0;

if (!empty($pageUser['id']) && ($pdo = db())) {
    try {
        $statement = $pdo->prepare('SELECT COALESCE(SUM(quantity), 0) AS used_slots FROM inventory_items WHERE user_id = ?');
        $statement->execute([(int) $pageUser['id']]);
        $inventoryUsed = (int) ($statement->fetch()['used_slots'] ?? 0);
    } catch (PDOException) {
        $inventoryUsed = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Z-Fusion | Inventorius</title>
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
        <small>Inventorius</small>
      </span>
    </a>

    <nav class="nav" aria-label="Inventoriaus navigacija">
      <a href="index.php">Pradžia</a>
      <a href="character.php">Veikėjas</a>
      <a href="game.php">Game</a>
      <a href="#equipment">Įranga</a>
      <a href="#items">Daiktai</a>
    </nav>

    <a class="topbar-action" href="game.php">Atgal į game</a>
  </header>

  <main class="inventory-shell">
    <section class="inventory-header">
      <div>
        <p class="label">Drag & drop inventorius</p>
        <h1>Veikėjo įranga</h1>
        <p>
          Tempk daiktą pele iš dešinės inventoriaus pusės į kairėje esantį
          veikėjo slotą. Netinkamas daiktas į netinkamą slotą neįsidės.
        </p>
      </div>
      <div class="inventory-player-chip">
        <span>Veikėjas</span>
        <strong id="inventory-player-name"><?= e($displayName) ?></strong>
      </div>
    </section>

    <section class="inventory-layout">
      <article id="equipment" class="equipment-panel">
        <div class="card-heading">
          <div>
            <p class="label">Kairė pusė</p>
            <h2>Siluetas ir slotai</h2>
          </div>
          <span class="pill">5 pagr.</span>
        </div>

        <div class="equipment-stage">
          <div class="equipment-slot head-slot drop-slot" data-accept="head">
            <span>Galva</span>
            <strong>Kepurė</strong>
          </div>
          <div class="equipment-slot clothes-slot drop-slot" data-accept="clothes">
            <span>Kūnas</span>
            <strong>Apranga</strong>
          </div>
          <div class="equipment-slot bracelet-slot drop-slot" data-accept="bracelet">
            <span>Ranka</span>
            <strong>Apyrankė</strong>
          </div>
          <div class="equipment-slot wraps-slot drop-slot" data-accept="wraps">
            <span>Rankos</span>
            <strong>Raiščiai</strong>
          </div>
          <div class="equipment-slot boots-slot drop-slot" data-accept="boots">
            <span>Kojos</span>
            <strong>Batai</strong>
          </div>

          <div class="inventory-silhouette">
            <div class="energy-halo"></div>
            <img class="game-silhouette game-silhouette-male" src="photo/character/male.png" alt="Vyro veikėjo siluetas inventoriuje">
            <img class="game-silhouette game-silhouette-female" src="photo/character/female.png" alt="Merginos veikėjo siluetas inventoriuje">
          </div>
        </div>

        <div class="quick-slots" aria-label="Greiti resursų slotai">
          <div class="quick-slot drop-slot" data-accept="ki">
            <span>Ki</span>
            <strong>Ki atstatymas</strong>
          </div>
          <div class="quick-slot drop-slot" data-accept="beans">
            <span>Pupelės</span>
            <strong>Energijos pupelės</strong>
          </div>
          <div class="quick-slot drop-slot" data-accept="health">
            <span>HP</span>
            <strong>Gyvybės atstatymas</strong>
          </div>
        </div>
      </article>

      <aside id="items" class="inventory-bag">
        <div class="card-heading">
          <div>
            <p class="label">Dešinė pusė</p>
            <h2>Visas inventorius</h2>
          </div>
          <span class="pill"><?= $inventoryUsed ?>/<?= $inventoryCapacity ?></span>
        </div>

        <div class="bag-grid" aria-label="Daiktų sąrašas">
          <button class="bag-item" type="button" draggable="true" data-item="Training Boots" data-slot="boots" data-quantity="1">
            <span class="item-quantity">x1</span>
            <span class="item-photo boots-photo">BT</span>
            <strong>Treniruočių batai</strong>
            <small>Slotas: batai</small>
          </button>
          <button class="bag-item" type="button" draggable="true" data-item="Starter Gi" data-slot="clothes" data-quantity="1">
            <span class="item-quantity">x1</span>
            <span class="item-photo clothes-photo">GI</span>
            <strong>Pradinė apranga</strong>
            <small>Slotas: apranga</small>
          </button>
          <button class="bag-item" type="button" draggable="true" data-item="Ki Bracelet" data-slot="bracelet" data-quantity="1">
            <span class="item-quantity">x1</span>
            <span class="item-photo bracelet-photo">AP</span>
            <strong>Ki apyrankė</strong>
            <small>Slotas: apyrankė</small>
          </button>
          <button class="bag-item" type="button" draggable="true" data-item="Hand Wraps" data-slot="wraps" data-quantity="2">
            <span class="item-quantity">x2</span>
            <span class="item-photo wraps-photo">RS</span>
            <strong>Kovos raiščiai</strong>
            <small>Slotas: raiščiai</small>
          </button>
          <button class="bag-item" type="button" draggable="true" data-item="Training Cap" data-slot="head" data-quantity="1">
            <span class="item-quantity">x1</span>
            <span class="item-photo head-photo">KP</span>
            <strong>Treniruočių kepurė</strong>
            <small>Slotas: galva</small>
          </button>
          <button class="bag-item" type="button" draggable="true" data-item="Ki Capsule" data-slot="ki" data-quantity="3">
            <span class="item-quantity">x3</span>
            <span class="item-photo ki-photo">KI</span>
            <strong>Ki kapsulė</strong>
            <small>Greitas slotas</small>
          </button>
          <button class="bag-item" type="button" draggable="true" data-item="Energy Bean" data-slot="beans" data-quantity="5">
            <span class="item-quantity">x5</span>
            <span class="item-photo bean-photo">PU</span>
            <strong>Energijos pupelės</strong>
            <small>Greitas slotas</small>
          </button>
          <button class="bag-item" type="button" draggable="true" data-item="Health Capsule" data-slot="health" data-quantity="4">
            <span class="item-quantity">x4</span>
            <span class="item-photo health-photo">HP</span>
            <strong>Gyvybės kapsulė</strong>
            <small>Greitas slotas</small>
          </button>
        </div>

        <p class="inventory-hint" id="inventory-hint">
          Paimk daiktą pele ir numesk ant tinkamo slot'o.
        </p>
      </aside>
    </section>
  </main>
</body>
</html>
