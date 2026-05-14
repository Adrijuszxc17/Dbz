# Z-Fusion

Z-Fusion — veiksmo kupinas drakonų kovų pasaulis, kuriame legendiniai kovotojai atrakina galingas transformacijas, valdo energijos galias ir kovoja epinėse arenose dėl stipriausiojo titulo. Prisijunk prie turnyrų, tobulink savo kovotoją ir tapk tikra fusion legenda.

PHP/MySQL projektas su futuristiniu drakonų kovų dizainu, registracijos/prisijungimo formomis, originalaus veikėjo kūrimu, game hub langu, drag & drop inventoriumi, NPC kovos sistema, statistikos blokais, arenos analitika ir turnyro reitingu.

## Failai

- `index.php` — pagrindinis Z-Fusion puslapis su PHP/MySQL registracija ir prisijungimu.
- `character.php` — originalaus žmogaus kovotojo kūrimas, lytis, pradiniai taškai ir saugojimas į MySQL.
- `game.php` — pagrindinis žaidimo langas su žaidėjo statusais, dienos progresu, DB veikėjo statistika, mini žemėlapiu, inventoriumi ir chat juosta.
- `inventory.php` — inventoriaus langas su veikėjo siluetu, 5 pagrindiniais įrangos slotais, greitais resursų slotais ir tempiamais daiktais.
- `fight.php` — bazinė kovos su NPC sistema su HP/Ki/Stamina barais, veiksmų mygtukais, NPC atsaku ir kovos logu.
- `auth.php` — registracijos ir prisijungimo handleris.
- `save-character.php` — veikėjo duomenų išsaugojimas į MySQL.
- `config/database.php` — PDO prisijungimas prie MySQL.
- `database/schema.sql` — MySQL lentelių sukūrimas.
- `database/2026_05_14_update_character_progress_defaults.sql` — migracija esamai DB, kad nauji veikėjai startuotų nuo level 0, XP 0 ir pilnų HP/Ki/Stamina.
- `database/2026_05_14_add_user_roles.sql` — migracija esamai DB, kad vartotojai turėtų `admin`, `vip`, `user`, `remejas` roles.
- `database/2026_05_14_add_chat_messages.sql` — migracija bendram DB chat langui.
- `script.js` — perkelia serverio vardą/siluetą į UI, valdo pradinių taškų limitą, drag & drop inventorių ir NPC kovos logiką.

Veikėjo siluetų failai:

- `photo/character/male.png`
- `photo/character/female.png`

## Paleidimas

1. Sukurk arba naudok duomenų bazę `aus37757_dbz`, importuodamas `database/schema.sql`.
   Jei DB jau sukurta anksčiau, papildomai paleisk `database/2026_05_14_update_character_progress_defaults.sql`, `database/2026_05_14_add_user_roles.sql` ir `database/2026_05_14_add_chat_messages.sql`.
2. Nustatyk DB aplinkos kintamuosius, jei reikia:
   - `DB_HOST`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
   
   Arba hostinge nukopijuok `config/local.example.php` į `config/local.php` ir įrašyk savo MySQL duomenis.
3. Paleisk PHP serverį:

```bash
php -S localhost:8000
```

4. Atidaryk `http://localhost:8000/index.php`.
