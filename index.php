<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageUser = current_user();
$pageCharacter = current_character();
$displayName = display_name($pageUser);
$displayGender = display_gender($pageCharacter);
$flash = flash_get();
$dashboardStats = dashboard_stats();
$topFighters = top_fighters(5);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Z-Fusion</title>
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
    <a class="brand" href="#top" aria-label="Z-Fusion pradžia">
      <span class="brand-mark">Z</span>
      <span>
        <strong>Z-Fusion</strong>
        <small>Fusion legenda</small>
      </span>
    </a>

    <nav class="nav" aria-label="Pagrindinė navigacija">
      <a href="#auth">Prisijungimas</a>
      <a href="character.php">Veikėjas</a>
      <a href="game.php">Game</a>
      <a href="inventory.php">Inventorius</a>
      <a href="fight.php">Kova</a>
      <a href="#stats">Statistika</a>
      <a href="#arena">Arenos</a>
      <a href="#ranking">Reitingas</a>
    </nav>

    <a class="topbar-action" href="#auth">Prisijungti</a>
  </header>

  <main id="top">
    <?php if ($flash): ?>
      <div class="flash-message flash-<?= e($flash['type']) ?>">
        <?= e($flash['message']) ?>
      </div>
    <?php endif; ?>

    <section class="hero-shell">
      <div class="hero-copy">
        <h1>Z-Fusion</h1>
        <p class="hero-text">
          Z-Fusion — veiksmo kupinas drakonų kovų pasaulis, kuriame legendiniai
          kovotojai atrakina galingas transformacijas, valdo energijos galias ir
          kovoja epinėse arenose dėl stipriausiojo titulo. Prisijunk prie
          turnyrų, tobulink savo kovotoją ir tapk tikra fusion legenda.
        </p>

        <div class="hero-actions">
          <a class="button button-primary" href="#auth">Sukurti paskyrą</a>
          <a class="button button-ghost" href="character.php">Pasirinkti veikėją</a>
          <a class="button button-ghost" href="game.php">Atidaryti game</a>
          <a class="button button-ghost" href="#ranking">Turnyro reitingas</a>
        </div>
      </div>

      <aside class="scanner-card" aria-label="Aktyvios kovos skeneris">
        <div class="scanner-header">
          <span>Live scan</span>
          <strong>Sector 07</strong>
        </div>

        <div class="scanner-core">
          <div class="radar-ring ring-one"></div>
          <div class="radar-ring ring-two"></div>
          <div class="radar-ring ring-three"></div>
          <span class="scanner-value">91%</span>
        </div>

        <div class="scanner-meta">
          <div>
            <span>Ki aktyvumas</span>
            <strong>Aukštas</strong>
          </div>
          <div>
            <span>Grėsmė</span>
            <strong>S klasė</strong>
          </div>
        </div>
      </aside>
    </section>

    <section id="auth" class="auth-section">
      <div class="section-title compact">
        <p class="label">Paskyros pradžia</p>
        <h2>Registracija ir prisijungimas</h2>
        <p>
          Susikurk Z-Fusion paskyrą arba prisijunk prie esamos. Originalaus
          kovotojo kūrimas jau paruoštas atskirame puslapyje.
        </p>
      </div>

      <?php if ($flash): ?>
        <div class="flash-message auth-flash flash-<?= e($flash['type']) ?>">
          <?= e($flash['message']) ?>
        </div>
      <?php endif; ?>

      <div class="auth-grid">
        <form class="auth-card" action="auth.php" method="post">
          <input type="hidden" name="action" value="login">
          <div class="form-heading">
            <span class="form-icon">01</span>
            <div>
              <h3>Prisijungimas</h3>
              <p>Grįžk į savo kovų profilį.</p>
            </div>
          </div>

          <label for="login-name">Vartotojo vardas arba el. paštas</label>
          <input id="login-name" name="login-name" type="text" placeholder="pvz. fusion_hero" autocomplete="username" required>

          <label for="login-password">Slaptažodis</label>
          <input id="login-password" name="login-password" type="password" placeholder="Įvesk slaptažodį" autocomplete="current-password" required>

          <div class="form-row">
            <label class="check-option">
              <input type="checkbox" name="remember">
              <span>Prisiminti mane</span>
            </label>
            <a href="#auth">Pamiršai?</a>
          </div>

          <button class="button button-primary" type="submit">Prisijungti</button>
        </form>

        <form class="auth-card auth-card-accent" action="auth.php" method="post">
          <input type="hidden" name="action" value="register">
          <div class="form-heading">
            <span class="form-icon">02</span>
            <div>
              <h3>Registracija</h3>
              <p>Pasiruošk tapti fusion legenda.</p>
            </div>
          </div>

          <label for="register-name">Vartotojo vardas</label>
          <input id="register-name" name="register-name" type="text" placeholder="pvz. z_fusion_legend" autocomplete="username" required>

          <label for="register-email">El. paštas</label>
          <input id="register-email" name="register-email" type="email" placeholder="tavo@email.lt" autocomplete="email" required>

          <label for="register-password">Slaptažodis</label>
          <input id="register-password" name="register-password" type="password" placeholder="Sukurk slaptažodį" autocomplete="new-password" required>

          <button class="button button-primary" type="submit">Registruotis</button>
        </form>
      </div>
    </section>

    <section id="stats" class="panel-section">
      <div class="section-title">
        <p class="label">Pagrindiniai skaičiai</p>
        <h2>Z-Fusion turnyro statistika</h2>
        <p>
          Stebėk aktyvius turnyrus, kovų intensyvumą, transformacijų progresą
          ir grėsmių lygį viename futuristiniame valdymo centre.
        </p>
      </div>

      <div class="stats-grid">
        <article class="stat-card highlight">
          <span class="stat-icon">01</span>
          <p>Aktyvūs kovotojai</p>
          <strong><?= format_stat_number($dashboardStats['active_fighters']) ?></strong>
          <small>+<?= format_stat_number($dashboardStats['new_fighters']) ?> naujų per 30 d.</small>
        </article>

        <article class="stat-card">
          <span class="stat-icon">02</span>
          <p>Užfiksuotos kovos</p>
          <strong><?= format_stat_number($dashboardStats['total_fights']) ?></strong>
          <small><?= format_stat_number($dashboardStats['completed_fights']) ?> baigtų kovų</small>
        </article>

        <article class="stat-card">
          <span class="stat-icon">03</span>
          <p>Vidutinis galios lygis</p>
          <strong><?= format_stat_number($dashboardStats['avg_power']) ?></strong>
          <small>Pagal DB taškus ir lygį</small>
        </article>

        <article class="stat-card danger">
          <span class="stat-icon">04</span>
          <p>Kritinės grėsmės</p>
          <strong><?= format_stat_number($dashboardStats['critical_threats']) ?></strong>
          <small>Aktyvios NPC kovos</small>
        </article>
      </div>
    </section>

    <section id="arena" class="analytics-layout">
      <article class="analytics-card power-chart">
        <div class="card-heading">
          <div>
            <p class="label">Galios kreivė</p>
            <h2>Formų progresas</h2>
          </div>
          <span class="pill">Arenos peržiūra</span>
        </div>

        <div class="chart-bars" aria-label="Kovotojų formų galios grafikas">
          <div class="bar-row">
            <span>Base</span>
            <div class="bar-track"><span style="--value: 35%"></span></div>
            <strong>35%</strong>
          </div>
          <div class="bar-row">
            <span>Super forma</span>
            <div class="bar-track"><span style="--value: 62%"></span></div>
            <strong>62%</strong>
          </div>
          <div class="bar-row">
            <span>Blue</span>
            <div class="bar-track"><span style="--value: 81%"></span></div>
            <strong>81%</strong>
          </div>
          <div class="bar-row">
            <span>Ultra</span>
            <div class="bar-track"><span style="--value: 94%"></span></div>
            <strong>94%</strong>
          </div>
        </div>
      </article>

      <article class="analytics-card threat-map">
        <div class="card-heading">
          <div>
            <p class="label">Žemėlapis</p>
            <h2>Grėsmių zona</h2>
          </div>
          <span class="pill pill-red">S class</span>
        </div>

        <div class="map-shell" aria-label="Statinis grėsmių zonų vaizdas">
          <span class="zone zone-one"></span>
          <span class="zone zone-two"></span>
          <span class="zone zone-three"></span>
          <span class="zone-label label-one">West City</span>
          <span class="zone-label label-two">Orbit arena</span>
          <span class="zone-label label-three">Wasteland</span>
        </div>
      </article>
    </section>

    <section id="ranking" class="panel-section">
      <div class="section-title compact">
        <p class="label">Reitingo lentelė</p>
        <h2>Top kovotojai</h2>
      </div>

      <div class="table-card">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Kovotojas</th>
              <th>Klasė</th>
              <th>Galios lygis</th>
              <th>Win rate</th>
              <th>Būsena</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($topFighters): ?>
              <?php foreach ($topFighters as $index => $fighter): ?>
                <?php
                  $completed = (int) $fighter['total_completed'];
                  $wins = (int) $fighter['wins'];
                  $winRate = $completed > 0 ? (int) round(($wins / $completed) * 100) : 0;
                  $statusClass = ((int) $fighter['hp']) <= 25 ? 'danger' : (((int) $fighter['hp']) < 100 ? 'idle' : 'online');
                  $statusText = ((int) $fighter['hp']) <= 25 ? 'Low HP' : (((int) $fighter['hp']) < 100 ? 'Resting' : 'Ready');
                ?>
                <tr>
                  <td><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></td>
                  <td><?= e((string) $fighter['name']) ?></td>
                  <td>Level <?= (int) $fighter['level'] ?></td>
                  <td><?= number_format((int) $fighter['power_level'], 0, '.', ' ') ?></td>
                  <td><?= $winRate ?>%</td>
                  <td><span class="status <?= e($statusClass) ?>"><?= e($statusText) ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6">DB dar neturi sukurtų kovotojų.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>
</html>
