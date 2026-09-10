<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/environment.php';
$container = require __DIR__ . '/../config/container.php';

/** @var PDO $db */
$db = $container->get(PDO::class);

$count = 0;

// Do all image files referenced in the `vg_pokemon` table exist?
$stmt = $db->prepare(
    'SELECT
        `icon`,
        `sprite`
    FROM `vg_pokemon`'
);
$stmt->execute();
while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $icon = $result['icon'];
    $sprite = $result['sprite'];
    if (!file_exists(__DIR__ . "/../public/images/pokemon/icons/$icon")) {
        $count++;
        echo "MISSING: pokemon/icons/$icon\n";
    }
    if (!file_exists(__DIR__ . "/../public/images/pokemon/sprites/$sprite")) {
        $count++;
        echo "MISSING: pokemon/sprites/$sprite\n";
    }
}

// Do all image files referenced in the `vg_items` table exist?
$stmt = $db->prepare(
    'SELECT
        `icon`
    FROM `vg_items`'
);
$stmt->execute();
while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $icon = $result['icon'];
    if (!file_exists(__DIR__ . "/../public/images/items/$icon")) {
        $count++;
        echo "MISSING: items/$icon\n";
    }
}

echo "Total missing files: $count\n";

/*
Also put these urls somewhere safe
https://archives.bulbagarden.net/wiki/Category:Champions_menu_sprites
https://archives.bulbagarden.net/wiki/Category:Champions_Shiny_menu_sprites
https://archives.bulbagarden.net/w/index.php?title=Category:HOME_artwork
https://bulbapedia.bulbagarden.net/wiki/List_of_items_by_index_number_in_Pok%C3%A9mon_Champions
https://bulbapedia.bulbagarden.net/wiki/Shop_(Champions)
*/
